<?php include("header.php");


?>
            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto pb-16 md:pb-0">
                <div class="py-6">
                    <div class="max-w-8xl mx-auto px-4 sm:px-6 md:px-8">
                        <!-- Breadcrumbs + Page Title -->
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center">
                <a href="index.php" class="text-sm text-gray-500 hover:text-primary-600">Dashboard</a>
                <i data-lucide="chevron-right" class="h-4 w-4 mx-2 text-gray-400"></i>
                <span class="text-sm font-medium text-gray-700">Cards</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Virtual Cards</h1>
        </div>
        <a href="apply-card.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            <i data-lucide="plus" class="h-4 w-4 mr-2"></i> Apply for Card
        </a>
    </div>
</div>
<?php if (!empty($_SESSION['error'])): ?>
<div class="mb-4 bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg text-sm">
    <?= htmlspecialchars($_SESSION['error']) ?>
</div>
<?php unset($_SESSION['error']); endif; ?>

<?php if (!empty($_SESSION['success'])): ?>
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 p-4 rounded-lg text-sm">
    <?= htmlspecialchars($_SESSION['success']) ?>
</div>
<?php unset($_SESSION['success']); endif; ?>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-primary-100 rounded-md p-3">
                    <i data-lucide="credit-card" class="h-6 w-6 text-gray-900"></i>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500 truncate">Active Cards</p>
                    <h3 class="text-lg font-semibold text-gray-600"><?= $activeCards ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                    <i data-lucide="hourglass" class="h-6 w-6 text-blue-600"></i>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500 truncate">Pending Applications</p>
                    <h3 class="text-lg font-semibold text-gray-900"><?= $pendingCards ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <i data-lucide="wallet" class="h-6 w-6 text-green-600"></i>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500 truncate">Total Card Balance</p>
                    <h3 class="text-lg font-semibold text-gray-900">$ <?= number_format($totalCardBalance, 2) ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Info Box -->
<div class="bg-primary-700 rounded-xl overflow-hidden shadow-md mb-6">
    <div class="md:flex">
        <div class="p-6 md:flex-1">
            <h2 class="text-white text-xl font-bold mb-2">Virtual Cards Made Easy</h2>
            <p class="text-gray-100 mb-4">Create virtual cards for secure online payments, subscription management, and more. Our virtual cards offer enhanced security and control over your spending.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0 bg-white bg-opacity-10 rounded-md p-2">
                        <i data-lucide="shield" class="h-5 w-5 text-white"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-white text-sm font-medium">Secure Payments</h3>
                        <p class="text-gray-100 text-xs">Protect your main account with separate virtual cards</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 bg-white bg-opacity-10 rounded-md p-2">
                        <i data-lucide="globe" class="h-5 w-5 text-white"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-white text-sm font-medium">Global Acceptance</h3>
                        <p class="text-gray-100 text-xs">Use anywhere major cards are accepted online</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 bg-white bg-opacity-10 rounded-md p-2">
                        <i data-lucide="sliders" class="h-5 w-5 text-white"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-white text-sm font-medium">Spending Controls</h3>
                        <p class="text-gray-100 text-xs">Set limits and monitor transactions in real-time</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 bg-white bg-opacity-10 rounded-md p-2">
                        <i data-lucide="zap" class="h-5 w-5 text-white"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-white text-sm font-medium">Instant Issuance</h3>
                        <p class="text-gray-100 text-xs">Create and use cards within minutes</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-6">
                <a href="apply-card.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-primary-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white focus:ring-offset-primary-600">
                    Apply Now
                </a>
            </div>
        </div>
        <div class="hidden md:flex md:items-center md:justify-center md:w-1/3 bg-primary-700 bg-opacity-50 p-6">
            <div class="relative w-48 h-32">
                <div class="absolute w-full h-full transform rotate-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 shadow-lg"></div>
                <div class="absolute w-full h-full rounded-xl bg-gradient-to-r from-primary-800 to-primary-600 shadow-lg">
                    <div class="p-4 h-full flex flex-col justify-between">
                        <div class="flex justify-between items-start">
                            <div class="text-xs font-mono text-white opacity-75">Virtual Card</div>
                            <i data-lucide="wifi" class="h-4 w-4 text-white opacity-75 transform rotate-90"></i>
                        </div>
                        <div class="text-xs font-mono text-white mt-4">•••• •••• •••• 1234</div>
                        <div class="flex justify-between items-end">
                            <div>
                                <div class="text-xs font-mono text-white opacity-75">VALID THRU</div>
                                <div class="text-xs font-mono text-white">12/25</div>
                            </div>
                            <div class="h-8 w-8">
                                <i data-lucide="credit-card" class="h-8 w-8 text-white opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Card Listings -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 mb-8">
    <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
        <h2 class="text-lg font-medium text-gray-900">Your Cards</h2>
        <a href="apply-card.php" class="text-sm text-primary-600 hover:text-primary-800 flex items-center">
            <i data-lucide="plus-circle" class="h-4 w-4 mr-1"></i> New Card
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
                            Daily limit: $<?= number_format($card['daily_limit'], 2) ?>
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

<!-- How It Works -->
<div class="mb-8">
    <h2 class="text-xl font-bold text-gray-900 mb-6">How Virtual Cards Work</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-center h-12 w-12 rounded-full bg-primary-100 mb-4">
                <i data-lucide="file-text" class="h-6 w-6 text-gray-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">1. Apply</h3>
            <p class="text-gray-600">Complete the application form for your virtual card. Select your preferred card type and set your spending limits.</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-center h-12 w-12 rounded-full bg-primary-100 mb-4">
                <i data-lucide="check-circle" class="h-6 w-6 text-gray-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">2. Activate</h3>
            <p class="text-gray-600">Once approved, your virtual card will be ready to use. View the card details and activate it from your dashboard.</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-center h-12 w-12 rounded-full bg-primary-100 mb-4">
                <i data-lucide="shopping-cart" class="h-6 w-6 text-gray-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">3. Use</h3>
            <p class="text-gray-600">Use your virtual card for online transactions anywhere major credit cards are accepted. Monitor transactions in real-time.</p>
        </div>
    </div>
</div>

<!-- FAQ -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 mb-8">
    <div class="border-b border-gray-200 px-6 py-4">
        <h2 class="text-lg font-medium text-gray-900">Frequently Asked Questions</h2>
    </div>
    
    <div class="p-6">
        <dl class="space-y-6">
            <div>
                <dt class="text-base font-medium text-gray-900">What is a virtual card?</dt>
                <dd class="mt-2 text-sm text-gray-600">A virtual card is a digital payment card that can be used for online transactions. It works just like a physical card but exists only in digital form, providing enhanced security for online purchases.</dd>
            </div>
            
            <div>
                <dt class="text-base font-medium text-gray-900">How secure are virtual cards?</dt>
                <dd class="mt-2 text-sm text-gray-600">Virtual cards offer additional security as they're separate from your primary account. You can create cards with specific spending limits and even create single-use cards for enhanced protection against fraud.</dd>
            </div>
            
            <div>
                <dt class="text-base font-medium text-gray-900">Can I have multiple virtual cards?</dt>
                <dd class="mt-2 text-sm text-gray-600">Yes, you can apply for multiple virtual cards for different purposes - such as one for subscriptions, another for shopping, etc. Each card can have its own limits and settings.</dd>
            </div>
            
            <div>
                <dt class="text-base font-medium text-gray-900">How long does it take to get a virtual card?</dt>
                <dd class="mt-2 text-sm text-gray-600">Virtual cards are typically issued within minutes after approval. Once approved, you can immediately view and use the card details for online transactions.</dd>
            </div>
        </dl>
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