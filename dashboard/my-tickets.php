<?php
include("header.php");

$stmt = $conn->prepare("
    SELECT id, subject, priority, status, created_at
    FROM support_tickets
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$tickets = $stmt->get_result();
?>
<main class="flex-1 overflow-y-auto pb-16 md:pb-0">
    <div class="py-6 max-w-7xl mx-auto px-4">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">My Support Tickets</h1>
            <p class="text-sm text-gray-500">Track your support requests</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <!-- <th class="px-6 py-3"></th> -->
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                <?php if ($tickets->num_rows === 0): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-6 text-center text-gray-500">
                            You have not submitted any tickets yet.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php while ($row = $tickets->fetch_assoc()): ?>
                    <?php
                        $badge = match($row['status']) {
                            'Open' => 'yellow',
                            'In Progress' => 'blue',
                            'Resolved', 'Closed' => 'green',
                            default => 'gray'
                        };
                    ?>
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            <?= htmlspecialchars($row['subject']) ?>
                        </td>

                        <td class="px-6 py-4 text-sm capitalize">
                            <?= htmlspecialchars($row['priority']) ?>
                        </td>

                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs bg-<?= $badge ?>-100 text-<?= $badge ?>-800">
                                <?= htmlspecialchars($row['status']) ?>
                            </span>
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?= date('M d, Y', strtotime($row['created_at'])) ?>
                        </td>

                        <!-- <td class="px-6 py-4 text-right">
                            <a href="view-ticket.php?id=<?= $row['id'] ?>" class="text-primary-600 hover:underline text-sm">
                                View
                            </a>
                        </td> -->
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
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