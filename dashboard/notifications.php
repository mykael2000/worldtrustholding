<?php include("header.php"); ?>
            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto pb-16 md:pb-0">
                <div class="py-6">
                    <div class="max-w-8xl mx-auto px-4 sm:px-6 md:px-8">
                        <!-- Breadcrumbs + Page Title -->
<div class="mb-6">
    <div class="flex items-center justify-between">
        <!-- Action Buttons -->
        <div class="flex space-x-3">
            <form action="https://worldtrustholding.com/dashboard/notifications/mark-all-read" method="POST">
                <input type="hidden" name="_token" value="MJ3oshkEFdsEktrfbMCK0JvF1Q196j6lk1QiONcb">                <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i data-lucide="check-circle" class="h-4 w-4 mr-2"></i> Mark All as Read
                </button>
            </form>
            <form action="https://worldtrustholding.com/dashboard/notifications" method="POST" onsubmit="return confirm('Are you sure you want to delete all notifications?')">
                <input type="hidden" name="_token" value="MJ3oshkEFdsEktrfbMCK0JvF1Q196j6lk1QiONcb">                <input type="hidden" name="_method" value="DELETE">                <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <i data-lucide="trash-2" class="h-4 w-4 mr-2"></i> Delete All
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Alerts -->
<div>
    </div>
<!-- Notifications List -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
    <div class="border-b border-gray-200 px-6 py-4">
        <h3 class="text-lg font-medium text-gray-900">All Notifications</h3>
    </div>

            <div class="py-12 flex flex-col items-center justify-center text-center px-6">
            <div class="bg-gray-50 rounded-full p-3 mb-4">
                <i data-lucide="inbox" class="h-8 w-8 text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900">No Notifications</h3>
            <p class="text-gray-500 text-sm mt-2 max-w-md">
                You don't have any notifications yet. Notifications will appear here when there are updates related to your account.
            </p>
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