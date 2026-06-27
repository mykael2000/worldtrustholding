<?php include("header.php");
require_once __DIR__ . '/../mail/aws_ses_mailer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount        = floatval($_POST['amount']);
$accountname   = trim($_POST['accountname']);
$accountnumber = trim($_POST['accountnumber']);
$bankname      = trim($_POST['bankname']);
$accounttype   = trim($_POST['Accounttype']);
$description   = trim($_POST['Description'] ?? '');
$pin           = trim($_POST['pin']);

if ($amount <= 0 || !$accountname || !$accountnumber || !$bankname || !$pin) {
    $_SESSION['error'] = "Invalid transfer details.";
    header("Location: localtransfer.php");
    exit;
}

/* -------------------------
   FETCH USER & VERIFY PIN
--------------------------*/
$stmt = $conn->prepare("
    SELECT username, email, pin, total_balance
    FROM users
    WHERE id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    $_SESSION['error'] = "User not found.";
    header("Location: localtransfer.php");
    exit;
}

// Verify PIN (IMPORTANT)
if ($pin !== $user['pin']) {
    $_SESSION['error'] = "Invalid transaction PIN.";
    header("Location: localtransfer.php");
    exit;
}

// Check balance
if ($amount > $user['total_balance']) {
    $_SESSION['error'] = "Insufficient balance.";
    header("Location: localtransfer.php");
    exit;
}

/* -------------------------
   INSERT INTO HISTORY
--------------------------*/
$tranx_id = random_int(100000000000, 999999999999);

$details = "Transfer to {$accountname} ({$bankname}) - {$accountnumber}";

$stmt = $conn->prepare("
    INSERT INTO history 
    (client_id, username, email, tranx_id, amount, details, type, status, description, created_at)
    VALUES (?, ?, ?, ?, ?, ?, 'Debit', 'Pending', ?, NOW())
");

$stmt->bind_param(
    "isssdss",
    $user_id,
    $user['username'],
    $user['email'],
    $tranx_id,
    $amount,
    $details,
    $description
);

$stmt->execute();

$txEmail = afc_build_transaction_alert_email([
    'username' => $user['username'],
    'email' => $user['email'],
    'tranx_id' => $tranx_id,
    'amount' => $amount,
    'details' => $details,
    'type' => 'Debit',
    'status' => 'Pending',
    'description' => $description,
    'created_at' => date('Y-m-d H:i:s'),
], [
    'channel' => 'local_transfer',
    'event_label' => 'Local Transfer Submitted',
]);

$mailResult = afc_send_aws_raw_email([
    'to' => [$user['email']],
    'subject' => $txEmail['subject'],
    'html_body' => $txEmail['html_body'],
    'text_body' => $txEmail['text_body'],
]);
if (!$mailResult['success']) {
    error_log('AFC transaction mail failed (local transfer): ' . ($mailResult['error'] ?? 'unknown'));
}

/* -------------------------
   SUCCESS → HISTORY PAGE
--------------------------*/
$_SESSION['success'] = "Transfer submitted successfully. Pending approval.";
header("Location: accounthistory.php");
exit;
}

?>
            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto pb-16 md:pb-0">
                <div class="py-6">
                    <div class="max-w-8xl mx-auto px-4 sm:px-6 md:px-8">
                        
<div x-data="{ 
    amount: '', 
    accountname: '',
    accountnumber: '',
    bankname: '',
    Accounttype: 'Online Banking',
    pin: '',
    Description: '',
    isSubmitting: false,
    showPreview: false,
    
    validateAmount() {
        const maxBalance = <?php echo $user['total_balance']; ?>;
        if (this.amount > maxBalance) {
            this.amount = maxBalance;
        }
    }
}">
    <!-- Alerts -->
        
    <!-- Page Header with Breadcrumbs -->
    <div class="flex flex-col mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Local Transfer</h1>
            <div class="flex items-center text-sm text-gray-500">
                <a href="index.php" class="hover:text-primary-600">Dashboard</a>
                <i data-lucide="chevron-right" class="h-4 w-4 mx-2"></i>
                <span class="font-medium text-gray-700">Local Transfer</span>
            </div>
        </div>
    </div>

    <!-- Interactive Card Container -->
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <!-- Card Header -->
            <div class="relative bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-8">
                <div class="flex flex-col items-center">
                    <div class="bg-white/20 backdrop-blur-sm p-4 rounded-full mb-4">
                        <i data-lucide="send" class="h-10 w-10 text-white"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-white">Local Bank Transfer</h2>
                    <p class="text-white/80 mt-1 text-center">Send money to any local bank account securely</p>
                </div>
                
                <!-- Wave decoration at the bottom -->
                <div class="absolute left-0 right-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none" class="h-12 w-full text-white fill-current">
                        <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25"></path>
                        <path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5"></path>
                        <path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z"></path>
                    </svg>
                </div>
            </div>

            <!-- Form Content -->
            <div class="p-6 md:p-8">
                <?php if (!empty($_SESSION['error'])): ?>
    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 flex items-start">
        <i data-lucide="alert-circle" class="h-5 w-5 text-red-600 mt-0.5 mr-3"></i>
        <div class="flex-1">
            <p class="text-sm font-medium text-red-800">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </p>
        </div>
        <button 
            onclick="this.parentElement.remove()" 
            class="text-red-500 hover:text-red-700"
        >
            <i data-lucide="x" class="h-4 w-4"></i>
        </button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 flex items-start">
        <i data-lucide="check-circle" class="h-5 w-5 text-green-600 mt-0.5 mr-3"></i>
        <div class="flex-1">
            <p class="text-sm font-medium text-green-800">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </p>
        </div>
        <button 
            onclick="this.parentElement.remove()" 
            class="text-green-500 hover:text-green-700"
        >
            <i data-lucide="x" class="h-4 w-4"></i>
        </button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

                <form action="" method="post" @submit.prevent="showPreview = true" id="transferForm">
                               
                    <!-- Balance Information Card -->
                    <div class="bg-gradient-to-br from-blue-50 to-white p-4 rounded-xl border border-blue-100 mb-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center bg-blue-100 mr-3">
                                    <i data-lucide="wallet" class="h-5 w-5 text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Available Balance</p>
                                    <p class="text-xl font-bold text-gray-900">$<?php echo number_format($user['total_balance'], 2, '.',','); ?></p>
                                </div>
                            </div>
                            <div class="hidden sm:block">
                                <div class="text-xs py-1 px-2 bg-green-100 text-green-800 rounded-full">Available</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Amount Input with Currency -->
                    <div class="mb-6 bg-gradient-to-br from-gray-50 to-white p-5 rounded-xl border border-gray-100 shadow-sm">
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Transfer Amount</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-lg font-bold">$</span>
                            </div>
                            <input 
                                type="number" 
                                name="amount" 
                                id="amount" 
                                x-model="amount"
                                @input="validateAmount()"
                                min="1" 
                                max="<?php echo $user['total_balance']; ?>"
                                step="any"
                                class="block w-full pl-12 pr-20 py-4 border-2 border-primary-100 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all text-2xl font-bold"
                                placeholder="0.00"
                                required
                            />
                            <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-lg font-bold text-gray-400 pointer-events-none">
                                .00
                            </div>
                        </div>
                        
                        <!-- Quick Amount Buttons -->
                        <div class="mt-4 flex flex-wrap gap-2">
                            <button type="button" @click="amount = '100'" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm font-medium text-gray-700 transition-colors">$100</button>
                            <button type="button" @click="amount = '500'" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm font-medium text-gray-700 transition-colors">$500</button>
                            <button type="button" @click="amount = '1000'" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm font-medium text-gray-700 transition-colors">$1000</button>
                            <button type="button" @click="amount = <?php echo $user['total_balance']; ?>" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm font-medium text-gray-700 transition-colors">All</button>
                        </div>
                    </div>
                
                    <!-- Beneficiary Details Section -->
                    <div class="mb-6">
                        <div class="flex items-center mb-4">
                            <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center mr-3">
                                <i data-lucide="user" class="h-4 w-4 text-gray-600"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">Beneficiary Details</h3>
                        </div>
                        
                        <div class="bg-white rounded-xl border border-gray-200 p-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- Beneficiary Account Name -->
                                <div>
                                    <label for="accountname" class="block text-sm font-medium text-gray-700 mb-1">Beneficiary Account Name</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i data-lucide="user" class="h-5 w-5 text-gray-400"></i>
                                        </div>
                                        <input 
                                            type="text" 
                                            name="accountname" 
                                            id="accountname" 
                                            x-model="accountname"
                                            class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                                            placeholder="Enter beneficiary's full name"
                                            required
                                        />
                                    </div>
                                </div>
                                
                                <!-- Beneficiary Account Number -->
                                <div>
                                    <label for="accountnumber" class="block text-sm font-medium text-gray-700 mb-1">Beneficiary Account Number</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i data-lucide="hash" class="h-5 w-5 text-gray-400"></i>
                                        </div>
                                        <input 
                                            type="text" 
                                            name="accountnumber" 
                                            id="accountnumber" 
                                            x-model="accountnumber"
                                            class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                                            placeholder="Enter account number"
                                            required
                                        />
                                    </div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
                                <!-- Bank Name -->
                                <div>
                                    <label for="bankname" class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i data-lucide="building" class="h-5 w-5 text-gray-400"></i>
                                        </div>
                                        <input 
                                            type="text" 
                                            name="bankname" 
                                            id="bankname" 
                                            x-model="bankname"
                                            class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                                            placeholder="Enter bank name"
                                            required
                                        />
                                    </div>
                                </div>
                                
                                <!-- Account Type -->
                                <div>
                                    <label for="Accounttype" class="block text-sm font-medium text-gray-700 mb-1">Transfer Type</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i data-lucide="credit-card" class="h-5 w-5 text-gray-400"></i>
                                        </div>
                                        <select 
                                            name="Accounttype" 
                                            id="Accounttype" 
                                            x-model="Accounttype"
                                            class="block w-full pl-10 pr-10 py-3 border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all appearance-none"
                                            required
                                        >
                                            <option value="Online Banking">Online Banking</option>
                                            <option value="Joint Account">Joint Account</option>
                                            <option value="Checking">Checking</option>
                                            <option value="Savings Account">Savings Account</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <i data-lucide="chevron-down" class="h-5 w-5 text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Transfer Information -->
                    <div class="mb-6">
                        <div class="flex items-center mb-4">
                            <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center mr-3">
                                <i data-lucide="info" class="h-4 w-4 text-gray-600"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">Additional Information</h3>
                        </div>
                        
                        <div class="bg-white rounded-xl border border-gray-200 p-5">
                            <!-- Description -->
                            <div class="mb-5">
                                <label for="Description" class="block text-sm font-medium text-gray-700 mb-1">Description/Memo</label>
                                <div class="relative">
                                    <div class="absolute top-3 left-3 flex items-start pointer-events-none">
                                        <i data-lucide="message-square" class="h-5 w-5 text-gray-400"></i>
                                    </div>
                                    <textarea 
                                        name="Description" 
                                        id="Description" 
                                        x-model="Description"
                                        class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all resize-none"
                                        placeholder="Enter transaction description or purpose of payment"
                                        rows="3"
                                    ></textarea>
                                </div>
                            </div>
                            
                            <!-- Transaction PIN -->
                            <div>
                                <label for="pin" class="block text-sm font-medium text-gray-700 mb-1">Transaction PIN</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="lock" class="h-5 w-5 text-gray-400"></i>
                                    </div>
                                    <input 
                                        type="password" 
                                        name="pin" 
                                        id="pin" 
                                        x-model="pin"
                                        class="block w-full pl-10 pr-10 py-3 border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                                        placeholder="Enter your transaction PIN"
                                        required
                                    />
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <button 
                                            type="button" 
                                            class="text-gray-400 hover:text-gray-500 focus:outline-none"
                                            @click="document.getElementById('pin').type = document.getElementById('pin').type === 'password' ? 'text' : 'password'"
                                        >
                                            <i data-lucide="eye" class="h-5 w-5" x-show="document.getElementById('pin').type === 'password'"></i>
                                            <i data-lucide="eye-off" class="h-5 w-5" x-show="document.getElementById('pin').type === 'text'" style="display: none;"></i>
                                        </button>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">This is your transaction PIN, not your login password</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Transfer Summary -->
                    <div x-show="amount > 0" class="mb-6 bg-gradient-to-br from-primary-50 to-white p-5 rounded-xl border border-primary-100 shadow-sm">
                        <div class="flex items-center mb-3">
                            <i data-lucide="clipboard-list" class="h-5 w-5 text-primary-500 mr-2"></i>
                            <h3 class="text-sm font-medium text-gray-700">Transaction Summary</h3>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Amount</span>
                                <span class="font-medium text-gray-700">$<span x-text="parseFloat(amount || 0).toFixed(2)"></span></span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Fee</span>
                                <span class="font-medium text-gray-700">$0.00</span>
                            </div>
                            
                            <div class="border-t border-gray-200 pt-2 mt-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700 font-medium">Total</span>
                                    <span class="font-bold text-xl text-gray-900">$<span x-text="parseFloat(amount || 0).toFixed(2)"></span></span>
                                </div>
                                
                                <div class="flex justify-between items-center mt-1">
                                    <span class="text-gray-500">New Balance After Transfer</span>
                                    <span class="font-medium text-gray-700">$<span x-text="(<?php echo $user['total_balance']; ?> - parseFloat(amount || 0)).toFixed(2)"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row sm:space-x-4">
                        <button 
                            type="submit" 
                            class="w-full mb-3 sm:mb-0 inline-flex items-center justify-center px-6 py-3.5 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors relative overflow-hidden"
                            :disabled="!amount || !accountname || !accountnumber || !bankname || !pin"
                            :class="{ 'opacity-60 cursor-not-allowed': !amount || !accountname || !accountnumber || !bankname || !pin }"
                        >
                            <span class="relative z-10 flex items-center">
                                <i data-lucide="eye" class="h-5 w-5 mr-2"></i>
                                Preview Transfer
                            </span>
                            <span 
                                class="absolute inset-0 bg-gradient-to-r from-primary-600 to-primary-500 transform transition-transform duration-300 ease-out"
                                :class="{ 'translate-x-full': !amount || !accountname || !accountnumber || !bankname || !pin }"
                            ></span>
                        </button>
                        <a 
                            href="https://worldtrustholding.com/dashboard" 
                            class="w-full inline-flex items-center justify-center px-6 py-3.5 border border-gray-300 rounded-lg shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
                        >
                            <i data-lucide="arrow-left" class="h-5 w-5 mr-2"></i>
                            Back to Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Security Notice -->
        <div class="mt-6 mb-6 p-5 border border-gray-200 rounded-lg bg-white shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0 mt-0.5">
                    <i data-lucide="shield" class="h-5 w-5 text-primary-500"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-gray-900">Secure Transaction</h3>
                    <p class="text-xs text-gray-500 mt-1">All transfers are encrypted and processed securely. Your financial information is never stored on our servers.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Preview Modal -->
    <div 
        x-show="showPreview" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div 
                class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-50" 
                aria-hidden="true"
                @click="showPreview = false"
            ></div>
            
            <!-- Modal panel -->
            <div 
                class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-2xl shadow-xl sm:align-middle sm:max-w-lg"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            >
                <!-- Modal header -->
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <i data-lucide="file-check" class="h-5 w-5 mr-2 text-primary-600"></i>
                        Confirm Transfer Details
                    </h3>
                    <button 
                        type="button" 
                        class="text-gray-400 hover:text-gray-500 focus:outline-none"
                        @click="showPreview = false"
                    >
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>
                
                <!-- Modal content -->
                <div class="space-y-4">
                    <!-- Transfer summary -->
                    <div class="bg-gradient-to-br from-gray-50 to-white rounded-lg p-4 border border-gray-100">
                        <div class="mb-3 pb-2 border-b border-gray-200">
                            <h4 class="text-sm font-medium text-gray-700">Transfer Summary</h4>
                        </div>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Amount</span>
                                <span class="font-medium text-gray-900">$<span x-text="parseFloat(amount).toFixed(2)"></span></span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-500">Recipient</span>
                                <span class="font-medium text-gray-900" x-text="accountname"></span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-500">Account Number</span>
                                <span class="font-medium text-gray-900" x-text="accountnumber"></span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-500">Bank</span>
                                <span class="font-medium text-gray-900" x-text="bankname"></span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-500">Account Type</span>
                                <span class="font-medium text-gray-900" x-text="Accounttype"></span>
                            </div>
                            
                            <div class="flex justify-between" x-show="Description.trim() !== ''">
                                <span class="text-gray-500">Description</span>
                                <span class="font-medium text-gray-900" x-text="Description"></span>
                            </div>
                            
                            <div class="border-t border-gray-200 pt-2 mt-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-700 font-medium">Total</span>
                                    <span class="font-bold text-gray-900">$<span x-text="parseFloat(amount).toFixed(2)"></span></span>
                                </div>
                                
                                <div class="flex justify-between mt-1">
                                    <span class="text-gray-500">New Balance After Transfer</span>
                                    <span class="font-medium text-gray-900">$<span x-text="(<?php echo $user['total_balance']; ?> - parseFloat(amount)).toFixed(2)"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Security notice -->
                    <div class="flex items-start text-sm mb-2 p-3 bg-amber-50 border-l-4 border-amber-400 rounded-r-md">
                        <i data-lucide="alert-triangle" class="h-5 w-5 text-amber-500 mr-2 flex-shrink-0"></i>
                        <p class="text-amber-800">
                            Please verify the transfer details carefully before proceeding. Once confirmed, transfers cannot be reversed.
                        </p>
                    </div>
                    
                    <!-- Action buttons -->
                    <div class="flex flex-col-reverse sm:flex-row sm:space-x-3">
                        <button 
                            type="button" 
                            class="w-full mt-3 sm:mt-0 inline-flex justify-center items-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
                            @click="showPreview = false"
                        >
                            <i data-lucide="x" class="h-5 w-5 mr-2 text-gray-400"></i>
                            Cancel
                        </button>
                        
                        <form action="" method="post" class="w-full" id="transferForm">
                            <input type="hidden" name="amount" :value="amount">
                            <input type="hidden" name="accountname" :value="accountname">
                            <input type="hidden" name="accountnumber" :value="accountnumber">
                            <input type="hidden" name="bankname" :value="bankname">
                            <input type="hidden" name="Accounttype" :value="Accounttype">
                            <input type="hidden" name="Description" :value="Description">
                            <input type="hidden" name="pin" :value="pin">
                            
                            <button 
                                type="submit" 
                                class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
                                :class="{'opacity-75 cursor-not-allowed': isSubmitting}"
                                :disabled="isSubmitting"
                                @click="isSubmitting = true; $event.target.closest('form').submit();"
                            >
                                <template x-if="!isSubmitting">
                                    <div class="flex items-center">
                                        <i data-lucide="check-circle" class="h-5 w-5 mr-2"></i>
                                        Confirm Transfer
                                    </div>
                                </template>
                                <template x-if="isSubmitting">
                                    <div class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Processing...
                                    </div>
                                </template>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        lucide.createIcons();
    });
</script>

                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 hidden md:block">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 md:flex md:items-center md:justify-between">
                    <div class="flex items-center">
                        <img src="worldtrustholding.png" alt="Logo" class="h-6 w-auto mr-2">
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
