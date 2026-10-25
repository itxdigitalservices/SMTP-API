<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

Dotenv::createImmutable(__DIR__)->safeLoad();

session_start();
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = (string)($_POST['csrf'] ?? '');
    if (!hash_equals($_SESSION['csrf'], $token)) {
        http_response_code(403);
        exit('Invalid request token.');
    }

    $to = filter_var(trim((string)($_POST['to'] ?? '')), FILTER_VALIDATE_EMAIL);
    if (!$to) {
        $message = 'Please enter a valid recipient email address.';
    } else {
        try {
            require __DIR__ . '/src/send_rest.php';
            $message = sendRestEmail($to, 'ITX ZeptoMail test', '<div><b>Test email sent successfully.</b></div>');
        } catch (Throwable $e) {
            error_log((string)$e);
            $message = 'Email could not be sent. Check the server error log and configuration.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ITX ZeptoMail Test</title>
</head>
<body>
  <h1>ITX ZeptoMail Test</h1>
  <?php if ($message !== ''): ?><p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
    <label>Recipient email
      <input type="email" name="to" required>
    </label>
    <button type="submit">Send test email</button>
  </form>
  <p>Keep this page private or remove it after testing. Do not expose API credentials in browser code.</p>
</body>
</html>
