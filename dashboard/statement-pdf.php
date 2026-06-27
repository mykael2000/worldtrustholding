<?php
ob_start();
session_start();
error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

include('../connection.php');

// ── Auth ────────────────────────────────────────────────────────────────────
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    ob_end_clean();
    header('Location: ../login.php');
    exit;
}

// ── Fetch user ──────────────────────────────────────────────────────────────
$stmt_user = $conn->prepare("SELECT firstname, lastname, email, account_id, account_type, currency FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();
$stmt_user->close();

if (!$user) {
    ob_end_clean();
    exit;
}

// ── Date range (from POST) ──────────────────────────────────────────────────
$dateFrom  = $_POST['from']   ?? date('Y-m-01');
$dateTo    = $_POST['to']     ?? date('Y-m-d');
$preset    = $_POST['preset'] ?? 'custom';
$label     = $_POST['label']  ?? 'Account Statement';

// Sanitise
$dateFrom = preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom) ? $dateFrom : date('Y-m-01');
$dateTo   = preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)   ? $dateTo   : date('Y-m-d');
if ($dateFrom > $dateTo) { [$dateFrom, $dateTo] = [$dateTo, $dateFrom]; }

// ── Fetch transactions ──────────────────────────────────────────────────────
$stmt = $conn->prepare("
    SELECT tranx_id, type, amount, details, description, status, created_at
    FROM history
    WHERE client_id = ?
      AND DATE(created_at) BETWEEN ? AND ?
    ORDER BY created_at ASC
");
$stmt->bind_param("iss", $user_id, $dateFrom, $dateTo);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ── Summary ─────────────────────────────────────────────────────────────────
$totalCredits = 0;
$totalDebits  = 0;
foreach ($rows as $r) {
    if (strtolower($r['type']) === 'credit') {
        $totalCredits += (float) $r['amount'];
    } else {
        $totalDebits += (float) $r['amount'];
    }
}
$net = $totalCredits - $totalDebits;

// ── Build HTML ──────────────────────────────────────────────────────────────
$generated = date('M d, Y \a\t g:i A');

$labelEsc              = htmlspecialchars($label);
$fromLabel             = date('M d, Y', strtotime($dateFrom));
$toLabel               = date('M d, Y', strtotime($dateTo));
$fullName              = htmlspecialchars($user['firstname'] . ' ' . $user['lastname']);
$accountId             = htmlspecialchars($user['account_id']);
$accountType           = htmlspecialchars($user['account_type'] ?: 'Savings Account');
$email                 = htmlspecialchars($user['email']);
$totalCreditsFormatted = number_format($totalCredits, 2);
$totalDebitsFormatted  = number_format($totalDebits,  2);
$netAbsFormatted       = number_format(abs($net),     2);
$netSign               = $net >= 0 ? '+$' : '-$';
$netClass              = $net >= 0 ? 'net-pos' : 'net-neg';
$txCount               = count($rows);

$html = <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #1a1a2e; background: #fff; }

/* Header band */
.header-band {
    background: #0047AB;
    color: white;
    padding: 20px 30px 16px;
    margin-bottom: 0;
}
.header-logo-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}
.brand-name {
    font-size: 17px;
    font-weight: bold;
    letter-spacing: 0.5px;
}
.brand-tagline {
    font-size: 9px;
    opacity: 0.75;
    margin-top: 2px;
}
.statement-title {
    font-size: 22px;
    font-weight: bold;
    letter-spacing: 0.5px;
}
.statement-period {
    font-size: 11px;
    opacity: 0.85;
    margin-top: 4px;
}

/* Info row */
.info-row {
    display: flex;
    justify-content: space-between;
    padding: 14px 30px;
    background: #f0f4ff;
    border-bottom: 1px solid #d4dff7;
    font-size: 10px;
    color: #333;
}
.info-block label { font-weight: bold; color: #0047AB; display: block; margin-bottom: 2px; }

/* Summary boxes */
.summary-section {
    padding: 16px 30px 12px;
    display: flex;
    gap: 12px;
}
.summary-box {
    flex: 1;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 10px 12px;
    text-align: center;
}
.summary-box .label { font-size: 9px; color: #666; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
.summary-box .value { font-size: 14px; font-weight: bold; }
.credit-color  { color: #16a34a; }
.debit-color   { color: #dc2626; }
.net-pos       { color: #16a34a; }
.net-neg       { color: #dc2626; }
.neutral-color { color: #0047AB; }

/* Table */
.table-section { padding: 0 30px 20px; }
table { width: 100%; border-collapse: collapse; margin-top: 8px; }
thead tr { background: #0047AB; color: white; }
thead th { padding: 7px 8px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.4px; }
tbody tr:nth-child(even) { background: #f8fafc; }
tbody tr:hover { background: #f0f4ff; }
tbody td { padding: 6px 8px; font-size: 10px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }

.badge {
    display: inline-block;
    padding: 1px 6px;
    border-radius: 99px;
    font-size: 8.5px;
    font-weight: bold;
}
.badge-credit     { background: #dcfce7; color: #16a34a; }
.badge-debit      { background: #fee2e2; color: #dc2626; }
.badge-completed  { background: #dcfce7; color: #16a34a; }
.badge-pending    { background: #fef9c3; color: #b45309; }
.badge-failed     { background: #fee2e2; color: #dc2626; }
.badge-default    { background: #f1f5f9; color: #475569; }

.amount-credit { color: #16a34a; font-weight: bold; }
.amount-debit  { color: #dc2626; font-weight: bold; }

/* Empty state */
.empty-state {
    text-align: center;
    padding: 40px;
    color: #999;
    font-size: 12px;
}

/* Footer */
.footer {
    margin-top: 20px;
    padding: 12px 30px;
    border-top: 2px solid #0047AB;
    display: flex;
    justify-content: space-between;
    font-size: 8.5px;
    color: #888;
}
.footer-brand { color: #0047AB; font-weight: bold; }
</style>
</head>
<body>

<!-- Header -->
<div class="header-band">
    <div class="header-logo-row">
        <div>
            <div class="brand-name">World Trust Holding</div>
            <div class="brand-tagline">Swift &amp; Secure Banking</div>
        </div>
        <div style="text-align:right; font-size:9px; opacity:0.8;">
            Official Account Statement<br>
            Generated: {$generated}
        </div>
    </div>
    <div class="statement-title">{$labelEsc}</div>
    <div class="statement-period">Period: {$fromLabel} &mdash; {$toLabel}</div>
</div>

<!-- Account info row -->
<div class="info-row">
    <div class="info-block">
        <label>Account Holder</label>
        {$fullName}
    </div>
    <div class="info-block">
        <label>Account Number</label>
        {$accountId}
    </div>
    <div class="info-block">
        <label>Account Type</label>
        {$accountType}
    </div>
    <div class="info-block">
        <label>Email</label>
        {$email}
    </div>
</div>
HTML;

// Summary
$html .= <<<HTML
<!-- Summary -->
<div class="summary-section">
    <div class="summary-box">
        <div class="label">Total Credits</div>
        <div class="value credit-color">+\${$totalCreditsFormatted}</div>
    </div>
    <div class="summary-box">
        <div class="label">Total Debits</div>
        <div class="value debit-color">-\${$totalDebitsFormatted}</div>
    </div>
    <div class="summary-box">
        <div class="label">Net Change</div>
        <div class="value {$netClass}">{$netSign}{$netAbsFormatted}</div>
    </div>
    <div class="summary-box">
        <div class="label">Transactions</div>
        <div class="value neutral-color">{$txCount}</div>
    </div>
</div>
HTML;

// Transactions table
$html .= '
<div class="table-section">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Reference</th>
                <th>Type</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
';

if (empty($rows)) {
    $html .= '<tr><td colspan="7" class="empty-state">No transactions in this period.</td></tr>';
} else {
    $i = 1;
    foreach ($rows as $row) {
        $typeClass   = strtolower($row['type']) === 'credit' ? 'badge-credit'  : 'badge-debit';
        $amtClass    = strtolower($row['type']) === 'credit' ? 'amount-credit' : 'amount-debit';
        $amtSign     = strtolower($row['type']) === 'credit' ? '+'             : '-';
        $statusClass = match ($row['status']) {
            'Completed' => 'badge-completed',
            'Pending'   => 'badge-pending',
            'Failed'    => 'badge-failed',
            default     => 'badge-default',
        };

        $ref  = htmlspecialchars($row['tranx_id']);
        $desc = htmlspecialchars($row['description']);
        $amt  = number_format($row['amount'], 2);
        $date = date('M d, Y', strtotime($row['created_at']));
        $type = htmlspecialchars($row['type']);
        $status = htmlspecialchars($row['status']);

        $html .= "
        <tr>
            <td>{$i}</td>
            <td><span style='font-family:monospace'>#{$ref}</span></td>
            <td><span class='badge {$typeClass}'>{$type}</span></td>
            <td>{$desc}</td>
            <td class='{$amtClass}'>{$amtSign}\${$amt}</td>
            <td><span class='badge {$statusClass}'>{$status}</span></td>
            <td>{$date}</td>
        </tr>";
        $i++;
    }
}

$html .= '
        </tbody>
    </table>
</div>';

$html .= <<<HTML
<!-- Footer -->
<div class="footer">
    <div>
        <span class="footer-brand">World Trust Holding</span> &mdash;
        This is an official account statement generated from your banking portal.
    </div>
    <div>
        Generated on {$generated} &bull; Page 1
    </div>
</div>

</body>
</html>
HTML;

// ── Render PDF ───────────────────────────────────────────────────────────────
$dompdf = new Dompdf(['isRemoteEnabled' => true]);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

ob_end_clean();

$slug     = preg_replace('/[^a-z0-9]+/', '_', strtolower($label));
$fileName = 'statement_' . $slug . '_' . date('Ymd') . '.pdf';

$dompdf->stream($fileName, ['Attachment' => true]);
exit;
