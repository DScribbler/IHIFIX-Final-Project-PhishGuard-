<?php
require_once __DIR__ . '/config.php';
function send_mail($to, $subject, $html) {
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= 'From: PhishGuard <' . (defined('SMTP_USER')?SMTP_USER:'noreply@phishguard.local') . '>' . "\r\n";
    @mail($to, $subject, $html, $headers);
    error_log('Mail queued to ' . $to);
    return true;
}
