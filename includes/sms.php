<?php
require_once __DIR__ . '/config.php';
function send_sms($to, $message) {
    if (TWILIO_SID === 'CHANGE_ME') {
        error_log('Twilio not configured - skipping SMS to ' . $to);
        return false;
    }
    $url = 'https://api.twilio.com/2010-04-01/Accounts/' . TWILIO_SID . '/Messages.json';
    $data = http_build_query(['From'=>TWILIO_FROM,'To'=>$to,'Body'=>$message]);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_USERPWD, TWILIO_SID . ':' . TWILIO_TOKEN);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $resp = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    if ($err) { error_log('Twilio curl error: ' . $err); return false; }
    return true;
}
