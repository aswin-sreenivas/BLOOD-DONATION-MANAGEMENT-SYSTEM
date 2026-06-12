<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once dirname(__DIR__) . '/config/constants.php';
require_once dirname(__DIR__) . '/config/database.php';

// Quick auth guard — public pages exempted
$current_page = basename($_SERVER['PHP_SELF']);
$public_pages = ['index.php', 'register.php', 'login_action.php', 'login.php'];

if (!in_array($current_page, $public_pages)) {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        header("Location: " . SITE_URL . "/index.php?error=unauthorized");
        exit();
    }
}

// Detect if this is the landing page (special full-width layout)
$is_landing = ($current_page === 'index.php' && !isset($_SESSION['user_id']));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="LifeDrop – Online Blood Donation Management System for GPTC MANANTHAVADY. Connect donors, recipients, hospitals, and staff on one unified platform.">
    <title>
        <?php echo defined('PAGE_TITLE') ? PAGE_TITLE . ' | ' : ''; ?>
        <?php echo SITE_NAME; ?>
    </title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Main CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>

<body class="<?php echo $is_landing ? 'landing-body' : ''; ?>">

    <?php if ($is_landing): ?>
        <!-- ── LANDING PAGE NAVBAR ────────────────────── -->
        <nav class="hero-nav">
            <a href="<?php echo SITE_URL; ?>" class="topbar-brand">
                <div class="brand-icon"><i class="fa-solid fa-droplet"></i></div>
                Life<span class="brand-drop">Drop</span>
            </a>
            <div class="hero-nav-links">
                <a href="#features">Features</a>
                <a href="#how-it-works">How It Works</a>
                <a href="#blood-types">Blood Types</a>
            </div>
            <div style="display:flex; gap:10px; align-items:center;">
                <a href="login.php" class="topbar-nav-link">Login</a>
                <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-primary">
                    <i class="fa-solid fa-heart-pulse"></i> Register
                </a>
            </div>
        </nav>

        <!-- Emergency Broadcast Banner -->
        <div id="global-emergency-banner" class="emergency-banner">
            🚨 EMERGENCY BLOOD REQUEST ACTIVE — PLEASE CHECK YOUR NOTIFICATIONS 🚨
        </div>

        <!-- Landing page sections are rendered directly in index.php without .main-content wrapper -->

    <?php else: ?>
        <!-- ── APP TOPBAR (for authenticated pages) ───── -->
        <div id="global-emergency-banner" class="emergency-banner">
            🚨 EMERGENCY BLOOD REQUEST ACTIVE — MATCHES YOUR CITY / BLOOD GROUP 🚨
        </div>

        <header class="topbar">
            <a href="<?php echo SITE_URL; ?>" class="topbar-brand">
                <div class="brand-icon"><i class="fa-solid fa-droplet"></i></div>
                Life<span class="brand-drop">Drop</span>
            </a>

            <div class="topbar-right">
                <?php if (isset($_SESSION['user_id'])): ?>

                    <!-- Notification Bell -->
                    <div class="notif-btn notification-trigger" title="Notifications">
                        <i class="fa-solid fa-bell"></i>
                        <?php
                        try {
                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM Notification WHERE User_ID = ? AND User_Type = ? AND Is_Read = FALSE");
                            $stmt->execute([$_SESSION['user_id'], $_SESSION['role']]);
                            $unread = $stmt->fetchColumn();
                            if ($unread > 0) {
                                echo "<span class='notif-badge'>$unread</span>";
                            }
                        } catch (PDOException $e) { /* silent */
                        }
                        ?>
                    </div>

                    <!-- User Chip -->
                    <div class="user-chip">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($_SESSION['user_name'] ?? $_SESSION['role'], 0, 1)); ?>
                        </div>
                        <span><?php echo htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['role']); ?></span>
                        <span class="badge badge-success" style="font-size:0.68rem;">
                            <?php echo $_SESSION['role']; ?>
                        </span>
                    </div>

                    <a href="<?php echo SITE_URL; ?>/logout.php" class="btn btn-danger btn-sm">
                        <i class="fa-solid fa-sign-out-alt"></i> Logout
                    </a>

                <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>/login.php" class="topbar-nav-link">Login</a>
                    <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-primary btn-sm">Register</a>
                <?php endif; ?>
            </div>
        </header>

        <div class="wrapper">
            <?php
            if (isset($_SESSION['role'])) {
                $role_lower = strtolower($_SESSION['role']);
                $sidebar_path = dirname(__DIR__) . "/includes/sidebar_{$role_lower}.php";
                if (file_exists($sidebar_path)) {
                    include $sidebar_path;
                    echo '<main class="main-content main-content-with-sidebar">';
                } else {
                    echo '<main class="main-content">';
                }
            } else {
                echo '<main class="main-content">';
            }
            ?>

        <?php endif; ?>