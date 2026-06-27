<?php
include("includes/header.php"); // admin header

$result = $conn->query("
    SELECT t.*, u.username
    FROM support_tickets t
    JOIN users u ON u.id = t.user_id
    ORDER BY t.created_at DESC
");
?>
<div class="content-wrapper">
<section class="content-header">
    <h1>Support Tickets</h1>
</section>

<section class="content">
<div class="card">
    <div class="card-header">
        <h3 class="card-title">All Support Requests</h3>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
            <?php if ($result->num_rows === 0): ?>
                <tr>
                    <td colspan="7" class="text-center">No tickets found</td>
                </tr>
            <?php endif; ?>

            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['subject']) ?></td>
                    <td class="text-capitalize"><?= $row['priority'] ?></td>
                    <td>
                        <span class="badge badge-<?=
                            $row['status'] === 'Closed' ? 'success' :
                            ($row['status'] === 'In Progress' ? 'warning' : 'secondary')
                        ?>">
                            <?= $row['status'] ?>
                        </span>
                    </td>
                    <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                    <td>
                        <a href="view_ticket.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">
                            View
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</section>
</div>
<?php include("includes/footer.php"); ?>
