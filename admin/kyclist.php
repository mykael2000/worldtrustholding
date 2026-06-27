<?php  
include("includes/header.php");
$query = mysqli_query($conn, "
    SELECT k.id, k.document_type, k.status, k.created_at,
           u.username, u.email
    FROM kyc_submissions k
    JOIN users u ON u.id = k.user_id
    ORDER BY k.created_at DESC
");
?>
<!-- Right side column. Contains the navbar and content of the page -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Dashboard
            <small>Control panel</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">KYC</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Main row -->
        <div class="row">
            <div class="container-fluid mt-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">KYC Submissions</h5>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>User</th>
                                        <th>Document</th>
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

                                        <td><?= htmlspecialchars($row['document_type']) ?></td>

                                        <td>
                                            <?php
                                            $badge = match ($row['status']) {
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                default    => 'warning'
                                            };
                                            ?>
                                            <span class="badge bg-<?= $badge ?>">
                                                <?= ucfirst($row['status']) ?>
                                            </span>
                                        </td>

                                        <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>

                                        <td class="text-end">
                                            <a href="kyc_manage.php?view=<?= $row['id'] ?>"
                                            class="btn btn-sm btn-primary">
                                                Review
                                            </a>
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
<?php  
include("includes/footer.php");

?>