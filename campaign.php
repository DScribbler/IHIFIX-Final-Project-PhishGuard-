<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/mailer.php';
require_once __DIR__ . '/includes/sms.php';
require_once __DIR__ . '/includes/template_checker_engine.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'] ?? '';
    $body = $_POST['body'] ?? '';
    $channel = $_POST['channel'] ?? 'email';
    $targets_raw = $_POST['targets'] ?? '';
    $send_type = $_POST['send_type'] ?? 'immediate';
    $scheduled_at = ($send_type === 'scheduled' && !empty($_POST['scheduled_at'])) ? $_POST['scheduled_at'] : null;

    $targets = array_filter(array_map('trim', explode(',', $targets_raw)));

    if (!$subject || !$body || empty($targets)) {
        $message = 'Subject, body and targets required.';
    } else {
        $analysis = analyze_template($subject . "\n\n" . $body);
        $status = ($send_type === 'scheduled' && $scheduled_at) ? 'scheduled' : 'pending';

        $stmt = $pdo->prepare('INSERT INTO campaigns (name, template, channel, strength, scheduled_at, status, created_at) 
                               VALUES (?,?,?,?,?,?,NOW())');
        $stmt->execute([$subject, $body, $channel, $analysis['score'], $scheduled_at, $status]);
        $campaign_id = $pdo->lastInsertId();

        foreach ($targets as $t) {
            $token = bin2hex(random_bytes(16));
            $stmt = $pdo->prepare('INSERT INTO messages (campaign_id,target,token,created_at) VALUES (?,?,?,NOW())');
            $stmt->execute([$campaign_id, $t, $token]);

            if ($send_type === 'immediate') {
                $link = get_base_url() . '/track.php?token=' . $token;
                if ($channel === 'email' && filter_var($t, FILTER_VALIDATE_EMAIL)) {
                    $html = nl2br(htmlspecialchars($body)) . "<p><a href='$link'>Click here</a></p>";
                    send_mail($t, $subject, $html);
                } else {
                    send_sms($t, $body . " Link: " . $link);
                }
            }
        }

        $message = ($send_type === 'immediate')
            ? 'Campaign sent immediately. Strength score: ' . $analysis['score']
            : 'Campaign scheduled for ' . htmlspecialchars($scheduled_at) . '. Strength score: ' . $analysis['score'];
    }
}

$recent = $pdo->query('SELECT * FROM campaigns ORDER BY id DESC LIMIT 10')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PhishGuard - Campaigns</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: #fdfdfd;
            color: #333;
        }

        header {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: #fff;
            padding: 30px 20px;
            text-align: center;
            position: relative;
        }

        header h1 {
            margin: 0;
            font-size: 2.2rem;
        }

        header .logout {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #e74c3c;
            color: #fff;
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.3s;
        }

        header .logout:hover {
            background: #c0392b;
        }

        header .template-checker {
            position: absolute;
            top: 20px;
            right: 110px;
            background: #2980b9;
            color: #fff;
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.3s;
        }

        header .template-checker:hover {
            background: #1c5980;
        }

        header .analytics {
            position: absolute;
            top: 20px;
            left: 20px;
            background: #27ae60;
            color: #fff;
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.3s;
        }

        header .analytics:hover {
            background: #1e8449;
        }

        .container {
            max-width: 900px;
            margin: auto;
            padding: 40px 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
        }

        form label {
            font-weight: 600;
            display: block;
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="datetime-local"],
        textarea,
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
        }

        textarea {
            resize: vertical;
        }

        .radio-group {
            margin-bottom: 20px;
        }

        .radio-group label {
            margin-right: 20px;
            font-weight: normal;
        }

        button {
            background: linear-gradient(135deg, #e67e22, #d35400);
            color: #fff;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(230, 126, 34, 0.5);
        }

        .success {
            background: #2ecc71;
            color: #fff;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 16px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #34495e;
            color: #fff;
        }

        tr:hover {
            background: #f9f9f9;
        }

        footer {
            background: #2c3e50;
            color: #fff;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
            font-size: 0.9rem;
        }

        @media (max-width: 600px) {
            header h1 {
                font-size: 1.6rem;
            }

            button {
                width: 100%;
            }

            header .analytics,
            header .template-checker,
            header .logout {
                position: static;
                display: inline-block;
                margin: 5px 10px 0 0;
            }
        }
    </style>
    <script>
        function toggleScheduleField() {
            let type = document.querySelector('input[name="send_type"]:checked').value;
            document.getElementById('schedule-field').style.display = (type === 'scheduled') ? 'block' : 'none';
        }
    </script>
</head>
<body>
<header>
    <a href="analytics.php" class="analytics">Analytics</a>
    <h1>Campaign Manager</h1>
    <a href="template_checker.php" class="template-checker">Template Checker</a>
    <a href="logout.php" class="logout">Logout</a>
</header>

<div class="container">
    <?php if ($message): ?>
        <p class="success"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <h2>Create New Campaign</h2>
    <form method="post">
        <label>Subject</label>
        <input type="text" name="subject" required>

        <label>Body</label>
        <textarea name="body" rows="6" required></textarea>

        <label>Channel</label>
        <select name="channel">
            <option value="email">Email</option>
            <option value="sms">SMS</option>
        </select>

        <label>Targets (comma-separated)</label>
        <input type="text" name="targets" placeholder="user1@example.com, user2@example.com">

        <label>Send Type</label>
        <div class="radio-group">
            <label><input type="radio" name="send_type" value="immediate" checked onclick="toggleScheduleField()"> Send Immediately</label>
            <label><input type="radio" name="send_type" value="scheduled" onclick="toggleScheduleField()"> Schedule</label>
        </div>

        <div id="schedule-field" style="display:none;">
            <label>Schedule Date/Time</label>
            <input type="datetime-local" name="scheduled_at">
        </div>

        <button type="submit">Save Campaign</button>
    </form>

    <h2>Recent Campaigns</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Subject</th>
            <th>Channel</th>
            <th>Strength</th>
            <th>Status</th>
            <th>Scheduled</th>
            <th>Created</th>
        </tr>
        <?php foreach ($recent as $c): ?>
            <tr>
                <td><?= $c['id'] ?></td>
                <td><?= htmlspecialchars($c['name']) ?></td>
                <td><?= $c['channel'] ?></td>
                <td><?= $c['strength'] ?></td>
                <td><?= $c['status'] ?></td>
                <td><?= $c['scheduled_at'] ?: '-' ?></td>
                <td><?= $c['created_at'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> PhishGuard. All Rights Reserved.</p>
</footer>
</body>
</html>
