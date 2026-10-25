# ITX Digital Services — ZeptoMail PHP Email Examples

PHP examples for sending mail using the Zoho CPaaS email REST endpoint and ZeptoMail SMTP.

## Security first

- The credentials supplied in chat are intentionally **not included** in this repository.
- If those credentials were live, revoke/rotate the REST API key and SMTP password before use.
- Never commit `.env`, API keys, SMTP passwords, or real customer email data.
- Keep this test project behind authentication or remove `index.php` after testing.
- Use a private GitHub repository if the project is business-sensitive.

## Requirements

- PHP 8.1+
- PHP cURL extension (REST example)
- Composer
- A verified sender domain/address in ZeptoMail

## Setup

```bash
composer install
cp .env.example .env
```

Edit `.env` locally and enter the newly rotated credentials. Confirm the sender address is verified in ZeptoMail.

## Test REST API

Configure `ZOHO_EMAIL_API_KEY` and `MAIL_FROM_ADDRESS` in `.env`, then open the project through a PHP-enabled web server and use `index.php` to send a test message.

The REST sample posts to `https://cpaas.zoho.com/v1.1/email`. It uses the authorization value from `ZOHO_EMAIL_API_KEY`; confirm the exact authorization header format in your Zoho CPaaS account documentation. Some Zoho products use a scheme prefix, so do not guess it.

## Test SMTP

Default configuration is SSL on port 465. To use TLS on port 587, set:

```dotenv
SMTP_PORT=587
SMTP_ENCRYPTION=tls
```

Then run:

```bash
php smtp-send.php recipient@example.com
```

Use the SMTP username/password shown for your ZeptoMail SMTP configuration. Do not assume a REST API key is interchangeable with SMTP credentials; use the credential type specified by Zoho.

## GitHub upload

1. Create a repository on GitHub. Do not add secrets or a `.env` file.
2. Extract this ZIP.
3. From the project folder run:

```bash
git init
git add .
git status
git commit -m "Add secure ZeptoMail PHP email examples"
git branch -M main
git remote add origin https://github.com/YOUR-USERNAME/YOUR-REPOSITORY.git
git push -u origin main
```

4. Before committing, inspect `git status` and confirm `.env` is not listed.
5. On a server, configure environment variables securely outside the repository.

## Notes

API responses and SMTP failures should be checked in server logs. Do not show raw API responses, stack traces, or secrets to public visitors. Restrict access to test endpoints and use rate limiting before production use.
