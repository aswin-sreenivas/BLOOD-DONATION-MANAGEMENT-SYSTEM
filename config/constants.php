<?php
// config/constants.php
// Global constants for the Online Blood Donation Management System

// Define the root URL of your project dynamically based on current server
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$current_path = $_SERVER['SCRIPT_NAME']; // SCRIPT_NAME is more reliable than PHP_SELF
$base_dir = dirname($current_path);

// Normalize directory separators
$base_dir = str_replace('\\', '/', $base_dir);

// Remove specific module directories if accessed directly
$modules = ['/admin', '/donor', '/hospital', '/recipient', '/staff', '/includes', '/config', '/assets'];
foreach ($modules as $module) {
    if (str_ends_with($base_dir, $module)) {
        $base_dir = substr($base_dir, 0, -strlen($module));
    }
}

// If base_dir is just '/', remove it to avoid double slashes
if ($base_dir === '/') {
    $base_dir = '';
}

$site_url = rtrim($protocol . "://" . $host . $base_dir, '/');
define('SITE_URL', $site_url);

define('SITE_NAME', 'LifeDrop | Online Blood Donation Management System');

define('ADMIN_ROLE', 'Admin');
define('DONOR_ROLE', 'Donor');
define('RECIPIENT_ROLE', 'Recipient');
define('HOSPITAL_ROLE', 'Hospital');
define('STAFF_ROLE', 'Staff');
?>