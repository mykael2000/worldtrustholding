<?php
    include("function.php");

    $currentPage = basename(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: ($_SERVER['PHP_SELF'] ?? ''));

    function navIsActive($pages) {
        global $currentPage;

        return in_array($currentPage, (array) $pages, true);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="MJ3oshkEFdsEktrfbMCK0JvF1Q196j6lk1QiONcb">
    <title>World Trust Holding | Customer Dashboard</title>
    <meta name="description" content="Swift and Secure Money Transfer to any UK bank account will become a breeze with World Trust Holding." />
    <link rel="shortcut icon" href="https://worldtrustholding.com/worldtrustholding.png" />
    <link rel="preload" href="path/to/GraphikRegular.otf" as="font" type="font/otf" crossorigin="anonymous">



    <!-- Initial theme colors setup (before anything else loads) -->
    <script>
        // Set CSS theme variables - these match our Tailwind theme
        document.documentElement.style.setProperty('--primary-color', '#0047AB');
        document.documentElement.style.setProperty('--primary-color-dark', '#003380');
        document.documentElement.style.setProperty('--primary-color-light', '#6A8FDB');
        document.documentElement.style.setProperty('--primary-color-lightest', '#6A8FDB');
        document.documentElement.style.setProperty('--secondary-color', '#FFC107');
        document.documentElement.style.setProperty('--secondary-color-dark', '#CC9900');
        document.documentElement.style.setProperty('--secondary-color-light', '#FFECB3');
        document.documentElement.style.setProperty('--accent-color', '#ec4899');
        document.documentElement.style.setProperty('--text-color', '#1A1A1A');
        document.documentElement.style.setProperty('--bg-color', '#f9fafb');
        document.documentElement.style.setProperty('--sidebar-bg-color', '#F4F5F7');
        document.documentElement.style.setProperty('--sidebar-text-color', '#0047AB');
        document.documentElement.style.setProperty('--card-bg-color', '#F4F5F7');
    </script>

    <!-- Tailwind CSS with custom color variables -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#6A8FDB',
                            100: '#6A8FDB',
                            200: '#6A8FDB',
                            300: '#6A8FDB',
                            400: '#6A8FDB',
                            500: '#0047AB',
                            600: '#0047AB',
                            700: '#003380',
                            800: '#003380',
                            900: '#003380',
                        },
                        secondary: {
                            50: '#FFECB3',
                            100: '#FFECB3',
                            200: '#FFECB3',
                            300: '#FFECB3',
                            400: '#FFECB3',
                            500: '#FFC107',
                            600: '#FFC107',
                            700: '#CC9900',
                            800: '#CC9900',
                            900: '#CC9900',
                        },
                        accent: {
                            50: '#fdf2f8',
                            100: '#fce7f3',
                            200: '#fbcfe8',
                            300: '#f9a8d4',
                            400: '#f472b6',
                            500: '#ec4899',
                            600: '#db2777',
                            700: '#be185d',
                            800: '#9d174d',
                            900: '#831843',
                        }
                    },
                    boxShadow: {
                        'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    }
                }
            }
        }
    </script>

    
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>

    <!-- Custom Fonts -->
  

    <!-- Modern Loading Animation -->
    <style>
        .page-loading {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            transition: all .4s .2s ease-in-out;
            background-color: #ffffff;
            visibility: hidden;
            z-index: 9999;
        }
        .page-loading.active {
            opacity: 1;
            visibility: visible;
        }
        .page-loading-inner {
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            text-align: center;
            transform: translateY(-50%);
            transition: opacity .2s ease-in-out;
            opacity: 0;
        }
        .page-loading.active>.page-loading-inner {
            opacity: 1;
        }

        .loading-container {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .loading-animation {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            margin-bottom: 1rem;
            position: relative;
        }

        .loading-animation .circle {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 4px solid transparent;
            mix-blend-mode: overlay;
            animation: rotateCircle 1.5s linear infinite;
        }

        .loading-animation .circle:nth-child(1) {
            border-top-color: var(--primary-color);
            animation-delay: 0s;
        }

        .loading-animation .circle:nth-child(2) {
            border-right-color: var(--primary-color-light);
            animation-delay: 0.2s;
        }

        .loading-animation .circle:nth-child(3) {
            border-bottom-color: var(--secondary-color);
            animation-delay: 0.4s;
        }

        .loading-animation .circle:nth-child(4) {
            border-left-color: var(--primary-color-lightest);
            animation-delay: 0.6s;
        }

        .loading-animation .core {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--primary-color-light), var(--primary-color-dark));
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.5);
            animation: pulse 1s ease-in-out infinite alternate;
        }

        .page-loading .text {
            color: var(--primary-color);
            font-weight: 500;
            letter-spacing: 0.05em;
            margin-top: 0.5rem;
            font-size: 0.875rem;
            background: linear-gradient(90deg, var(--primary-color-dark), var(--primary-color-light), var(--primary-color-dark));
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradient 2s linear infinite;
        }

        @keyframes  rotateCircle {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes  pulse {
            from {
                transform: scale(0.8);
                opacity: 0.8;
            }
            to {
                transform: scale(1.2);
                opacity: 1;
            }
        }

        @keyframes  gradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    <!-- Web Application Manifest -->
<link rel="manifest" href="https://worldtrustholding.com/manifest.json">
<!-- Chrome for Android theme color -->
<meta name="theme-color" content="#000000">

<!-- Add to homescreen for Chrome on Android -->
<meta name="mobile-web-app-capable" content="yes">
<meta name="application-name" content="PWA">
<link rel="icon" sizes="512x512" href="/images/icons/icon-512x512.png">

<!-- Add to homescreen for Safari on iOS -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-mobile-web-app-title" content="PWA">
<link rel="apple-touch-icon" href="/images/icons/icon-512x512.png">


<link href="/images/icons/splash-640x1136.png" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
<link href="/images/icons/splash-750x1334.png" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
<link href="/images/icons/splash-1242x2208.png" media="(device-width: 621px) and (device-height: 1104px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image" />
<link href="/images/icons/splash-1125x2436.png" media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image" />
<link href="/images/icons/splash-828x1792.png" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
<link href="/images/icons/splash-1242x2688.png" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image" />
<link href="/images/icons/splash-1536x2048.png" media="(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
<link href="/images/icons/splash-1668x2224.png" media="(device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
<link href="/images/icons/splash-1668x2388.png" media="(device-width: 834px) and (device-height: 1194px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
<link href="/images/icons/splash-2048x2732.png" media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />

<!-- Tile for Win8 -->
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="/images/icons/icon-512x512.png">

<script type="text/javascript">
    // Initialize the service worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/serviceworker.js', {
            scope: '.'
        }).then(function (registration) {
            // Registration was successful
            console.log('Laravel PWA: ServiceWorker registration successful with scope: ', registration.scope);
        }, function (err) {
            // registration failed :(
            console.log('Laravel PWA: ServiceWorker registration failed: ', err);
        });
    }
</script></head>

<body class="bg-gray-50">
    <!-- Modern Page Loader -->
    <div class="page-loading active">
        <div class="page-loading-inner">
            <div class="loading-container">
                <div class="loading-animation">
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="core"></div>
                </div>
                <div class="text">World Trust Holding Banking</div>
            </div>
        </div>
    </div>

    <!-- Main Layout -->
    <div class="flex h-screen overflow-hidden" x-data="{sidebarOpen: false, mobileMenuOpen: false, userDropdownOpen: false, notificationsOpen: false}">
        <!-- Sidebar - Desktop -->
        <div class="hidden md:flex md:w-64 md:flex-col bg-white h-full border-r border-gray-200 shadow-sm">
            <div class="flex flex-col flex-grow pt-5 pb-4 overflow-y-auto">
                <!-- Logo -->
                <div class="flex items-center justify-center flex-shrink-0 px-4 mb-6">
                    <a href="/" class="flex items-center">
                        <img src="../worldtrustholding.png" alt="Logo" class="h-10 w-auto">
                    </a>
                </div>

                <!-- User Info Card - Desktop Sidebar -->
                <div class="px-4 mb-6">
                    <div class="bg-gray-50 rounded-xl p-4 shadow-sm border border-gray-100">
                        <div class="flex items-center mb-3">
                            <div class="flex-shrink-0 mr-3">
                                                        <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center text-white font-bold border-2 border-primary-100">
                                        <?php echo $user['firstname'][0].$user['lastname'][0]; ?>
                                    </div>
                                    </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    <?php echo $user['firstname'].' '.$user['lastname']; ?>
                                </p>
                                <p class="text-xs text-gray-500 truncate">
                                    ID: <?php echo $user['account_id']; ?>
                                </p>
                            </div>
                        </div>


                        <!-- KYC Verification Status -->
                       
                        <div class="mb-3">
                            <?php if (empty($user['kyc_status'])): ?>

                                <a href="verify.php"
                                class="flex items-center justify-center py-1 rounded-md
                                        bg-red-50 border border-red-100 hover:bg-red-100 transition-colors">
                                    <span class="text-xs text-red-800 font-medium flex items-center">
                                        <i data-lucide="alert-circle" class="h-3 w-3 mr-1"></i>
                                        Verify KYC
                                    </span>
                                </a>
                            <?php elseif ($user['kyc_status'] === 'unverified'): ?>

                                <a href="verify.php"
                                class="flex items-center justify-center py-1 rounded-md
                                        bg-red-50 border border-red-100 hover:bg-red-100 transition-colors">
                                    <span class="text-xs text-red-800 font-medium flex items-center">
                                        <i data-lucide="alert-circle" class="h-3 w-3 mr-1"></i>
                                        Verify KYC
                                    </span>
                                </a>

                            <?php elseif ($user['kyc_status'] === 'pending'): ?>

                                <div
                                    class="flex items-center justify-center py-1 rounded-md
                                        bg-yellow-50 border border-yellow-100">
                                    <span class="text-xs text-yellow-800 font-medium flex items-center">
                                        <i data-lucide="clock" class="h-3 w-3 mr-1"></i>
                                        KYC Pending Review
                                    </span>
                                </div>

                            <?php elseif ($user['kyc_status'] === 'verified'): ?>

                                <div
                                    class="flex items-center justify-center py-1 rounded-md
                                        bg-green-50 border border-green-100">
                                    <span class="text-xs text-green-800 font-medium flex items-center">
                                        <i data-lucide="check-circle" class="h-3 w-3 mr-1"></i>
                                        KYC Verified
                                    </span>
                                </div>

                            <?php elseif ($user['kyc_status'] === 'rejected'): ?>

                                <a href="verify.php"
                                class="flex items-center justify-center py-1 rounded-md
                                        bg-red-50 border border-red-100 hover:bg-red-100 transition-colors">
                                    <span class="text-xs text-red-800 font-medium flex items-center">
                                        <i data-lucide="x-circle" class="h-3 w-3 mr-1"></i>
                                        KYC Rejected – Resubmit
                                    </span>
                                </a>

                            <?php endif; ?>
                        </div>


                        <div class="flex space-x-2">
                            <a href="account-settings.php" class="flex-1 inline-flex justify-center items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                <i data-lucide="user" class="h-3 w-3 mr-1"></i> Profile
                            </a>
                            <a href="logout.php"
                                onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();"
                                class="flex-1 inline-flex justify-center items-center px-2.5 py-1.5 border border-transparent shadow-sm text-xs font-medium rounded text-white bg-primary-600 hover:bg-primary-700">
                                <i data-lucide="log-out" class="h-3 w-3 mr-1"></i> Logout
                            </a>
                            <form id="logout-form-sidebar" action="logout.php" method="POST" style="display: none;">
                                <input type="hidden" name="_token" value="MJ3oshkEFdsEktrfbMCK0JvF1Q196j6lk1QiONcb">
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Menu Items -->
                <nav class="flex-1 px-4 space-y-1">
                    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">Main Menu</p>

                    <a href="index.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg <?= navIsActive('index.php') ? 'bg-primary-50 text-black border-l-4 border-primary-500 pl-2' : 'text-gray-700 hover:bg-gray-50' ?>">
                        <i data-lucide="home" class="mr-3 h-5 w-5 <?= navIsActive('index.php') ? 'text-black' : 'text-gray-500' ?>"></i>
                        Dashboard
                    </a>

                    <a href="accounthistory.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg <?= navIsActive('accounthistory.php') ? 'bg-primary-50 text-black border-l-4 border-primary-500 pl-2' : 'text-gray-700 hover:bg-gray-50' ?>">
                        <i data-lucide="activity" class="mr-3 h-5 w-5 <?= navIsActive('accounthistory.php') ? 'text-black' : 'text-gray-500' ?>"></i>
                        Transactions
                    </a>

                    <a href="statement.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg <?= navIsActive(['statement.php', 'statement-pdf.php', 'transaction-detail.php']) ? 'bg-primary-50 text-black border-l-4 border-primary-500 pl-2' : 'text-gray-700 hover:bg-gray-50' ?>">
                        <i data-lucide="file-text" class="mr-3 h-5 w-5 <?= navIsActive(['statement.php', 'statement-pdf.php', 'transaction-detail.php']) ? 'text-black' : 'text-gray-500' ?>"></i>
                        Account Statement
                    </a>

                    <!-- Cards Menu Item -->
                    <a href="cards.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg <?= navIsActive(['cards.php', 'apply-card.php', 'create_card.php']) ? 'bg-primary-50 text-black border-l-4 border-primary-500 pl-2' : 'text-gray-700 hover:bg-gray-50' ?>">
                        <i data-lucide="credit-card" class="mr-3 h-5 w-5 <?= navIsActive(['cards.php', 'apply-card.php', 'create_card.php']) ? 'text-black' : 'text-gray-500' ?>"></i>
                        Cards
                    </a>

                    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">Transfers</p>

                    <a href="localtransfer.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg <?= navIsActive('localtransfer.php') ? 'bg-primary-50 text-black border-l-4 border-primary-500 pl-2' : 'text-gray-700 hover:bg-gray-50' ?>">
                        <i data-lucide="send" class="mr-3 h-5 w-5 <?= navIsActive('localtransfer.php') ? 'text-black' : 'text-gray-500' ?>"></i>
                        Local Transfer
                    </a>

                    <a href="internationaltransfer.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg <?= navIsActive('internationaltransfer.php') ? 'bg-primary-50 text-black border-l-4 border-primary-500 pl-2' : 'text-gray-700 hover:bg-gray-50' ?>">
                        <i data-lucide="globe" class="mr-3 h-5 w-5 <?= navIsActive('internationaltransfer.php') ? 'text-black' : 'text-gray-500' ?>"></i>
                        International Wire
                    </a>

                    <a href="deposits.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg <?= navIsActive(['deposits.php', 'newdeposit.php', 'deposit_confirm.php']) ? 'bg-primary-50 text-black border-l-4 border-primary-500 pl-2' : 'text-gray-700 hover:bg-gray-50' ?>">
                        <i data-lucide="download" class="mr-3 h-5 w-5 <?= navIsActive(['deposits.php', 'newdeposit.php', 'deposit_confirm.php']) ? 'text-black' : 'text-gray-500' ?>"></i>
                        Deposit
                    </a>

                    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">Services</p>

                    <a href="loan.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg <?= navIsActive('loan.php') ? 'bg-primary-50 text-black border-l-4 border-primary-500 pl-2' : 'text-gray-700 hover:bg-gray-50' ?>">
                        <i data-lucide="credit-card" class="mr-3 h-5 w-5 <?= navIsActive('loan.php') ? 'text-black' : 'text-gray-500' ?>"></i>
                        Loan Request
                    </a>

                    <a href="viewloan.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg <?= navIsActive('viewloan.php') ? 'bg-primary-50 text-black border-l-4 border-primary-500 pl-2' : 'text-gray-700 hover:bg-gray-50' ?>">
                        <i data-lucide="history" class="mr-3 h-5 w-5 <?= navIsActive('viewloan.php') ? 'text-black' : 'text-gray-500' ?>"></i>
                        Loan History
                    </a>

                    <a href="irs-refund.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg <?= navIsActive('irs-refund.php') ? 'bg-primary-50 text-black border-l-4 border-primary-500 pl-2' : 'text-gray-700 hover:bg-gray-50' ?>">
                        <i data-lucide="receipt" class="mr-3 h-5 w-5 <?= navIsActive('irs-refund.php') ? 'text-black' : 'text-gray-500' ?>"></i>
                        IRS Tax Refund
                    </a>
                    <a href="irs_list.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg <?= navIsActive('irs_list.php') ? 'bg-primary-50 text-black border-l-4 border-primary-500 pl-2' : 'text-gray-700 hover:bg-gray-50' ?>">
                        <i data-lucide="receipt" class="mr-3 h-5 w-5 <?= navIsActive('irs_list.php') ? 'text-black' : 'text-gray-500' ?>"></i>
                        IRS Tax Refund History
                    </a>

                    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">Account</p>

                    <a href="account-settings.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg <?= navIsActive(['account-settings.php', 'editpass.php', 'pin.php']) ? 'bg-primary-50 text-black border-l-4 border-primary-500 pl-2' : 'text-gray-700 hover:bg-gray-50' ?>">
                        <i data-lucide="settings" class="mr-3 h-5 w-5 <?= navIsActive(['account-settings.php', 'editpass.php', 'pin.php']) ? 'text-black' : 'text-gray-500' ?>"></i>
                        Settings
                    </a>

                    <a href="support.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg <?= navIsActive('support.php') ? 'bg-primary-50 text-black border-l-4 border-primary-500 pl-2' : 'text-gray-700 hover:bg-gray-50' ?>">
                        <i data-lucide="help-circle" class="mr-3 h-5 w-5 <?= navIsActive('support.php') ? 'text-black' : 'text-gray-500' ?>"></i>
                        Support Ticket
                    </a>
                    <a href="my-tickets.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg <?= navIsActive(['my-tickets.php', 'notifications.php']) ? 'bg-primary-50 text-black border-l-4 border-primary-500 pl-2' : 'text-gray-700 hover:bg-gray-50' ?>">
                        <i data-lucide="help-circle" class="mr-3 h-5 w-5 <?= navIsActive(['my-tickets.php', 'notifications.php']) ? 'text-black' : 'text-gray-500' ?>"></i>
                        My Tickets
                    </a>
                </nav>
            </div>

            <!-- App Version -->
            <div class="p-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i data-lucide="shield-check" class="h-4 w-4 text-green-500 mr-2"></i>
                        <span class="text-xs text-gray-500">Secure Banking</span>
                    </div>
                    <span class="text-xs text-gray-400">v1.2.0</span>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm z-20">
                <div class="flex items-center justify-between px-4 py-3">
                    <!-- Mobile: Logo + Menu button -->
                    <div class="flex items-center md:hidden">
                        <button
                            @click="sidebarOpen = false; mobileMenuOpen = !mobileMenuOpen"
                            type="button"
                            class="text-gray-500 hover:text-gray-600 focus:outline-none"
                            aria-label="Toggle menu">
                            <i data-lucide="menu" class="h-6 w-6"></i>
                        </button>
                        <a href="/" class="ml-4">
                            <img src="../worldtrustholding.png" alt="Logo" class="h-8 w-auto rounded-full">
                        </a>
                    </div>

                    <!-- Desktop: Current Date & Time + Search bar -->
                    <div class="hidden md:flex md:flex-1 md:items-center">
                        <div class="text-sm text-gray-600 flex items-center">
                            <i data-lucide="calendar" class="h-4 w-4 mr-2 text-gray-400"></i>
                            <span><?php echo date("l, F j, Y"); ?></span>
                        </div>
                    </div>

                    <!-- Right Nav Items (Both mobile & desktop) -->
                    <div class="flex items-center space-x-4">
                        <!-- Balance indicator (desktop only) -->
                        <div class="hidden md:flex items-center px-3 py-1.5 bg-primary-50 rounded-full">
                            <i data-lucide="wallet" class="h-4 w-4 text-gray-900 mr-2"></i>
                            <span class="text-sm font-medium text-gray-900">
                               $<?php echo number_format($user['total_balance'],2,'.',',') ?> 
                            </span>
                        </div>

                        <!-- Notification Bell -->
                        <div class="relative" x-data="{ notificationsOpen: false }">
                            <button
                                @click="notificationsOpen = !notificationsOpen; userDropdownOpen = false"
                                class="relative p-1 text-gray-500 hover:text-gray-600 focus:outline-none">
                                <i data-lucide="bell" class="h-6 w-6"></i>
                                                            </button>

                            <!-- Notification dropdown -->
                            <div
                                x-show="notificationsOpen"
                                @click.away="notificationsOpen = false"
                                class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                                        <form action="" method="POST">
                                            <input type="hidden" name="_token" value="MJ3oshkEFdsEktrfbMCK0JvF1Q196j6lk1QiONcb">                                            <button type="submit" class="text-xs text-black hover:text-primary-500">Mark all as read</button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Notification items -->
                                <div class="max-h-60 overflow-y-auto">
                                    
                                                                            <div class="py-6 text-center">
                                            <i data-lucide="inbox" class="h-8 w-8 mx-auto text-gray-300 mb-1"></i>
                                            <p class="text-sm text-gray-500">No notifications yet</p>
                                        </div>
                                                                    </div>

                                <div class="px-4 py-3 border-t border-gray-100 text-center">
                                    <a href="notifications.php" class="text-sm font-medium text-black hover:text-primary-500">View all notifications</a>
                                </div>
                            </div>
                        </div>

                        <!-- User Profile Dropdown -->
                        <div class="relative">
                            <button
    @click="userDropdownOpen = !userDropdownOpen; notificationsOpen = false"
    class="flex items-center max-w-xs text-sm rounded-full focus:outline-none"
    id="user-menu-button"
    aria-expanded="false"
    aria-haspopup="true"
>
    <span class="sr-only">Open user menu</span>

                    <div class="h-8 w-8 rounded-full bg-primary-100 text-white flex items-center justify-center font-semibold border-2 border-gray-200">
            <?php echo $user['firstname'][0].$user['lastname'][0]; ?>
        </div>
    </button>


                            <!-- User dropdown menu -->
                            <div
                                x-show="userDropdownOpen"
                                @click.away="userDropdownOpen = false"
                                class="origin-top-right absolute right-0 mt-2 w-48 rounded-lg shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                                role="menu"
                                aria-orientation="vertical"
                                aria-labelledby="user-menu-button"
                                tabindex="-1"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900"><?php echo $user['firstname'].' '.$user['lastname']; ?></p>
                                    <p class="text-xs text-gray-500 mt-1">ID: <?php echo $user['account_id']; ?></p>

                                    <!-- KYC Verification Status -->
                                        <div class="mt-2">
                                             <?php if (empty($user['kyc_status'])): ?>

                                <a href="verify.php"
                                class="flex items-center justify-center py-1 rounded-md
                                        bg-red-50 border border-red-100 hover:bg-red-100 transition-colors">
                                    <span class="text-xs text-red-800 font-medium flex items-center">
                                        <i data-lucide="alert-circle" class="h-3 w-3 mr-1"></i>
                                        Verify KYC
                                    </span>
                                </a>
                            <?php elseif ($user['kyc_status'] === 'unverified'): ?>

                                <a href="verify.php"
                                class="flex items-center justify-center py-1 rounded-md
                                        bg-red-50 border border-red-100 hover:bg-red-100 transition-colors">
                                    <span class="text-xs text-red-800 font-medium flex items-center">
                                        <i data-lucide="alert-circle" class="h-3 w-3 mr-1"></i>
                                        Verify KYC
                                    </span>
                                </a>

                            <?php elseif ($user['kyc_status'] === 'pending'): ?>

                                <div
                                    class="flex items-center justify-center py-1 rounded-md
                                        bg-yellow-50 border border-yellow-100">
                                    <span class="text-xs text-yellow-800 font-medium flex items-center">
                                        <i data-lucide="clock" class="h-3 w-3 mr-1"></i>
                                        KYC Pending Review
                                    </span>
                                </div>

                            <?php elseif ($user['kyc_status'] === 'verified'): ?>

                                <div
                                    class="flex items-center justify-center py-1 rounded-md
                                        bg-green-50 border border-green-100">
                                    <span class="text-xs text-green-800 font-medium flex items-center">
                                        <i data-lucide="check-circle" class="h-3 w-3 mr-1"></i>
                                        KYC Verified
                                    </span>
                                </div>

                            <?php elseif ($user['kyc_status'] === 'rejected'): ?>

                                <a href="verify.php"
                                class="flex items-center justify-center py-1 rounded-md
                                        bg-red-50 border border-red-100 hover:bg-red-100 transition-colors">
                                    <span class="text-xs text-red-800 font-medium flex items-center">
                                        <i data-lucide="x-circle" class="h-3 w-3 mr-1"></i>
                                        KYC Rejected – Resubmit
                                    </span>
                                </a>

                            <?php endif; ?>

                                        </div>
                                                                    </div>

                                <a href="support.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center" role="menuitem">
                                    <i data-lucide="help-circle" class="h-4 w-4 mr-3 text-gray-500"></i> Support Ticket
                                </a>
                                <a href="account-settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center" role="menuitem">
                                    <i data-lucide="user" class="h-4 w-4 mr-3 text-gray-500"></i> My Profile
                                </a>
                                <a
                                    href="logout.php"
                                    onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();"
                                    class=" block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center"
                                    role="menuitem">
                                    <i data-lucide="log-out" class="h-4 w-4 mr-3 text-gray-500"></i> Sign Out
                                </a>
                                <form id="logout-form-header" action="logout.php" method="POST" style="display: none;">
                                    <input type="hidden" name="_token" value="MJ3oshkEFdsEktrfbMCK0JvF1Q196j6lk1QiONcb">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Mobile Menu Popup - Centered Floating Box -->
            <div
                x-show="mobileMenuOpen"
                class="fixed inset-0 flex items-center justify-center z-40 md:hidden"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition-all ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-90">
                <!-- Overlay -->
                <div
                    class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm"
                    aria-hidden="true"
                    @click="mobileMenuOpen = false"></div>

                <!-- Popup Content - Centered Box -->
                <div class="relative w-11/12 max-w-md bg-white rounded-2xl shadow-2xl p-5 z-50">
                    <!-- Close button -->
                    <button
                        type="button"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-500"
                        @click="mobileMenuOpen = false">
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>

                    <!-- User info for mobile -->
                    <div class="flex items-center mb-6 border-b border-gray-100 pb-4">
                        <div class="flex-shrink-0 mr-3">
                    <div class="h-12 w-12 rounded-full bg-primary-100 text-white flex items-center justify-center font-bold border-2 border-primary-100">
            <?php echo $user['firstname'][0].$user['lastname'][0]; ?>
        </div>
    </div>

                        <div>
                            <h2 class="text-base font-semibold text-gray-900"><?php echo $user['firstname'].' '.$user['lastname']; ?></h2>
                            <p class="text-sm text-gray-500">Account: <?php echo $user['account_id']; ?></p>

                            <!-- KYC Verification Status -->
                                <div class="mt-1">
                                     <?php if (empty($user['kyc_status'])): ?>

                                <a href="verify.php"
                                class="flex items-center justify-center py-1 rounded-md
                                        bg-red-50 border border-red-100 hover:bg-red-100 transition-colors">
                                    <span class="text-xs text-red-800 font-medium flex items-center">
                                        <i data-lucide="alert-circle" class="h-3 w-3 mr-1"></i>
                                        Verify KYC
                                    </span>
                                </a>
                            <?php elseif ($user['kyc_status'] === 'unverified'): ?>

                                <a href="verify.php"
                                class="flex items-center justify-center py-1 rounded-md
                                        bg-red-50 border border-red-100 hover:bg-red-100 transition-colors">
                                    <span class="text-xs text-red-800 font-medium flex items-center">
                                        <i data-lucide="alert-circle" class="h-3 w-3 mr-1"></i>
                                        Verify KYC
                                    </span>
                                </a>

                            <?php elseif ($user['kyc_status'] === 'pending'): ?>

                                <div
                                    class="flex items-center justify-center py-1 rounded-md
                                        bg-yellow-50 border border-yellow-100">
                                    <span class="text-xs text-yellow-800 font-medium flex items-center">
                                        <i data-lucide="clock" class="h-3 w-3 mr-1"></i>
                                        KYC Pending Review
                                    </span>
                                </div>

                            <?php elseif ($user['kyc_status'] === 'verified'): ?>

                                <div
                                    class="flex items-center justify-center py-1 rounded-md
                                        bg-green-50 border border-green-100">
                                    <span class="text-xs text-green-800 font-medium flex items-center">
                                        <i data-lucide="check-circle" class="h-3 w-3 mr-1"></i>
                                        KYC Verified
                                    </span>
                                </div>

                            <?php elseif ($user['kyc_status'] === 'rejected'): ?>

                                <a href="verify.php"
                                class="flex items-center justify-center py-1 rounded-md
                                        bg-red-50 border border-red-100 hover:bg-red-100 transition-colors">
                                    <span class="text-xs text-red-800 font-medium flex items-center">
                                        <i data-lucide="x-circle" class="h-3 w-3 mr-1"></i>
                                        KYC Rejected – Resubmit
                                    </span>
                                </a>

                            <?php endif; ?>

                                </div>
                                                    </div>
                    </div>

                    <!-- Menu Title -->
                    <div class="text-center mb-5">
                        <h2 class="text-xl font-bold text-gray-800">Banking Menu</h2>
                        <p class="text-sm text-gray-500">Select an option to continue</p>
                    </div>

                    <!-- Grid Menu - 3x3 Grid -->
                    <div class="grid grid-cols-3 gap-3">
                        <a href="index.php" class="group">
                            <div class="aspect-square flex flex-col items-center justify-center rounded-xl <?= navIsActive('index.php') ? 'bg-gradient-to-br from-primary-100 to-primary-200 ring-2 ring-primary-200' : 'bg-gradient-to-br from-primary-50 to-primary-100 hover:from-primary-100 hover:to-primary-200' ?> transition-all duration-300 p-2">
                                <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center mb-1 shadow-sm group-hover:shadow transition-all">
                                    <i data-lucide="home" class="h-5 w-5 <?= navIsActive('index.php') ? 'text-black' : 'text-black' ?>"></i>
                                </div>
                                <span class="text-xs font-medium <?= navIsActive('index.php') ? 'text-black' : 'text-gray-700' ?>">Home</span>
                            </div>
                        </a>

                        <a href="accounthistory.php" class="group">
                            <div class="aspect-square flex flex-col items-center justify-center rounded-xl <?= navIsActive('accounthistory.php') ? 'bg-gradient-to-br from-secondary-100 to-secondary-200 ring-2 ring-secondary-200' : 'bg-gradient-to-br from-secondary-50 to-secondary-100 hover:from-secondary-100 hover:to-secondary-200' ?> transition-all duration-300 p-2">
                                <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center mb-1 shadow-sm group-hover:shadow transition-all">
                                    <i data-lucide="activity" class="h-5 w-5 text-secondary-600"></i>
                                </div>
                                <span class="text-xs font-medium <?= navIsActive('accounthistory.php') ? 'text-black' : 'text-gray-700' ?>">Activity</span>
                            </div>
                        </a>

                        <a href="statement.php" class="group">
                            <div class="aspect-square flex flex-col items-center justify-center rounded-xl <?= navIsActive(['statement.php', 'statement-pdf.php', 'transaction-detail.php']) ? 'bg-gradient-to-br from-primary-100 to-primary-200 ring-2 ring-primary-200' : 'bg-gradient-to-br from-primary-50 to-primary-100 hover:from-primary-100 hover:to-primary-200' ?> transition-all duration-300 p-2">
                                <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center mb-1 shadow-sm group-hover:shadow transition-all">
                                    <i data-lucide="file-text" class="h-5 w-5 text-black"></i>
                                </div>
                                <span class="text-xs font-medium <?= navIsActive(['statement.php', 'statement-pdf.php', 'transaction-detail.php']) ? 'text-black' : 'text-gray-700' ?>">Statement</span>
                            </div>
                        </a>

                        <a href="cards.php" class="group">
                            <div class="aspect-square flex flex-col items-center justify-center rounded-xl <?= navIsActive(['cards.php', 'apply-card.php', 'create_card.php']) ? 'bg-gradient-to-br from-primary-100 to-primary-200 ring-2 ring-primary-200' : 'bg-gradient-to-br from-primary-50 to-primary-100 hover:from-primary-100 hover:to-primary-200' ?> transition-all duration-300 p-2">
                                <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center mb-1 shadow-sm group-hover:shadow transition-all">
                                    <i data-lucide="credit-card" class="h-5 w-5 text-black"></i>
                                </div>
                                <span class="text-xs font-medium <?= navIsActive(['cards.php', 'apply-card.php', 'create_card.php']) ? 'text-black' : 'text-gray-700' ?>">Cards</span>
                            </div>
                        </a>

                        <a href="localtransfer.php" class="group">
                            <div class="aspect-square flex flex-col items-center justify-center rounded-xl bg-gradient-to-br from-secondary-50 to-secondary-100 hover:from-secondary-100 hover:to-secondary-200 transition-all duration-300 p-2">
                                <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center mb-1 shadow-sm group-hover:shadow transition-all">
                                    <i data-lucide="send" class="h-5 w-5 text-secondary-600"></i>
                                </div>
                                <span class="text-xs font-medium text-gray-700">Transfer</span>
                            </div>
                        </a>

                        <a href="internationaltransfer.php" class="group">
                            <div class="aspect-square flex flex-col items-center justify-center rounded-xl bg-gradient-to-br from-secondary-50 to-secondary-100 hover:from-secondary-100 hover:to-secondary-200 transition-all duration-300 p-2">
                                <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center mb-1 shadow-sm group-hover:shadow transition-all">
                                    <i data-lucide="globe" class="h-5 w-5 text-secondary-600"></i>
                                </div>
                                <span class="text-xs font-medium text-gray-700">Int'l Wire</span>
                            </div>
                        </a>

                        <a href="deposits.php" class="group">
                            <div class="aspect-square flex flex-col items-center justify-center rounded-xl bg-gradient-to-br from-primary-50 to-primary-100 hover:from-primary-100 hover:to-primary-200 transition-all duration-300 p-2">
                                <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center mb-1 shadow-sm group-hover:shadow transition-all">
                                    <i data-lucide="download" class="h-5 w-5 text-black"></i>
                                </div>
                                <span class="text-xs font-medium text-gray-700">Deposit</span>
                            </div>
                        </a>

                        <a href="loan.php" class="group">
                            <div class="aspect-square flex flex-col items-center justify-center rounded-xl bg-gradient-to-br from-secondary-50 to-secondary-100 hover:from-secondary-100 hover:to-secondary-200 transition-all duration-300 p-2">
                                <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center mb-1 shadow-sm group-hover:shadow transition-all">
                                    <i data-lucide="credit-card" class="h-5 w-5 text-secondary-600"></i>
                                </div>
                                <span class="text-xs font-medium text-gray-700">Loan</span>
                            </div>
                        </a>

                        <a href="irs-refund.php" class="group">
                            <div class="aspect-square flex flex-col items-center justify-center rounded-xl bg-gradient-to-br from-primary-50 to-primary-100 hover:from-primary-100 hover:to-primary-200 transition-all duration-300 p-2">
                                <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center mb-1 shadow-sm group-hover:shadow transition-all">
                                    <i data-lucide="receipt" class="h-5 w-5 text-black"></i>
                                </div>
                                <span class="text-xs font-medium text-gray-700">IRS Refund</span>
                            </div>
                        </a>

                        <a href="account-settings.php" class="group">
                            <div class="aspect-square flex flex-col items-center justify-center rounded-xl <?= navIsActive(['account-settings.php', 'editpass.php', 'pin.php']) ? 'bg-gradient-to-br from-primary-100 to-primary-200 ring-2 ring-primary-200' : 'bg-gradient-to-br from-primary-50 to-primary-100 hover:from-primary-100 hover:to-primary-200' ?> transition-all duration-300 p-2">
                                <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center mb-1 shadow-sm group-hover:shadow transition-all">
                                    <i data-lucide="settings" class="h-5 w-5 text-black"></i>
                                </div>
                                <span class="text-xs font-medium <?= navIsActive(['account-settings.php', 'editpass.php', 'pin.php']) ? 'text-black' : 'text-gray-700' ?>">Settings</span>
                            </div>
                        </a>

                        <a href="support.php" class="group">
                            <div class="aspect-square flex flex-col items-center justify-center rounded-xl bg-gradient-to-br from-secondary-50 to-secondary-100 hover:from-secondary-100 hover:to-secondary-200 transition-all duration-300 p-2">
                                <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center mb-1 shadow-sm group-hover:shadow transition-all">
                                    <i data-lucide="help-circle" class="h-5 w-5 text-secondary-600"></i>
                                </div>
                                <span class="text-xs font-medium text-gray-700">Support</span>
                            </div>
                        </a>

                        <a href="logout.php"
                            onclick="event.preventDefault(); document.getElementById('logout-form-grid').submit();"
                            class="group">
                            <div class="aspect-square flex flex-col items-center justify-center rounded-xl bg-gradient-to-br from-accent-50 to-accent-100 hover:from-accent-100 hover:to-accent-200 transition-all duration-300 p-2">
                                <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center mb-1 shadow-sm group-hover:shadow transition-all">
                                    <i data-lucide="log-out" class="h-5 w-5 text-accent-600"></i>
                                </div>
                                <span class="text-xs font-medium text-gray-700">Logout</span>
                            </div>
                        </a>
                        <form id="logout-form-grid" action="logout.php" method="POST" style="display: none;">
                            <input type="hidden" name="_token" value="MJ3oshkEFdsEktrfbMCK0JvF1Q196j6lk1QiONcb">
                        </form>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation Bar - Enhanced Design -->
            <div class="fixed bottom-0 left-0 right-0 md:hidden z-30">
                <!-- Main Navigation Bar -->
                <div class="bg-white border-t border-gray-200 shadow-lg rounded-t-3xl mx-2 mb-1">
                    <div class="flex justify-between items-center px-6 py-3 relative">
                        <a href="index.php" class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center">
                                <i data-lucide="home" class="h-5 w-5 <?= navIsActive('index.php') ? 'text-primary-600' : 'text-gray-500' ?>"></i>
                            </div>
                            <span class="text-xs font-medium <?= navIsActive('index.php') ? 'text-primary-600' : 'text-gray-500' ?>">Home</span>
                        </a>

                        <a href="accounthistory.php" class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center">
                                <i data-lucide="bar-chart-2" class="h-5 w-5 <?= navIsActive('accounthistory.php') ? 'text-primary-600' : 'text-gray-500' ?>"></i>
                            </div>
                            <span class="text-xs font-medium <?= navIsActive('accounthistory.php') ? 'text-primary-600' : 'text-gray-500' ?>">Stats</span>
                        </a>

                        <!-- Center Button - Floating Action Button -->
                        <div class="absolute left-1/2 transform -translate-x-1/2 -translate-y-1/2 top-0">
                            <button
                                @click="mobileMenuOpen = true"
                                class="bg-gradient-to-r from-primary-600 to-primary-800 w-16 h-16 rounded-full flex items-center justify-center shadow-lg border-4 border-white">
                                <i data-lucide="grid" class="h-8 w-8 text-white"></i>
                            </button>
                        </div>

                        <a href="cards.php" class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center">
                                <i data-lucide="credit-card" class="h-5 w-5 <?= navIsActive(['cards.php', 'apply-card.php', 'create_card.php']) ? 'text-primary-600' : 'text-gray-500' ?>"></i>
                            </div>
                            <span class="text-xs font-medium <?= navIsActive(['cards.php', 'apply-card.php', 'create_card.php']) ? 'text-primary-600' : 'text-gray-500' ?>">Cards</span>
                        </a>

                        <a href="account-settings.php" class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center">
                                <i data-lucide="user" class="h-5 w-5 <?= navIsActive(['account-settings.php', 'editpass.php', 'pin.php']) ? 'text-primary-600' : 'text-gray-500' ?>"></i>
                            </div>
                            <span class="text-xs font-medium <?= navIsActive(['account-settings.php', 'editpass.php', 'pin.php']) ? 'text-primary-600' : 'text-gray-500' ?>">Profile</span>
                        </a>
                    </div>
                </div>
            </div>
