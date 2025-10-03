<?php
function analyze_template($text) {
    $score = 100;
    $issues = [];
    if (preg_match('/\b(u|pls|4u|gr8)\b/i', $text)) { $score -= 10; $issues[] = 'Unprofessional abbreviations'; }
    if (!preg_match('/\b(Dear|Hello|Hi)\b/i', $text)) { $score -= 15; $issues[] = 'No personalization detected'; }
    if (!preg_match('/https?:\/\//i', $text)) { $score -= 20; $issues[] = 'No link detected (phishing often uses links)'; }
    if (preg_match('/urgent|immediately|action required|verify/i', $text)) { $score -= 10; $issues[] = 'Uses urgency language'; }
    if (!preg_match('/(Regards|Sincerely|Thank you)/i', $text)) { $score -= 5; $issues[] = 'Missing professional closing'; }
    if (str_word_count(strip_tags($text)) < 20) { $score -= 5; $issues[] = 'Too short - may look suspicious'; }
    if ($score < 0) $score = 0;
    return ['score'=>$score,'issues'=>$issues];
}
