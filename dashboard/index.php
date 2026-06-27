<?php include("header.php");
$txQuery = mysqli_query(
    $conn,
    "SELECT * FROM history 
     WHERE client_id = '{$userId}' 
     ORDER BY created_at DESC LIMIT 5"
);

$hasTransactions = mysqli_num_rows($txQuery) > 0;

?>
            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto pb-16 md:pb-0">
                <div class="py-6">
                    <div class="max-w-8xl mx-auto px-4 sm:px-6 md:px-8">
                        
<div x-data="{
    showBankAccount: false,
    showSendMoney: false,
    currentTime: '',
    greeting: '',
    currentDate: '',
    balanceVisible: true,
    toggleBalance() {
        this.balanceVisible = !this.balanceVisible;
    },
    updateTime() {
        const now = new Date();

        // Format the time (HH:MM:SS)
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const seconds = now.getSeconds().toString().padStart(2, '0');
        this.currentTime = `${hours}:${minutes}:${seconds}`;

        // Set greeting based on hours
        if (now.getHours() < 12) {
            this.greeting = 'Good Morning';
        } else if (now.getHours() < 18) {
            this.greeting = 'Good Afternoon';
        } else {
            this.greeting = 'Good Evening';
        }

        // Format the date (Day, Month Date, Year)
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        this.currentDate = now.toLocaleDateString(undefined, options);
    }
}" x-init="
    updateTime();
    setInterval(() => updateTime(), 1000);
">
    <!-- Alerts -->
        
    <!-- Top Stats Summary Bar -->
    <div class="hidden lg:grid grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-r from-primary-50 to-white rounded-xl p-4 border border-primary-100 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-800">Current Balance</p>
                <p class="text-lg font-bold text-gray-800"><?php echo $user['currency']; ?><?php echo number_format($user['total_balance'],2,'.',','); ?></p>
            </div>
            <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
                <i data-lucide="wallet" class="h-5 w-5 text-gray-800"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-50 to-white rounded-xl p-4 border border-green-100 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500">Monthly Income</p>
                <p class="text-lg font-bold text-green-700"><?php echo $user['currency']; ?><?php echo number_format($user['monthly_income'],2,'.',','); ?></p>
            </div>
            <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                <i data-lucide="trending-up" class="h-5 w-5 text-green-600"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-red-50 to-white rounded-xl p-4 border border-red-100 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500">Monthly Outgoing</p>
                <p class="text-lg font-bold text-red-700"><?php echo $user['currency']; ?><?php echo number_format($user['monthly_outgoing'],2,'.',','); ?></p>
            </div>
            <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                <i data-lucide="trending-down" class="h-5 w-5 text-red-600"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-purple-50 to-white rounded-xl p-4 border border-purple-100 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500">Transaction Limit</p>
                <p class="text-lg font-bold text-purple-700"><?php echo $user['currency']; ?><?php echo number_format($user['transaction_limit'],2,'.',','); ?></p>
            </div>
            <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                <i data-lucide="gauge" class="h-5 w-5 text-purple-600"></i>
            </div>
        </div>
    </div>


    <!-- Main Dashboard Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Balance and Quick Actions -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Balance Card with Interactive Elements -->
            <div class="bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 rounded-2xl shadow-lg text-white relative overflow-hidden">
                <!-- Day/Night Decoration -->
                <div class="absolute inset-0 w-full h-full overflow-hidden">
                    <div class="absolute opacity-5 right-0 top-0">
                        <div class="bg-blue-900 rounded-full h-32 w-32 -mt-10 -mr-10 blur-xl"></div>
                    </div>
                    <div class="absolute opacity-5 left-1/2 top-1/2">
                        <div class="bg-indigo-900 rounded-full h-40 w-40 blur-xl"></div>
                    </div>
                                            <!-- Nighttime stars -->
                                                    <div class="absolute opacity-20 rounded-full bg-white h-1 w-1" style="left: 76%; top: 82%"></div>
                                                    <div class="absolute opacity-20 rounded-full bg-white h-1 w-1" style="left: 63%; top: 34%"></div>
                                                    <div class="absolute opacity-20 rounded-full bg-white h-1 w-1" style="left: 92%; top: 52%"></div>
                                                    <div class="absolute opacity-20 rounded-full bg-white h-1 w-1" style="left: 15%; top: 82%"></div>
                                                    <div class="absolute opacity-20 rounded-full bg-white h-1 w-1" style="left: 47%; top: 60%"></div>
                                                    <div class="absolute opacity-20 rounded-full bg-white h-1 w-1" style="left: 70%; top: 95%"></div>
                                                    <div class="absolute opacity-20 rounded-full bg-white h-1 w-1" style="left: 25%; top: 51%"></div>
                                                    <div class="absolute opacity-20 rounded-full bg-white h-1 w-1" style="left: 10%; top: 62%"></div>
                                                            </div>

                <!-- Card Content -->
                <div class="relative z-10 p-6">
                    <!-- Header with time and user -->
                    <div class="flex items-center justify-between mb-6">
                       <div class="flex items-center space-x-3">
                    <div class="h-12 w-12 rounded-full bg-white/20 text-white flex items-center justify-center font-bold border-2 border-white/20">
            <?php echo $user['firstname'][0].$user['lastname'][0]; ?>
        </div>
        <div>
        <div class="text-sm text-white/80" x-text="greeting"></div>
        <div class="font-medium text-white"><?php echo $user['firstname']; ?></div>
    </div>
</div>

                        <div class="text-right">
                            <div class="text-lg font-bold" x-text="currentTime"></div>
                            <div class="text-xs text-white/70" x-text="currentDate"></div>
                        </div>
                    </div>
                    <!-- Balance with hide/show toggle -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-medium mb-1">Available Balance</h2>
                            <button @click="toggleBalance()" class="text-white/80 hover:text-white focus:outline-none transition-all">
                                <i x-show="balanceVisible" data-lucide="eye-off" class="h-5 w-5"></i>
                                <i x-show="!balanceVisible" data-lucide="eye" class="h-5 w-5"></i>
                            </button>
                        </div>
                        <div x-show="balanceVisible" x-transition class="text-3xl font-bold">
                            <?php echo $user['currency']; ?><?php echo number_format($user['total_balance'],2,'.',','); ?>
                        </div>
                        <div x-show="!balanceVisible" x-transition class="text-3xl font-bold">
                            *******
                        </div>
                    </div>

                    <!-- Account Info Bar -->
<div class="relative z-10 p-4 bg-white/10 rounded-lg backdrop-blur-sm">
    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
        <!-- Mobile layout (side-by-side) -->
        <div class="sm:hidden flex items-center justify-between w-full">
            <div class="flex items-center flex-1 min-w-0">
                <div class="flex-shrink-0 mr-3">
                    <div class="h-10 w-10 bg-white/20 rounded-full flex items-center justify-center">
                        <i data-lucide="shield" class="h-5 w-5 text-white"></i>
                    </div>
                </div>
                <div class="truncate">
                    <div class="text-sm font-medium">Your Account Number</div>
                    <div class="flex items-center">
                        <div class="text-lg font-bold truncate mr-2"><?php echo $user['account_id']; ?></div>
                        <div class="flex-shrink-0">
                            <?php
                            $status = $user['kyc_status'] ?? 'unverified';

                            $styles = [
                                'verified' => [
                                    'bg' => 'bg-green-100',
                                    'text' => 'text-green-800',
                                    'dot' => 'bg-green-600',
                                    'label' => 'Verified'
                                ],
                                'pending' => [
                                    'bg' => 'bg-yellow-100',
                                    'text' => 'text-yellow-800',
                                    'dot' => 'bg-yellow-600',
                                    'label' => 'Pending'
                                ],
                                'rejected' => [
                                    'bg' => 'bg-red-100',
                                    'text' => 'text-red-800',
                                    'dot' => 'bg-red-600',
                                    'label' => 'Rejected'
                                ],
                                'unverified' => [
                                    'bg' => 'bg-gray-100',
                                    'text' => 'text-gray-800',
                                    'dot' => 'bg-gray-500',
                                    'label' => 'Unverified'
                                ]
                            ];

                            $badge = $styles[$status];
                            ?>

                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $badge['bg']; ?> <?= $badge['text']; ?>">
                                <span class="h-1.5 w-1.5 rounded-full <?= $badge['dot']; ?> mr-1"></span>
                                <?= $badge['label']; ?>
                            </span>

                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-2 ml-2">
                <a href="accounthistory.php" class="inline-flex items-center justify-center px-2 py-1 bg-white text-primary-600 text-xs font-medium rounded-md hover:bg-gray-50">
                    <i data-lucide="activity" class="h-3 w-3 mr-1"></i> Transactions
                </a>
                <a href="deposits.php" class="inline-flex items-center justify-center px-2 py-1 bg-primary-700 text-white text-xs font-medium rounded-md hover:bg-primary-800 border border-white/10">
                    <i data-lucide="wallet" class="h-3 w-3 mr-1"></i> Top up
                </a>
            </div>
        </div>

        <!-- Desktop layout - hidden on mobile -->
        <div class="hidden sm:flex sm:items-center sm:flex-1">
            <div class="flex-shrink-0 mr-4">
                <div class="h-10 w-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i data-lucide="shield" class="h-5 w-5 text-white"></i>
                </div>
            </div>
            <div>
                <div class="flex items-center">
                    <div class="text-sm font-medium mr-2">Your Account Number</div>
                    <?php
$status = $user['kyc_status'] ?? 'unverified';

$styles = [
    'verified' => [
        'bg' => 'bg-green-100',
        'text' => 'text-green-800',
        'dot' => 'bg-green-600',
        'label' => 'Verified'
    ],
    'pending' => [
        'bg' => 'bg-yellow-100',
        'text' => 'text-yellow-800',
        'dot' => 'bg-yellow-600',
        'label' => 'Pending'
    ],
    'rejected' => [
        'bg' => 'bg-red-100',
        'text' => 'text-red-800',
        'dot' => 'bg-red-600',
        'label' => 'Rejected'
    ],
    'unverified' => [
        'bg' => 'bg-gray-100',
        'text' => 'text-gray-800',
        'dot' => 'bg-gray-500',
        'label' => 'Unverified'
    ]
];

$badge = $styles[$status];
?>

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $badge['bg']; ?> <?= $badge['text']; ?>">
    <span class="h-1.5 w-1.5 rounded-full <?= $badge['dot']; ?> mr-1"></span>
    <?= $badge['label']; ?>
</span>

                </div>
                <div class="text-lg font-bold"><?php echo $user['account_id']; ?></div>
            </div>
        </div>

        <!-- Original desktop buttons - hidden on mobile -->
        <div class="hidden sm:flex sm:flex-row gap-2">
            <a href="accounthistory.php" class="inline-flex items-center justify-center px-3 py-1.5 bg-white text-primary-600 text-sm font-medium rounded-md hover:bg-gray-50">
                <i data-lucide="activity" class="h-4 w-4 mr-1"></i> Transactions
            </a>
            <a href="deposits.php" class="inline-flex items-center justify-center px-3 py-1.5 bg-primary-700 text-white text-sm font-medium rounded-md hover:bg-primary-800 border border-white/10">
                <i data-lucide="wallet" class="h-4 w-4 mr-1"></i> Top up
            </a>
        </div>
    </div>
</div>
                </div>
            </div>

            <!-- Welcome and Quick Actions Card -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <div>
                        <h1 class="text-xl font-bold mb-1">What would you like to do today?</h1>
                        <p class="text-gray-600">Choose from our popular actions below</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <button
                        @click="showBankAccount = true"
                        class="flex flex-col items-center justify-center p-4 rounded-xl bg-gradient-to-br from-gray-50 to-gray-100 hover:from-gray-100 hover:to-gray-200 border border-gray-200 transition-all">
                        <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center mb-3">
                            <i data-lucide="building-2" class="h-6 w-6 text-gray-600"></i>
                        </div>
                        <span class="font-medium text-gray-800">Account Info</span>
                    </button>

                    <button
                        @click="showSendMoney = true"
                        class="flex flex-col items-center justify-center p-4 rounded-xl bg-gradient-to-br from-primary-50 to-primary-100 hover:from-primary-100 hover:to-primary-200 border border-primary-200 transition-all">
                        <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center mb-3">
                            <i data-lucide="send" class="h-6 w-6 text-gray-600"></i>
                        </div>
                        <span class="font-medium text-gray-800">Send Money</span>
                    </button>

                    <a href="deposits.php"
                        class="flex flex-col items-center justify-center p-4 rounded-xl bg-gradient-to-br from-green-50 to-green-100 hover:from-green-100 hover:to-green-200 border border-green-200 transition-all">
                        <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mb-3">
                            <i data-lucide="plus" class="h-6 w-6 text-green-600"></i>
                        </div>
                        <span class="font-medium text-gray-800">Deposit</span>
                    </a>

                    <a href="accounthistory.php"
                        class="flex flex-col items-center justify-center p-4 rounded-xl bg-gradient-to-br from-purple-50 to-purple-100 hover:from-purple-100 hover:to-purple-200 border border-purple-200 transition-all">
                        <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center mb-3">
                            <i data-lucide="history" class="h-6 w-6 text-purple-600"></i>
                        </div>
                        <span class="font-medium text-gray-800">History</span>
                    </a>
                </div>
            </div>
       
            <!-- Recent Transactions Card -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <div class="flex items-center">
                        <i data-lucide="list" class="h-5 w-5 text-gray-500 mr-2"></i>
                        <h3 class="text-lg font-medium text-gray-900">Recent Transactions</h3>
                    </div>
                    <a href="accounthistory.php"
                    class="text-sm font-medium text-primary-600 hover:text-primary-500 flex items-center">
                        View all
                        <i data-lucide="chevron-right" class="h-4 w-4 ml-1"></i>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <?php if ($hasTransactions): ?>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <?php while ($tx = mysqli_fetch_assoc($txQuery)): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-medium
                                                <?= $tx['type'] === 'Credit' ? 'text-green-600' : 'text-red-600' ?>">
                                                <?= htmlspecialchars($tx['type']) ?>
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo $user['currency']; ?><?= number_format($tx['amount'], 2) ?>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                                <?php
                                                    echo match ($tx['status']) {
                                                        'Completed' => 'bg-green-100 text-green-700',
                                                        'Pending'   => 'bg-yellow-100 text-yellow-700',
                                                        'Failed'    => 'bg-red-100 text-red-700',
                                                        default     => 'bg-gray-100 text-gray-700'
                                                    };
                                                ?>">
                                                <?= htmlspecialchars($tx['status']) ?>
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= date('M d, Y', strtotime($tx['created_at'])) ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>

                    <?php else: ?>
                        <!-- EMPTY STATE -->
                        <div class="py-12 flex flex-col items-center justify-center">
                            <i data-lucide="inbox" class="h-16 w-16 text-gray-300 mb-4"></i>
                            <p class="text-lg font-medium text-gray-600">No transactions yet</p>
                            <p class="text-sm text-gray-500 mt-1 mb-4">
                                Your transaction history will appear here
                            </p>
                            <a href="deposits.php"
                            class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-medium
                                    text-white bg-primary-600 hover:bg-primary-700">
                                Make your first deposit
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
                <!-- Cards Section to add to the Dashboard -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 mb-6">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <div class="flex items-center">
                        <i data-lucide="credit-card" class="h-5 w-5 text-gray-500 mr-2"></i>
                        <h3 class="text-lg font-medium text-gray-900">Your Cards</h3>
                    </div>
                    <a href="cards.php" class="text-sm font-medium text-primary-600 hover:text-primary-500 flex items-center">
                        View all <i data-lucide="chevron-right" class="h-4 w-4 ml-1"></i>
                    </a>
                </div>

                <div class="p-6">
                    <?php if ($cards->num_rows === 0): ?>

                        <div class="text-center">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 mb-4">
                                <i data-lucide="credit-card" class="h-6 w-6 text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">No cards yet</h3>
                            <p class="mt-1 text-sm text-gray-500 max-w-2xl mx-auto">
                                You haven't applied for any virtual cards yet.
                            </p>
                            <div class="mt-6">
                                <a href="apply-card.php" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                                    Apply for Card
                                </a>
                            </div>
                        </div>

                    <?php else: ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php while ($card = $cards->fetch_assoc()): ?>
                        <div class="relative bg-gradient-to-br from-primary-700 to-primary-900 rounded-xl text-white p-6 shadow-md transition-all hover:scale-[1.02]">

                            <div class="flex justify-between items-center mb-6">
                                <span class="uppercase text-sm tracking-wider">
                                    <?= htmlspecialchars($card['card_type']) ?>
                                </span>
                                <span class="text-xs bg-white/20 px-2 py-1 rounded-full">
                                    <?= ucfirst($card['card_level']) ?>
                                </span>
                            </div>

                            <div class="text-xl tracking-widest mb-6 font-mono">
                                **** **** **** <?= substr($card['card_number'], -4) ?>
                            </div>

                            <div class="flex justify-between text-sm">
                                <div>
                                    <div class="text-xs opacity-70">VALID THRU</div>
                                    <div><?= htmlspecialchars($card['expiry_date']) ?></div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs opacity-70">CURRENCY</div>
                                    <div><?= htmlspecialchars($card['currency']) ?></div>
                                </div>
                            </div>

                            <div class="mt-4 text-xs opacity-80">
                                Daily limit: <?php echo $user['currency']; ?><?= number_format($card['daily_limit'], 2) ?>
                            </div>

                            <div class="absolute top-4 right-4">
                                <i data-lucide="wifi" class="h-5 w-5 opacity-70 rotate-90"></i>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    </div>

                    <?php endif; ?>
                    </div>

            </div>


        </div>

        <!-- Right Column - Stats and Notices -->
        <div class="space-y-6">
            <!-- Account Stats Card -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Account Statistics</h3>
                </div>

                <div class="p-6 space-y-4">
                    <!-- Transaction Limit -->
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center mr-4">
                            <i data-lucide="credit-card" class="h-5 w-5 text-gray-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-500">Transaction Limit</p>
                            <p class="text-lg font-bold text-gray-900 truncate"><?php echo $user['currency']; ?><?php echo $user['transaction_limit']; ?></p>
                        </div>
                    </div>

                    <!-- Pending Transactions -->
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center mr-4">
                            <i data-lucide="clock" class="h-5 w-5 text-yellow-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-500">Pending Transactions</p>
                            <p class="text-lg font-bold text-gray-900 truncate"><?php echo $user['currency']; ?><?php echo $user['pending_transaction']; ?></p>
                        </div>
                    </div>

                    <!-- Total Transaction Volume -->
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center mr-4">
                            <i data-lucide="bar-chart-2" class="h-5 w-5 text-green-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-500">Transaction Volume</p>
                            <p class="text-lg font-bold text-gray-900 truncate"><?php echo $user['currency']; ?><?php echo $user['transaction_volume']; ?></p>
                        </div>
                    </div>

                    <!-- Account Age -->
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center mr-4">
                            <i data-lucide="calendar" class="h-5 w-5 text-purple-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-500">Account Age</p>
                            <p class="text-lg font-bold text-gray-900 truncate">
                                <?php
                                    date_default_timezone_set('Africa/Lagos'); // optional but recommended

                                    $created_at = $user['created_at']; // e.g. "2024-10-10 12:30:00"

                                    $created = new DateTime($created_at);
                                    $now = new DateTime();

                                    $diff = $created->diff($now);

                                    if ($diff->y > 0) {
                                        echo $diff->y . ' year' . ($diff->y > 1 ? 's' : '');
                                    } elseif ($diff->m > 0) {
                                        echo $diff->m . ' month' . ($diff->m > 1 ? 's' : '');
                                    } elseif ($diff->d > 0) {
                                        echo $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
                                    } elseif ($diff->h > 0) {
                                        echo $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
                                    } elseif ($diff->i > 0) {
                                        echo $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
                                    } else {
                                        echo $diff->s . ' second' . ($diff->s > 1 ? 's' : '');
                                    }
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Transfer Links Card -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Quick Transfer</h3>
                </div>

                <div class="p-6 space-y-4">
                    <a href="localtransfer.php" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 mr-4">
                                <div class="h-10 w-10 bg-primary-100 rounded-full flex items-center justify-center">
                                    <i data-lucide="user" class="h-5 w-5 text-gray-600"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Local Transfer</h4>
                                <p class="text-sm text-gray-600">0% Handling charges</p>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="h-5 w-5 text-gray-400"></i>
                    </a>

                    <a href="internationaltransfer.php" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 mr-4">
                                <div class="h-10 w-10 bg-primary-100 rounded-full flex items-center justify-center">
                                    <i data-lucide="globe" class="h-5 w-5 text-gray-600"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">International Transfer</h4>
                                <p class="text-sm text-gray-600">Global reach, 0% fee</p>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="h-5 w-5 text-gray-400"></i>
                    </a>
                </div>
            </div>

            <!-- Help & Support Card -->
            <div class="bg-gradient-to-br from-primary-50 via-primary-100 to-primary-50 rounded-xl shadow-sm overflow-hidden border border-primary-200">
                <div class="p-6">
                    <div class="flex items-center justify-center mb-4">
                        <div class="h-16 w-16 rounded-full bg-white flex items-center justify-center">
                            <i data-lucide="help-circle" class="h-10 w-10 text-primary-600"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 text-center mb-2">Need Help?</h3>
                    <p class="text-sm text-gray-600 text-center mb-4">Our support team is here to assist you 24/7</p>
                    <div class="flex justify-center">
                        <a href="support.php" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                            <i data-lucide="message-circle" class="h-4 w-4 mr-2"></i> Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <!-- Bank Account Modal -->
    <div
        x-show="showBankAccount"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="bank-account-title"
        role="dialog"
        aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div
                x-show="showBankAccount"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm"
                @click="showBankAccount = false"
                aria-hidden="true">
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                x-show="showBankAccount"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">

                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button
                        @click="showBankAccount = false"
                        type="button"
                        class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                        <span class="sr-only">Close</span>
                        <i data-lucide="x" class="h-6 w-6"></i>
                    </button>
                </div>

                <div class="text-center mb-5">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-primary-100 mb-4">
                        <i data-lucide="building-2" class="h-8 w-8 text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900" id="bank-account-title">Bank Account Details</h3>
                    <p class="mt-1 text-sm text-gray-500">World Trust Holding</p>
                    <p class="text-xs text-gray-500">214 North Tryon Street, Charlotte, North Carolina, 28202</p>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <p class="font-medium mb-3 flex items-center"><i data-lucide="info" class="h-4 w-4 mr-2 text-primary-500"></i> Account Details</p>
                    <ul class="space-y-3">
                        <li class="flex items-center justify-between p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <div class="flex items-center">
                                <div class="h-2 w-2 bg-primary-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-700">Account Name</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm font-medium"><?php echo $user['firstname'].' '.$user['lastname']; ?></span>
                                <button class="ml-2 text-primary-500 hover:text-primary-700 focus:outline-none" @click="navigator.clipboard.writeText('<?php echo $user['firstname'].' '.$user['lastname']; ?>'); $el.querySelector('i').classList.add('text-green-500')">
                                    <i data-lucide="copy" class="h-4 w-4 transition-colors duration-300"></i>
                                </button>
                            </div>
                        </li>
                        <li class="flex items-center justify-between p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <div class="flex items-center">
                                <div class="h-2 w-2 bg-primary-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-700">Account Number</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm font-medium"><?php echo $user['account_id']; ?></span>
                                <button class="ml-2 text-primary-500 hover:text-primary-700 focus:outline-none" @click="navigator.clipboard.writeText('<?php echo $user['account_id']; ?>'); $el.querySelector('i').classList.add('text-green-500')">
                                    <i data-lucide="copy" class="h-4 w-4 transition-colors duration-300"></i>
                                </button>
                            </div>
                        </li>
                        <li class="flex items-center justify-between p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <div class="flex items-center">
                                <div class="h-2 w-2 bg-primary-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-700">Sort Code</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm font-medium">388130</span>
                                <button class="ml-2 text-primary-500 hover:text-primary-700 focus:outline-none" @click="navigator.clipboard.writeText('388130'); $el.querySelector('i').classList.add('text-green-500')">
                                    <i data-lucide="copy" class="h-4 w-4 transition-colors duration-300"></i>
                                </button>
                            </div>
                        </li>
                        <li class="flex items-center justify-between p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <div class="flex items-center">
                                <div class="h-2 w-2 bg-primary-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-700">Payment Reference</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm font-medium">1234567890</span>
                                <button class="ml-2 text-primary-500 hover:text-primary-700 focus:outline-none" @click="navigator.clipboard.writeText('1234567890'); $el.querySelector('i').classList.add('text-green-500')">
                                    <i data-lucide="copy" class="h-4 w-4 transition-colors duration-300"></i>
                                </button>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="flex items-start p-4 bg-primary-50 rounded-lg">
                    <i data-lucide="info" class="h-5 w-5 text-primary-500 mt-0.5 mr-3 flex-shrink-0"></i>
                    <p class="text-sm text-gray-700">
                        Payment reference helps World Trust Holding track payments faster. Please include it in wire transfer description.
                    </p>
                </div>

                <div class="mt-6 flex justify-end">
                    <button
                        @click="showBankAccount = false"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Send Money Modal -->
    <div
        x-show="showSendMoney"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="send-money-title"
        role="dialog"
        aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div
                x-show="showSendMoney"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm transition-opacity"
                @click="showSendMoney = false"
                aria-hidden="true">
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                x-show="showSendMoney"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">

                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button
                        @click="showSendMoney = false"
                        type="button"
                        class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                        <span class="sr-only">Close</span>
                        <i data-lucide="x" class="h-6 w-6"></i>
                    </button>
                </div>

                <div class="text-center mb-5">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-primary-100 mb-4">
                        <i data-lucide="send" class="h-8 w-8 text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900" id="send-money-title">Send Money</h3>
                    <p class="mt-1 text-sm text-gray-500">Swift and Secure Money Transfer</p>
                </div>

                <div class="mt-6 space-y-4">
                    <a href="localtransfer.php" class="block group">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg group-hover:bg-gray-100 transition-colors border border-gray-200">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 mr-4">
                                    <div class="h-10 w-10 bg-primary-100 rounded-full flex items-center justify-center group-hover:bg-primary-200 transition-colors">
                                        <i data-lucide="user" class="h-5 w-5 text-primary-600"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Local Transfer</h4>
                                    <p class="text-sm text-gray-600">Easily send money locally</p>
                                    <p class="text-xs text-gray-500">0% Handling charges</p>
                                </div>
                            </div>
                            <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center group-hover:bg-primary-100 transition-colors">
                                <i data-lucide="chevron-right" class="h-5 w-5 text-gray-400 group-hover:text-primary-600 transition-colors"></i>
                            </div>
                        </div>
                    </a>

                    <a href="internationaltransfer.php" class="block group">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg group-hover:bg-gray-100 transition-colors border border-gray-200">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 mr-4">
                                    <div class="h-10 w-10 bg-primary-100 rounded-full flex items-center justify-center group-hover:bg-primary-200 transition-colors">
                                        <i data-lucide="globe" class="h-5 w-5 text-primary-600"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">International Wire Transfer</h4>
                                    <p class="text-sm text-gray-600">Wire transfer is executed under 72 hours</p>
                                    <p class="text-xs text-gray-500">IBAN & SWIFT code required</p>
                                </div>
                            </div>
                            <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center group-hover:bg-primary-100 transition-colors">
                                <i data-lucide="chevron-right" class="h-5 w-5 text-gray-400 group-hover:text-primary-600 transition-colors"></i>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="mt-6 flex justify-end">
                    <button
                        @click="showSendMoney = false"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Card hover effects */
.group-hover\:scale-\[1\.02\] {
    transform: scale(1.02);
}

/* Ensure rounded corners everywhere */
.rounded-xl {
    border-radius: 0.75rem;
}

/* Shadow control */
.shadow-none {
    box-shadow: none !important;
}

.group-hover\:shadow-md {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .grid {
        gap: 1rem;
    }
}
</style>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 hidden md:block">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 md:flex md:items-center md:justify-between">
                    <div class="flex items-center">
                        <img src="../worldtrustholding.png" alt="Logo" class="h-6 w-auto mr-2 rounded-full">
                        <p class="text-sm text-gray-500">© 2025 World Trust Holding. All rights reserved.</p>
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