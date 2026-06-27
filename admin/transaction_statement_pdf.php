<?php
session_start();
ob_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit();
}

require_once __DIR__ . '/../dashboard/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

include 'includes/connection.php';

function statementEscape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$transactionId = (int) ($_POST['transaction_id'] ?? 0);

if ($transactionId <= 0) {
    exit('Invalid transaction selected.');
}

$statementTitle = trim((string) ($_POST['statement_title'] ?? 'Official Transaction Statement'));
$statementTitle = $statementTitle !== '' ? $statementTitle : 'Official Transaction Statement';

$completionWindow = trim((string) ($_POST['completion_window'] ?? '3-5 business days'));
$preparedBy = trim((string) ($_POST['prepared_by'] ?? 'Operations Control Desk'));
$statusNote = trim((string) ($_POST['status_note'] ?? 'Transaction is under standard banking review and processing controls.'));
$adminNote = trim((string) ($_POST['admin_note'] ?? ''));

$stmt = $conn->prepare(
    'SELECT h.*, u.firstname, u.middlename, u.lastname, u.account_id, u.account_type, u.currency, u.phone, u.address, u.city, u.state, u.country
     FROM history h
     LEFT JOIN users u ON u.id = h.client_id
     WHERE h.id = ?
     LIMIT 1'
);
$stmt->bind_param('i', $transactionId);
$stmt->execute();
$transaction = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$transaction) {
    exit('Transaction could not be found.');
}

$nameParts = array_filter([
    trim((string) ($transaction['firstname'] ?? '')),
    trim((string) ($transaction['middlename'] ?? '')),
    trim((string) ($transaction['lastname'] ?? '')),
]);
$customerName = $nameParts ? implode(' ', $nameParts) : (string) ($transaction['username'] ?? 'N/A');

$detailItems = [];
$decodedDetails = json_decode((string) $transaction['details'], true);
if (json_last_error() === JSON_ERROR_NONE && is_array($decodedDetails)) {
    foreach ($decodedDetails as $label => $value) {
        $detailItems[] = [
            'label' => ucwords(str_replace(['_', '-'], ' ', (string) $label)),
            'value' => (string) $value,
        ];
    }
} elseif (trim((string) $transaction['details']) !== '') {
    $detailItems[] = [
        'label' => 'Transaction details',
        'value' => (string) $transaction['details'],
    ];
}

$detailRows = '';
foreach ($detailItems as $item) {
    $detailRows .= '<tr><td class="detail-label">' . statementEscape($item['label']) . '</td><td class="detail-value">' . nl2br(statementEscape($item['value'])) . '</td></tr>';
}

if ($detailRows === '') {
    $detailRows = '<tr><td class="detail-value" colspan="2">No additional transaction fields were stored for this entry.</td></tr>';
}

$logoDataUri = '';
$logoPath = realpath(__DIR__ . '/../worldtrustholding.png');
if ($logoPath && is_file($logoPath)) {
    $mimeType = function_exists('mime_content_type') ? mime_content_type($logoPath) : 'image/png';
    $logoDataUri = 'data:' . $mimeType . ';base64,' . base64_encode((string) file_get_contents($logoPath));
}

$statementNumber = 'AFC-' . str_pad((string) $transaction['id'], 6, '0', STR_PAD_LEFT);
$createdAt = strtotime((string) $transaction['created_at']);
$issueDate = date('M d, Y h:i A');
$transactionDate = $createdAt ? date('M d, Y h:i A', $createdAt) : statementEscape($transaction['created_at']);
$statusClass = strtolower((string) $transaction['status']);
$statusBadgeClass = in_array($statusClass, ['completed', 'pending', 'failed'], true) ? $statusClass : 'default';
$recordedUsername = trim((string) ($transaction['username'] ?? ''));
$recordedEmail = trim((string) ($transaction['email'] ?? ''));
$recordedPhone = trim((string) ($transaction['phone'] ?? ''));
$recordedCurrency = trim((string) ($transaction['currency'] ?? 'USD'));
$recordedAccountType = trim((string) ($transaction['account_type'] ?? 'N/A'));
$preparedByValue = $preparedBy !== '' ? $preparedBy : 'Operations Control Desk';
$statusNoteValue = $statusNote !== '' ? $statusNote : 'Transaction is under standard banking review and processing controls.';
$completionWindowValue = $completionWindow !== '' ? $completionWindow : 'Not specified';
$fullAddress = trim(implode(', ', array_filter([
    (string) ($transaction['address'] ?? ''),
    (string) ($transaction['city'] ?? ''),
    (string) ($transaction['state'] ?? ''),
    (string) ($transaction['country'] ?? ''),
])));
$fullAddress = $fullAddress !== '' ? $fullAddress : 'Not available';

$html = '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { margin: 34px 34px 40px; }
body { font-family: DejaVu Sans, Arial, sans-serif; color: #16324f; font-size: 11px; line-height: 1.45; position: relative; background: #ffffff; }
.page-shell { position: relative; }
.watermark { position: fixed; top: 240px; left: 115px; width: 330px; text-align: center; opacity: 0.07; z-index: 0; }
.watermark img { width: 220px; height: auto; }
.watermark-text { margin-top: 8px; font-size: 28px; letter-spacing: 6px; font-weight: bold; color: #0d3f7a; }
.content { position: relative; z-index: 2; }
.top-strip { height: 12px; background: #c8a74e; border-radius: 10px 10px 0 0; }
.header-wrap { border: 1px solid #d5e1ee; border-radius: 18px; overflow: hidden; margin-bottom: 18px; }
.header { background: #0b3a75; color: #fff; padding: 24px 28px 22px; }
.header-table { width: 100%; border-collapse: collapse; }
.header-table td { vertical-align: top; }
.header-left { width: 67%; }
.header-right { width: 33%; text-align: right; }
.brand-title { font-size: 24px; font-weight: bold; letter-spacing: 0.5px; }
.brand-subtitle { font-size: 10px; opacity: 0.88; margin-top: 5px; }
.document-title { margin-top: 16px; font-size: 21px; font-weight: bold; }
.document-subtitle { font-size: 11px; opacity: 0.9; margin-top: 5px; }
.chip { display: inline-block; padding: 5px 10px; border-radius: 999px; font-size: 10px; font-weight: bold; background: rgba(255,255,255,0.18); color: #fff; }
.meta-grid { width: 100%; border-collapse: separate; border-spacing: 0 0; margin-bottom: 18px; }
.meta-grid td { vertical-align: top; }
.meta-spacer { width: 14px; }
.meta-card { background: #f8fbff; border: 1px solid #dbe8f5; border-radius: 14px; padding: 16px 18px; }
.section-title { font-size: 12px; text-transform: uppercase; letter-spacing: 1.1px; font-weight: bold; color: #0b3a75; margin-bottom: 12px; }
.meta-table, .detail-table, .summary-table { width: 100%; border-collapse: collapse; }
.meta-table td { padding: 5px 0; vertical-align: top; }
.label { width: 34%; color: #6c7d90; font-size: 10px; text-transform: uppercase; letter-spacing: 0.4px; }
.value { font-weight: bold; color: #16324f; }
.summary-strip { background: #fff; border: 1px solid #dfe9f4; border-radius: 14px; padding: 10px 12px; margin-bottom: 18px; }
.summary-table td { width: 25%; padding: 10px 8px; vertical-align: top; border-right: 1px solid #edf2f7; }
.summary-table td:last-child { border-right: 0; }
.summary-label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.8px; color: #6c7d90; margin-bottom: 6px; }
.summary-value { font-size: 16px; font-weight: bold; color: #0b3a75; }
.status-pill { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 10px; font-weight: bold; }
.status-completed { background: #dcf5e7; color: #18794e; }
.status-pending { background: #fff3d6; color: #9d6800; }
.status-failed { background: #fde0df; color: #b42318; }
.status-default { background: #eaf0f6; color: #445568; }
.panel { border: 1px solid #dfe9f4; border-radius: 14px; padding: 18px; margin-bottom: 18px; background: #fff; }
.note-box { background: #f8fbff; border-left: 4px solid #14519a; padding: 14px 16px; border-radius: 10px; }
.detail-table th { text-align: left; background: #0b3a75; color: #fff; padding: 10px 12px; font-size: 10px; letter-spacing: 0.5px; text-transform: uppercase; }
.detail-table td { border-bottom: 1px solid #edf2f7; padding: 10px 12px; vertical-align: top; }
.detail-label { width: 30%; color: #5d6f82; font-weight: bold; }
.detail-value { color: #16324f; }
.section-band { background: #edf5fd; border: 1px solid #dbe8f5; border-radius: 12px; padding: 12px 14px; margin-bottom: 16px; }
.legal-panel { border: 1px solid #dfe9f4; border-radius: 14px; padding: 18px; margin-bottom: 18px; background: #fbfcfe; }
.legal-block { border-left: 4px solid #c8a74e; background: #fffdf7; padding: 12px 14px; margin-top: 12px; }
.legal-title { font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; font-weight: bold; color: #0b3a75; margin-bottom: 6px; }
.legal-copy { color: #445568; line-height: 1.7; }
.footer { margin-top: 24px; padding-top: 12px; border-top: 1px solid #dbe8f5; font-size: 9px; color: #5d6f82; }
.footer-table { width: 100%; border-collapse: collapse; }
.footer-table td { vertical-align: top; }
.footer-right { text-align: right; }
</style>
</head>
<body>
<div class="page-shell">';

if ($logoDataUri !== '') {
    $html .= '<div class="watermark"><img src="' . $logoDataUri . '" alt="Confidential stamp"><div class="watermark-text">CONFIDENTIAL</div></div>';
}

$html .= '<div class="content">
    <div class="header-wrap">
        <div class="top-strip"></div>
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="header-left">
                        <div class="brand-title">World Trust Holding</div>
                        <div class="brand-subtitle">Official banking correspondence generated by administration</div>
                        <div class="document-title">' . statementEscape($statementTitle) . '</div>
                        <div class="document-subtitle">Single transaction record prepared for customer servicing, internal review and client communication.</div>
                    </td>
                    <td class="header-right">
                        <div class="chip">Statement No. ' . statementEscape($statementNumber) . '</div>
                        <div style="margin-top:12px;font-size:10px;line-height:1.7;">
                            Issue Date: ' . statementEscape($issueDate) . '<br>
                            Reference: #' . statementEscape($transaction['tranx_id']) . '<br>
                            Account No: ' . statementEscape($transaction['account_id'] ?? 'N/A') . '
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <table class="meta-grid">
        <tr>
            <td>
                <div class="meta-card">
                    <div class="section-title">Customer Profile</div>
                    <table class="meta-table">
                        <tr><td class="label">Account Holder</td><td class="value">' . statementEscape($customerName) . '</td></tr>
                        <tr><td class="label">Account Number</td><td class="value">' . statementEscape($transaction['account_id'] ?? 'N/A') . '</td></tr>
                        <tr><td class="label">Account Type</td><td class="value">' . statementEscape($transaction['account_type'] ?? 'N/A') . '</td></tr>
                        <tr><td class="label">Currency</td><td class="value">' . statementEscape($transaction['currency'] ?? 'USD') . '</td></tr>
                        <tr><td class="label">Email</td><td class="value">' . statementEscape($transaction['email']) . '</td></tr>
                    </table>
                </div>
            </td>
            <td class="meta-spacer"></td>
            <td>
                <div class="meta-card">
                    <div class="section-title">Transaction Profile</div>
                    <table class="meta-table">
                        <tr><td class="label">Transaction Type</td><td class="value">' . statementEscape($transaction['type']) . '</td></tr>
                        <tr><td class="label">Transaction Date</td><td class="value">' . statementEscape($transactionDate) . '</td></tr>
                        <tr><td class="label">Processing Status</td><td class="value"><span class="status-pill status-' . statementEscape($statusBadgeClass) . '">' . statementEscape($transaction['status']) . '</span></td></tr>
                        <tr><td class="label">Description</td><td class="value">' . statementEscape($transaction['description']) . '</td></tr>
                        <tr><td class="label">Prepared By</td><td class="value">' . statementEscape($preparedBy !== '' ? $preparedBy : 'Operations Control Desk') . '</td></tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <div class="summary-strip">
        <table class="summary-table">
            <tr>
                <td>
                    <div class="summary-label">Amount</div>
                    <div class="summary-value">$' . number_format((float) $transaction['amount'], 2) . '</div>
                </td>
                <td>
                    <div class="summary-label">Reference</div>
                    <div class="summary-value" style="font-size:13px;">#' . statementEscape($transaction['tranx_id']) . '</div>
                </td>
                <td>
                    <div class="summary-label">Completion Window</div>
                    <div class="summary-value" style="font-size:13px;">' . statementEscape($completionWindowValue) . '</div>
                </td>
                <td>
                    <div class="summary-label">Client ID</div>
                    <div class="summary-value" style="font-size:13px;">' . statementEscape($transaction['client_id']) . '</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-band">
        <strong style="font-size:12px; color:#0b3a75;">Statement Summary</strong><br>
        This document records the transaction as booked on the platform and presents the processing guidance supplied by administration for official customer communication.
    </div>

    <div class="panel">
        <div class="section-title">Processing Guidance</div>
        <div class="note-box">
            <strong>Status Note:</strong> ' . statementEscape($statusNoteValue) . '<br><br>
            <strong>Expected Completion Window:</strong> ' . statementEscape($completionWindowValue) . '<br><br>
            <strong>Prepared By:</strong> ' . statementEscape($preparedByValue) . '
        </div>
    </div>';

if ($adminNote !== '') {
    $html .= '<div class="panel"><div class="section-title">Administrative Note</div><div style="white-space:pre-line;">' . statementEscape($adminNote) . '</div></div>';
}

$html .= '<div class="panel">
        <div class="section-title">Full Transaction Details</div>
        <table class="detail-table">
            <thead>
                <tr><th style="width:30%;">Field</th><th>Value</th></tr>
            </thead>
            <tbody>
                <tr><td class="detail-label">Statement Number</td><td class="detail-value">' . statementEscape($statementNumber) . '</td></tr>
                <tr><td class="detail-label">Issue Date</td><td class="detail-value">' . statementEscape($issueDate) . '</td></tr>
                <tr><td class="detail-label">Transaction Reference</td><td class="detail-value">#' . statementEscape($transaction['tranx_id']) . '</td></tr>
                <tr><td class="detail-label">Client Identifier</td><td class="detail-value">' . statementEscape($transaction['client_id']) . '</td></tr>
                <tr><td class="detail-label">Customer Name</td><td class="detail-value">' . statementEscape($customerName) . '</td></tr>
                <tr><td class="detail-label">Username</td><td class="detail-value">' . statementEscape($recordedUsername !== '' ? $recordedUsername : 'Not available') . '</td></tr>
                <tr><td class="detail-label">Email</td><td class="detail-value">' . statementEscape($recordedEmail !== '' ? $recordedEmail : 'Not available') . '</td></tr>
                <tr><td class="detail-label">Phone</td><td class="detail-value">' . statementEscape($recordedPhone !== '' ? $recordedPhone : 'Not available') . '</td></tr>
                <tr><td class="detail-label">Account Number</td><td class="detail-value">' . statementEscape($transaction['account_id'] ?? 'N/A') . '</td></tr>
                <tr><td class="detail-label">Account Type</td><td class="detail-value">' . statementEscape($recordedAccountType) . '</td></tr>
                <tr><td class="detail-label">Account Currency</td><td class="detail-value">' . statementEscape($recordedCurrency) . '</td></tr>
                <tr><td class="detail-label">Address</td><td class="detail-value">' . statementEscape($fullAddress) . '</td></tr>
                <tr><td class="detail-label">Transaction Type</td><td class="detail-value">' . statementEscape($transaction['type']) . '</td></tr>
                <tr><td class="detail-label">Transaction Amount</td><td class="detail-value">$' . number_format((float) $transaction['amount'], 2) . '</td></tr>
                <tr><td class="detail-label">Transaction Date</td><td class="detail-value">' . statementEscape($transactionDate) . '</td></tr>
                <tr><td class="detail-label">Recorded Status</td><td class="detail-value">' . statementEscape($transaction['status']) . '</td></tr>
                <tr><td class="detail-label">Transaction Description</td><td class="detail-value">' . statementEscape($transaction['description']) . '</td></tr>
                <tr><td class="detail-label">Completion Window</td><td class="detail-value">' . statementEscape($completionWindowValue) . '</td></tr>
                <tr><td class="detail-label">Prepared By</td><td class="detail-value">' . statementEscape($preparedByValue) . '</td></tr>
                <tr><td class="detail-label">Status Guidance</td><td class="detail-value">' . statementEscape($statusNoteValue) . '</td></tr>
                <tr><td class="detail-label">Administrative Note</td><td class="detail-value">' . statementEscape($adminNote !== '' ? $adminNote : 'No additional administrative note supplied at the time of statement generation.') . '</td></tr>
                ' . $detailRows . '
            </tbody>
        </table>
    </div>

    <div class="legal-panel">
        <div class="section-title">Official Notice And Disclaimer</div>
        <div class="legal-block">
            <div class="legal-title">Document Authenticity Notice</div>
            <div class="legal-copy">
                This statement has been generated from the World Trust Holding administrative records for the specific transaction referenced above. It is intended to summarize the transaction status, customer record and processing guidance available at the time of issue.
            </div>
        </div>
        <div class="legal-block">
            <div class="legal-title">Processing Disclaimer</div>
            <div class="legal-copy">
                Any completion window shown in this document is an administrative estimate only and may vary based on compliance review, banking partner timelines, destination institution controls, customer verification requirements, public holidays or other operational constraints outside the immediate control of World Trust Holding.
            </div>
        </div>
        <div class="legal-block">
            <div class="legal-title">Use Restriction</div>
            <div class="legal-copy">
                This document is confidential and intended solely for the account holder, authorized representatives of the account holder, or official review purposes. Unauthorized disclosure, duplication, alteration or reliance on this statement without independent confirmation from the institution is discouraged.
            </div>
        </div>
        <div class="legal-block">
            <div class="legal-title">Verification Advisory</div>
            <div class="legal-copy">
                If any detail appears inconsistent with the customer instruction, beneficiary information, or prior banking correspondence, the customer should contact World Trust Holding support immediately for verification before taking further action.
            </div>
        </div>
    </div>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>
                    This document was generated from the World Trust Holding administration console and is intended for official customer transaction communication.
                </td>
                <td class="footer-right">
                    Confidential banking document<br>
                    Generated on ' . statementEscape($issueDate) . '
                </td>
            </tr>
        </table>
    </div>
</div>
</div>
</body>
</html>';

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$safeReference = preg_replace('/[^A-Za-z0-9\-_]/', '-', (string) $transaction['tranx_id']);
$filename = 'transaction-statement-' . ($safeReference !== '' ? $safeReference : (string) $transaction['id']) . '.pdf';

ob_end_clean();
$dompdf->stream($filename, ['Attachment' => true]);
exit;