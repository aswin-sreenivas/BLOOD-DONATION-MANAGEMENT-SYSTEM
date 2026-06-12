<?php
// includes/footer.php
$current_page = basename($_SERVER['PHP_SELF']);
$is_landing = ($current_page === 'index.php' && !isset($_SESSION['user_id']));
?>

<?php if (!$is_landing): ?>
    </main> <!-- End .main-content -->
    </div> <!-- End .wrapper -->
    <footer class="site-footer">
        <p>&copy; <?php echo date('Y'); ?> LifeDrop | Online Blood Donation Management System &mdash; GPTC MANANTHAVADY</p>
        <p style="margin-top:4px; font-size:0.78rem; color: var(--primary);">
            <i class="fa-solid fa-circle" style="font-size:7px;"></i> System Online
        </p>
    </footer>
<?php endif; ?>

<!-- Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Notification bell click
        const notifTrigger = document.querySelector('.notification-trigger');
        if (notifTrigger) {
            notifTrigger.addEventListener('click', function () {
                alert('Notifications: No new notifications at this time.');
            });
        }

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    });
</script>
</body>

</html>