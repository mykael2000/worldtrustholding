<?php
include('includes/header.php');
require_once __DIR__ . '/../mail/aws_ses_mailer.php';

$flashType = '';
$flashMessage = '';
$debugMessage = '';
$composerDefaults = [
    'greeting_name' => (string) ($_POST['greeting_name'] ?? 'Valued Customer'),
    'preheader' => (string) ($_POST['preheader'] ?? 'Important communication from World Trust Holding.'),
    'intro_line' => (string) ($_POST['intro_line'] ?? 'Please review the official update below from World Trust Holding.'),
    'highlight_title' => (string) ($_POST['highlight_title'] ?? 'Official Notice'),
    'highlight_text' => (string) ($_POST['highlight_text'] ?? 'This message was issued by the World Trust Holding administration desk.'),
    'cta_label' => (string) ($_POST['cta_label'] ?? 'Visit Website'),
    'cta_url' => (string) ($_POST['cta_url'] ?? afc_mail_env('APP_URL', 'https://worldtrustholding.com')),
    'signature_name' => (string) ($_POST['signature_name'] ?? 'Client Service Desk'),
    'signature_role' => (string) ($_POST['signature_role'] ?? 'World Trust Holding'),
];

$selectedTransactionId = isset($_GET['transaction_id']) ? (int) $_GET['transaction_id'] : 0;
$selectedTransactionValue = $selectedTransactionId > 0 ? (string) $selectedTransactionId : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['resend_transaction'])) {
        $txId = (int) ($_POST['transaction_id'] ?? 0);
        $recipientOverride = trim((string) ($_POST['recipient_override'] ?? ''));

        if ($txId <= 0) {
            $flashType = 'danger';
            $flashMessage = 'Please select a valid transaction.';
        } else {
            $stmt = $conn->prepare('SELECT * FROM history WHERE id = ? LIMIT 1');
            $stmt->bind_param('i', $txId);
            $stmt->execute();
            $tx = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$tx) {
                $flashType = 'danger';
                $flashMessage = 'Transaction not found.';
            } else {
                $recipient = $tx['email'] ?? '';
                if ($recipientOverride !== '') {
                    $recipient = $recipientOverride;
                }

                if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                    $flashType = 'danger';
                    $flashMessage = 'A valid recipient email is required.';
                } else {
                    $txEmail = afc_build_transaction_alert_email($tx, [
                        'channel' => strtolower((string) ($tx['type'] ?? '')) === 'debit' ? 'transfer' : 'transaction',
                        'event_label' => 'Admin Resend Notification',
                    ]);

                    $send = afc_send_aws_raw_email([
                        'to' => [$recipient],
                        'subject' => $txEmail['subject'],
                        'html_body' => $txEmail['html_body'],
                        'text_body' => $txEmail['text_body'],
                    ]);

                    if ($send['success']) {
                        $flashType = 'success';
                        $flashMessage = 'Transaction alert resent successfully.';
                        $debugMessage = 'Message ID: ' . ($send['message_id'] ?? 'N/A');
                    } else {
                        $flashType = 'danger';
                        $flashMessage = 'Unable to resend transaction alert.';
                        $debugMessage = (string) ($send['error'] ?? 'Unknown SES error.');
                    }
                }
            }
        }
    }

    if (isset($_POST['send_general'])) {
        $to = afc_mail_normalize_email_list($_POST['to'] ?? '');
        $cc = afc_mail_normalize_email_list($_POST['cc'] ?? '');
        $bcc = afc_mail_normalize_email_list($_POST['bcc'] ?? '');
        $replyTo = trim((string) ($_POST['reply_to'] ?? ''));
        $subject = trim((string) ($_POST['subject'] ?? ''));
        $body = trim((string) ($_POST['message_body'] ?? ''));
        $greetingName = trim((string) ($_POST['greeting_name'] ?? 'Valued Customer'));
        $preheader = trim((string) ($_POST['preheader'] ?? 'Important communication from World Trust Holding.'));
        $introLine = trim((string) ($_POST['intro_line'] ?? 'Please review the official update below from World Trust Holding.'));
        $highlightTitle = trim((string) ($_POST['highlight_title'] ?? 'Official Notice'));
        $highlightText = trim((string) ($_POST['highlight_text'] ?? 'This message was issued by the World Trust Holding administration desk.'));
        $ctaLabel = trim((string) ($_POST['cta_label'] ?? 'Visit Website'));
        $ctaUrl = trim((string) ($_POST['cta_url'] ?? afc_mail_env('APP_URL', 'https://worldtrustholding.com')));
        $signatureName = trim((string) ($_POST['signature_name'] ?? 'Client Service Desk'));
        $signatureRole = trim((string) ($_POST['signature_role'] ?? 'World Trust Holding'));

        $attachmentResult = afc_mail_collect_upload_attachments($_FILES['attachments'] ?? []);
        $attachments = $attachmentResult['attachments'];
        $attachmentErrors = $attachmentResult['errors'];

        if (empty($to)) {
            $flashType = 'danger';
            $flashMessage = 'Please provide at least one valid recipient.';
        } elseif ($subject === '' || $body === '') {
            $flashType = 'danger';
            $flashMessage = 'Subject and message body are required.';
        } elseif ($replyTo !== '' && !filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
            $flashType = 'danger';
            $flashMessage = 'Reply-To email is invalid.';
        } elseif ($ctaLabel !== '' && $ctaUrl !== '' && !filter_var($ctaUrl, FILTER_VALIDATE_URL)) {
            $flashType = 'danger';
            $flashMessage = 'CTA URL must be a valid full URL.';
        } elseif (!empty($attachmentErrors)) {
            $flashType = 'danger';
            $flashMessage = implode(' ', $attachmentErrors);
        } else {
            $template = afc_build_general_email_template($body, $subject, [
                'greeting' => $greetingName,
                'preheader' => $preheader,
                'intro' => $introLine,
                'highlight_title' => $highlightTitle,
                'highlight_text' => $highlightText,
                'cta_label' => $ctaLabel,
                'cta_url' => $ctaUrl,
                'signature_name' => $signatureName,
                'signature_role' => $signatureRole,
            ]);

            $send = afc_send_aws_raw_email([
                'to' => $to,
                'cc' => $cc,
                'bcc' => $bcc,
                'reply_to' => $replyTo,
                'subject' => $subject,
                'html_body' => $template['html_body'],
                'text_body' => $template['text_body'],
                'attachments' => $attachments,
            ]);

            if ($send['success']) {
                $flashType = 'success';
                $flashMessage = 'Email sent successfully through AWS SES.';
                $debugMessage = 'Message ID: ' . ($send['message_id'] ?? 'N/A');
                $_POST = [];
            } else {
                $flashType = 'danger';
                $flashMessage = 'Unable to send email through AWS SES.';
                $debugMessage = (string) ($send['error'] ?? 'Unknown SES error.');
            }
        }
    }
}

$txList = mysqli_query($conn, "
    SELECT id, username, email, tranx_id, amount, type, status, created_at
    FROM history
    ORDER BY created_at DESC
    LIMIT 50
");
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Mail Center <small>AWS SES Alerts</small></h1>
    </section>

    <section class="content">
        <?php if ($flashMessage !== ''): ?>
            <div class="alert alert-<?= htmlspecialchars($flashType) ?>">
                <?= htmlspecialchars($flashMessage) ?>
                <?php if ($debugMessage !== ''): ?>
                    <br><small><?= htmlspecialchars($debugMessage) ?></small>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Resend Transaction Alert</h3>
                    </div>
                    <form method="post">
                        <div class="box-body">
                            <div class="form-group">
                                <label>Transaction ID</label>
                                <input type="number" name="transaction_id" class="form-control" min="1" value="<?= htmlspecialchars($selectedTransactionValue, ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Recipient Override (optional)</label>
                                <input type="email" name="recipient_override" class="form-control" placeholder="customer@example.com">
                                <p class="help-block">Leave empty to use the transaction's stored email.</p>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" name="resend_transaction" class="btn btn-primary">Resend Alert</button>
                            <?php if ($selectedTransactionId > 0): ?>
                                <a href="transaction_statement.php?transaction_id=<?= (int) $selectedTransactionId ?>" class="btn btn-info" style="margin-left:8px;">Generate PDF Statement</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">Recent Transactions</h3>
                    </div>
                    <div class="box-body table-responsive no-padding" style="max-height:360px;overflow:auto;">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Ref</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($tx = mysqli_fetch_assoc($txList)): ?>
                                    <tr>
                                        <td>#<?= (int) $tx['id'] ?></td>
                                        <td><?= htmlspecialchars((string) ($tx['username'] ?: $tx['email'])) ?></td>
                                        <td><?= htmlspecialchars((string) $tx['tranx_id']) ?></td>
                                        <td>$<?= number_format((float) $tx['amount'], 2) ?></td>
                                        <td><?= htmlspecialchars((string) $tx['status']) ?></td>
                                        <td>
                                            <a href="mailer.php?transaction_id=<?= (int) $tx['id'] ?>" class="btn btn-xs btn-primary">Use</a>
                                            <a href="transaction_statement.php?transaction_id=<?= (int) $tx['id'] ?>" class="btn btn-xs btn-info">PDF</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Send General Email</h3>
                    </div>
                    <form method="post" enctype="multipart/form-data">
                        <div class="box-body">
                            <div class="callout callout-info" style="margin-top:0;">
                                <h4 style="margin-top:0;">Professional email composer</h4>
                                <p style="margin-bottom:0;">Use the extra fields below to add a branded intro, highlight box, CTA button and signature. You can also attach PDFs or images.</p>
                            </div>
                            <div class="form-group">
                                <label>To</label>
                                <input type="text" name="to" class="form-control" placeholder="user@example.com, another@example.com" value="<?= htmlspecialchars((string) ($_POST['to'] ?? '')) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>CC</label>
                                <input type="text" name="cc" class="form-control" value="<?= htmlspecialchars((string) ($_POST['cc'] ?? '')) ?>">
                            </div>
                            <div class="form-group">
                                <label>BCC</label>
                                <input type="text" name="bcc" class="form-control" value="<?= htmlspecialchars((string) ($_POST['bcc'] ?? '')) ?>">
                            </div>
                            <div class="form-group">
                                <label>Reply-To</label>
                                <input type="email" name="reply_to" class="form-control" value="<?= htmlspecialchars((string) ($_POST['reply_to'] ?? '')) ?>">
                            </div>
                            <div class="form-group">
                                <label>Subject</label>
                                <input type="text" name="subject" class="form-control" value="<?= htmlspecialchars((string) ($_POST['subject'] ?? '')) ?>" required>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Greeting Name</label>
                                        <input type="text" name="greeting_name" class="form-control" value="<?= htmlspecialchars($composerDefaults['greeting_name']) ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Preheader</label>
                                        <input type="text" name="preheader" class="form-control" value="<?= htmlspecialchars($composerDefaults['preheader']) ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Intro Line</label>
                                <input type="text" name="intro_line" class="form-control" value="<?= htmlspecialchars($composerDefaults['intro_line']) ?>">
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Highlight Title</label>
                                        <input type="text" name="highlight_title" class="form-control" value="<?= htmlspecialchars($composerDefaults['highlight_title']) ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>CTA Button Label</label>
                                        <input type="text" name="cta_label" class="form-control" value="<?= htmlspecialchars($composerDefaults['cta_label']) ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Highlight Text</label>
                                <textarea name="highlight_text" rows="3" class="form-control"><?= htmlspecialchars($composerDefaults['highlight_text']) ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>CTA URL</label>
                                <input type="url" name="cta_url" class="form-control" value="<?= htmlspecialchars($composerDefaults['cta_url']) ?>">
                            </div>
                            <div class="form-group">
                                <label>Message</label>
                                <textarea name="message_body" rows="8" class="form-control" required><?= htmlspecialchars((string) ($_POST['message_body'] ?? '')) ?></textarea>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Signature Name</label>
                                        <input type="text" name="signature_name" class="form-control" value="<?= htmlspecialchars($composerDefaults['signature_name']) ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Signature Role</label>
                                        <input type="text" name="signature_role" class="form-control" value="<?= htmlspecialchars($composerDefaults['signature_role']) ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Attachments</label>
                                <input type="file" name="attachments[]" class="form-control" accept=".pdf,image/jpeg,image/png,image/gif,image/webp" multiple>
                                <p class="help-block">Attach PDFs or images. Max 5MB per file, 7MB total.</p>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" name="send_general" class="btn btn-success">Send via AWS SES</button>
                            <span class="text-muted" style="margin-left:8px;">Sender: noreply@worldtrustholding.com</span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include('includes/footer.php'); ?>
