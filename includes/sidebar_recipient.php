<?php // includes/sidebar_recipient.php ?>
<aside class="sidebar">
    <div style="padding:20px 20px 12px; border-bottom:1px solid var(--border);">
        <span class="section-label" style="font-size:0.65rem; color:var(--text-muted);">Recipient Portal</span>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="<?php echo SITE_URL; ?>/recipient/dashboard.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
        </li>
        <li class="sidebar-section-label">Blood Requests</li>
        <li>
            <a href="<?php echo SITE_URL; ?>/recipient/new_request.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'new_request.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-plus-circle"></i> New Request
            </a>
        </li>
        <li>
            <a href="<?php echo SITE_URL; ?>/recipient/my_requests.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'my_requests.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-clipboard-list"></i> My Requests
            </a>
        </li>
        <li class="sidebar-section-label">Account</li>
        <li>
            <a href="<?php echo SITE_URL; ?>/recipient/profile.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-id-card"></i> My Profile
            </a>
        </li>
        <li style="border-top:1px solid var(--border); margin-top:12px;">
            <a href="<?php echo SITE_URL; ?>/logout.php" style="color:var(--accent-red) !important;">
                <i class="fa-solid fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</aside>