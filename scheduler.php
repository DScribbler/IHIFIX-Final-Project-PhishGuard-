<?php
require 'includes/db.php';
require 'includes/functions.php'; // sending logic here

$now = date('Y-m-d H:i:s');

$stmt = $pdo->prepare("SELECT * FROM campaigns WHERE status='scheduled' AND scheduled_time <= ?");
$stmt->execute([$now]);
$campaigns = $stmt->fetchAll();

foreach ($campaigns as $c) {
    // Send campaign (reuse your existing send function)
    send_campaign($c['id'], $c['targets'], $c['message']);
    
    // Mark as sent
    $update = $pdo->prepare("UPDATE campaigns SET status='sent' WHERE id=?");
    $update->execute([$c['id']]);
}
?>
