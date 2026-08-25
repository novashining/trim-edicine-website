<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /contact/');
    exit;
}

function clean_line($value, $max = 200) {
    $value = trim((string)$value);
    $value = str_replace(["\r", "\n"], ' ', $value);
    return mb_substr($value, 0, $max);
}

$name = clean_line($_POST['name'] ?? '', 120);
$email = clean_line($_POST['email'] ?? '', 180);
$subject = clean_line($_POST['subject'] ?? '', 160);
$message = trim((string)($_POST['message'] ?? ''));
$website = trim((string)($_POST['website'] ?? ''));

if ($website !== '') {
    header('Location: /contact/?sent=1');
    exit;
}

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
    header('Location: /contact/?error=1');
    exit;
}

$message = mb_substr($message, 0, 8000);
$to = 'info@trim-edicine.com';
$mailSubject = 'Website inquiry';
if ($subject !== '') {
    $mailSubject .= ': ' . $subject;
}

$body = "A message was submitted through trim-edicine.com.\n\n";
$body .= "Name: {$name}\n";
$body .= "Email: {$email}\n";
$body .= "Subject: " . ($subject !== '' ? $subject : '(not provided)') . "\n\n";
$body .= "Message:\n{$message}\n";

$headers = [
    'From: TRIM-edicine Website <info@trim-edicine.com>',
    'Content-Type: text/plain; charset=UTF-8'
];

$sent = mail($to, $mailSubject, $body, implode("\r\n", $headers));
header('Location: /contact/?' . ($sent ? 'sent=1' : 'error=1'));
exit;
?>