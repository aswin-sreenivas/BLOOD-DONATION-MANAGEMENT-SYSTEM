<?php // includes/sidebar_donor.php ?>
<aside class="sidebar">
    <div style="padding:20px 20px 12px; border-bottom:1px solid var(--border);">
        <span class="section-label" style="font-size:0.65rem; color:var(--text-muted);">Donor Portal</span>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="<?php echo SITE_URL; ?>/donor/dashboard.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
        </li>
        <li class="sidebar-section-label">My Account</li>
        <li>
            <a href="<?php echo SITE_URL; ?>/donor/profile.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-id-card"></i> My Profile
            </a>
        </li>
        <li>
            <a href="<?php echo SITE_URL; ?>/donor/my_donations.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'my_donations.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-hand-holding-droplet"></i> My Donations
            </a>
        </li>
        <li class="sidebar-section-label">Alerts</li>
        <li>
            <a href="<?php echo SITE_URL; ?>/donor/emergency_matches.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'emergency_matches.php' ? 'active' : ''; ?>"
                style="color:var(--accent-red) !important;">
                <i class="fa-solid fa-triangle-exclamation"></i> Emergency Matches
            </a>
        </li>
        <li style="border-top:1px solid var(--border); margin-top:12px;">
            <a href="<?php echo SITE_URL; ?>/logout.php" style="color:var(--accent-red) !important;">
                <i class="fa-solid fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</aside>