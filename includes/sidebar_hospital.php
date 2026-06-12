<?php // includes/sidebar_hospital.php ?>
<aside class="sidebar">
    <div style="padding:20px 20px 12px; border-bottom:1px solid var(--border);">
        <span class="section-label" style="font-size:0.65rem; color:var(--text-muted);">Hospital Portal</span>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="<?php echo SITE_URL; ?>/hospital/dashboard.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
        </li>
        <li class="sidebar-section-label">Inventory</li>
        <li>
            <a href="<?php echo SITE_URL; ?>/hospital/inventory.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'inventory.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-boxes-stacked"></i> Current Stock
            </a>
        </li>
        <li>
            <a href="<?php echo SITE_URL; ?>/hospital/expiring_units.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'expiring_units.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-clock-rotate-left"></i> Expiring Units
            </a>
        </li>
        <li class="sidebar-section-label">Donations</li>
        <li>
            <a href="<?php echo SITE_URL; ?>/hospital/confirm_donations.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'confirm_donations.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-circle-check"></i> Confirm Donations
            </a>
        </li>
        <li>
            <a href="<?php echo SITE_URL; ?>/hospital/donation_records.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'donation_records.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-file-medical"></i> Donation Records
            </a>
        </li>
        <li class="sidebar-section-label">Operations</li>
        <li>
            <a href="<?php echo SITE_URL; ?>/hospital/pending_requests.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'pending_requests.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-hand-holding-droplet"></i> Pending Requests
            </a>
        </li>
        <li>
            <a href="<?php echo SITE_URL; ?>/hospital/blood_issues.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'blood_issues.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-droplet"></i> Blood Issues
            </a>
        </li>
        <li class="sidebar-section-label">Staff</li>
        <li>
            <a href="<?php echo SITE_URL; ?>/hospital/staff.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'staff.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-user-nurse"></i> Manage Staff
            </a>
        </li>

        <li style="border-top:1px solid var(--border); margin-top:12px;">
            <a href="<?php echo SITE_URL; ?>/logout.php" style="color:var(--accent-red) !important;">
                <i class="fa-solid fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</aside>