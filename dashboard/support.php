<?php include("header.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $subject  = trim($_POST['subject'] ?? '');
    $priority = $_POST['selectPriority'] ?? 'low';
    $message  = trim($_POST['message'] ?? '');
    $email    = $_POST['email'] ?? '';
    $name     = $_POST['name'] ?? '';

    if ($subject === '' || $message === '') {
        $_SESSION['error'] = "All fields are required.";
    } else {

        $stmt = $conn->prepare("
            INSERT INTO support_tickets 
            (user_id, name, email, subject, priority, message) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "isssss",
            $user_id,
            $name,
            $email,
            $subject,
            $priority,
            $message
        );

        if ($stmt->execute()) {
            $_SESSION['success'] = "Your support ticket has been submitted successfully.";
        } else {
            $_SESSION['error'] = "Unable to submit ticket. Please try again.";
        }

        $stmt->close();
    }

    header("Location: support.php");
    exit;
}

?>
            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto pb-16 md:pb-0">
                <div class="py-6">
                    <div class="max-w-8xl mx-auto px-4 sm:px-6 md:px-8">
                        
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <!-- Alerts -->
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

    <!-- Page Header with Breadcrumbs -->
    <div class="flex flex-col mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Support Center</h1>
            <div class="flex items-center text-sm text-gray-500">
                <a href="index.php" class="hover:text-primary-600">Dashboard</a>
                <i data-lucide="chevron-right" class="h-4 w-4 mx-2"></i>
                <span class="font-medium text-gray-700">Support</span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <!-- Content Header -->
        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                <i data-lucide="life-buoy" class="h-5 w-5 mr-2 text-primary-600"></i>
                Submit a Support Ticket
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                We're here to help. Tell us about your issue and we'll find a solution.
            </p>
        </div>
        
        <!-- Support Icon -->
        <div class="flex justify-center py-8">
            <div class="h-24 w-24 rounded-full bg-primary-50 flex items-center justify-center">
                <i data-lucide="message-circle-question" class="h-12 w-12 text-gray-600"></i>
            </div>
        </div>
        
        <!-- Form Content -->
        <div class="p-6">
            <form action="" method="post" class="space-y-6">
                              
                <!-- Hidden Fields -->
                <input type="hidden" name="email" value="<?php echo $user['email']; ?>">
                <input type="hidden" name="name" value="<?php echo $user['firstname']; ?>" />
                
                <!-- Ticket Title -->
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">
                        Ticket Title
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="bookmark" class="h-5 w-5 text-gray-400"></i>
                        </div>
                        <input 
                            type="text" 
                            id="subject" 
                            name="subject"
                            class="block w-full pl-10 py-3 border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                            placeholder="Briefly describe your issue"
                            required
                        />
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Be specific to help us understand your issue</p>
                </div>
                
                <!-- Priority Selection -->
                <div>
                    <label for="selectPriority" class="block text-sm font-medium text-gray-700 mb-1">
                        Priority Level
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="flag" class="h-5 w-5 text-gray-400"></i>
                        </div>
                        <select 
                            id="selectPriority" 
                            name="selectPriority"
                            class="block w-full pl-10 py-3 border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all appearance-none"
                            required
                        >
                            <option value="low">Low Priority</option>
                            <option value="medium">Medium Priority</option>
                            <option value="high">High Priority</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i data-lucide="chevron-down" class="h-5 w-5 text-gray-400"></i>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Select based on urgency of your request</p>
                </div>
                
                <!-- Message Content -->
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">
                        Describe Your Issue
                    </label>
                    <div class="relative">
                        <div class="absolute top-3 left-3 flex items-start pointer-events-none">
                            <i data-lucide="message-square-text" class="h-5 w-5 text-gray-400"></i>
                        </div>
                        <textarea 
                            id="message" 
                            name="message"
                            rows="6"
                            class="block w-full pl-10 py-3 border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all preserveLines"
                            placeholder="Please provide all relevant details about your issue so we can help you better"
                            required
                        ></textarea>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Include any relevant details that might help us resolve your issue</p>
                </div>
                
                <!-- Support Info Card -->
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i data-lucide="info" class="h-5 w-5 text-blue-500"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Support Information</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>
                                    Our support team typically responds within 24 hours. For urgent matters, 
                                    please select "High Priority" and we'll do our best to assist you sooner.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="pt-3">
                    <button 
                        type="submit"
                        class="w-full md:w-auto px-6 py-3 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors flex items-center justify-center"
                    >
                        <i data-lucide="send" class="h-5 w-5 mr-2"></i>
                        Submit Ticket
                    </button>
                </div>
            </form>
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