<?php
include("includes/header.php"); // must include DB + admin auth

// Handle approve / reject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $kycId  = intval($_POST['kyc_id']);
    $userId = intval($_POST['user_id']);

    if (isset($_POST['approve'])) {
        mysqli_query($conn, "
            UPDATE kyc_submissions 
            SET status='approved', updated_at=NOW() 
            WHERE id='{$kycId}'
        ");

        mysqli_query($conn, "
            UPDATE users 
            SET kyc_status='verified' 
            WHERE id='{$userId}'
        ");
    }

    if (isset($_POST['reject'])) {
        mysqli_query($conn, "
            UPDATE kyc_submissions 
            SET status='rejected', updated_at=NOW() 
            WHERE id='{$kycId}'
        ");

        mysqli_query($conn, "
            UPDATE users 
            SET kyc_status='rejected' 
            WHERE id='{$userId}'
        ");
    }

    header("Location: kyclist.php");
    exit;
}

// Check if admin is viewing a specific KYC
$viewId = isset($_GET['view']) ? intval($_GET['view']) : null;
if ($viewId):

$data = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT k.*, u.username, u.email
    FROM kyc_submissions k
    JOIN users u ON u.id = k.user_id
    WHERE k.id='{$viewId}'
    LIMIT 1
"));

if (!$data) die("KYC record not found");
?>
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
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">KYC Review — <?= htmlspecialchars($data['username']) ?></h5>
            <a href="kyc_manage.php" class="btn btn-sm btn-secondary">← Back</a>
        </div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>Full Name:</strong> <?= $data['full_name'] ?></p>
                    <p><strong>Email:</strong> <?= $data['email'] ?></p>
                    <p><strong>Phone:</strong> <?= $data['phone'] ?></p>
                    <p><strong>Gender:</strong> <?= $data['gender'] ?></p>
                    <p><strong>DOB:</strong> <?= $data['dob'] ?></p>
                    <p><strong>Account Type:</strong> <?= $data['account_type'] ?></p>
                    <p><strong>Employment:</strong> <?= $data['employment_type'] ?></p>
                    <p><strong>Income:</strong> <?= $data['income_range'] ?></p>
                </div>

                <div class="col-md-6">
                    <p><strong>Document Type:</strong> <?= $data['document_type'] ?></p>

                    <div class="mb-3">
                        <label class="form-label">Front</label><br>
                        <img src="../dashboard/<?= $data['document_front'] ?>" class="img-thumbnail" style="max-height:180px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Back</label><br>
                        <img src="../dashboard/<?= $data['document_back'] ?>" class="img-thumbnail" style="max-height:180px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Passport Photo</label><br>
                        <img src="../dashboard/<?= $data['passport_photo'] ?>" class="img-thumbnail" style="max-height:180px;">
                    </div>
                </div>
            </div>

            <form method="post" class="d-flex gap-3">
                <input type="hidden" name="kyc_id" value="<?= $data['id'] ?>">
                <input type="hidden" name="user_id" value="<?= $data['user_id'] ?>">

                <button name="approve" class="btn btn-success">
                    Approve
                </button>

                <button name="reject" class="btn btn-danger">
                    Reject
                </button>
            </form>
        </div>
    </div>
</div>
</div>
</section>
</div>
<?php
include("includes/footer.php");
exit;
endif;
?>
