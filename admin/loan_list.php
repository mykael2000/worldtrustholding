<?php
include("includes/header.php");

/* FETCH LOANS */
$query = mysqli_query($conn, "
    SELECT l.loan_id, l.amount, l.facility, l.purpose, l.duration, l.status, l.created_at,
           u.username, u.email, u.id AS user_id
    FROM loans l
    JOIN users u ON u.id = l.user_id
    ORDER BY l.created_at DESC
");
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Loan Management
            <small>Approve or Reject Loans</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Loans</li>
        </ol>
    </section>

    <section class="content">
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible show" role="alert">
                <strong>Error:</strong> <?= isset($_SESSION['error']) ? htmlspecialchars($_SESSION['error']) : '' ?>

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible show" role="alert">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div class="row">
            <div class="container-fluid mt-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">Loan Applications</h5>
                    </div>
                        
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>User</th>
                                        <th>Facility</th>
                                        <th>Amount</th>
                                        <th>Duration</th>
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

                                        <td><?= htmlspecialchars($row['facility']) ?></td>

                                        <td>$<?= number_format($row['amount'], 2) ?></td>

                                        <td><?= intval($row['duration']) ?> months</td>

                                        <td>
                                            <?php
                                            $badge = match ($row['status']) {
                                                'Approved'  => 'success',
                                                'Rejected'  => 'danger',
                                                default     => 'warning'
                                            };
                                            ?>
                                            <span class="badge bg-<?= $badge ?>">
                                                <?= htmlspecialchars($row['status']) ?>
                                            </span>
                                        </td>

                                        <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>

                                        <td class="text-end">
                                            <?php if ($row['status'] === 'Pending'): ?>
                                                <form method="POST" action="loan_action.php" class="d-inline">
                                                    <input type="hidden" name="loan_id" value="<?= $row['loan_id'] ?>">
                                                    <input type="hidden" name="action" value="approve">
                                                    <button class="btn btn-sm btn-success">
                                                        Approve
                                                    </button>
                                                </form>

                                                <form method="POST" action="loan_action.php" class="d-inline">
                                                    <input type="hidden" name="loan_id" value="<?= $row['loan_id'] ?>">
                                                    <input type="hidden" name="action" value="reject">
                                                    <button class="btn btn-sm btn-danger">
                                                        Reject
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-muted">Processed</span>
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
