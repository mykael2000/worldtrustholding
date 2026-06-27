<?php include("header.php");

$hasActiveLoan = false;

$stmt = $conn->prepare("
    SELECT id 
    FROM loans 
    WHERE user_id = ? 
    AND status IN ('Pending','Approved')
    LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $hasActiveLoan = true;
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($hasActiveLoan) {
        $_SESSION['error'] = "You already have an active or pending loan.";
        header("Location: loan.php");
        exit;
    }

    $amount   = floatval($_POST['amount'] ?? 0);
    $duration = intval($_POST['duration'] ?? 0);
    $facility = trim($_POST['facility'] ?? '');
    $purpose  = trim($_POST['purpose'] ?? '');
    $income   = trim($_POST['income'] ?? '');

    if ($amount <= 0 || !$duration || !$facility || !$purpose || !$income) {
        $_SESSION['error'] = "Please complete all required fields.";
        header("Location: loan.php");
        exit;
    }

    $loan_id = 'LN' . strtoupper(bin2hex(random_bytes(5)));

    $stmt = $conn->prepare("
        INSERT INTO loans
        (user_id, username, email, loan_id, amount, duration, facility, purpose, income, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')
    ");

    $stmt->bind_param(
        "isssdisss",
        $user_id,
        $user['username'],
        $user['email'],
        $loan_id,
        $amount,
        $duration,
        $facility,
        $purpose,
        $income
    );

    if ($stmt->execute()) {
        $_SESSION['success'] = "Loan application submitted successfully. Pending review.";
    } else {
        $_SESSION['error'] = "Unable to submit loan application. Please try again.";
    }

    $stmt->close();
    header("Location: loan.php");
    exit;
}


?>
            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto pb-16 md:pb-0">
                <div class="py-6">
                    <div class="max-w-8xl mx-auto px-4 sm:px-6 md:px-8">
                            
<div x-data="{ 
    showApplicationForm: false,
    amount: '',
    facility: '', 
    duration: '12',
    purpose: '',
    income: '',
    isSubmitting: false,
    hasActiveLoan: <?= $hasActiveLoan ? 'true' : 'false' ?>
}">
    <!-- Alerts -->
            <div>
    </div>
    <!-- Page Header with Breadcrumbs -->
    <div class="flex flex-col mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Loan Services</h1>
            <div class="flex items-center text-sm text-gray-500">
                <a href="index.php" class="hover:text-primary-600">Dashboard</a>
                <i data-lucide="chevron-right" class="h-4 w-4 mx-2"></i>
                <span class="font-medium text-gray-700">Loan Services</span>
            </div>
        </div>
    </div>

    <!-- Active Loan Alert -->
    <div x-show="hasActiveLoan" class="max-w-4xl mx-auto mb-8">
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="alert-circle" class="h-5 w-5 text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Loan Application Restricted</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>You currently have an active or pending loan application. You cannot apply for a new loan until your current loan is completed or your application is processed.</p>
                        <p class="mt-2">Please check your <a href="viewloan.php" class="font-medium underline text-yellow-800 hover:text-yellow-900">existing loans</a> for more information.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Information Section (Displayed Initially) -->
    <div x-show="!showApplicationForm" class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <!-- Card Header -->
            <div class="relative bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-8">
                <div class="flex flex-col items-center">
                    <div class="bg-white/20 backdrop-blur-sm p-4 rounded-full mb-4">
                        <i data-lucide="banknote" class="h-10 w-10 text-white"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-white">Loan Services</h2>
                    <p class="text-white/80 mt-1 text-center">Financial solutions to help you achieve your goals</p>
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
            <?php if (!empty($_SESSION['error'])): ?>
                <div class="max-w-4xl mx-auto mb-6 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['success'])): ?>
                <div class="max-w-4xl mx-auto mb-6 bg-green-50 border border-green-200 text-green-800 p-4 rounded-lg">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <!-- Loan Information Content -->
            <div class="p-6 md:p-8">
                <!-- Loan Benefits -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center mr-3">
                            <i data-lucide="check-circle" class="h-4 w-4 text-gray-600"></i>
                        </div>
                        Why Choose Our Loan Services
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4 flex items-start">
                            <div class="mr-3 mt-1">
                                <i data-lucide="clock" class="h-5 w-5 text-primary-500"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-1">Quick Approval</h4>
                                <p class="text-sm text-gray-600">Get a decision within hours and funds within days</p>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4 flex items-start">
                            <div class="mr-3 mt-1">
                                <i data-lucide="percent" class="h-5 w-5 text-primary-500"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-1">Competitive Rates</h4>
                                <p class="text-sm text-gray-600">Low interest rates tailored to your credit profile</p>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4 flex items-start">
                            <div class="mr-3 mt-1">
                                <i data-lucide="file-text" class="h-5 w-5 text-primary-500"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-1">Simple Process</h4>
                                <p class="text-sm text-gray-600">Straightforward application with minimal paperwork</p>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4 flex items-start">
                            <div class="mr-3 mt-1">
                                <i data-lucide="shield" class="h-5 w-5 text-primary-500"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-1">Secure & Confidential</h4>
                                <p class="text-sm text-gray-600">Your information is protected with bank-level security</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Loan Types -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center mr-3">
                            <i data-lucide="layers" class="h-4 w-4 text-gray-600"></i>
                        </div>
                        Available Loan Types
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- First row of loan types -->
                        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center mb-2">
                                <i data-lucide="home" class="h-5 w-5 text-primary-500 mr-2"></i>
                                <h4 class="font-medium text-gray-900">Personal Home Loans</h4>
                            </div>
                            <p class="text-sm text-gray-600">Finance your dream home with competitive rates</p>
                        </div>
                        
                        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center mb-2">
                                <i data-lucide="car" class="h-5 w-5 text-primary-500 mr-2"></i>
                                <h4 class="font-medium text-gray-900">Automobile Loans</h4>
                            </div>
                            <p class="text-sm text-gray-600">Get on the road with flexible auto financing</p>
                        </div>
                        
                        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center mb-2">
                                <i data-lucide="briefcase" class="h-5 w-5 text-primary-500 mr-2"></i>
                                <h4 class="font-medium text-gray-900">Business Loans</h4>
                            </div>
                            <p class="text-sm text-gray-600">Grow your business with tailored financing solutions</p>
                        </div>
                        
                        <!-- Second row of loan types -->
                        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center mb-2">
                                <i data-lucide="users" class="h-5 w-5 text-primary-500 mr-2"></i>
                                <h4 class="font-medium text-gray-900">Joint Mortgage</h4>
                            </div>
                            <p class="text-sm text-gray-600">Share responsibility with a co-borrower</p>
                        </div>
                        
                        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center mb-2">
                                <i data-lucide="credit-card" class="h-5 w-5 text-primary-500 mr-2"></i>
                                <h4 class="font-medium text-gray-900">Secured Overdraft</h4>
                            </div>
                            <p class="text-sm text-gray-600">Access funds when needed with asset backing</p>
                        </div>
                        
                        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center mb-2">
                                <i data-lucide="stethoscope" class="h-5 w-5 text-primary-500 mr-2"></i>
                                <h4 class="font-medium text-gray-900">Health Finance</h4>
                            </div>
                            <p class="text-sm text-gray-600">Cover medical expenses with flexible payment options</p>
                        </div>
                    </div>
                    
                    <!-- <div class="mt-4 text-center">
                        <a href="#" class="text-primary-600 hover:text-primary-700 text-sm font-medium inline-flex items-center">
                            View all loan options
                            <i data-lucide="chevron-right" class="h-4 w-4 ml-1"></i>
                        </a>
                    </div> -->
                </div>
                
                <!-- How It Works -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center mr-3">
                            <i data-lucide="info" class="h-4 w-4 text-gray-600"></i>
                        </div>
                        How It Works
                    </h3>
                    
                    <div class="relative">
                        <!-- Line connector -->
                        <div class="absolute left-[1.65rem] top-10 bottom-10 w-0.5 bg-gray-200 z-0"></div>
                        
                        <div class="space-y-6 relative z-10">
                            <!-- Step 1 -->
                            <div class="flex">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center mr-4 z-10">
                                    <span class="text-gray-600 font-bold">1</span>
                                </div>
                                <div class="pt-1">
                                    <h4 class="font-medium text-gray-900 mb-1">Apply Online</h4>
                                    <p class="text-sm text-gray-600">Complete our simple online application form with your details and loan requirements</p>
                                </div>
                            </div>
                            
                            <!-- Step 2 -->
                            <div class="flex">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center mr-4 z-10">
                                    <span class="text-gray-600 font-bold">2</span>
                                </div>
                                <div class="pt-1">
                                    <h4 class="font-medium text-gray-900 mb-1">Quick Review</h4>
                                    <p class="text-sm text-gray-600">Our team reviews your application and may contact you for additional information</p>
                                </div>
                            </div>
                            
                            <!-- Step 3 -->
                            <div class="flex">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center mr-4 z-10">
                                    <span class="text-gray-600 font-bold">3</span>
                                </div>
                                <div class="pt-1">
                                    <h4 class="font-medium text-gray-900 mb-1">Approval & Disbursement</h4>
                                    <p class="text-sm text-gray-600">Once approved, the loan amount will be transferred to your account</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- FAQs Preview -->
                <div class="mb-8 bg-gray-50 rounded-xl p-5 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                        <i data-lucide="help-circle" class="h-5 w-5 text-primary-500 mr-2"></i>
                        Frequently Asked Questions
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="p-3 bg-white rounded-lg border border-gray-100">
                            <h4 class="font-medium text-gray-900 mb-1">What documents do I need to apply?</h4>
                            <p class="text-sm text-gray-600">You'll need identification, proof of income, and address verification. Additional documents may be requested based on loan type.</p>
                        </div>
                        
                        <div class="p-3 bg-white rounded-lg border border-gray-100">
                            <h4 class="font-medium text-gray-900 mb-1">How long does approval take?</h4>
                            <p class="text-sm text-gray-600">Standard applications are typically processed within 1-3 business days, depending on verification requirements.</p>
                        </div>
                        
                        <a href="#" class="text-primary-600 hover:text-primary-700 text-sm font-medium inline-flex items-center mt-2">
                            View all FAQs
                            <i data-lucide="chevron-right" class="h-4 w-4 ml-1"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Apply Now CTA -->
                <div class="bg-gradient-to-r from-primary-50 to-blue-50 rounded-xl p-6 text-center border border-primary-100">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Ready to get started?</h3>
                    <p class="text-sm text-gray-600 mb-4">Apply now and get a decision on your loan application quickly</p>
                    <button 
                        @click="showApplicationForm = true"
                        x-bind:disabled="hasActiveLoan"
                        x-bind:class="{ 'opacity-50 cursor-not-allowed': hasActiveLoan, 'hover:bg-primary-700': !hasActiveLoan }"
                        class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
                    >
                        <template x-if="!hasActiveLoan">
                            <div class="flex items-center">
                                <i data-lucide="file-edit" class="h-5 w-5 mr-2"></i>
                                Apply for a Loan
                            </div>
                        </template>
                        <template x-if="hasActiveLoan">
                            <div class="flex items-center">
                                <i data-lucide="lock" class="h-5 w-5 mr-2"></i>
                                Application Restricted
                            </div>
                        </template>
                    </button>
                    <p x-show="hasActiveLoan" class="mt-3 text-xs text-gray-500">You already have an active or pending loan application</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Form -->
    <div x-show="showApplicationForm && !hasActiveLoan" class="max-w-4xl mx-auto mt-8">
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <!-- Card Header -->
            <div class="relative bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-8">
                <div class="flex flex-col items-center">
                    <div class="bg-white/20 backdrop-blur-sm p-4 rounded-full mb-4">
                        <i data-lucide="file-edit" class="h-10 w-10 text-white"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-white">Loan Application Form</h2>
                    <p class="text-white/80 mt-1 text-center">Complete the form below to apply for your loan</p>
                </div>
                
                <!-- Wave decoration at the bottom -->
                <div class="absolute bottom-0 left-0 right-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none" class="h-12 w-full text-white fill-current">
                        <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25"></path>
                        <path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5"></path>
                        <path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z"></path>
                    </svg>
                </div>
            </div>

            <!-- Form Content -->
            <div class="p-6 md:p-8">
                <div class="flex justify-between items-center mb-6">
                    <button 
                        @click="showApplicationForm = false" 
                        class="inline-flex items-center text-gray-600 hover:text-primary-600 transition-colors"
                    >
                        <i data-lucide="arrow-left" class="h-5 w-5 mr-2"></i>
                        Back to Information
                    </button>
                    
                    <div class="text-sm text-gray-500">
                        <span class="text-red-500">*</span> Required fields
                    </div>
                </div>
                
                <form action="" method="post" x-ref="loanForm" @submit="isSubmitting = true">
                
                    <!-- Loan Details Section -->
                    <div class="mb-8">
                        <div class="flex items-center mb-5">
                            <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center mr-3">
                                <i data-lucide="banknote" class="h-4 w-4 text-primary-600"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">Loan Details</h3>
                        </div>
                    
                        <div class="bg-white rounded-xl border border-gray-200 p-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- Loan Amount -->
                                <div>
                                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                                        Loan Amount (USD) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500">$</span>
                                        </div>
                                        <input 
                                            type="text" 
                                            name="amount" 
                                            id="amount" 
                                            x-model="amount"
                                            class="block w-full pl-10 py-3 border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                                            placeholder="Enter loan amount"
                                            required
                                        />
                                    </div>
                                </div>
                                
                                <!-- Loan Duration -->
                                <div>
                                    <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">
                                        Duration (Months) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i data-lucide="calendar" class="h-5 w-5 text-gray-400"></i>
                                        </div>
                                        <select 
                                            name="duration" 
                                            id="duration" 
                                            x-model="duration"
                                            class="block w-full pl-10 pr-10 py-3 border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all appearance-none"
                                            required
                                        >
                                            <option value="6">6 Months</option>
                                            <option value="12">12 Months</option>
                                            <option value="24">2 Years</option>
                                            <option value="36">3 Years</option>
                                            <option value="48">4 Years</option>
                                            <option value="60">5 Years</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <i data-lucide="chevron-down" class="h-5 w-5 text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Credit Facility -->
                            <div class="mt-5">
                                <label for="facility" class="block text-sm font-medium text-gray-700 mb-1">
                                    Credit Facility <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="building" class="h-5 w-5 text-gray-400"></i>
														</div>
                                    <select 
                                        name="facility" 
                                        id="facility" 
                                        x-model="facility"
                                        class="block w-full pl-10 pr-10 py-3 border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all appearance-none"
                                        required
                                    >
					<option selected value="">Select Loan/Credit Facility</option>
                                            <option value="Personal Home Loans">Personal Home Loans</option>
                                        <option value="Joint Mortgage">Joint Mortgage</option>
                                        <option value="Automobile Loans">Automobile Loans</option>
                                            <option value="Salary loans">Salary loans</option>
                                            <option value="Secured Overdraft">Secured Overdraft</option>
                                            <option value="Contract Finance">Contract Finance</option>
                                            <option value="Secured Term Loans">Secured Term Loans</option>
                                            <option value="StartUp/Products Financing">StartUp/Products Financing</option>
                                            <option value="Local Purchase Orders Finance">Local Purchase Orders Finance</option>
                                            <option value="Operational Vehicles">Operational Vehicles</option>
                                            <option value="Revenue Loans and Overdraft">Revenue Loans and Overdraft</option>
                                            <option value="Retail TOD">Retail TOD</option>
                                            <option value="Commercial Mortgage">Commercial Mortgage</option>
                                            <option value="Office Equipment">Office Equipment</option>
                                        <option value="Health Finance Product Guideline">Health Finance Product Guideline</option>
                                        <option value="Health Finance">Health Finance</option>
				</select>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <i data-lucide="chevron-down" class="h-5 w-5 text-gray-400"></i>
                                    </div>
                                </div>
													</div>

                            <!-- Purpose of Loan -->
                            <div class="mt-5">
                                <label for="purpose" class="block text-sm font-medium text-gray-700 mb-1">
                                    Purpose of Loan <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute top-3 left-3 flex items-start pointer-events-none">
                                        <i data-lucide="message-square" class="h-5 w-5 text-gray-400"></i>
                                    </div>
                                    <textarea 
                                        name="purpose" 
                                        id="purpose" 
                                        x-model="purpose"
                                        class="block w-full pl-10 py-3 border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all preserveLines"
                                        placeholder="Please describe the purpose of this loan..."
                                        rows="5"
                                        required
                                    ></textarea>
                                </div>
                            </div>
                        </div>
                                                                                            </div>

                    <!-- Financial Information Section -->
                    <div class="mb-8">
                        <div class="flex items-center mb-5">
                            <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center mr-3">
                                <i data-lucide="wallet" class="h-4 w-4 text-primary-600"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">Financial Information</h3>
								</div>

                        <div class="bg-white rounded-xl border border-gray-200 p-5">
                            <!-- Monthly Net Income -->
                            <div>
                                <label for="income" class="block text-sm font-medium text-gray-700 mb-1">
                                    Monthly Net Income <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="dollar-sign" class="h-5 w-5 text-gray-400"></i>
                                    </div>
                                    <select 
                                        name="income" 
                                        id="income" 
                                        x-model="income"
                                        class="block w-full pl-10 pr-10 py-3 border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all appearance-none"
                                        required
                                    >
                                        <option value="2,000-5,000">$2,000 - $5,000</option>
                                        <option value="6,000-10,000">$6,000 - $10,000</option>
                                        <option value="11,000-20,000">$11,000 - $20,000</option>
                                        <option value="21,000-50,000">$21,000 - $50,000</option>
                                        <option value="100,000 and above">$100,000 and above</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <i data-lucide="chevron-down" class="h-5 w-5 text-gray-400"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Terms & Submit -->
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200 mb-6">
                        <div class="flex items-start mb-4">
                            <div class="flex items-center h-5 pt-1">
                                <input 
                                    id="terms" 
                                    name="terms" 
                                    type="checkbox" 
                                    required
                                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                                />
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="terms" class="font-medium text-gray-700">I agree to the terms and conditions</label>
                                <p class="text-gray-500 mt-1">
                                    By submitting this application, I confirm that all information provided is accurate and complete. I authorize World Trust Holding to verify my information and credit history.
                                </p>
                            </div>
                        </div>
                                                                        </div>
								
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row sm:space-x-4">
                        <button 
                            type="submit" 
                            class="w-full mb-3 sm:mb-0 inline-flex items-center justify-center px-6 py-3.5 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
                            :disabled="isSubmitting"
                            :class="{ 'opacity-60 cursor-not-allowed': isSubmitting }"
                        >
                            <template x-if="!isSubmitting">
                                <div class="flex items-center">
                                    <i data-lucide="send" class="h-5 w-5 mr-2"></i>
                                    Submit Loan Application
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
                        <button 
                            type="button" 
                            @click="showApplicationForm = false"
                            class="w-full inline-flex items-center justify-center px-6 py-3.5 border border-gray-300 rounded-lg shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
                        >
                            <i data-lucide="x" class="h-5 w-5 mr-2"></i>
                            Cancel
									</button>
								</div>
			</form>
	  </div>
	</div>
  </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

  
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