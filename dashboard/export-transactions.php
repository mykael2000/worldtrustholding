<?php
ob_start();
session_start();
error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

include('../connection.php');

// ✅ GET LOGGED IN USER ID
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    exit; // safety
}

// Fetch user
$stmt_user = $conn->prepare("SELECT firstname, lastname, email FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();

$userId = $user_id;

// Get POST values
$exportAs = $_POST['exportAs'] ?? 'download';
$orderBy  = ($_POST['orderBy'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';

// Fetch transactions
$stmt = $conn->prepare("
    SELECT tranx_id, type, amount, status, description, created_at
    FROM history
    WHERE client_id = ?
    ORDER BY created_at $orderBy
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$query = $stmt->get_result();

// Build HTML
$html = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
h2 { text-align: center; }
table { width:100%; border-collapse: collapse; }
th, td { border:1px solid #ddd; padding:6px; }
th { background:#f5f5f5; }
.credit { color: green; }
.debit { color: red; }
</style>
</head>
<body>

<h2>Transaction Statement</h2>
<p>
<strong>Name:</strong> '.$user['firstname'].' '.$user['lastname'].'<br>
<strong>Email:</strong> '.$user['email'].'<br>
<strong>Date:</strong> '.date('M d, Y').'
</p>

<table>
<thead>
<tr>
<th>ID</th>
<th>Type</th>
<th>Amount</th>
<th>Status</th>
<th>Description</th>
<th>Date</th>
</tr>
</thead>
<tbody>
';

while ($row = $query->fetch_assoc()) {
    $html .= '
    <tr>
        <td>#'.$row['tranx_id'].'</td>
        <td class="'.strtolower($row['type']).'">'.$row['type'].'</td>
        <td>$'.number_format($row['amount'], 2).'</td>
        <td>'.$row['status'].'</td>
        <td>'.$row['description'].'</td>
        <td>'.date('M d, Y', strtotime($row['created_at'])).'</td>
    </tr>';
}

$html .= '
</tbody>
</table>

<p style="margin-top:20px;text-align:center;font-size:10px">
© '.date('Y').' World Trust Holding
</p>

</body>
</html>';

// Generate PDF
$dompdf = new Dompdf(['isRemoteEnabled' => true]);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// ✅ CLEAN OUTPUT BUFFER
ob_end_clean();

$fileName = 'transactions_'.date('Ymd_His').'.pdf';

// Stream
$dompdf->stream($fileName, [
    'Attachment' => ($exportAs !== 'view')
]);

exit;
