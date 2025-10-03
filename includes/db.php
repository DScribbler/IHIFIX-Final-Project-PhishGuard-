<?php
require_once __DIR__ . '/config.php';
try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    die('DB connection failed: ' . $e->getMessage() . '. Ensure you created the database and updated includes/config.php');
}
// create tables if they don't exist
$pdo->exec("CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, email VARCHAR(160) UNIQUE, password_hash VARCHAR(255), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);"); 
$pdo->exec("CREATE TABLE IF NOT EXISTS campaigns (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255), template TEXT, channel ENUM('email','sms'), strength INT, scheduled_at DATETIME NULL, created_at DATETIME);"); 
$pdo->exec("CREATE TABLE IF NOT EXISTS messages (id INT AUTO_INCREMENT PRIMARY KEY, campaign_id INT, target VARCHAR(255), token VARCHAR(64), sent_at DATETIME NULL, submitted_at DATETIME NULL, created_at DATETIME);"); 
$pdo->exec("CREATE TABLE IF NOT EXISTS clicks (id INT AUTO_INCREMENT PRIMARY KEY, campaign_id INT, user VARCHAR(255), token VARCHAR(64), ip VARCHAR(100), user_agent TEXT, created_at DATETIME);"); 
$pdo->exec("CREATE TABLE IF NOT EXISTS submissions (id INT AUTO_INCREMENT PRIMARY KEY, campaign_id INT, user VARCHAR(255), username VARCHAR(255), password VARCHAR(255), created_at DATETIME);"); 
// ensure default admin exists
$stmt = $pdo->query("SELECT COUNT(*) FROM users"); $count = $stmt->fetchColumn(); if ($count == 0) { $hash = password_hash('admin123', PASSWORD_BCRYPT); $ins = $pdo->prepare('INSERT INTO users (email,password_hash) VALUES (?,?)'); $ins->execute(['admin@phishguard.local', $hash]); }
