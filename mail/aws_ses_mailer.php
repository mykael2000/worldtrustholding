<?php

if (!defined('AFC_DEFAULT_SITE_URL')) {
    define('AFC_DEFAULT_SITE_URL', 'https://worldtrustholding.com');
}
if (!defined('AFC_TRANSFER_EMAIL_THEME')) {
    define('AFC_TRANSFER_EMAIL_THEME', [
        'accent' => '#5b21b6',
        'accent_soft' => '#f5f3ff',
        'headline' => 'Transfer Alert',
        'subline' => 'Your transfer request has been recorded on World Trust Holding.',
    ]);
}
if (!defined('AFC_TRANSACTION_EMAIL_THEME')) {
    define('AFC_TRANSACTION_EMAIL_THEME', [
        'accent' => '#0b5ed7',
        'accent_soft' => '#eff6ff',
        'headline' => 'Transaction Alert',
        'subline' => 'Your account transaction has been updated on World Trust Holding.',
    ]);
}

if (!function_exists('afc_mail_load_root_env')) {
    function afc_mail_load_root_env(): void
    {
        static $loaded = false;

        if ($loaded) {
            return;
        }

        $loaded = true;
        $envPath = dirname(__DIR__) . '/.env';
        if (!is_file($envPath) || !is_readable($envPath)) {
            return;
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (str_starts_with($line, 'export ')) {
                $line = trim(substr($line, 7));
            }

            $parts = explode('=', $line, 2);
            if (count($parts) !== 2) {
                continue;
            }

            $name = trim($parts[0]);
            $value = trim($parts[1]);

            if ($name === '') {
                continue;
            }

            if ((str_starts_with($value, '"') && str_ends_with($value, '"')) || (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                $value = substr($value, 1, -1);
            }

            if (getenv($name) === false || getenv($name) === '') {
                putenv($name . '=' . $value);
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}

if (!function_exists('afc_mail_env')) {
    function afc_mail_env(string $key, ?string $default = null): ?string
    {
        afc_mail_load_root_env();

        $value = getenv($key);
        if ($value === false || $value === null || $value === '') {
            return $default;
        }

        return $value;
    }
}

if (!function_exists('afc_mail_h')) {
    function afc_mail_h($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('afc_mail_default_sender')) {
    function afc_mail_default_sender(): string
    {
        $domain = parse_url(AFC_DEFAULT_SITE_URL, PHP_URL_HOST);
        if (!is_string($domain) || $domain === '') {
            $domain = 'worldtrustholding.com';
        }

        return 'noreply@' . $domain;
    }
}

if (!function_exists('afc_mail_normalize_email_list')) {
    function afc_mail_normalize_email_list($input): array
    {
        if (is_array($input)) {
            $emails = [];
            foreach ($input as $value) {
                $emails = array_merge($emails, preg_split('/[,;]+/', (string) $value));
            }
            $emails = array_filter(array_map('trim', $emails));
        } else {
            $emails = array_filter(array_map('trim', preg_split('/[,;]+/', (string) $input)));
        }
        $valid = [];

        foreach ($emails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $valid[] = strtolower($email);
            }
        }

        return array_values(array_unique($valid));
    }
}

if (!function_exists('afc_mail_collect_upload_attachments')) {
    function afc_mail_collect_upload_attachments(array $fileBag, array $options = []): array
    {
        $attachments = [];
        $errors = [];
        $maxFileSize = (int) ($options['max_file_size'] ?? (5 * 1024 * 1024));
        $maxTotalSize = (int) ($options['max_total_size'] ?? (7 * 1024 * 1024));
        $allowedMimeTypes = $options['allowed_mime_types'] ?? [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
        ];

        if (empty($fileBag) || !isset($fileBag['name'])) {
            return [
                'attachments' => [],
                'errors' => [],
            ];
        }

        $names = is_array($fileBag['name']) ? $fileBag['name'] : [$fileBag['name']];
        $tmpNames = is_array($fileBag['tmp_name']) ? $fileBag['tmp_name'] : [$fileBag['tmp_name']];
        $sizes = is_array($fileBag['size']) ? $fileBag['size'] : [$fileBag['size']];
        $errorsBag = is_array($fileBag['error']) ? $fileBag['error'] : [$fileBag['error']];
        $types = is_array($fileBag['type']) ? $fileBag['type'] : [$fileBag['type']];

        $totalSize = 0;
        $finfo = function_exists('finfo_open') ? finfo_open(FILEINFO_MIME_TYPE) : false;

        foreach ($names as $index => $originalName) {
            $uploadError = (int) ($errorsBag[$index] ?? UPLOAD_ERR_NO_FILE);

            if ($uploadError === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if ($uploadError !== UPLOAD_ERR_OK) {
                $errors[] = 'One attachment could not be uploaded. Please try again.';
                continue;
            }

            $tmpName = (string) ($tmpNames[$index] ?? '');
            $size = (int) ($sizes[$index] ?? 0);
            $safeName = trim((string) $originalName);

            if ($tmpName === '' || !is_uploaded_file($tmpName)) {
                $errors[] = 'One attachment upload is invalid.';
                continue;
            }

            if ($safeName === '') {
                $safeName = 'attachment-' . ($index + 1);
            }

            if ($size <= 0) {
                $errors[] = $safeName . ' is empty and was not attached.';
                continue;
            }

            if ($size > $maxFileSize) {
                $errors[] = $safeName . ' exceeds the 5MB per-file limit.';
                continue;
            }

            $totalSize += $size;
            if ($totalSize > $maxTotalSize) {
                $errors[] = 'Total attachment size exceeds the 7MB limit for email delivery.';
                break;
            }

            $mimeType = (string) ($types[$index] ?? 'application/octet-stream');
            if ($finfo) {
                $detected = finfo_file($finfo, $tmpName);
                if (is_string($detected) && $detected !== '') {
                    $mimeType = $detected;
                }
            }

            if (!in_array($mimeType, $allowedMimeTypes, true)) {
                $errors[] = $safeName . ' is not an allowed attachment type. Use PDF, JPG, PNG, GIF or WEBP.';
                continue;
            }

            $content = file_get_contents($tmpName);
            if ($content === false) {
                $errors[] = $safeName . ' could not be read for attachment.';
                continue;
            }

            $attachments[] = [
                'name' => $safeName,
                'type' => $mimeType,
                'content' => $content,
            ];
        }

        if ($finfo) {
            finfo_close($finfo);
        }

        return [
            'attachments' => $attachments,
            'errors' => array_values(array_unique($errors)),
        ];
    }
}

if (!function_exists('afc_mail_bootstrap_sdk')) {
    function afc_mail_bootstrap_sdk(): bool
    {
        if (class_exists('Aws\\Ses\\SesClient')) {
            return true;
        }

        $paths = [
            afc_mail_env('AFC_AWS_AUTOLOAD_PATH'),
            dirname(__DIR__) . '/aws/vendor/autoload.php',
            dirname(__DIR__) . '/admin/aws/vendor/autoload.php',
        ];

        foreach ($paths as $path) {
            if (!is_string($path) || $path === '') {
                continue;
            }

            if (file_exists($path)) {
                require_once $path;
                break;
            }
        }

        return class_exists('Aws\\Ses\\SesClient');
    }
}

if (!function_exists('afc_mail_build_raw_email')) {
    function afc_mail_build_raw_email(
        string $fromEmail,
        string $fromName,
        array $to,
        array $cc,
        array $bcc,
        ?string $replyTo,
        string $subject,
        string $htmlBody,
        string $textBody,
        array $attachments = []
    ): string {
        $mixedBoundary = 'mixed_' . bin2hex(random_bytes(16));
        $altBoundary = 'alt_' . bin2hex(random_bytes(16));

        $safeFromName = mb_encode_mimeheader($fromName, 'UTF-8');
        $headers = [];
        $headers[] = "From: {$safeFromName} <{$fromEmail}>";

        if (!empty($to)) {
            $headers[] = 'To: ' . implode(', ', $to);
        }
        if (!empty($cc)) {
            $headers[] = 'Cc: ' . implode(', ', $cc);
        }
        if (!empty($bcc)) {
            $headers[] = 'Bcc: ' . implode(', ', $bcc);
        }
        if (!empty($replyTo) && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
            $headers[] = "Reply-To: {$replyTo}";
        }

        $headers[] = 'Subject: ' . mb_encode_mimeheader($subject, 'UTF-8');
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = "Content-Type: multipart/mixed; boundary=\"{$mixedBoundary}\"";

        $message = implode("\r\n", $headers) . "\r\n\r\n";

        $message .= "--{$mixedBoundary}\r\n";
        $message .= "Content-Type: multipart/alternative; boundary=\"{$altBoundary}\"\r\n\r\n";

        $message .= "--{$altBoundary}\r\n";
        $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $message .= chunk_split(base64_encode($textBody)) . "\r\n";

        $message .= "--{$altBoundary}\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $message .= chunk_split(base64_encode($htmlBody)) . "\r\n";

        $message .= "--{$altBoundary}--\r\n";

        foreach ($attachments as $attachment) {
            $filename = str_replace(["\r", "\n", '"'], '', (string) ($attachment['name'] ?? 'attachment'));
            $mime = (string) ($attachment['type'] ?? 'application/octet-stream');
            $content = (string) ($attachment['content'] ?? '');

            if ($content === '') {
                continue;
            }

            $message .= "--{$mixedBoundary}\r\n";
            $message .= "Content-Type: {$mime}; name=\"{$filename}\"\r\n";
            $message .= "Content-Description: {$filename}\r\n";
            $message .= "Content-Disposition: attachment; filename=\"{$filename}\"; size=" . strlen($content) . ";\r\n";
            $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
            $message .= chunk_split(base64_encode($content)) . "\r\n";
        }

        $message .= "--{$mixedBoundary}--";

        return $message;
    }
}

if (!function_exists('afc_send_aws_raw_email')) {
    function afc_send_aws_raw_email(array $payload): array
    {
        if (!afc_mail_bootstrap_sdk()) {
            return [
                'success' => false,
                'error' => 'AWS SDK not found. Upload aws/vendor/autoload.php or set AFC_AWS_AUTOLOAD_PATH.',
            ];
        }

        $to = afc_mail_normalize_email_list($payload['to'] ?? []);
        $cc = afc_mail_normalize_email_list($payload['cc'] ?? []);
        $bcc = afc_mail_normalize_email_list($payload['bcc'] ?? []);

        if (empty($to)) {
            return [
                'success' => false,
                'error' => 'At least one valid recipient is required.',
            ];
        }

        $subject = trim((string) ($payload['subject'] ?? ''));
        $htmlBody = (string) ($payload['html_body'] ?? '');
        $textBody = (string) ($payload['text_body'] ?? '');

        if ($subject === '' || $htmlBody === '' || $textBody === '') {
            return [
                'success' => false,
                'error' => 'Subject, HTML body, and text body are required.',
            ];
        }

        $fromEmail = trim((string) ($payload['from_email'] ?? afc_mail_env('MAIL_FROM_ADDRESS', afc_mail_default_sender())));
        $fromName = trim((string) ($payload['from_name'] ?? afc_mail_env('MAIL_FROM_NAME', 'World Trust Holding')));
        $replyTo = trim((string) ($payload['reply_to'] ?? ''));

        if (!filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'error' => 'Invalid sender email configured.',
            ];
        }

        try {
            $clientConfig = [
                'version' => 'latest',
                'region' => afc_mail_env('AWS_SES_REGION', afc_mail_env('AWS_DEFAULT_REGION', 'us-east-1')),
            ];

            $awsKey = afc_mail_env('AWS_ACCESS_KEY_ID');
            $awsSecret = afc_mail_env('AWS_SECRET_ACCESS_KEY');
            if (!empty($awsKey) && !empty($awsSecret)) {
                $clientConfig['credentials'] = [
                    'key' => $awsKey,
                    'secret' => $awsSecret,
                ];
            }

            $rawMessage = afc_mail_build_raw_email(
                $fromEmail,
                $fromName,
                $to,
                $cc,
                $bcc,
                $replyTo,
                $subject,
                $htmlBody,
                $textBody,
                $payload['attachments'] ?? []
            );

            $client = new Aws\Ses\SesClient($clientConfig);

            $result = $client->sendRawEmail([
                'Source' => $fromEmail,
                'Destinations' => array_values(array_unique(array_merge($to, $cc, $bcc))),
                'RawMessage' => [
                    'Data' => $rawMessage,
                ],
            ]);

            return [
                'success' => true,
                'message_id' => $result->get('MessageId'),
            ];
        } catch (Aws\Exception\AwsException $e) {
            return [
                'success' => false,
                'error' => $e->getAwsErrorMessage() ?: $e->getMessage(),
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}

if (!function_exists('afc_format_transaction_details')) {
    function afc_format_transaction_details($details): array
    {
        $details = (string) $details;
        $decoded = json_decode($details, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $lines = [];
            foreach ($decoded as $key => $value) {
                if (is_scalar($value) && trim((string) $value) !== '') {
                    $label = ucwords(str_replace(['_', '-'], ' ', (string) $key));
                    $lines[] = [$label, (string) $value];
                }
            }

            if (!empty($lines)) {
                return $lines;
            }
        } elseif ($details !== '') {
            $trimmedDetails = trim($details);
            $looksLikeJson = (strpos($trimmedDetails, '{') === 0 && substr($trimmedDetails, -1) === '}')
                || (strpos($trimmedDetails, '[') === 0 && substr($trimmedDetails, -1) === ']');
            if ($looksLikeJson) {
                error_log('AFC transaction detail JSON parse failed: ' . json_last_error_msg());
            }
        }

        return [['Details', $details !== '' ? $details : 'N/A']];
    }
}

if (!function_exists('afc_build_transaction_alert_email')) {
    function afc_build_transaction_alert_email(array $transaction, array $options = []): array
    {
        $siteUrl = afc_mail_env('APP_URL', AFC_DEFAULT_SITE_URL);
        $dashboardUrl = rtrim($siteUrl, '/') . '/login.php';

        $amount = number_format((float) ($transaction['amount'] ?? 0), 2);
        $type = strtoupper((string) ($transaction['type'] ?? 'TRANSACTION'));
        $status = strtoupper((string) ($transaction['status'] ?? 'PENDING'));
        $reference = (string) ($transaction['tranx_id'] ?? 'N/A');
        $createdAt = (string) ($transaction['created_at'] ?? date('Y-m-d H:i:s'));
        $name = (string) ($options['recipient_name'] ?? $transaction['username'] ?? 'Customer');
        $eventLabel = (string) ($options['event_label'] ?? 'Transaction Update');
        $description = trim((string) ($transaction['description'] ?? ''));
        $detailsRows = afc_format_transaction_details($transaction['details'] ?? '');

        $isTransfer = isset($options['is_transfer'])
            ? (bool) $options['is_transfer']
            : in_array(strtolower((string) ($options['channel'] ?? '')), ['local_transfer', 'international_transfer', 'transfer'], true);

        $theme = $isTransfer ? AFC_TRANSFER_EMAIL_THEME : AFC_TRANSACTION_EMAIL_THEME;
        $accent = (string) ($theme['accent'] ?? '#0b5ed7');
        $accentSoft = (string) ($theme['accent_soft'] ?? '#eff6ff');
        $headline = (string) ($theme['headline'] ?? 'Transaction Alert');
        $subline = (string) ($theme['subline'] ?? 'Your account transaction has been updated on World Trust Holding.');

        $rowsHtml = '';
        $rowsText = '';
        foreach ($detailsRows as [$label, $value]) {
            $rowsHtml .= '<tr><td style="padding:8px 10px;border-bottom:1px solid #eef2f7;color:#4b5563;font-size:13px;">' . afc_mail_h($label) . '</td><td style="padding:8px 10px;border-bottom:1px solid #eef2f7;color:#111827;font-size:13px;">' . afc_mail_h($value) . '</td></tr>';
            $rowsText .= "- {$label}: {$value}\n";
        }

        $subject = $headline . ': ' . $type . ' $' . $amount . ' (' . $status . ')';

        $html = '<!doctype html><html><body style="margin:0;background:#f4f6fb;font-family:Arial,Helvetica,sans-serif;color:#111827;">'
            . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:24px 12px;"><tr><td align="center">'
            . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px;background:#ffffff;border-radius:14px;overflow:hidden;border:1px solid #e5e7eb;">'
            . '<tr><td style="background:' . $accent . ';padding:22px 28px;color:#ffffff;">'
            . '<div style="font-size:22px;font-weight:700;">World Trust Holding</div>'
            . '<div style="font-size:14px;opacity:.95;margin-top:6px;">' . afc_mail_h($headline) . '</div>'
            . '</td></tr>'
            . '<tr><td style="padding:24px 28px;">'
            . '<p style="margin:0 0 10px;font-size:15px;">Hello ' . afc_mail_h($name) . ',</p>'
            . '<p style="margin:0 0 16px;font-size:14px;color:#374151;line-height:1.6;">' . afc_mail_h($subline) . '</p>'
            . '<div style="background:' . $accentSoft . ';border:1px solid #ddd6fe;border-radius:10px;padding:14px 16px;margin-bottom:16px;">'
            . '<div style="font-size:13px;color:#6b7280;margin-bottom:6px;">' . afc_mail_h($eventLabel) . '</div>'
            . '<div style="font-size:26px;line-height:1.1;font-weight:800;color:' . $accent . ';">$' . afc_mail_h($amount) . '</div>'
            . '<div style="font-size:13px;color:#374151;margin-top:8px;">Type: <strong>' . afc_mail_h($type) . '</strong> &nbsp;|&nbsp; Status: <strong>' . afc_mail_h($status) . '</strong></div>'
            . '</div>'
            . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border:1px solid #eef2f7;border-radius:8px;overflow:hidden;">'
            . '<tr><td style="padding:8px 10px;background:#f8fafc;color:#64748b;font-size:12px;font-weight:700;" colspan="2">Transaction Information</td></tr>'
            . '<tr><td style="padding:8px 10px;border-bottom:1px solid #eef2f7;color:#4b5563;font-size:13px;">Reference</td><td style="padding:8px 10px;border-bottom:1px solid #eef2f7;color:#111827;font-size:13px;">' . afc_mail_h($reference) . '</td></tr>'
            . '<tr><td style="padding:8px 10px;border-bottom:1px solid #eef2f7;color:#4b5563;font-size:13px;">Created</td><td style="padding:8px 10px;border-bottom:1px solid #eef2f7;color:#111827;font-size:13px;">' . afc_mail_h($createdAt) . '</td></tr>'
            . ($description !== '' ? '<tr><td style="padding:8px 10px;border-bottom:1px solid #eef2f7;color:#4b5563;font-size:13px;">Description</td><td style="padding:8px 10px;border-bottom:1px solid #eef2f7;color:#111827;font-size:13px;">' . afc_mail_h($description) . '</td></tr>' : '')
            . $rowsHtml
            . '</table>'
            . '<p style="margin:18px 0 0;font-size:13px;color:#4b5563;line-height:1.6;">For your security, never share your account credentials. If you did not authorize this activity, contact support immediately.</p>'
            . '<p style="margin:18px 0 0;"><a href="' . afc_mail_h($dashboardUrl) . '" style="display:inline-block;padding:11px 18px;background:' . $accent . ';color:#fff;text-decoration:none;border-radius:8px;">Open Dashboard</a></p>'
            . '</td></tr>'
            . '<tr><td style="padding:18px 28px;background:#f8fafc;color:#6b7280;font-size:12px;">' . afc_mail_h(afc_mail_default_sender()) . ' · ' . afc_mail_h($siteUrl) . '</td></tr>'
            . '</table></td></tr></table></body></html>';

        $text = "World Trust Holding\n{$headline}\n\nHello {$name},\n{$subline}\n\n"
            . "Event: {$eventLabel}\nAmount: \${$amount}\nType: {$type}\nStatus: {$status}\nReference: {$reference}\nCreated: {$createdAt}\n"
            . ($description !== '' ? "Description: {$description}\n" : '')
            . "\nDetails:\n{$rowsText}\n"
            . "Dashboard: {$dashboardUrl}\n"
            . "If you did not authorize this activity, contact support immediately.\n";

        return [
            'subject' => $subject,
            'html_body' => $html,
            'text_body' => $text,
        ];
    }
}

if (!function_exists('afc_build_general_email_template')) {
    function afc_build_general_email_template(string $body, string $title = 'World Trust Holding Message', array $options = []): array
    {
        $siteUrl = afc_mail_env('APP_URL', AFC_DEFAULT_SITE_URL);
        $safeBody = nl2br(afc_mail_h(trim($body)));
        $preheader = trim((string) ($options['preheader'] ?? 'Important communication from World Trust Holding.'));
        $greeting = trim((string) ($options['greeting'] ?? 'Valued Customer'));
        $intro = trim((string) ($options['intro'] ?? 'Please review the official update below from World Trust Holding.'));
        $highlightTitle = trim((string) ($options['highlight_title'] ?? 'Official Notice'));
        $highlightText = trim((string) ($options['highlight_text'] ?? 'This message was issued by the World Trust Holding administration desk.'));
        $ctaLabel = trim((string) ($options['cta_label'] ?? 'Visit Website'));
        $ctaUrl = trim((string) ($options['cta_url'] ?? $siteUrl));
        $signatureName = trim((string) ($options['signature_name'] ?? 'Client Service Desk'));
        $signatureRole = trim((string) ($options['signature_role'] ?? 'World Trust Holding'));

        $showCta = $ctaLabel !== '' && filter_var($ctaUrl, FILTER_VALIDATE_URL);
        $ctaHtml = $showCta
            ? '<p style="margin:22px 0 0;"><a href="' . afc_mail_h($ctaUrl) . '" style="display:inline-block;padding:12px 20px;background:#0b5ed7;color:#ffffff;text-decoration:none;border-radius:999px;font-size:13px;font-weight:700;">' . afc_mail_h($ctaLabel) . '</a></p>'
            : '';

        $highlightHtml = $highlightText !== ''
            ? '<tr><td style="padding:0 32px 22px;">'
                . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:linear-gradient(180deg,#eff6ff 0%,#f8fbff 100%);border:1px solid #dbeafe;border-radius:14px;">'
                . '<tr><td style="padding:18px 20px;">'
                . '<div style="font-size:11px;letter-spacing:.9px;text-transform:uppercase;color:#1d4ed8;font-weight:700;">' . afc_mail_h($highlightTitle !== '' ? $highlightTitle : 'Official Notice') . '</div>'
                . '<div style="font-size:14px;line-height:1.7;color:#1f2937;margin-top:8px;">' . nl2br(afc_mail_h($highlightText)) . '</div>'
                . '</td></tr></table></td></tr>'
            : '';

        $html = '<!doctype html><html><body style="margin:0;background:#eef3f8;font-family:Arial,Helvetica,sans-serif;color:#111827;">'
            . '<div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">' . afc_mail_h($preheader) . '</div>'
            . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:28px 12px;"><tr><td align="center">'
            . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:720px;background:#ffffff;border-radius:22px;overflow:hidden;border:1px solid #dbe4ef;box-shadow:0 12px 30px rgba(15,23,42,0.08);">'
            . '<tr><td style="background:linear-gradient(135deg,#0b3a75 0%,#14519a 100%);padding:26px 32px;color:#ffffff;">'
            . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0"><tr>'
            . '<td style="vertical-align:top;">'
            . '<div style="font-size:25px;font-weight:700;letter-spacing:.4px;">World Trust Holding</div>'
            . '<div style="font-size:13px;opacity:.92;margin-top:6px;">Professional client communication from the administration desk</div>'
            . '<div style="font-size:22px;font-weight:700;line-height:1.3;margin-top:18px;">' . afc_mail_h($title) . '</div>'
            . '</td>'
            . '<td style="vertical-align:top;text-align:right;">'
            . '<div style="display:inline-block;padding:6px 12px;border-radius:999px;background:rgba(255,255,255,0.16);font-size:11px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;">Official Mail</div>'
            . '</td>'
            . '</tr></table></td></tr>'
            . '<tr><td style="padding:30px 32px 18px;font-size:14px;line-height:1.8;color:#334155;">'
            . '<p style="margin:0 0 12px;font-size:15px;color:#0f172a;">Hello ' . afc_mail_h($greeting) . ',</p>'
            . '<p style="margin:0;">' . afc_mail_h($intro) . '</p>'
            . '</td></tr>'
            . $highlightHtml
            . '<tr><td style="padding:0 32px 22px;">'
            . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border:1px solid #e6edf5;border-radius:16px;background:#ffffff;">'
            . '<tr><td style="padding:22px 22px 20px;font-size:14px;line-height:1.85;color:#334155;">' . $safeBody . $ctaHtml . '</td></tr>'
            . '</table></td></tr>'
            . '<tr><td style="padding:0 32px 26px;">'
            . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f8fafc;border:1px solid #e5edf5;border-radius:14px;">'
            . '<tr><td style="padding:18px 20px;font-size:13px;line-height:1.8;color:#475569;">'
            . '<div style="font-weight:700;color:#0f172a;">' . afc_mail_h($signatureName) . '</div>'
            . '<div>' . afc_mail_h($signatureRole) . '</div>'
            . '<div style="margin-top:10px;">This communication may contain confidential account-related information. If you received it in error, please disregard it and notify the sender.</div>'
            . '</td></tr></table></td></tr>'
            . '<tr><td style="padding:18px 32px;background:#f8fafc;color:#64748b;font-size:12px;border-top:1px solid #e5edf5;">'
            . '<strong style="color:#334155;">World Trust Holding</strong><br>'
            . 'Sender: ' . afc_mail_h(afc_mail_default_sender()) . ' &nbsp;|&nbsp; Website: ' . afc_mail_h($siteUrl) . '<br>'
            . 'Please do not share your login credentials or security details by email.'
            . '</td></tr>'
            . '</table></td></tr></table></body></html>';

        $text = ($preheader !== '' ? $preheader . "\n\n" : '')
            . $title . "\n\n"
            . 'Hello ' . $greeting . ",\n"
            . $intro . "\n\n"
            . ($highlightTitle !== '' || $highlightText !== '' ? ($highlightTitle !== '' ? $highlightTitle . ":\n" : '') . $highlightText . "\n\n" : '')
            . trim($body) . "\n\n"
            . ($showCta ? $ctaLabel . ': ' . $ctaUrl . "\n\n" : '')
            . $signatureName . ' - ' . $signatureRole . "\n"
            . $siteUrl;

        return [
            'html_body' => $html,
            'text_body' => $text,
        ];
    }
}
