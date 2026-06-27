<?php include("header.php"); 

// ── Resolve & validate the transaction ID ──────────────────────────────────
$txId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($txId <= 0) {
    header('Location: statement.php');
    exit;
}

// ── Fetch transaction — must belong to the logged-in user ─────────────────
$stmt = $conn->prepare("
    SELECT id, tranx_id, type, amount, details, description, status, created_at
    FROM history
    WHERE id = ? AND client_id = ?
    LIMIT 1
");
$stmt->bind_param("ii", $txId, $userId);
$stmt->execute();
$tx = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$tx) {
    // Not found or belongs to another user
    header('Location: statement.php');
    exit;
}

// Decode details if JSON
$detailsDecoded = json_decode($tx['details'], true);
$detailsIsJson  = (json_last_error() === JSON_ERROR_NONE && is_array($detailsDecoded));

// Determine badge colours
$typeBadge = strtolower($tx['type']) === 'credit'
    ? 'bg-green-100 text-green-700'
    : 'bg-red-100 text-red-700';

$statusBadge = match ($tx['status']) {
    'Completed' => 'bg-green-100 text-green-700',
    'Pending'   => 'bg-yellow-100 text-yellow-700',
    'Failed'    => 'bg-red-100 text-red-700',
    default     => 'bg-gray-100 text-gray-700',
};
?>

<!-- Main Content -->
<main class="flex-1 overflow-y-auto pb-16 md:pb-0">
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 md:px-8">

    <!-- Breadcrumb -->
    <div class="flex items-center text-sm text-gray-500 mb-6">
        <a href="index.php" class="hover:text-primary-600">Dashboard</a>
        <i data-lucide="chevron-right" class="h-4 w-4 mx-2"></i>
        <a href="statement.php" class="hover:text-primary-600">Statement</a>
        <i data-lucide="chevron-right" class="h-4 w-4 mx-2"></i>
        <span class="font-medium text-gray-700">Transaction Detail</span>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <!-- Coloured header strip -->
        <div class="<?= strtolower($tx['type']) === 'credit' ? 'bg-green-600' : 'bg-primary-600' ?> px-6 py-8 text-white text-center">
            <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-white bg-opacity-20 mb-3">
                <?php if (strtolower($tx['type']) === 'credit'): ?>
                <i data-lucide="arrow-down-circle" class="h-8 w-8 text-white"></i>
                <?php else: ?>
                <i data-lucide="arrow-up-circle" class="h-8 w-8 text-white"></i>
                <?php endif; ?>
            </div>
            <p class="text-sm font-medium opacity-80 mb-1"><?= htmlspecialchars($tx['type']) ?> Transaction</p>
            <p class="text-4xl font-bold">
                <?= strtolower($tx['type']) === 'credit' ? '+' : '-' ?>$<?= number_format($tx['amount'], 2) ?>
            </p>
            <p class="text-sm opacity-75 mt-2"><?= date('l, F j, Y \a\t g:i A', strtotime($tx['created_at'])) ?></p>
        </div>

        <!-- Details section -->
        <div class="px-6 py-6 divide-y divide-gray-100">

            <!-- Row: Reference -->
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-2">
                    <i data-lucide="hash" class="h-4 w-4 text-gray-400"></i>
                    <span class="text-sm font-medium text-gray-600">Reference</span>
                </div>
                <span class="text-sm font-mono font-semibold text-gray-900">#<?= htmlspecialchars($tx['tranx_id']) ?></span>
            </div>

            <!-- Row: Type -->
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-2">
                    <i data-lucide="tag" class="h-4 w-4 text-gray-400"></i>
                    <span class="text-sm font-medium text-gray-600">Type</span>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $typeBadge ?>">
                    <?= htmlspecialchars($tx['type']) ?>
                </span>
            </div>

            <!-- Row: Status -->
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-2">
                    <i data-lucide="check-circle" class="h-4 w-4 text-gray-400"></i>
                    <span class="text-sm font-medium text-gray-600">Status</span>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusBadge ?>">
                    <?= htmlspecialchars($tx['status']) ?>
                </span>
            </div>

            <!-- Row: Description -->
            <?php if (!empty($tx['description'])): ?>
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-2">
                    <i data-lucide="file-text" class="h-4 w-4 text-gray-400"></i>
                    <span class="text-sm font-medium text-gray-600">Description</span>
                </div>
                <span class="text-sm text-gray-800 text-right max-w-xs"><?= htmlspecialchars($tx['description']) ?></span>
            </div>
            <?php endif; ?>

            <!-- Row: Date -->
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-2">
                    <i data-lucide="calendar" class="h-4 w-4 text-gray-400"></i>
                    <span class="text-sm font-medium text-gray-600">Date & Time</span>
                </div>
                <span class="text-sm text-gray-800"><?= date('M d, Y \a\t g:i A', strtotime($tx['created_at'])) ?></span>
            </div>

            <!-- Row: Account -->
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-2">
                    <i data-lucide="user" class="h-4 w-4 text-gray-400"></i>
                    <span class="text-sm font-medium text-gray-600">Account Holder</span>
                </div>
                <span class="text-sm text-gray-800">
                    <?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?>
                    &nbsp;·&nbsp; <?= htmlspecialchars($user['account_id']) ?>
                </span>
            </div>

            <!-- Details / metadata block -->
            <?php if (!empty($tx['details'])): ?>
            <div class="py-4">
                <div class="flex items-center space-x-2 mb-3">
                    <i data-lucide="info" class="h-4 w-4 text-gray-400"></i>
                    <span class="text-sm font-medium text-gray-600">Additional Details</span>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                    <?php if ($detailsIsJson): ?>
                        <?php foreach ($detailsDecoded as $key => $value): ?>
                        <div class="flex justify-between text-sm">
                            <span class="font-medium text-gray-500 capitalize"><?= htmlspecialchars(str_replace('_', ' ', $key)) ?></span>
                            <span class="text-gray-800 font-mono break-all text-right max-w-xs"><?= htmlspecialchars((string) $value) ?></span>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-sm text-gray-700"><?= htmlspecialchars($tx['details']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>

        <!-- Footer actions -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row gap-3">
            <a href="statement.php"
               class="flex-1 inline-flex items-center justify-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <i data-lucide="arrow-left" class="h-4 w-4 mr-2"></i> Back to Statement
            </a>
            <a href="accounthistory.php"
               class="flex-1 inline-flex items-center justify-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <i data-lucide="list" class="h-4 w-4 mr-2"></i> All Transactions
            </a>
        </div>

    </div>

        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});

window.onload = function() {
    const preloader = document.querySelector('.page-loading');

    if (!preloader) {
        return;
    }

    setTimeout(function() {
        preloader.classList.remove('active');
        setTimeout(function() {
            preloader.remove();
        }, 500);
    }, 800);
};
</script>

<?php include("footer.php"); ?>
