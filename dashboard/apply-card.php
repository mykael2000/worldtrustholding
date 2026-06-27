<?php include("header.php"); ?>
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
                <a href="cards.php" class="text-sm text-gray-500 hover:text-primary-600">Cards</a>
                <i data-lucide="chevron-right" class="h-4 w-4 mx-2 text-gray-400"></i>
                <span class="text-sm font-medium text-gray-700">Apply</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Apply for Virtual Card</h1>
        </div>
        <a href="cards.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            <i data-lucide="arrow-left" class="h-4 w-4 mr-2"></i> Back to Cards
        </a>
    </div>
</div>

<!-- Main Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Card Info Banner -->
    <div class="bg-gradient-to-r from-primary-600 to-primary-800 p-6 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold">Apply for a Virtual Card</h2>
                <p class="text-gray-100 mt-1">Get instant access to a virtual card for online payments and subscriptions</p>
            </div>
            <div class="hidden md:block">
                <i data-lucide="credit-card" class="h-16 w-16 text-gray-300 opacity-75"></i>
            </div>
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

    <!-- Application Form -->
    <div class="p-6">
        <form action="create_card.php" method="POST" class="space-y-6">
          
            <section class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Card Details</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Card Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Card Type</label>
                        <div class="space-y-4">
                            <div class="relative border rounded-lg p-4  border-gray-200 ">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="visa" name="card_type" type="radio" value="visa" class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300" checked>
                                    </div>
                                    <div class="ml-3 flex justify-between w-full">
                                        <div>
                                            <label for="visa" class="font-medium text-gray-900">Visa</label>
                                            <p class="text-gray-500 text-sm">Accepted worldwide, suitable for most online purchases</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <svg class="h-8 w-12" viewBox="0 0 1000 324" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M433.334 318.516H348.568L403.315 5.01855H488.081L433.334 318.516Z" fill="#1434CB"/>
                                                <path d="M727.98 15.9043C712.039 9.56622 686.227 2.51855 653.355 2.51855C574.054 2.51855 519.055 45.5277 518.802 106.826C518.306 152.343 558.126 177.912 587.98 193.316C618.34 209.233 629.315 219.335 629.315 233.014C629.061 254.008 603.493 263.591 579.702 263.591C547.839 263.591 530.7 258.287 505.123 245.892L493.902 240.335L482.166 313.221C501.069 323.07 536.096 331.667 572.643 332.163C657.14 332.163 711.128 289.897 711.895 224.144C712.394 188.508 689.871 160.445 643.015 136.91C615.2 121.744 598.306 111.638 598.554 97.2356C598.554 84.5987 613.224 70.9167 648.747 70.9167C677.823 70.4206 699.053 77.7188 715.707 85.5144L726.681 90.8186L738.417 19.9214L727.98 15.9043Z" fill="#1434CB"/>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M841.897 5.01855H773.691C755.782 5.01855 742.397 10.0624 734.862 30.0238L630.236 318.516H715.464C715.464 318.516 729.143 280.806 732.429 272.002C742.901 272.002 822.706 272.002 835.844 272.002C838.373 283.215 845.93 318.516 845.93 318.516H922.667L841.897 5.01855ZM762.22 208.49C769.018 189.752 796.85 116.686 796.85 116.686C796.602 117.174 803.16 99.19 806.699 88.0899L812.02 114.188C812.02 114.188 829.775 192.026 833.061 208.49H762.22Z" fill="#1434CB"/>
                                                <path d="M251.994 5.01855L171.471 219.335L162.137 174.313C146.216 129.054 102.175 80.0894 52.8662 56.8035L126.462 318.02H212.215L336.832 5.01855H251.994Z" fill="#1434CB"/>
                                                <path d="M127.963 5.01855H0.66211L0.167969 10.3101C98.1497 31.7971 163.301 76.5646 190.132 129.298L162.633 13.5747C157.329 -0.60098 144.316 5.51465 127.963 5.01855Z" fill="#1434CB"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="relative border rounded-lg p-4  border-gray-200 ">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="mastercard" name="card_type" type="radio" value="mastercard" class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300">
                                    </div>
                                    <div class="ml-3 flex justify-between w-full">
                                        <div>
                                            <label for="mastercard" class="font-medium text-gray-900">Mastercard</label>
                                            <p class="text-gray-500 text-sm">Global acceptance with enhanced security features</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <svg class="h-8 w-12" viewBox="0 0 131.39 86.9" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M48.37 15.14h34.66v56.61H48.37z" fill="#ff5f00"/>
                                                <path d="M52.37 43.45a35.94 35.94 0 0113.75-28.3 36 36 0 100 56.61 35.94 35.94 0 01-13.75-28.31z" fill="#eb001b"/>
                                                <path d="M120.39 65.54V64.5h.48v-.24h-1.19v.24h.47v1.04zm2.31 0v-1.29h-.36l-.42.91-.42-.91h-.36v1.29h.26V64.9l.39.89h.27l.39-.89v.89z" fill="#f79e1b"/>
                                                <path d="M123.94 43.45a36 36 0 01-58 28.3 36 36 0 000-56.61 36 36 0 0158 28.3z" fill="#f79e1b"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="relative border rounded-lg p-4  border-gray-200 ">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="amex" name="card_type" type="radio" value="american_express" class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300">
                                    </div>
                                    <div class="ml-3 flex justify-between w-full">
                                        <div>
                                            <label for="amex" class="font-medium text-gray-900">American Express</label>
                                            <p class="text-gray-500 text-sm">Premium benefits and exclusive rewards program</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <img src="https://bremtglobal.com/dash/images/cards/amex.png" class="h-8 w-16" alt="American Express">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                                            </div>
                    
                    <!-- Card Level -->
                    <div>
                        <label for="card_level" class="block text-sm font-medium text-gray-700 mb-1">Card Level <span class="text-red-500">*</span></label>
                        <select id="card_level" name="card_level" class="mt-1 block w-full py-2 px-3 border  border-gray-300  rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            <option value="">Select a card level</option>
                            <option value="standard" >Standard - $5.00</option>
                            <option value="gold" >Gold - $15.00</option>
                            <option value="platinum" >Platinum - $25.00</option>
                            <option value="black" >Black - $50.00</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Different levels offer varied spending limits and features</p>
                                            </div>
                    
                    <!-- Currency -->
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                        <select id="currency" name="currency" class="mt-1 block w-full py-2 px-3 border  border-gray-300  rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            <option value="USD">USD - US Dollar</option>
                            <option value="EUR">EUR - Euro</option>
                            <option value="GBP">GBP - British Pound</option>
                        </select>
                                            </div>
                    
                    <!-- Daily Limit -->
                    <div>
                        <label for="daily_limit" class="block text-sm font-medium text-gray-700 mb-1">Daily Spending Limit</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="daily_limit" id="daily_limit" min="1000" max="10000" value="1000" class="focus:ring-primary-500 focus:border-primary-500 block w-full pb-2 pt-2 pl-7 pr-12 sm:text-sm border-gray-300 rounded-md ">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">USD</span>
                            </div>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Limits: $1,000.00 - $10,000.00</p>
                                            </div>
                </div>
            </section>
            
            <section class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Billing Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Card Holder Name -->
                    <div class="md:col-span-2">
                        <label for="card_holder_name" class="block text-sm font-medium text-gray-700 mb-1">Cardholder Name</label>
                        <input type="text" name="card_holder_name" id="card_holder_name" value="<?php echo $user['firstname']; ?>" class="mt-1 focus:ring-primary-500 pl-2 pb-2 pt-2 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md ">
                        <p class="mt-1 text-sm text-gray-500">Name as it will appear on your card</p>
                                            </div>
                    
                    <!-- Billing Address -->
                    <div class="md:col-span-2">
                        <label for="billing_address" class="block text-sm font-medium text-gray-700 mb-1">Billing Address</label>
                        <textarea name="billing_address" id="billing_address" rows="3" class="mt-1 pl-2 pt-2 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md "><?php echo $user['address']; ?></textarea>
                        <p class="mt-1 text-sm text-gray-500">Address used for verification when making purchases</p>
                                            </div>
                </div>
            </section>
            
            <!-- Fee and Terms -->
            <section class="border-t border-gray-200 pt-6">
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i data-lucide="info" class="h-5 w-5 text-blue-500"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-gray-900">Card Issuance Fee</h3>
                            <div class="mt-2 text-sm text-gray-700">
                                <p>There is a one-time issuance fee for your new virtual card:</p>
                                <ul class="list-disc pl-5 mt-1 space-y-1">
                                    <li>Standard: $5.00</li>
                                    <li>Gold: $15.00</li>
                                    <li>Platinum: $25.00</li>
                                    <li>Black: $50.00</li>
                                </ul>
                                <p class="mt-2">The fee will be charged to your account immediately upon approval.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-start mb-4">
                    <div class="flex items-center h-5">
                        <input id="terms" name="terms_accepted" type="checkbox" class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded" required>
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="terms" class="font-medium text-gray-700">I agree to the terms and conditions</label>
                        <p class="text-gray-500">By applying for this card, you agree to our <a href="#" class="text-primary-600 hover:text-primary-500">Terms of Service</a> and <a href="#" class="text-primary-600 hover:text-primary-500">Card Agreement</a>.</p>
                    </div>
                </div>
                                
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Apply for Card
                    </button>
                </div>
            </section>
        </form>
    </div>
</div>

<!-- FAQs Section -->
<div class="mt-8">
    <h2 class="text-xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
    
    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden divide-y divide-gray-200">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900">How soon will my virtual card be ready?</h3>
            <p class="mt-2 text-gray-600">Virtual cards are typically issued within minutes after approval. You'll receive a notification when your card is ready to use.</p>
        </div>
        
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900">Can I use my virtual card for all online purchases?</h3>
            <p class="mt-2 text-gray-600">Yes, your virtual card works for most online merchants that accept Visa or Mastercard. However, some merchants may require a physical card for verification purposes.</p>
        </div>
        
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900">What are the differences between card levels?</h3>
            <p class="mt-2 text-gray-600">
                <span class="block"><strong>Standard:</strong> Basic features with lower spending limits and standard fraud protection.</span>
                <span class="block mt-1"><strong>Gold:</strong> Higher spending limits, enhanced fraud protection, and basic rewards.</span>
                <span class="block mt-1"><strong>Platinum:</strong> High spending limits, priority support, comprehensive fraud protection, and premium rewards.</span>
                <span class="block mt-1"><strong>Black:</strong> Highest spending limits, concierge service, exclusive benefits, and elite rewards program.</span>
            </p>
        </div>
        
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900">How do I fund my virtual card?</h3>
            <p class="mt-2 text-gray-600">Your virtual card is linked to your account balance. Funds are automatically drawn from your main balance when making purchases. You can also transfer specific amounts to your card from your dashboard.</p>
        </div>
    </div>
</div>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 hidden md:block">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 md:flex md:items-center md:justify-between">
                    <div class="flex items-center">
                        <img src="../worldtrustholding.png" alt="Logo" class="h-6 w-auto mr-2">
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