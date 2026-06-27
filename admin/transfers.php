<?php
include("includes/header.php");
require_once __DIR__ . '/../mail/aws_ses_mailer.php';

/* --------------------------
   HANDLE ACTIONS
---------------------------*/
if (isset($_POST['action'], $_POST['tx_id'])) {

    $tx_id   = intval($_POST['tx_id']);
    $action  = $_POST['action'];

    // Fetch transaction
    $tx = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT * FROM history WHERE id = $tx_id
    "));

    if ($tx && $tx['status'] === 'Pending' && $tx['type'] === 'Debit') {

        if ($action === 'complete') {

            // Fetch user balance
            $user = mysqli_fetch_assoc(mysqli_query($conn, "
                SELECT total_balance FROM users WHERE id = {$tx['client_id']}
            "));

            if ($user && $user['total_balance'] >= $tx['amount']) {

                // Deduct balance
                mysqli_query($conn, "
                    UPDATE users 
                    SET total_balance = total_balance - {$tx['amount']}
                    WHERE id = {$tx['client_id']}
                ");

                // Mark completed
                mysqli_query($conn, "
                    UPDATE history 
                    SET status = 'Completed'
                    WHERE id = $tx_id
                ");

                $tx['status'] = 'Completed';

                $_SESSION['success'] = "Transfer completed successfully.";
            } else {
                $_SESSION['error'] = "Insufficient user balance.";
            }

        } elseif ($action === 'fail') {

            // Mark failed
            mysqli_query($conn, "
                UPDATE history 
                SET status = 'Failed'
                WHERE id = $tx_id
            ");

            $tx['status'] = 'Failed';

            $_SESSION['success'] = "Transfer marked as failed.";
        }

        if (in_array($tx['status'], ['Completed', 'Failed'], true) && !empty($tx['email'])) {
            $txEmail = afc_build_transaction_alert_email($tx, [
                'channel' => 'transfer',
                'event_label' => $tx['status'] === 'Completed' ? 'Transfer Approved' : 'Transfer Marked Failed',
            ]);

            $mailResult = afc_send_aws_raw_email([
                'to' => [$tx['email']],
                'subject' => $txEmail['subject'],
                'html_body' => $txEmail['html_body'],
                'text_body' => $txEmail['text_body'],
            ]);
            if (!$mailResult['success']) {
                error_log('AFC transaction mail failed (transfer action): ' . ($mailResult['error'] ?? 'unknown'));
            }
        }
    }

    header("Location: transfers.php");
    exit;
}

/* --------------------------
   FETCH TRANSFERS
---------------------------*/
$query = mysqli_query($conn, "
    SELECT h.*, u.username, u.email
    FROM history h
    JOIN users u ON u.id = h.client_id
    WHERE h.type = 'Debit'
    ORDER BY h.created_at DESC
");
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Dashboard
            <small>Control panel</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Transfers</li>
        </ol>
    </section>

    <section class="content">

        <!-- Alerts -->
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="container-fluid mt-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">Transfers</h5>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>User</th>
                                        <th>Reference</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($query)): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($row['username']) ?></strong><br>
                                                <small><?= htmlspecialchars($row['email']) ?></small>
                                            </td>

                                            <td>#<?= $row['tranx_id'] ?></td>

                                            <td>$<?= number_format($row['amount'], 2) ?></td>

                                            <td>
                                                <?php
                                                $badge = match ($row['status']) {
                                                    'Completed' => 'success',
                                                    'Failed'    => 'danger',
                                                    default     => 'warning'
                                                };
                                                ?>
                                                <span class="badge bg-<?= $badge ?>">
                                                    <?= $row['status'] ?>
                                                </span>
                                            </td>

                                            <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>

                                            <td class="text-end">
                                                 <?php if ($row['status'] === 'Pending'): ?>
                                                    <form method="post" class="d-inline">
                                                        <input type="hidden" name="tx_id" value="<?= $row['id'] ?>">
                                                        <button name="action" value="complete"
                                                            class="btn btn-sm btn-success"
                                                            onclick="return confirm('Complete this transfer?')">
                                                            Complete
                                                        </button>
                                                    </form>

                                                     <form method="post" class="d-inline">
                                                         <input type="hidden" name="tx_id" value="<?= $row['id'] ?>">
                                                         <button name="action" value="fail"
                                                             class="btn btn-sm btn-danger"
                                                             onclick="return confirm('Fail this transfer?')">
                                                             Fail
                                                         </button>
                                                     </form>
                                                     <a href="mailer.php?transaction_id=<?= $row['id'] ?>"
                                                         class="btn btn-sm btn-primary">
                                                         Resend Alert
                                                     </a>
                                                     <a href="transaction_statement.php?transaction_id=<?= $row['id'] ?>"
                                                         class="btn btn-sm btn-info">
                                                         Generate PDF
                                                     </a>
                                                 <?php else: ?>
                                                     <a href="mailer.php?transaction_id=<?= $row['id'] ?>"
                                                         class="btn btn-sm btn-primary">
                                                         Resend Alert
                                                     </a>
                                                     <a href="transaction_statement.php?transaction_id=<?= $row['id'] ?>"
                                                         class="btn btn-sm btn-info">
                                                         Generate PDF
                                                     </a>
                                                 <?php endif; ?>
                                             </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </section>
</div>

<?php include("includes/footer.php"); ?>
