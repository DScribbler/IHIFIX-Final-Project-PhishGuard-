<?php

// Fallback stub for analyze_template() if missing (remove if you have a real one)
if (!function_exists('analyze_template')) {
    function analyze_template(string $template): array {
        // Simple stub: returns perfect score with no issues
        return [
            'score' => 100,
            'issues' => []
        ];
    }
}

// Helper: analyze links found in text and produce issues + deductions
function analyze_links_in_text(string $text): array {
    $issues = [];
    $details = [];
    $deduction = 0;

    // Find URLs (basic, covers http/https and common forms)
    preg_match_all('/\bhttps?:\/\/[^\s"<>\)]+/i', $text, $matches);
    $urls = array_unique($matches[0] ?? []);

    // Also catch common shortened link forms without scheme (e.g. bit.ly/abc)
    preg_match_all('/\bwww\.[^\s"<>\)]+/i', $text, $m2);
    foreach ($m2[0] ?? [] as $u) {
        if (!in_array($u, $urls, true)) $urls[] = 'http://' . $u;
    }

    // Common URL shorteners (not exhaustive)
    $shorteners = [
        'bit.ly','tinyurl.com','t.co','goo.gl','ow.ly','buff.ly','is.gd','bit.do','adf.ly','shorturl.at',
        'cutt.ly','tiny.cc','lc.chat','rebrand.ly','rb.gy'
    ];

    foreach ($urls as $url) {
        $record = ['url' => $url, 'issues' => []];

        // Basic parse
        $parsed = parse_url($url);
        $host = $parsed['host'] ?? '';
        $scheme = $parsed['scheme'] ?? '';
        $path = $parsed['path'] ?? '';
        $query = $parsed['query'] ?? '';
        $user = $parsed['user'] ?? null;

        if ($host && filter_var($host, FILTER_VALIDATE_IP)) {
            $record['issues'][] = 'URL uses an IP address instead of a domain (suspicious).';
            $deduction += 20;
        }

        if ($user !== null || (strpos($url, '@') !== false && preg_match('/https?:\/\/[^\/]*@/i', $url))) {
            $record['issues'][] = 'URL contains embedded userinfo (@) — often used to obfuscate real destination.';
            $deduction += 20;
        }

        if ($scheme !== 'https') {
            $record['issues'][] = 'Not using HTTPS — connections are not encrypted (use HTTPS links).';
            $deduction += 10;
        }

        if (stripos($host, 'xn--') !== false) {
            $record['issues'][] = 'Domain uses punycode (xn--) which can visually mimic others (IDN homograph risk).';
            $deduction += 20;
        }

        foreach ($shorteners as $s) {
            if (stripos($host, $s) !== false) {
                $record['issues'][] = "Shortened URL service detected ({$s}) — hides final destination.";
                $deduction += 15;
                break;
            }
        }

        if (strlen($url) > 120) {
            $record['issues'][] = 'URL is very long (hard to inspect visually).';
            $deduction += 5;
        }

        if ($query && strlen($query) > 60) {
            $record['issues'][] = 'Long query string — may indicate tracking/obfuscation.';
            $deduction += 5;
        }

        $dotCount = substr_count($host, '.');
        if ($dotCount >= 3) {
            $record['issues'][] = 'Host has many subdomain levels — could be a subdomain trick (e.g., paypal.example.com).';
            $deduction += 10;
        }

        if (isset($parsed['port']) && !in_array($parsed['port'], [80, 443], true)) {
            $record['issues'][] = 'URL uses a non-standard port which may indicate an unusual service.';
            $deduction += 5;
        }

        $record['host'] = $host ?: '(no host)';
        $record['scheme'] = $scheme ?: '(no scheme)';
        $record['ok'] = empty($record['issues']);
        $details[] = $record;
    }

    if (empty($urls)) {
        return ['issues' => [], 'details' => [], 'deduction' => 0, 'count' => 0];
    }

    foreach ($details as $d) {
        foreach ($d['issues'] as $i) {
            if (!in_array($i, $issues, true)) $issues[] = $i;
        }
    }

    return [
        'issues' => $issues,
        'details' => $details,
        'deduction' => $deduction,
        'count' => count($urls)
    ];
}

// Main analysis flow
$result = null;
$extra_link_analysis = null;
$final_score = null;
$template = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $template = $_POST['template'] ?? '';
    $engine_result = analyze_template($template);
    $base_score = isset($engine_result['score']) ? intval($engine_result['score']) : 100;
    $engine_issues = $engine_result['issues'] ?? [];

    $link_analysis = analyze_links_in_text($template);
    $extra_link_analysis = $link_analysis;

    $combined = $base_score - $link_analysis['deduction'];
    if ($combined < 0) $combined = 0;
    if ($combined > 100) $combined = 100;

    $combined_issues = $engine_issues;
    foreach ($link_analysis['issues'] as $li) {
        if (!in_array($li, $combined_issues, true)) $combined_issues[] = $li;
    }

    $result = [
        'base' => [
            'score' => $base_score,
            'issues' => $engine_issues
        ],
        'links' => $link_analysis,
        'combined' => [
            'score' => $combined,
            'issues' => $combined_issues
        ]
    ];

    $final_score = $combined;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>PhishGuard - Template Checker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
        }
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: #fdfdfd;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: #fff;
            padding: 30px 20px;
            text-align: center;
            position: relative;
        }
        header h1 { margin: 0; font-size: 2.2rem; }
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
        header .logout:hover { background: #c0392b; }

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

        .container { flex: 1 0 auto; max-width: 900px; margin: auto; padding: 40px 20px; }

        label { font-weight: 600; display: block; margin-bottom: 8px; color: #2c3e50; font-size: 1.05rem; }
        textarea { width: 100%; min-height: 200px; padding: 12px; border: 1px solid #ccc; border-radius: 8px; font-size: 1rem; resize: vertical; box-sizing: border-box; margin-bottom: 20px; font-family: inherit; }
        button { background: linear-gradient(135deg, #e67e22, #d35400); color: #fff; padding: 12px 24px; border: none; border-radius: 8px; font-size: 1rem; cursor: pointer; transition: transform 0.2s ease, box-shadow 0.2s ease; }
        button:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(230,126,34,0.5); }

        .result { background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.08); padding: 20px; border-radius: 10px; color: #34495e; margin-bottom: 20px; }
        .result h3 { margin-top: 0; color: #2c3e50; }
        .result ul { list-style-type: disc; margin-left: 20px; padding-left: 0; }
        .result ul li { margin-bottom: 6px; }

        .score-pill { display:inline-block; padding:8px 14px; border-radius:999px; font-weight:700; color:#fff; }
        .score-good { background:#2ecc71; }
        .score-warn { background:#f1c40f; color:#2c3e50; }
        .score-bad { background:#e74c3c; }

        .link-details { margin-top: 12px; }
        .link-row { background:#fafafa; border:1px solid #eee; padding:10px 12px; border-radius:8px; margin-bottom:10px; font-family:monospace; font-size:0.95rem; }
        .suggestions { margin-top:12px; padding:12px; background:#fff8f0; border-radius:8px; border:1px solid #ffe6cc; color:#8a4b1a; }

        footer { background: #2c3e50; color: #fff; text-align: center; padding: 20px; font-size: 0.9rem; margin-top: 20px; flex-shrink: 0; }

        @media (max-width:600px) {
            header h1 { font-size: 1.6rem; }
            button { width:100%; }
        }
    </style>
</head>
<body>
<header>
    <a href="campaign.php" class="back-btn">← Back</a>
    <h1>Template Strength Checker</h1>
    <a href="logout.php" class="logout">Logout</a>
</header>

<div class="container">
    <form method="post" novalidate>
        <label for="template">Enter your template text below:</label>
        <textarea id="template" name="template" placeholder="Paste your template code here..." required><?= htmlspecialchars($template) ?></textarea>
        <button type="submit">Analyze Template</button>
    </form>

    <?php if ($result !== null): ?>
        <div class="result">
            <h3>Base Template Score: 
                <span class="score-pill <?= $result['base']['score'] >= 80 ? 'score-good' : ($result['base']['score'] >= 50 ? 'score-warn' : 'score-bad') ?>">
                    <?= $result['base']['score'] ?>/100
                </span>
            </h3>
            <?php if (count($result['base']['issues']) > 0): ?>
                <p>Issues found:</p>
                <ul>
                    <?php foreach ($result['base']['issues'] as $issue): ?>
                        <li><?= htmlspecialchars($issue) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No issues found in base template analysis.</p>
            <?php endif; ?>

            <hr>

            <h3>Link Analysis (<?= $extra_link_analysis['count'] ?? 0 ?> links found):</h3>
            <?php if (($extra_link_analysis['count'] ?? 0) === 0): ?>
                <p>No URLs detected in the template text.</p>
            <?php else: ?>
                <?php foreach ($extra_link_analysis['details'] as $link): ?>
                    <div class="link-row">
                        <strong><?= htmlspecialchars($link['url']) ?></strong>
                        <?php if (empty($link['issues'])): ?>
                            <p style="color:green; margin: 4px 0 0;">No issues detected.</p>
                        <?php else: ?>
                            <ul style="margin:4px 0 0; color:#d35400;">
                                <?php foreach ($link['issues'] as $issue): ?>
                                    <li><?= htmlspecialchars($issue) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <?php if (count($extra_link_analysis['issues']) > 0): ?>
                    <p><strong>Summary of link issues:</strong></p>
                    <ul>
                        <?php foreach ($extra_link_analysis['issues'] as $issue): ?>
                            <li><?= htmlspecialchars($issue) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php endif; ?>

            <hr>

            <h3>Final Combined Score: 
                <span class="score-pill <?= $final_score >= 80 ? 'score-good' : ($final_score >= 50 ? 'score-warn' : 'score-bad') ?>">
                    <?= $final_score ?>/100
                </span>
            </h3>
            <?php if (count($result['combined']['issues']) > 0): ?>
                <p>Combined issues from all checks:</p>
                <ul>
                    <?php foreach ($result['combined']['issues'] as $issue): ?>
                        <li><?= htmlspecialchars($issue) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No issues found in combined analysis.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<footer>
    &copy; <?= date('Y') ?> PhishGuard - Template Security
</footer>
</body>
</html>
