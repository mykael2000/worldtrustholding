<?php
include("header.php");

$search   = trim($_GET['search'] ?? '');
$status   = trim($_GET['status'] ?? '');
$orderBy  = ($_GET['orderBy'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';
$perPage  = max(1, intval($_GET['perPage'] ?? 10));
$page     = max(1, intval($_GET['page'] ?? 1));
$offset   = ($page - 1) * $perPage;

/* -------------------------
   BUILD QUERY CONDITIONS
--------------------------*/
$where  = "WHERE user_id = ?";
$params = [$user_id];
$types  = "i";

if ($status !== '') {
    $where .= " AND status = ?";
    $params[] = $status;
    $types   .= "s";
}

if ($search !== '') {
    $where .= " AND (
        name LIKE CONCAT('%', ?, '%')
        OR idme_email LIKE CONCAT('%', ?, '%')
        OR country LIKE CONCAT('%', ?, '%')
    )";
    $params[] = $search;
    $params[] = $search;
    $params[] = $search;
    $types   .= "sss";
}

/* -------------------------
   COUNT TOTAL RECORDS
--------------------------*/
$countSql = "SELECT COUNT(*) FROM irs_refund_requests $where";
$stmt = $conn->prepare($countSql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$stmt->bind_result($totalRows);
$stmt->fetch();
$stmt->close();

$totalPages = ceil($totalRows / $perPage);

/* -------------------------
   FETCH IRS REQUESTS
--------------------------*/
$sql = "
    SELECT id, name, idme_email, country, status, created_at
    FROM irs_refund_requests
    $where
    ORDER BY created_at $orderBy
    LIMIT ? OFFSET ?
";

$params[] = $perPage;
$params[] = $offset;
$types   .= "ii";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$records = $stmt->get_result();
?>

<!-- Main Content -->
<main class="flex-1 overflow-y-auto pb-16 md:pb-0">
<div class="py-6">
<div class="max-w-8xl mx-auto px-4 sm:px-6 md:px-8">

<!-- Header -->
<div class="flex flex-col mb-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-1">IRS Refund History</h1>
    <div class="flex items-center text-sm text-gray-500">
        <a href="index.php" class="hover:text-primary-600">Dashboard</a>
        <i data-lucide="chevron-right" class="h-4 w-4 mx-2"></i>
        <span class="font-medium text-gray-700">IRS Refund Requests</span>
    </div>
</div>

<!-- Card -->
<div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">

<!-- Card Header -->
<div class="relative bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-4">
    <div class="flex items-center">
        <div class="bg-white/20 p-2 rounded-full mr-3">
            <i data-lucide="receipt" class="h-6 w-6 text-white"></i>
        </div>
        <div>
            <h2 class="text-xl font-bold text-white">Your IRS Requests</h2>
            <p class="text-white/80 text-sm">Track submitted tax refund requests</p>
        </div>
    </div>
</div>

<!-- Card Body -->
<div class="p-6">

<!-- Search -->
<form method="get" class="mb-6">
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i data-lucide="search" class="h-5 w-5 text-gray-400"></i>
        </div>
        <input
            type="search"
            name="search"
            value="<?= htmlspecialchars($search) ?>"
            class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-lg"
            placeholder="Search by name, email, or country..."
        />
    </div>
</form>

<!-- Table -->
<div class="overflow-x-auto">
<table class="min-w-full bg-white rounded-lg overflow-hidden">
<thead class="bg-gray-50">
<tr>
    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Full Name</th>
    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID.me Email</th>
    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Country</th>
    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Submitted</th>
</tr>
</thead>

<tbody class="divide-y divide-gray-200">
<?php if ($records->num_rows === 0): ?>
<tr>
    <td colspan="5" class="px-6 py-10 text-center text-gray-500">
        No IRS refund requests found
    </td>
</tr>
<?php else: ?>
<?php while ($row = $records->fetch_assoc()): ?>
<tr>
    <td class="px-6 py-4 text-sm font-medium text-gray-900">
        <?= htmlspecialchars($row['name']) ?>
    </td>
    <td class="px-6 py-4 text-sm text-gray-700">
        <?= htmlspecialchars($row['idme_email']) ?>
    </td>
    <td class="px-6 py-4 text-sm text-gray-700">
        <?= htmlspecialchars($row['country']) ?>
    </td>
    <td class="px-6 py-4">
        <?php
        $badge = match ($row['status']) {
            'Completed'  => 'green',
            'Rejected'   => 'red',
            'Processing' => 'blue',
            default      => 'yellow'
        };
        ?>
        <span class="px-2 py-1 rounded-full text-xs bg-<?= $badge ?>-100 text-<?= $badge ?>-800">
            <?= htmlspecialchars($row['status']) ?>
        </span>
    </td>
    <td class="px-6 py-4 text-sm text-gray-500">
        <?= date('M d, Y', strtotime($row['created_at'])) ?>
    </td>
</tr>
<?php endwhile; ?>
<?php endif; ?>
</tbody>
</table>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<div class="mt-6 flex justify-center space-x-2">
<?php for ($i = 1; $i <= $totalPages; $i++): ?>
<a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&orderBy=<?= strtolower($orderBy) ?>&perPage=<?= $perPage ?>"
class="px-3 py-1 rounded-md text-sm <?= $i == $page ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700' ?>">
<?= $i ?>
</a>
<?php endfor; ?>
</div>
<?php endif; ?>

</div>
</div>

</div>
</div>
</main>
  <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 hidden md:block">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 md:flex md:items-center md:justify-between">
                    <div class="flex items-center">
                        <img src="../storage/app/public/photos/rnaxX97vE61cQEoOT6xDiehSkpsnSbvEWlLu2WFk.png" alt="Logo" class="h-6 w-auto mr-2">
                        <p class="text-sm text-gray-500">© 2025 Bremt Global Bank. All rights reserved.</p>
                    </div>
                    <div class="flex space-x-6 mt-4 md:mt-0">
                        <a href="#" class="text-sm text-gray-500 hover:text-gray-700">Privacy Policy</a>
                        <a href="#" class="text-sm text-gray-500 hover:text-gray-700">Terms of Service</a>
                        <a href="support.php" class="text-sm text-gray-500 hover:text-gray-700">Contact Support</a>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>

    <!-- Enhanced Page Loading Animation -->
    <script>
        window.onload = function() {
            const preloader = document.querySelector('.page-loading');

            // Add a slight delay to make loading animation more noticeable
            setTimeout(function() {
                preloader.classList.remove('active');
                setTimeout(function() {
                    preloader.remove();
                }, 500);
            }, 800);
        };
    </script>

    <!-- Date and Time Updates -->
    <script>
        // Function to update current time
        function updateDateTime() {
            const now = new Date();
            const timeElements = document.querySelectorAll('[data-current-time]');
            const dateElements = document.querySelectorAll('[data-current-date]');

            if (timeElements.length > 0) {
                const timeString = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                timeElements.forEach(el => {
                    el.textContent = timeString;
                });
            }

            if (dateElements.length > 0) {
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                const dateString = now.toLocaleDateString(undefined, options);
                dateElements.forEach(el => {
                    el.textContent = dateString;
                });
            }
        }

        // Update time every minute
        updateDateTime();
        setInterval(updateDateTime, 60000);
    </script>

<?php include("footer.php"); ?>
