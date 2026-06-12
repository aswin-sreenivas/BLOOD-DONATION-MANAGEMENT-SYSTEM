<?php // includes/sidebar_staff.php ?>
<aside class="sidebar">
    <div style="padding:20px 20px 12px; border-bottom:1px solid var(--border);">
        <span class="section-label" style="font-size:0.65rem; color:var(--text-muted);">Staff Portal</span>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="<?php echo SITE_URL; ?>/staff/dashboard.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
        </li>
        <li class="sidebar-section-label">Daily Tasks</li>
        <li>
            <a href="<?php echo SITE_URL; ?>/staff/process_donation.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'process_donation.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-heart-pulse"></i> Process Donations
            </a>
        </li>
        <li>
            <a href="<?php echo SITE_URL; ?>/staff/issue_units.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'issue_units.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-droplet"></i> Issue Blood Units
            </a>
        </li>

        <li style="border-top:1px solid var(--border); margin-top:12px;">
            <a href="<?php echo SITE_URL; ?>/logout.php" style="color:var(--accent-red) !important;">
                <i class="fa-solid fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</aside>