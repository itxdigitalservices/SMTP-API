<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;

Dotenv::createImmutable(__DIR__)->safeLoad();

$to = $argv[1] ?? '';
if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Usage: php smtp-send.php recipient@example.com\n");
    exit(2);
}

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = getenv('SMTP_HOST') ?: 'smtp.zeptomail.com';
    $mail->SMTPAuth = true;
    $mail->Username = getenv('SMTP_USERNAME') ?: '';
    $mail->Password = getenv('SMTP_PASSWORD') ?: '';
    $mail->Port = (int)(getenv('SMTP_PORT') ?: 465);
    $encryption = strtolower(getenv('SMTP_ENCRYPTION') ?: 'ssl');
    $mail->SMTPSecure = $encryption === 'tls'
        ? PHPMailer::ENCRYPTION_STARTTLS
        : PHPMailer::ENCRYPTION_SMTPS;

    $from = getenv('MAIL_FROM_ADDRESS') ?: '';
    if ($from === '' || $mail->Username === '' || $mail->Password === '') {
        throw new RuntimeException('Configure MAIL_FROM_ADDRESS, SMTP_USERNAME and SMTP_PASSWORD in .env.');
    }

    $mail->setFrom($from, getenv('MAIL_FROM_NAME') ?: 'ITX Digital Services');
    $mail->addAddress($to);
    $mail->isHTML(true);
    $mail->Subject = 'ITX ZeptoMail SMTP test';
    $mail->Body = '<div><b>Test email sent successfully.</b></div>';
    $mail->AltBody = 'Test email sent successfully.';
    $mail->send();
    echo "Email sent successfully.\n";
} catch (Throwable $e) {
    error_log('SMTP send failed: ' . $e->getMessage());
    fwrite(STDERR, "Email sending failed. Check server logs and SMTP settings.\n");
    exit(1);
}
