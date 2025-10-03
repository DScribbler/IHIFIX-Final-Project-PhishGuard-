<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/includes/db.php';

$totals = [
    'messages' => $pdo->query('SELECT COUNT(*) FROM messages')->fetchColumn(),
    'clicks' => $pdo->query('SELECT COUNT(*) FROM clicks')->fetchColumn(),
    'submissions' => $pdo->query('SELECT COUNT(*) FROM submissions')->fetchColumn(),
];

$campaigns = $pdo->query('SELECT * FROM campaigns ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PhishGuard - Analytics</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background: #3498db;
            color: #fff;
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.3s;
        }

        .back-btn:hover {
            background: #2980b9;
        }

        .container {
            max-width: 900px;
            margin: auto;
            padding: 40px 20px;
        }

        h1, h2 {
            text-align: center;
            color: #2c3e50;
        }

        h1 {
            margin-bottom: 20px;
        }

        .stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 40px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .card {
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            padding: 20px;
            border-radius: 10px;
            flex: 1 1 150px;
            text-align: center;
            font-weight: 600;
            font-size: 1.2rem;
            color: #34495e;
        }

        .card .big {
            display: block;
            font-size: 2.8rem;
            margin-top: 8px;
            color: #e67e22;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 40px;
        }

        th, td {
            padding: 12px 16px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #34495e;
            color: #fff;
            font-weight: 600;
        }

        tr:hover {
            background: #f9f9f9;
        }

        /* Chart styling - fix endless scroll */
        canvas#chart {
            display: block;
            margin: 0 auto 40px auto;
            max-width: 100%;
            height: 250px !important;
        }

        footer {
            background: #2c3e50;
            color: #fff;
            text-align: center;
            padding: 20px;
            font-size: 0.9rem;
        }

        a {
            color: #e67e22;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            header h1 {
                font-size: 1.6rem;
            }

            .stats {
                flex-direction: column;
                align-items: center;
            }

            .card {
                width: 80%;
            }
        }
    </style>
</head>
<body>

<header>
    <a href="campaign.php" class="back-btn">← Back</a>
    <h1>Analytics</h1>
    <a href="logout.php" class="logout">Logout</a>
</header>

<div class="container">

    <div class="stats">
        <div class="card">
            Messages
            <span class="big"><?= $totals['messages'] ?></span>
        </div>
        <div class="card">
            Clicks
            <span class="big"><?= $totals['clicks'] ?></span>
        </div>
        <div class="card">
            Submissions
            <span class="big"><?= $totals['submissions'] ?></span>
        </div>
    </div>

    <h2>Campaigns</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Subject</th>
                <th>Channel</th>
                <th>Strength</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($campaigns as $c): ?>
            <tr>
                <td><?= $c['id'] ?></td>
                <td><?= htmlspecialchars($c['name']) ?></td>
                <td><?= $c['channel'] ?></td>
                <td><?= $c['strength'] ?></td>
                <td><?= $c['created_at'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Behavior</h2>
    <canvas id="chart" width="600"></canvas>

    <script>
        const ctx = document.getElementById('chart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Messages', 'Clicks', 'Submissions'],
                datasets: [{
                    label: 'Counts',
                    data: [
                        <?= $totals['messages'] ?>,
                        <?= $totals['clicks'] ?>,
                        <?= $totals['submissions'] ?>
                    ],
                    backgroundColor: ['#4e73df', '#1cc88a', '#e74a3b']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision:0
                        }
                    }
                }
            }
        });
    </script>

</div>

<footer>
    <p>&copy; <?= date('Y') ?> PhishGuard. All Rights Reserved.</p>
</footer>

</body>
</html>
