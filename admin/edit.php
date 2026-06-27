<?php
include "includes/header.php";

$userid = intval($_GET['id']);

$sqleu = "SELECT * FROM users WHERE id = $userid";
$queryeu = mysqli_query($conn, $sqleu);
$usereu = mysqli_fetch_assoc($queryeu);

$message = "";

if (isset($_POST['submit'])) {

    // Financials
    $total_balance        = floatval($_POST['total_balance']);
    $transaction_limit    = floatval($_POST['transaction_limit']);
    $pending_transaction  = floatval($_POST['pending_transaction']);
    $transaction_volume   = floatval($_POST['transaction_volume']);
    $pending_withdrawals  = floatval($_POST['pending_withdrawals']);
    $monthly_income       = floatval($_POST['monthly_income']);
    $monthly_outgoing     = floatval($_POST['monthly_outgoing']);

    // Account
    $account_type = mysqli_real_escape_string($conn, $_POST['account_type']);
    $currency     = mysqli_real_escape_string($conn, $_POST['currency']);
    $kyc_status   = mysqli_real_escape_string($conn, $_POST['kyc_status']);

    $sqlup = "
        UPDATE users SET
            total_balance = '$total_balance',
            transaction_limit = '$transaction_limit',
            pending_transaction = '$pending_transaction',
            transaction_volume = '$transaction_volume',
            pending_withdrawals = '$pending_withdrawals',
            monthly_income = '$monthly_income',
            monthly_outgoing = '$monthly_outgoing',
            account_type = '$account_type',
            currency = '$currency',
            kyc_status = '$kyc_status'
        WHERE id = $userid
    ";

    mysqli_query($conn, $sqlup);

    header("Location: edit.php?id=$userid&message=success");
    exit;
}

if (@$_GET['message'] === "success") {
    $message = '
    <div class="alert alert-success">
        <strong>Success!</strong> User details updated successfully.
    </div>';
}
?>

<div class="content-wrapper">
<section class="content-header">
    <h1>Edit User</h1>
</section>

<section class="content">
<div class="row">
<div class="col-md-6">
<div class="box box-primary">

<form method="post">
<?php echo $message; ?>

<div class="box-body">

<div class="form-group">
    <label>Email</label>
    <input type="email" class="form-control" value="<?php echo $usereu['email']; ?>" readonly>
</div>

<div class="form-group">
    <label>Total Balance</label>
    <input type="text" name="total_balance" class="form-control"
           value="<?php echo $usereu['total_balance']; ?>">
</div>

<div class="form-group">
    <label>Transaction Limit</label>
    <input type="text" name="transaction_limit" class="form-control"
           value="<?php echo $usereu['transaction_limit']; ?>">
</div>

<div class="form-group">
    <label>Pending Transactions</label>
    <input type="text" name="pending_transaction" class="form-control"
           value="<?php echo $usereu['pending_transaction']; ?>">
</div>

<div class="form-group">
    <label>Transaction Volume</label>
    <input type="text" name="transaction_volume" class="form-control"
           value="<?php echo $usereu['transaction_volume']; ?>">
</div>

<div class="form-group">
    <label>Pending Withdrawals</label>
    <input type="text" name="pending_withdrawals" class="form-control"
           value="<?php echo $usereu['pending_withdrawals']; ?>">
</div>

<div class="form-group">
    <label>Monthly Income</label>
    <input type="text" name="monthly_income" class="form-control"
           value="<?php echo $usereu['monthly_income']; ?>">
</div>

<div class="form-group">
    <label>Monthly Outgoing</label>
    <input type="text" name="monthly_outgoing" class="form-control"
           value="<?php echo $usereu['monthly_outgoing']; ?>">
</div>

<div class="form-group">
    <label>Account Type</label>
    <input type="text" name="account_type" class="form-control"
           value="<?php echo $usereu['account_type']; ?>">
</div>

<div class="form-group">
    <label>Currency</label>
    <input type="text" name="currency" class="form-control"
           value="<?php echo $usereu['currency']; ?>">
</div>

<div class="form-group">
    <label>KYC Status</label>
    <select name="kyc_status" class="form-control">
        <option value="unverified" <?php if($usereu['kyc_status']=='unverified') echo 'selected'; ?>>Unverified</option>
        <option value="pending" <?php if($usereu['kyc_status']=='pending') echo 'selected'; ?>>Pending</option>
        <option value="verified" <?php if($usereu['kyc_status']=='verified') echo 'selected'; ?>>Verified</option>
        <option value="rejected" <?php if($usereu['kyc_status']=='rejected') echo 'selected'; ?>>Rejected</option>
    </select>
</div>

</div>

<div class="box-footer">
    <button type="submit" name="submit" class="btn btn-primary">Update User</button>
</div>

</form>
</div>
</div>
</div>
</section>
</div>

<?php include "includes/footer.php"; ?>
