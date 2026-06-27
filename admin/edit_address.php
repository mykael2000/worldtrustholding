<?php
include("includes/header.php");

$id = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM deposit_accounts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$address = $stmt->get_result()->fetch_assoc();

if (!$address) {
    header("Location: address.php");
    exit;
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Edit Deposit Address</h1>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="card shadow-sm">
                <div class="card-body">

                    <form action="update_address.php" method="post">
                        <input type="hidden" name="id" value="<?= $address['id'] ?>">

                        <div class="mb-3">
                            <label class="form-label">Method</label>
                            <input type="text" class="form-control" value="<?= $address['method'] ?>" disabled>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Currency</label>
                                <input type="text" name="currency" class="form-control"
                                       value="<?= $address['currency'] ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Network</label>
                                <input type="text" name="network" class="form-control"
                                       value="<?= $address['network'] ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address / Bank Details</label>
                            <textarea name="address" rows="4" class="form-control" required><?= htmlspecialchars($address['address']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="is_active" class="form-select">
                                <option value="1" <?= $address['is_active'] ? 'selected' : '' ?>>Active</option>
                                <option value="0" <?= !$address['is_active'] ? 'selected' : '' ?>>Disabled</option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-success">Save Changes</button>
                            <a href="address.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </section>
</div>

<?php include("includes/footer.php"); ?>
