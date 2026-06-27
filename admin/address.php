<?php
include("includes/header.php");

$q = mysqli_query($conn, "SELECT * FROM deposit_accounts ORDER BY method ASC");
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Deposit Addresses</h1>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">Manage Deposit Addresses</h5>
                    <a href="add_address.php" class="btn btn-primary btn-sm">
                        Add New
                    </a>
                </div>

                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Method</th>
                                <th>Currency</th>
                                <th>Network</th>
                                <th>Address / Details</th>
                                <th>Status</th>
                                
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php while ($row = mysqli_fetch_assoc($q)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['method']) ?></td>
                                <td><?= htmlspecialchars($row['currency'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['network'] ?? '-') ?></td>
                                <td style="max-width:300px;">
                                    <small><?= nl2br(htmlspecialchars($row['address'])) ?></small>
                                </td>
                                 <td>
                                    <span class="badge bg-<?= $row['is_active'] ? 'success' : 'secondary' ?>">
                                        <?= $row['is_active'] ? 'Active' : 'Disabled' ?>
                                    </span>
                                </td>
                                
                                <td class="text-end">
                                    <a href="edit_address.php?id=<?= $row['id'] ?>"
                                       class="btn btn-sm btn-warning">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </section>
</div>

<?php include("includes/footer.php"); ?>
