<?php
include("includes/header.php");

$id = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare("
    SELECT * FROM support_tickets WHERE id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$ticket = $stmt->get_result()->fetch_assoc();

if (!$ticket) {
    echo "<div class='alert alert-danger'>Ticket not found.</div>";
    exit;
}
?>
<div class="content-wrapper">
<section class="content-header">
    <h1>View Ticket</h1>
</section>

<section class="content">
<div class="card">
    <div class="card-body">

        <h4><?= htmlspecialchars($ticket['subject']) ?></h4>

        <p>
            <strong>Name:</strong> <?= htmlspecialchars($ticket['name']) ?><br>
            <strong>Email:</strong> <?= htmlspecialchars($ticket['email']) ?><br>
            <strong>Priority:</strong> <?= ucfirst($ticket['priority']) ?><br>
            <strong>Status:</strong> <?= $ticket['status'] ?><br>
            <strong>Date:</strong> <?= date('M d, Y H:i', strtotime($ticket['created_at'])) ?>
        </p>

        <hr>

        <p><?= nl2br(htmlspecialchars($ticket['message'])) ?></p>

        <hr>

        <!-- CONTACT VIA EMAIL -->
        <a 
            href="mailto:<?= $ticket['email'] ?>?subject=Re:%20<?= urlencode($ticket['subject']) ?>"
            class="btn btn-success"
        >
            📧 Contact via Email
        </a>

        <!-- STATUS UPDATE -->
        <form action="update_ticket_status.php" method="post" class="mt-3">
            <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">

            <select name="status" class="form-control w-25 d-inline">
                <option <?= $ticket['status']=='Open'?'selected':'' ?>>Open</option>
                <option <?= $ticket['status']=='In Progress'?'selected':'' ?>>In Progress</option>
                <option <?= $ticket['status']=='Closed'?'selected':'' ?>>Closed</option>
            </select>

            <button type="submit" class="btn btn-primary ml-2">
                Update Status
            </button>
        </form>

    </div>
</div>
</section>
</div>
<?php include("includes/footer.php"); ?>
