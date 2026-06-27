<?php include("header.php"); 

$kycStatus = $user['kyc_status'] ?? 'unverified';
?>
            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto pb-16 md:pb-0">
                <div class="py-6">
                    <div class="max-w-8xl mx-auto px-4 sm:px-6 md:px-8">
                        
<div class="container mx-auto px-4 py-6 max-w-8xl">
    <!-- Alerts -->
            <div>
    </div>
    <!-- Page Header with Breadcrumbs -->
    <div class="flex flex-col mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Account Verification</h1>
            <div class="flex items-center text-sm text-gray-500">
                <a href="index.php" class="hover:text-primary-600">Dashboard</a>
                <i data-lucide="chevron-right" class="h-4 w-4 mx-2"></i>
                <span class="font-medium text-gray-700">Account Verification</span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <!-- Content Header -->
        <div class="border-b border-gray-200 px-6 py-4">
            <div class="flex items-center">
                <i data-lucide="shield-check" class="h-6 w-6 mr-2 text-primary-600"></i>
                <h2 class="text-xl font-semibold text-gray-900">Verify Your Identity</h2>
            </div>
        </div>
        <?php if ($kycStatus === 'pending'): ?>

        <div class="max-w-3xl mx-auto mt-16 bg-white rounded-xl shadow-md border border-yellow-200 p-8 text-center">
            <i data-lucide="clock" class="h-16 w-16 text-yellow-500 mx-auto mb-4"></i>

            <h2 class="text-2xl font-semibold text-gray-900 mb-2">
                KYC Verification Pending
            </h2>

            <p class="text-gray-600 mb-6">
                Your documents have been submitted successfully and are currently under review.
                This process usually takes <strong>24 – 48 hours</strong>.
            </p>

            <span class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium
                        bg-yellow-100 text-yellow-800">
                Status: Pending Review
            </span>
        </div>

        <?php endif; ?>

        <?php if ($kycStatus === 'verified'): ?>

            <div class="max-w-3xl mx-auto mt-16 bg-white rounded-xl shadow-md border border-green-200 p-8 text-center">
                <i data-lucide="check-circle" class="h-16 w-16 text-green-600 mx-auto mb-4"></i>

                <h2 class="text-2xl font-semibold text-gray-900 mb-2">
                    KYC Verified
                </h2>

                <p class="text-gray-600 mb-6">
                    Your account has been successfully verified.
                    You now have full access to all features.
                </p>

                <span class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium
                            bg-green-100 text-green-800">
                    Status: Verified
                </span>

                <div class="mt-6">
                    <a href="index.php"
                    class="inline-flex items-center px-5 py-2 rounded-md text-white bg-primary-600 hover:bg-primary-700">
                        Go to Dashboard
                    </a>
                </div>
            </div>

        <?php endif; ?>

        <?php if ($kycStatus === 'rejected'): ?>
        <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
            <p class="text-sm text-red-700">
                Your previous verification was rejected. Please review your details and resubmit.
            </p>
        </div>
        <?php endif; ?>

        <?php if ($kycStatus === 'unverified' || $kycStatus === 'rejected'): ?>
        <!-- Form Content -->
        <div class="p-6">
            <div class="max-w-8xl mx-auto">
                <!-- Welcome Message -->
                                    <!-- Welcome Card -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                        <div class="flex items-center mb-4">
                            <div class="h-12 w-12 rounded-full bg-primary-100 flex items-center justify-center mr-4">
                                <i data-lucide="user-check" class="h-6 w-6 text-primary-600"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">Welcome to World Trust Holding</h2>
                                <p class="text-sm text-gray-500">Complete your account verification to access all features</p>
                            </div>
                        </div>
                        
                        <div class="prose prose-sm max-w-none text-gray-600 mb-4">
                            <p class="mb-2"><strong>Dear <?php echo $user['firstname'].' '.$user['lastname']; ?> Oshiomokhai,</strong></p>
                            
                            <p>Welcome Onboard! World Trust Holding is the market's most innovative and fastest-growing company in the financial industry. We look forward to working with you to help you get the most out of our financial services and realize your banking goals.</p>
                            
                            <p>Here at World Trust Holding, we are committed to providing a wide variety of savings, investment, and loan products, all designed to meet your specific needs. Our services are being used by over two million customers around the world.</p>
                            
                            <p>Our excellent customer support team is available 24/7 to help you with any questions. You can contact them at: <a href="mailto:admin@worldtrustholding.com" class="text-primary-600 hover:text-primary-700">admin@worldtrustholding.com</a>.</p>
                            
                            <p>We need a little more information to complete your registration, including completing the AML/KYC form. Please review our terms and conditions below before proceeding.</p>
                        </div>
                    </div>
                
                    <!-- Terms and Conditions Card -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <i data-lucide="file-text" class="h-5 w-5 mr-2 text-primary-600"></i>
                            Terms and Conditions
                        </h3>
                        
                        <div class="bg-white border border-gray-200 rounded-lg p-4 h-72 overflow-y-auto text-sm text-gray-700 mb-6">
                            <p class="mb-4">
                                Before you can start using our online service you must agree to be bound by the conditions below. You must read the 
                                conditions before you decide whether to accept them. If you agree to be bound by these conditions, please click the 
                                I accept button below. If you click on the Decline button, you will not be able to continue your registration for our 
                                online services. We strongly recommend that you print a copy of these conditions for your reference.
                            </p>
                            
                            <h4 class="font-semibold text-gray-900 mt-4 mb-2">1. DEFINITIONS</h4>
                            <p class="mb-2">In these conditions the following words have the following meanings:</p>
                            <ul class="list-disc pl-6 mb-4 space-y-1">
                                <li><span class="font-medium">ACCOUNT:</span> any account which you hold and access via our online service.</li>
                                <li><span class="font-medium">ADDITIONAL SECURITY DETAILS:</span> the additional information you give us to help us identify you including the additional security question you provide yourself.</li>
                                <li><span class="font-medium">IDENTITY DETAILS:</span> the access code we may provide you with.</li>
                                <li><span class="font-medium">World Trust Holding ACCOUNT NUMBER, PASSWORD and ACCOUNT PIN:</span> you choose to identify yourself when you use our online service.</li>
                                <li><span class="font-medium">YOU, YOUR and YOURSELF:</span> refer to the person who has entered into this agreement with us.</li>
                            </ul>
                            
                            <h4 class="font-semibold text-gray-900 mt-4 mb-2">2. USING THE ONLINE SERVICE</h4>
                            <ol class="list-alpha pl-6 mb-4 space-y-2">
                                <li>These conditions apply to your use of our online service and in relation to any accounts. They explain the relationship between you and us in relation to our online service. You should read these conditions carefully to understand how these services work and your and our rights and duties under them. If there is a conflict between these conditions and your account conditions, these conditions will apply. This means that, when you use our online service both sets of conditions will apply unless they contradict each other in which case, the relevant condition in these conditions apply.</li>
                                <li>If any of your accounts is a joint account, these conditions apply to all of you together and any of you separately. If more than one of you uses our online service you must each choose your own username, password and additional security details.</li>
                                <li>By registering to use our online service, you accept these conditions and agree that we may communicate with you by e-mail or through our website.</li>
                                <li>When you use our online service you must follow the instructions we give you from time to time. You are responsible for ensuring that your computer, software and other equipment are capable of being used with our online service.</li>
                                <li>Our online sites are secure. Disconnection from the Internet or leaving these sites will not automatically log you off. You must always use the log off facility when you are finished and never leave your machine unattended while you are logged in. As a security measure, if you have not used the sites for more than a specified period of time we will ask you to log in again.</li>
                            </ol>
                            
                            <h4 class="font-semibold text-gray-900 mt-4 mb-2">3. WHAT RULES APPLY TO SECURITY?</h4>
                            <ol class="list-alpha pl-6 mb-4 space-y-2">
                                <li>As part of the registration for our online service you must provide us with identity details before we will allow you to use the services for the first time. You must enter your identity details immediately after signing in, so we can identify you.</li>
                                <li>Every time you use our online service you must give us your username, your password and the answer to an additional security question.</li>
                                <li>You can change your username or password online by following the instructions on the screen.</li>
                                <li>For administration or security reasons, we can require you to choose a new username or change your password before you use (or carry on using) our online service.</li>
                                <li>You must not write down, store (whether encrypted or otherwise) on your computer or let anyone else know your password, identity details or additional security details, and the fact that they are for use with your accounts.</li>
                            </ol>
                            
                            <h4 class="font-semibold text-gray-900 mt-4 mb-2">4. WHAT IS THE EXTENT OF YOUR LIABILITY?</h4>
                            <ol class="list-alpha pl-6 mb-4 space-y-2">
                                <li>If you are a victim of online fraud, we guarantee that you won't lose any money on your accounts and will always be reimbursed in full.</li>
                                <li>Unless you are a victim of fraud you are responsible for all instructions and other information sent using your username, password or additional security details.</li>
                                <li>You will not be held responsible for any instructions or information sent after you have told us that someone knows your password or additional security details and has used any of them to access our online service.</li>
                                <li>World Trust Holding does not accept responsibility for any loss you or anybody else may suffer because any instructions or information you sent us are sent in error, fail to reach us or are distorted unless you have been the victim of fraud.</li>
                                <li>World Trust Holding does not accept responsibility for any loss you or anybody else may suffer because any instructions or information we send you fail to reach you or are distorted unless you have been the victim of fraud.</li>
                            </ol>
                            
                            <h4 class="font-semibold text-gray-900 mt-4 mb-2">5. HOW WE CAN CHANGE THESE CONDITIONS</h4>
                            <ol class="list-alpha pl-6 mb-4 space-y-2">
                                <li>World Trust Holding may change these conditions for any reason by giving you written notice or publishing the change on our website.</li>
                                <li>We may send all written notices to you at the last e-mail address you gave us. You must let us know immediately if you change your e-mail address (you can do so online), to make sure that we have your current e-mail address at all times.</li>
                            </ol>
                            
                            <h4 class="font-semibold text-gray-900 mt-4 mb-2">6. GENERAL</h4>
                            <p>World Trust Holding service is available to you if you are 18 years of age or over.</p>
                        </div>
                        
                        <div class="flex flex-wrap gap-4 justify-center sm:justify-start">
                            <a href="kyc-form.php" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                                <i data-lucide="check-circle" class="h-4 w-4 mr-2"></i>
                                I Accept & Proceed to Verification
                            </a>
                            
                            <a href="logout.php" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i data-lucide="x-circle" class="h-4 w-4 mr-2 text-gray-500"></i>
                                Decline
                            </a>
                            <form id="logout-form" action="logout.php" method="POST" class="hidden">
                                <input type="hidden" name="_token" value="MJ3oshkEFdsEktrfbMCK0JvF1Q196j6lk1QiONcb">
                            </form>
                        </div>
                    </div>
                            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Support Section -->
    <div class="mt-8 bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="p-6">
            <div class="flex flex-col md:flex-row gap-6 items-center">
                <div class="flex-shrink-0">
                    <div class="h-16 w-16 bg-primary-50 rounded-full flex items-center justify-center">
                        <i data-lucide="life-buoy" class="h-8 w-8 text-primary-600"></i>
                    </div>
                </div>
                <div class="flex-grow text-center md:text-left">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Need help with verification?</h3>
                    <p class="text-gray-600">Our support team is available 24/7 to assist you with the verification process. Reach out with any questions.</p>
                </div>
                <div class="flex-shrink-0">
                    <a href="support.php" class="inline-flex items-center justify-center px-4 py-2 border border-primary-600 rounded-md shadow-sm text-sm font-medium text-primary-600 bg-white hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                        <i data-lucide="message-square" class="h-4 w-4 mr-2"></i>
                        Contact Support
                    </a>
                </div>
            </div>
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