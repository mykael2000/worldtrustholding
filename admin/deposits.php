<?php
include("includes/header.php");

$query = mysqli_query($conn, "
    SELECT h.id, h.tranx_id, h.amount, h.details, h.status, h.created_at,
           u.id AS user_id, u.username, u.email
    FROM history h
    JOIN users u ON u.id = h.client_id
    WHERE h.type = 'Credit'
    ORDER BY h.created_at DESC
");
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Deposits
            <small>Manage Deposits</small>
        </h1>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Deposit Requests</h5>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Transaction</th>
                                    <th>Details</th>
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

                                    <td>#<?= htmlspecialchars($row['tranx_id']) ?></td>

                                    <td>
                                        <?php
                                        $decoded = json_decode($row['details'], true);
                                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                            foreach ($decoded as $k => $v) {
                                                echo "<div><strong>".ucfirst($k).":</strong> ".htmlspecialchars($v)."</div>";
                                            }
                                        } else {
                                            echo htmlspecialchars($row['details']);
                                        }
                                        ?>
                                    </td>

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
                                             <a href="deposit_action.php?action=approve&id=<?= $row['id'] ?>"
                                                class="btn btn-sm btn-success">
                                                 Approve
                                            </a>

                                             <a href="deposit_action.php?action=fail&id=<?= $row['id'] ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Fail this deposit?')">
                                                 Fail
                                             </a>
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
    </section>
</div>

<?php include("includes/footer.php"); ?>
