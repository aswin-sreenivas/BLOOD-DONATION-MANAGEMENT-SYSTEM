<?php // includes/sidebar_admin.php ?>
<aside class="sidebar">
    <!-- Sidebar Brand Sub-header -->
    <div style="padding:20px 20px 12px; border-bottom:1px solid var(--border);">
        <span class="section-label" style="font-size:0.65rem; color:var(--text-muted);">Admin Control Panel</span>
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="<?php echo SITE_URL; ?>/admin/dashboard.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
        </li>

        <li class="sidebar-section-label">User Management</li>

        <li>
            <a href="<?php echo SITE_URL; ?>/admin/manage_donors.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_donors.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-heart"></i> Donors
            </a>
        </li>
        <li>
            <a href="<?php echo SITE_URL; ?>/admin/manage_recipients.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_recipients.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-bed-pulse"></i> Recipients
            </a>
        </li>
        <li>
            <a href="<?php echo SITE_URL; ?>/admin/manage_hospitals.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_hospitals.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-hospital"></i> Hospitals
            </a>
        </li>
        <li>
            <a href="<?php echo SITE_URL; ?>/admin/manage_staff.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_staff.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-user-nurse"></i> Staff
            </a>
        </li>

        <li class="sidebar-section-label">Operations</li>

        <li>
            <a href="<?php echo SITE_URL; ?>/admin/all_requests.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'all_requests.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-clipboard-list"></i> Blood Requests
            </a>
        </li>
        <li>
            <a href="<?php echo SITE_URL; ?>/admin/all_donations.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'all_donations.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-hand-holding-droplet"></i> Donations
            </a>
        </li>
        <li>
            <a href="<?php echo SITE_URL; ?>/admin/global_inventory.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'global_inventory.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-boxes-stacked"></i> Global Inventory
            </a>
        </li>

        <li class="sidebar-section-label">System</li>

        <li>
            <a href="<?php echo SITE_URL; ?>/admin/reports.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-chart-column"></i> Reports
            </a>
        </li>

        <li style="border-top:1px solid var(--border); margin-top:12px;">
            <a href="<?php echo SITE_URL; ?>/logout.php" style="color:var(--accent-red) !important;">
                <i class="fa-solid fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</aside>