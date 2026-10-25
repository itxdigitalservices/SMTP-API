<?php
declare(strict_types=1);

/**
 * Sends email via Zoho CPaaS / ZeptoMail REST API.
 * Credentials are read from environment variables, never hard-coded.
 */
function sendRestEmail(string $recipient, string $subject, string $htmlBody): string
{
    $apiKey = getenv('ZOHO_EMAIL_API_KEY') ?: '';
    $from = getenv('MAIL_FROM_ADDRESS') ?: '';
    $fromName = getenv('MAIL_FROM_NAME') ?: 'ITX Digital Services';

    if ($apiKey === '' || $from === '') {
        throw new RuntimeException('Missing ZOHO_EMAIL_API_KEY or MAIL_FROM_ADDRESS.');
    }
    if (!function_exists('curl_init')) {
        throw new RuntimeException('The PHP cURL extension is required.');
    }

    $payload = [
        'from' => ['address' => $from, 'name' => $fromName],
        'to' => [['email_address' => ['address' => $recipient]]],
        'subject' => $subject,
        'htmlbody' => $htmlBody,
    ];

    $ch = curl_init('https://cpaas.zoho.com/v1.1/email');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_THROW_ON_ERROR),
        CURLOPT_HTTPHEADER => [
            'accept: application/json',
            'authorization: ' . $apiKey,
            'content-type: application/json',
        ],
    ]);

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false) {
        throw new RuntimeException('REST request failed: ' . $curlError);
    }
    if ($status < 200 || $status >= 300) {
        error_log('Zoho REST HTTP ' . $status . ': ' . $response);
        throw new RuntimeException('Zoho REST API returned HTTP ' . $status . '.');
    }

    return 'Email request accepted by the API. Response: ' . substr((string)$response, 0, 1000);
}
