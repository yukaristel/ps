<?php if (isset($_SESSION['user_id']) && $page != 'login'): ?>
        </div> <!-- Close content-wrapper -->
    </div> <!-- Close main-content -->
    
    <footer class="footer">
        <div class="footer-content">
            <p>&copy; <?php echo date('Y'); ?> Rental PlayStation. All rights reserved.</p>
            <p>Developed by <strong>PKL Team</strong></p>
        </div>
    </footer>
    <?php else: ?>
    </div> <!-- Close login-wrapper -->
    <?php endif; ?>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Auto hide alert after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('[style*="background-color"]');
            alerts.forEach(alert => {
                if (alert.textContent.includes('Success') || 
                    alert.textContent.includes('Error') || 
                    alert.textContent.includes('Warning')) {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }
            });
        }, 5000);

        // Confirm delete
        function confirmDelete(message) {
            return confirm(message || 'Yakin ingin menghapus data ini?');
        }

        // Format rupiah input
        function formatRupiahInput(input) {
            let value = input.value.replace(/[^,\d]/g, '');
            let split = value.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            input.value = rupiah;
        }

        // Print function
        function printDiv(divId) {
            const content = document.getElementById(divId).innerHTML;
            const printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write('<html><head><title>Print</title>');
            printWindow.document.write('<style>body{font-family:Arial;padding:20px;}</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write(content);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }
    </script>
</body>
</html>