<?php include("header.php"); 

// ── Date range resolution ──────────────────────────────────────────────────
$preset  = $_GET['preset']  ?? 'this_month';
$dateFrom = $_GET['from']  ?? '';
$dateTo   = $_GET['to']    ?? '';

$today     = date('Y-m-d');
$thisYear  = (int) date('Y');
$thisMonth = (int) date('m');

switch ($preset) {
    case 'last_month':
        $dateFrom = date('Y-m-01', strtotime('first day of last month'));
        $dateTo   = date('Y-m-t',  strtotime('last day of last month'));
        break;
    case 'last_3_months':
        $dateFrom = date('Y-m-d', strtotime('-3 months', strtotime(date('Y-m-01'))));
        $dateTo   = $today;
        break;
    case 'last_6_months':
        $dateFrom = date('Y-m-d', strtotime('-6 months', strtotime(date('Y-m-01'))));
        $dateTo   = $today;
        break;
    case 'last_year':
        $dateFrom = ($thisYear - 1) . '-01-01';
        $dateTo   = ($thisYear - 1) . '-12-31';
        break;
    case 'ytd':
        $dateFrom = $thisYear . '-01-01';
        $dateTo   = $today;
        break;
    case 'custom':
        // Use supplied from/to; fall through to validation below
        break;
    case 'this_month':
    default:
        $preset   = 'this_month';
        $dateFrom = date('Y-m-01');
        $dateTo   = $today;
        break;
}

// Sanitise & clamp custom range
$dateFrom = preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom) ? $dateFrom : date('Y-m-01');
$dateTo   = preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)   ? $dateTo   : $today;
if ($dateFrom > $dateTo) { [$dateFrom, $dateTo] = [$dateTo, $dateFrom]; }

// ── Fetch transactions for the period ─────────────────────────────────────
$stmt = $conn->prepare("
    SELECT id, tranx_id, type, amount, details, description, status, created_at
    FROM history
    WHERE client_id = ?
      AND DATE(created_at) BETWEEN ? AND ?
    ORDER BY created_at DESC
");
$stmt->bind_param("iss", $userId, $dateFrom, $dateTo);
$stmt->execute();
$txResult = $stmt->get_result();
$transactions = $txResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ── Summary totals ─────────────────────────────────────────────────────────
$totalCredits = 0;
$totalDebits  = 0;
foreach ($transactions as $tx) {
    if (strtolower($tx['type']) === 'credit') {
        $totalCredits += (float) $tx['amount'];
    } else {
        $totalDebits += (float) $tx['amount'];
    }
}
$netChange = $totalCredits - $totalDebits;
$txCount   = count($transactions);

// ── Period label ──────────────────────────────────────────────────────────
$presetLabels = [
    'this_month'   => 'This Month',
    'last_month'   => 'Last Month',
    'last_3_months'=> 'Last 3 Months',
    'last_6_months'=> 'Last 6 Months',
    'last_year'    => 'Last Year',
    'ytd'          => 'Year to Date',
    'custom'       => 'Custom Range',
];
$periodLabel = $presetLabels[$preset] ?? 'Custom Range';
?>

<!-- Main Content -->
<main class="flex-1 overflow-y-auto pb-16 md:pb-0">
    <div class="py-6">
        <div class="max-w-8xl mx-auto px-4 sm:px-6 md:px-8">

<div x-data="statementApp()">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Account Statement</h1>
            <div class="flex items-center text-sm text-gray-500">
                <a href="index.php" class="hover:text-primary-600">Dashboard</a>
                <i data-lucide="chevron-right" class="h-4 w-4 mx-2"></i>
                <span class="font-medium text-gray-700">Statement</span>
            </div>
        </div>
        <div class="flex mt-4 md:mt-0 space-x-3">
            <button
                @click="showPeriodModal = true"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition-colors">
                <i data-lucide="calendar" class="h-4 w-4 mr-2"></i> Change Period
            </button>
            <button
                @click="downloadPdf()"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 focus:outline-none transition-colors">
                <i data-lucide="download" class="h-4 w-4 mr-2"></i> Download PDF
            </button>
        </div>
    </div>

    <!-- Period Banner -->
    <div class="bg-primary-50 border border-primary-100 rounded-xl px-5 py-4 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center space-x-3">
            <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
                <i data-lucide="calendar-range" class="h-5 w-5 text-primary-700"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-primary-800"><?= htmlspecialchars($periodLabel) ?></p>
                <p class="text-xs text-primary-600">
                    <?= date('M d, Y', strtotime($dateFrom)) ?> &mdash; <?= date('M d, Y', strtotime($dateTo)) ?>
                </p>
            </div>
        </div>
        <div class="flex items-center text-sm text-primary-700 font-medium">
            <i data-lucide="user-circle" class="h-4 w-4 mr-1"></i>
            <?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?>
            &nbsp;&middot;&nbsp; A/C: <?= htmlspecialchars($user['account_id']) ?>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Credits</p>
                <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                    <i data-lucide="trending-up" class="h-4 w-4 text-green-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-green-600">$<?= number_format($totalCredits, 2) ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Debits</p>
                <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center">
                    <i data-lucide="trending-down" class="h-4 w-4 text-red-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-red-600">$<?= number_format($totalDebits, 2) ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Net Change</p>
                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                    <i data-lucide="activity" class="h-4 w-4 text-blue-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold <?= $netChange >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                <?= ($netChange >= 0 ? '+' : '') . '$' . number_format(abs($netChange), 2) ?>
            </p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Transactions</p>
                <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center">
                    <i data-lucide="list" class="h-4 w-4 text-purple-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900"><?= $txCount ?></p>
        </div>
    </div>

    <!-- Transactions Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i data-lucide="file-text" class="h-5 w-5 text-gray-500"></i>
                <h2 class="text-base font-semibold text-gray-900">Statement Transactions</h2>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                    <?= $txCount ?> total
                </span>
            </div>
            <!-- Search within page -->
            <div class="relative hidden sm:block">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="search" class="h-4 w-4 text-gray-400"></i>
                </div>
                <input
                    type="search"
                    x-model="search"
                    class="pl-9 pr-3 py-1.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                    placeholder="Search reference…"
                />
            </div>
        </div>
        <div class="overflow-x-auto">
            <?php if ($txCount > 0): ?>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Reference</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Amount</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Description</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">Date</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100" id="statement-tbody">
                    <?php foreach ($transactions as $tx): ?>
                    <tr class="hover:bg-gray-50 transition-colors tx-row"
                        data-ref="<?= htmlspecialchars(strtolower($tx['tranx_id'])) ?>">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="text-sm font-mono font-medium text-gray-800">#<?= htmlspecialchars($tx['tranx_id']) ?></span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                <?= strtolower($tx['type']) === 'credit' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                <?= strtolower($tx['type']) === 'credit'
                                    ? '<i data-lucide="arrow-down-circle" class="h-3 w-3 mr-1"></i>'
                                    : '<i data-lucide="arrow-up-circle" class="h-3 w-3 mr-1"></i>' ?>
                                <?= htmlspecialchars($tx['type']) ?>
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="text-sm font-semibold <?= strtolower($tx['type']) === 'credit' ? 'text-green-700' : 'text-red-700' ?>">
                                <?= strtolower($tx['type']) === 'credit' ? '+' : '-' ?>$<?= number_format($tx['amount'], 2) ?>
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap hidden md:table-cell">
                            <span class="text-sm text-gray-600"><?= htmlspecialchars($tx['description']) ?></span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium
                                <?php echo match ($tx['status']) {
                                    'Completed' => 'bg-green-100 text-green-700',
                                    'Pending'   => 'bg-yellow-100 text-yellow-700',
                                    'Failed'    => 'bg-red-100 text-red-700',
                                    default     => 'bg-gray-100 text-gray-700'
                                }; ?>">
                                <?= htmlspecialchars($tx['status']) ?>
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap hidden sm:table-cell text-sm text-gray-500">
                            <?= date('M d, Y', strtotime($tx['created_at'])) ?>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-right">
                            <a href="transaction-detail.php?id=<?= (int) $tx['id'] ?>"
                               class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-primary-600 bg-primary-50 hover:bg-primary-100 rounded-lg transition-colors">
                                <i data-lucide="eye" class="h-3 w-3 mr-1"></i> View
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="py-16 flex flex-col items-center justify-center">
                <i data-lucide="inbox" class="h-16 w-16 text-gray-300 mb-4"></i>
                <p class="text-lg font-medium text-gray-600">No transactions found</p>
                <p class="text-sm text-gray-500 mt-1 mb-5">
                    There are no transactions for the selected period.
                </p>
                <button
                    @click="showPeriodModal = true"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg">
                    <i data-lucide="calendar" class="h-4 w-4 mr-2"></i> Change Period
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ── Period Picker Modal ──────────────────────────────────────────── -->
    <div
        x-show="showPeriodModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm" @click="showPeriodModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                x-show="showPeriodModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-2xl shadow-xl text-left overflow-hidden transform transition-all sm:my-8 sm:align-middle w-full max-w-md mx-auto p-6">

                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Select Statement Period</h3>
                        <p class="text-sm text-gray-500 mt-0.5">Choose a period or set a custom date range</p>
                    </div>
                    <button @click="showPeriodModal = false" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>

                <!-- Preset buttons -->
                <div class="grid grid-cols-2 gap-2 mb-5">
                    <?php
                    $presets = [
                        'this_month'    => 'This Month',
                        'last_month'    => 'Last Month',
                        'last_3_months' => 'Last 3 Months',
                        'last_6_months' => 'Last 6 Months',
                        'ytd'           => 'Year to Date',
                        'last_year'     => 'Last Year',
                    ];
                    foreach ($presets as $key => $label):
                    ?>
                    <a href="?preset=<?= $key ?>"
                       class="flex items-center justify-center px-3 py-2.5 text-sm font-medium rounded-lg border transition-colors
                              <?= $preset === $key
                                    ? 'bg-primary-600 text-white border-primary-600'
                                    : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' ?>">
                        <?= $label ?>
                    </a>
                    <?php endforeach; ?>
                </div>

                <hr class="my-4 border-gray-100">

                <!-- Custom range -->
                <form method="GET" action="statement.php">
                    <input type="hidden" name="preset" value="custom">
                    <p class="text-sm font-semibold text-gray-700 mb-3">Custom Date Range</p>
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">From</label>
                            <input type="date" name="from"
                                value="<?= htmlspecialchars($dateFrom) ?>"
                                class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">To</label>
                            <input type="date" name="to"
                                value="<?= htmlspecialchars($dateTo) ?>"
                                class="block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>
                    <button type="submit"
                        class="w-full flex justify-center items-center px-4 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors">
                        <i data-lucide="filter" class="h-4 w-4 mr-2"></i> Apply Custom Range
                    </button>
                </form>
            </div>
        </div>
    </div>

</div><!-- /x-data -->

        </div>
    </div>
</main>

<!-- Hidden PDF download form -->
<form id="pdf-form" method="POST" action="statement-pdf.php" style="display:none">
    <input type="hidden" name="from"   value="<?= htmlspecialchars($dateFrom) ?>">
    <input type="hidden" name="to"     value="<?= htmlspecialchars($dateTo) ?>">
    <input type="hidden" name="preset" value="<?= htmlspecialchars($preset) ?>">
    <input type="hidden" name="label"  value="<?= htmlspecialchars($periodLabel) ?>">
</form>

<script>
document.addEventListener('alpine:init', function() {
    Alpine.data('statementApp', function() {
        return {
            showPeriodModal: false,
            search: '',

            init() {
                this.$watch('search', value => this.filterRows(value));
            },

            downloadPdf() {
                document.getElementById('pdf-form').submit();
            },

            filterRows(q) {
                const rows = document.querySelectorAll('.tx-row');
                rows.forEach(row => {
                    const ref = row.getAttribute('data-ref') || '';
                    row.style.display = ref.includes(q.toLowerCase()) ? '' : 'none';
                });
            }
        };
    });
});

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
