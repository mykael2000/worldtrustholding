<?php
include("header.php");

$deposit = $_SESSION['deposit_confirm'] ?? null;

if (!$deposit) {
    header("Location: deposits.php");
    exit;
}
?>
  <!-- Main Content -->
            <main class="flex-1 overflow-y-auto pb-16 md:pb-0">
                <div class="py-6">
                    <div class="max-w-8xl mx-auto px-4 sm:px-6 md:px-8">
<div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow mt-8">
    <h2 class="text-xl font-bold mb-4">Complete Your Deposit</h2>

    <p class="mb-2">
        Transaction ID: <strong><?= htmlspecialchars($deposit['tranx_id']) ?></strong>
    </p>

    <p class="mb-4">
        Send <strong>$<?= number_format($deposit['amount'], 2) ?></strong>
        via <strong><?= htmlspecialchars($deposit['method']) ?></strong>
    </p>

    <div class="bg-gray-100 p-4 rounded-lg mb-4">
    <p><strong>Currency:</strong> <?= htmlspecialchars($deposit['currency']) ?></p>
    <p><strong>Network:</strong> <?= htmlspecialchars($deposit['network']) ?></p>

    <p class="mt-3 font-medium">Deposit Address:</p>
    <div class="bg-white p-2 rounded border mb-3">
        <code class="text-sm break-all"><?= htmlspecialchars($deposit['address']) ?></code>
    </div>

    <button
        onclick="navigator.clipboard.writeText('<?= $deposit['address'] ?>')"
        class="mb-3 text-primary-600 text-sm underline"
    >
        Copy Address
    </button>

    <?php if (in_array($deposit['method'], ['Bitcoin', 'USDT'])): ?>
        <div class="mt-4 text-center">
            <p class="text-sm font-medium mb-2">Scan QR Code</p>
            <div id="qrcode" class="inline-block p-3 bg-white rounded-lg border"></div>
        </div>
    <?php endif; ?>
</div>


    <p class="text-sm text-gray-500">
        Your balance will be credited after confirmation.
    </p>

    <a href="accounthistory.php"
       class="inline-block mt-4 px-4 py-2 bg-primary-600 text-white rounded">
        View Transaction
    </a>
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    <?php if (in_array($deposit['method'], ['Bitcoin', 'USDT'])): ?>
        new QRCode(document.getElementById("qrcode"), {
            text: "<?= $deposit['address'] ?>",
            width: 180,
            height: 180,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    <?php endif; ?>
});
</script>

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<?php include("footer.php"); ?>
