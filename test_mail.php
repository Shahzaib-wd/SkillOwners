<?php
require_once 'config.php';
require_once 'helpers/MailHelper.php';

echo "Testing SMTP Mailer...\n";
echo "Host: " . SMTP_HOST . "\n";
echo "Port: " . SMTP_PORT . "\n";
echo "User: " . SMTP_USER . "\n";

$to = 'test@example.com'; // Replace with a real email if testing manually
$subject = 'Test Email from Skill Owners';
$body = 'This is a test email sent via the custom SMTPMailer class.';

echo "Sending email to $to...\n";

if (MailHelper::send($to, $subject, $body)) {
    echo "Email sent successfully!\n";
} else {
    echo "Failed to send email. Check error logs.\n";
}
