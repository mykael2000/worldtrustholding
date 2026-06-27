<?php
include("includes/header.php");

$query = $conn->query("
    SELECT cr.*, u.username, u.email, u.total_balance
    FROM card_requests cr
    JOIN users u ON u.id = cr.user_id
    ORDER BY cr.created_at DESC
");
?>

<div class="content-wrapper">
<section class="content-header">
    <h1>Card Requests</h1>
</section>

<section class="content">
<div class="card">
<div class="card-body table-responsive">

<table class="table table-bordered table-hover">
<thead>
<tr>
    <th>User</th>
    <th>Card</th>
    <th>Level</th>
    <th>Fee</th>
    <th>Status</th>
    <th>Action</th>
</tr>
</thead>

<tbody>
<?php while ($row = $query->fetch_assoc()): ?>
<tr>
    <td>
        <strong><?= htmlspecialchars($row['username']) ?></strong><br>
        <small><?= htmlspecialchars($row['email']) ?></small>
    </td>

    <td><?= strtoupper($row['card_type']) ?></td>
    <td><?= ucfirst($row['card_level']) ?></td>
    <td>$<?= number_format($row['fee'],2) ?></td>

    <td>
        <span class="badge badge-<?= $row['status']=='Pending'?'warning':($row['status']=='Approved'?'success':'danger') ?>">
            <?= $row['status'] ?>
        </span>
    </td>

    <td>
        <?php if ($row['status'] === 'Pending'): ?>
        <form action="process_card.php" method="post" style="display:inline">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <button name="approve" class="btn btn-success btn-sm">Approve</button>
            <button name="reject" class="btn btn-danger btn-sm">Reject</button>
        </form>
        <?php else: ?>
            —
        <?php endif; ?>
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
