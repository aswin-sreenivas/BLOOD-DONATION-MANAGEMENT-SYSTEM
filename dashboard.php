<?php
// index.php — LifeDrop Landing Page
require_once 'includes/header.php';

// Redirect logged-in users to their dashboard
if (isset($_SESSION['user_id'])) {
    $role = strtolower($_SESSION['role']);
    header("Location: {$role}/dashboard.php");
    exit();
}
?>

<style>
    /* ── Hero ─────────────────────────────────────────────── */
    .hero-section {
        min-height: 80vh;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        background: linear-gradient(145deg, #f0fdf9 0%, #fff5f6 50%, #f0f7ff 100%);
        position: relative;
        overflow: hidden;
        padding: var(--topbar-h) 40px 40px;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: -120px;
        right: -120px;
        width: 550px;
        height: 550px;
        background: radial-gradient(circle, rgba(0, 184, 148, 0.12) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .hero-section::after {
        content: '';
        position: absolute;
        bottom: -80px;
        left: -80px;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(230, 57, 70, 0.08) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .hero-main {
        max-width: 1440px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        align-items: center;
        position: relative;
        z-index: 1;
        padding-top: 0;
    }

    /* Left column */
    .hero-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: white;
        border: 1.5px solid var(--border);
        border-radius: 40px;
        padding: 7px 16px;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--primary);
        margin-bottom: 28px;
        box-shadow: var(--shadow-xs);
    }

    .hero-pill .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--primary);
        animation: pulse-dot 2s ease-in-out infinite;
    }

    @keyframes pulse-dot {

        0%,
        100% {
            opacity: 1;
            transform: scale(1);
        }

        50% {
            opacity: 0.5;
            transform: scale(1.4);
        }
    }

    .hero-title {
        font-size: clamp(2.4rem, 4vw, 3.5rem);
        font-weight: 900;
        line-height: 1.12;
        letter-spacing: -0.03em;
        color: var(--text-dark);
        margin-bottom: 22px;
    }

    .hero-title .highlight {
        background: linear-gradient(135deg, var(--primary), #00d4aa);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .hero-title .red-word {
        color: var(--accent-red);
    }

    .hero-subtitle {
        font-size: 1.05rem;
        color: var(--text-muted);
        line-height: 1.75;
        max-width: 480px;
        margin-bottom: 36px;
    }

    .hero-cta-row {
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 44px;
    }

    /* Live Stats Bar */
    .hero-stats-bar {
        display: flex;
        gap: 0;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-md);
        background: white;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .hero-stat-item {
        flex: 1;
        padding: 14px 18px;
        text-align: center;
        border-right: 1px solid var(--border);
        transition: var(--transition);
    }

    .hero-stat-item:last-child {
        border-right: none;
    }

    .hero-stat-item:hover {
        background: var(--primary-glow);
    }

    .hero-stat-item strong {
        display: block;
        font-size: 1.5rem;
        font-weight: 900;
        color: var(--text-dark);
        font-family: 'Poppins', sans-serif;
        line-height: 1.2;
    }

    .hero-stat-item span {
        font-size: 0.72rem;
        color: var(--text-muted);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    /* Right column – Visual */
    .hero-visual-col {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .hero-blob {
        position: absolute;
        width: 380px;
        height: 380px;
        background: linear-gradient(135deg, rgba(0, 184, 148, 0.15), rgba(0, 212, 170, 0.08));
        border-radius: 60% 40% 55% 45% / 50% 60% 40% 50%;
        animation: blobMorph 8s ease-in-out infinite;
        z-index: 0;
    }

    @keyframes blobMorph {

        0%,
        100% {
            border-radius: 60% 40% 55% 45% / 50% 60% 40% 50%;
        }

        33% {
            border-radius: 45% 55% 40% 60% / 60% 40% 55% 45%;
        }

        66% {
            border-radius: 55% 45% 60% 40% / 45% 55% 45% 55%;
        }
    }

    .hero-cards-stack {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        gap: 14px;
        width: 340px;
    }

    .hero-info-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: var(--radius-md);
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: var(--shadow-md);
        transition: var(--transition);
    }

    .hero-info-card:hover {
        transform: translateX(6px);
        box-shadow: var(--shadow-lg);
    }

    .hero-info-card .card-icon {
        width: 42px;
        height: 42px;
        flex-shrink: 0;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .hero-info-card strong {
        display: block;
        font-size: 0.9rem;
        color: var(--text-dark);
        font-weight: 700;
    }

    .hero-info-card small {
        font-size: 0.76rem;
        color: var(--text-muted);
    }

    /* Blood type live badge (floating) */
    .hero-badge-float {
        position: absolute;
        background: white;
        border-radius: var(--radius-md);
        padding: 10px 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.78rem;
        font-weight: 700;
        box-shadow: var(--shadow-md);
        border: 1.5px solid var(--border);
        z-index: 3;
        animation: floatBadge 4s ease-in-out infinite;
    }

    .hero-badge-float.top {
        top: -20px;
        right: -10px;
    }

    .hero-badge-float.bottom {
        bottom: -20px;
        left: -20px;
        animation-delay: 2s;
    }

    @keyframes floatBadge {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-8px);
        }
    }

    /* ── Sections – reused ─────────────────────────────── */
    .section-inner {
        max-width: 1440px;
        margin: 0 auto;
    }

    .section-heading {
        text-align: center;
        margin-bottom: 52px;
    }

    .section-label {
        display: inline-block;
        background: var(--primary-glow);
        color: var(--primary);
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-bottom: 14px;
    }

    .section-heading h2 {
        font-size: 2.1rem;
        font-weight: 800;
        margin-bottom: 12px;
    }

    .section-heading p {
        color: var(--text-muted);
        max-width: 560px;
        margin: 0 auto;
        font-size: 0.97rem;
    }

    /* ── Login Section ─────────────────────────────────── */
    .login-section {
        background: linear-gradient(135deg, #e8faf6 0%, #f7f8fc 100%);
        padding: 90px 40px;
    }

    .login-inner {
        max-width: 1100px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        align-items: center;
    }

    .auth-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: 38px;
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border);
    }

    .auth-logo {
        text-align: center;
        margin-bottom: 28px;
    }

    .logo-icon-big {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.6rem;
        margin: 0 auto 14px;
        box-shadow: var(--shadow-primary);
    }

    .auth-logo h2 {
        font-size: 1.4rem;
        font-weight: 800;
    }

    .auth-logo p {
        color: var(--text-muted);
        font-size: 0.88rem;
    }

    /* Role info pills */
    .role-info-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        background: white;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-xs);
        border: 1px solid var(--border);
        transition: var(--transition);
    }

    .role-info-card:hover {
        transform: translateX(4px);
        box-shadow: var(--shadow-sm);
    }

    /* ── Features ───────────────────────────────────────── */
    .features-section {
        padding: 90px 40px;
        background: white;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }

    .feature-card {
        padding: 28px 24px;
        border-radius: var(--radius-md);
        border: 1.5px solid var(--border);
        transition: var(--transition);
        background: white;
    }

    .feature-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
        border-color: var(--primary);
    }

    .feature-card .feat-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        margin-bottom: 16px;
    }

    .feature-card h3 {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .feature-card p {
        font-size: 0.85rem;
        color: var(--text-muted);
        line-height: 1.6;
    }

    /* ── How it Works ───────────────────────────────────── */
    .how-section {
        padding: 90px 40px;
        background: var(--bg-page);
    }

    .steps-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        position: relative;
    }

    .steps-row::before {
        content: '';
        position: absolute;
        top: 42px;
        left: calc(50% / 3 + 0px);
        right: calc(50% / 3 + 0px);
        height: 3px;
        background: linear-gradient(90deg, var(--primary), var(--accent-red));
        z-index: 0;
        border-radius: 2px;
    }

    .step-card {
        text-align: center;
        padding: 36px 24px 28px;
        background: white;
        border-radius: var(--radius-lg);
        border: 1.5px solid var(--border);
        position: relative;
        z-index: 1;
        transition: var(--transition);
    }

    .step-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
    }

    .step-num {
        width: 52px;
        height: 52px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 1.3rem;
        margin: 0 auto 20px;
        box-shadow: var(--shadow-primary);
    }

    .step-card h3 {
        font-size: 1.05rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .step-card p {
        font-size: 0.85rem;
        color: var(--text-muted);
        line-height: 1.65;
    }

    /* ── Blood Types ───────────────────────────────────── */
    .blood-section {
        padding: 90px 40px;
        background: white;
    }

    .blood-types-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 30px;
    }

    .blood-type-card {
        text-align: center;
        padding: 30px 20px;
        border-radius: var(--radius-md);
        border: 2px solid var(--border);
        cursor: pointer;
        transition: var(--transition);
        background: white;
    }

    .blood-type-card:hover {
        border-color: var(--accent-red);
        background: var(--accent-red-light);
        transform: scale(1.04);
        box-shadow: 0 8px 24px rgba(230, 57, 70, 0.15);
    }

    .bt-label {
        font-size: 2.2rem;
        font-weight: 900;
        color: var(--accent-red);
        font-family: 'Poppins', sans-serif;
        line-height: 1;
        margin-bottom: 6px;
    }

    .bt-sub {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-weight: 500;
        text-transform: uppercase;
    }

    /* ── Roles ─────────────────────────────────────────── */
    .roles-section {
        padding: 90px 40px;
        background: var(--bg-page);
    }

    /* ── CTA ───────────────────────────────────────────── */
    .cta-section {
        padding: 90px 40px;
        background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 50%, #00d4aa 100%);
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .cta-section::before {
        content: '';
        position: absolute;
        top: -50px;
        left: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.06);
        border-radius: 50%;
    }

    .cta-section::after {
        content: '';
        position: absolute;
        bottom: -70px;
        right: -30px;
        width: 280px;
        height: 280px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }

    .cta-inner {
        max-width: 700px;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }

    .cta-inner h2 {
        font-size: 2.4rem;
        font-weight: 900;
        color: white;
        margin-bottom: 16px;
    }

    .cta-inner p {
        color: rgba(255, 255, 255, 0.85);
        font-size: 1.05rem;
        margin-bottom: 36px;
        line-height: 1.7;
    }

    .cta-btn-row {
        display: flex;
        gap: 14px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-cta-white {
        background: white;
        color: var(--primary);
        font-weight: 700;
        padding: 14px 30px;
        border-radius: var(--radius-md);
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
        box-shadow: var(--shadow-md);
    }

    .btn-cta-white:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        color: var(--primary-dark);
    }

    .btn-cta-outline {
        background: rgba(255, 255, 255, 0.12);
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.5);
        font-weight: 700;
        padding: 14px 30px;
        border-radius: var(--radius-md);
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
        backdrop-filter: blur(4px);
    }

    .btn-cta-outline:hover {
        background: rgba(255, 255, 255, 0.22);
        color: white;
        transform: translateY(-2px);
    }

    /* ── Footer ─────────────────────────────────────────── */
    .landing-footer {
        background: #0f1923;
        color: rgba(255, 255, 255, 0.65);
        padding: 60px 40px 0;
    }

    .footer-inner {
        max-width: 1440px;
        margin: 0 auto;
    }

    .footer-top {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr;
        gap: 40px;
        padding-bottom: 50px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .footer-brand-col .brand-name {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.3rem;
        font-weight: 800;
        color: white;
        font-family: 'Poppins', sans-serif;
        margin-bottom: 14px;
    }

    .footer-brand-col p {
        font-size: 0.85rem;
        line-height: 1.7;
    }

    .footer-col h4 {
        color: white;
        font-size: 0.9rem;
        font-weight: 700;
        margin-bottom: 16px;
    }

    .footer-col a {
        display: block;
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.55);
        margin-bottom: 10px;
        transition: var(--transition);
    }

    .footer-col a:hover {
        color: var(--primary);
    }

    .footer-bottom {
        padding: 20px 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.82rem;
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(40px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive */
    @media (max-width: 900px) {

        .hero-main,
        .login-inner {
            grid-template-columns: 1fr;
        }

        .hero-visual-col {
            display: none;
        }

        .features-grid,
        .steps-row {
            grid-template-columns: 1fr 1fr;
        }

        .blood-types-grid {
            grid-template-columns: repeat(4, 1fr);
        }

        .footer-top {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 600px) {

        .features-grid,
        .steps-row {
            grid-template-columns: 1fr;
        }

        .hero-section {
            padding-left: 20px;
            padding-right: 20px;
        }

        .blood-types-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }
</style>

<!-- ═══════════════════════════════════════════════
     SECTION 1 — HERO (enhanced)
═══════════════════════════════════════════════ -->
<section class="hero-section">
    <div class="hero-main">

        <!-- Left: Text Content -->
        <div class="hero-text-col" style="animation: fadeInUp 0.7s ease both;">
            <div class="hero-pill">
                <span class="dot"></span>
                GPTC MANANTHAVADY · Computer Engineering · 
            </div>

            <h1 class="hero-title">
                Every Drop<br>
                <span class="highlight">Saves a Life</span> —<br>
                Donate <span class="red-word">Blood</span> Today
            </h1>

            <p class="hero-subtitle">
                LifeDrop is Wayanad's unified blood donation platform — connecting <strong>donors</strong>,
                <strong>recipients</strong>, <strong>hospitals</strong> and <strong>staff</strong> in real-time so that
                no emergency goes unanswered.
            </p>

            <div class="hero-cta-row">
                <a href="register.php" class="btn btn-primary btn-lg">
                    <i class="fa-solid fa-heart-pulse"></i> Become a Donor
                </a>
                <a href="login.php" class="btn btn-outline btn-lg">
                    <i class="fa-solid fa-right-to-bracket"></i> Sign In
                </a>
            </div>

            <!-- Live Stats Bar -->
            <div class="hero-stats-bar">
                <div class="hero-stat-item">
                    <strong id="count-roles">5</strong>
                    <span>User Roles</span>
                </div>
                <div class="hero-stat-item">
                    <strong id="count-types">8+</strong>
                    <span>Blood Types</span>
                </div>
                <div class="hero-stat-item">
                    <strong>24/7</strong>
                    <span>Emergency Alerts</span>
                </div>
                <div class="hero-stat-item">
                    <strong>100%</strong>
                    <span>Secure &amp; Verified</span>
                </div>
            </div>
        </div>

        <!-- Right: Visual Cards -->
        <div class="hero-visual-col">
            <div class="hero-blob"></div>

            <div class="hero-badge-float top">
                <i class="fa-solid fa-shield-check" style="color:var(--primary); font-size:1rem;"></i>
                Admin Verified System
            </div>

            <div class="hero-cards-stack">
                <div class="hero-info-card" style="animation: slideInRight 0.55s ease 0.10s both;">
                    <div class="card-icon" style="background:#e8faf6; color:var(--primary);">
                        <i class="fa-solid fa-droplet"></i>
                    </div>
                    <div>
                        <strong>Real-time Matching</strong>
                        <small>Donors matched by blood group + district instantly</small>
                    </div>
                </div>

                <div class="hero-info-card" style="animation: slideInRight 0.55s ease 0.22s both;">
                    <div class="card-icon" style="background:#fff0f1; color:var(--accent-red);">
                        <i class="fa-solid fa-bell-ring"></i>
                    </div>
                    <div>
                        <strong>Emergency Broadcasts</strong>
                        <small>Critical requests alert all matching donors at once</small>
                    </div>
                </div>

                <div class="hero-info-card" style="animation: slideInRight 0.55s ease 0.34s both;">
                    <div class="card-icon" style="background:#e8f4fd; color:var(--info);">
                        <i class="fa-solid fa-hospital"></i>
                    </div>
                    <div>
                        <strong>Hospital Inventory</strong>
                        <small>Live blood stock tracking per hospital &amp; blood type</small>
                    </div>
                </div>

                <div class="hero-info-card" style="animation: slideInRight 0.55s ease 0.46s both;">
                    <div class="card-icon" style="background:#f3e8ff; color:#8b5cf6;">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    <div>
                        <strong>Admin Analytics</strong>
                        <small>Full reports, donor &amp; hospital management dashboard</small>
                    </div>
                </div>

                <div class="hero-info-card" style="animation: slideInRight 0.55s ease 0.58s both;">
                    <div class="card-icon" style="background:#fff8ec; color:var(--warning);">
                        <i class="fa-solid fa-user-nurse"></i>
                    </div>
                    <div>
                        <strong>Staff Operations</strong>
                        <small>Schedule donations, confirm &amp; update blood units</small>
                    </div>
                </div>
            </div>

            <div class="hero-badge-float bottom">
                <span
                    style="width:10px;height:10px;border-radius:50%;background:var(--accent-red);animation:pulse-dot 1.5s ease-in-out infinite;display:inline-block;"></span>
                Emergency Ready · Active Now
            </div>
        </div>
    </div>


</section>

<style>
    @keyframes bounce-down {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(5px);
        }
    }
</style>

<!-- Login Section removed and extracted to login.php -->
<!-- ═══════════════════════════════════════════════
     SECTION 3 — KEY FEATURES
═══════════════════════════════════════════════ -->
<section class="features-section" id="features">
    <div class="section-inner">
        <div class="section-heading">
            <span class="section-label">Why LifeDrop?</span>
            <h2>Key Features That Make<br>Our System Stand Out</h2>
            <p>Purpose-built for the blood donation ecosystem, LifeDrop streamlines every step — from registration to
                donation confirmation.</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feat-icon" style="background:#e8faf6; color:var(--primary);"><i
                        class="fa-solid fa-user-check"></i></div>
                <h3>Multi-Role Access</h3>
                <p>Separate dashboards for Admins, Donors, Recipients, Hospitals, and Staff — each with role-specific
                    tools.</p>
            </div>
            <div class="feature-card">
                <div class="feat-icon" style="background:#fff0f1; color:var(--accent-red);"><i
                        class="fa-solid fa-bell"></i></div>
                <h3>Emergency Alerts</h3>
                <p>Broadcast emergency blood needs instantly. Matched donors and local hospitals notified in real-time.
                </p>
            </div>
            <div class="feature-card">
                <div class="feat-icon" style="background:#e8f4fd; color:var(--info);"><i
                        class="fa-solid fa-boxes-stacked"></i></div>
                <h3>Inventory Management</h3>
                <p>Hospitals track live blood stock per type. Admins see the global inventory across all hospitals.</p>
            </div>
            <div class="feature-card">
                <div class="feat-icon" style="background:#f3e8ff; color:#8b5cf6;"><i
                        class="fa-solid fa-chart-column"></i></div>
                <h3>Admin Reports</h3>
                <p>Comprehensive reports on donations, requests, and user activity. Export-ready for audit purposes.</p>
            </div>
            <div class="feature-card">
                <div class="feat-icon" style="background:#fff8ec; color:var(--warning);"><i
                        class="fa-solid fa-shield-halved"></i></div>
                <h3>Secure & Approved</h3>
                <p>All registrations require admin approval. Passwords are hashed. Sessions are role-protected.</p>
            </div>
            <div class="feature-card">
                <div class="feat-icon" style="background:#e8faf6; color:var(--primary);"><i
                        class="fa-solid fa-handshake-angle"></i></div>
                <h3>Full Donation Lifecycle</h3>
                <p>Request → match → hospital confirmation → inventory update. Every step tracked and visible.</p>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 4 — HOW IT WORKS
═══════════════════════════════════════════════ -->
<section class="how-section" id="how-it-works">
    <div class="section-inner">
        <div class="section-heading">
            <span class="section-label">Simple Process</span>
            <h2>How LifeDrop Works</h2>
            <p>Three easy steps to connect donors with people who need blood most urgently.</p>
        </div>
        <div class="steps-row">
            <div class="step-card">
                <div class="step-num">1</div>
                <h3>Register & Get Approved</h3>
                <p>Sign up as Donor, Recipient, or Hospital. Admin reviews and approves your account for full access.
                </p>
            </div>
            <div class="step-card">
                <div class="step-num">2</div>
                <h3>Match &amp; Notify</h3>
                <p>Recipients submit blood requests. The system automatically matches with available donors by blood
                    group &amp; district and sends alerts.</p>
            </div>
            <div class="step-card">
                <div class="step-num">3</div>
                <h3>Donate &amp; Track</h3>
                <p>Donations are confirmed by hospital staff. Inventory updates automatically. History recorded for all
                    parties.</p>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 5 — BLOOD TYPES
═══════════════════════════════════════════════ -->
<section class="blood-section" id="blood-types">
    <div class="section-inner">
        <div class="section-heading">
            <span class="section-label">Compatibility Guide</span>
            <h2>All 8 Blood Types Supported</h2>
            <p>LifeDrop manages all ABO and Rh blood group combinations for precise donor–recipient matching.</p>
        </div>

        <div class="blood-types-grid">
            <?php
            $blood_types = [
                ['group' => 'A+', 'can_give' => 'A+, AB+'],
                ['group' => 'A−', 'can_give' => 'A+, A−, AB+, AB−'],
                ['group' => 'B+', 'can_give' => 'B+, AB+'],
                ['group' => 'B−', 'can_give' => 'B+, B−, AB+, AB−'],
                ['group' => 'AB+', 'can_give' => 'AB+ only'],
                ['group' => 'AB−', 'can_give' => 'AB+, AB−'],
                ['group' => 'O+', 'can_give' => 'A+, B+, AB+, O+'],
                ['group' => 'O−', 'can_give' => 'All blood types'],
            ];
            foreach ($blood_types as $bt):
                ?>
                <div class="blood-type-card" title="Can donate to: <?php echo $bt['can_give']; ?>">
                    <div class="bt-label"><?php echo htmlspecialchars($bt['group']); ?></div>
                    <div class="bt-sub">Blood Type</div>
                    <?php if ($bt['group'] === 'O−'): ?>
                        <div
                            style="font-size:0.65rem; color:var(--accent-red); font-weight:700; margin-top:6px; background:var(--accent-red-light); padding:2px 8px; border-radius:20px;">
                            Universal Donor</div>
                    <?php elseif ($bt['group'] === 'AB+'): ?>
                        <div
                            style="font-size:0.65rem; color:var(--primary); font-weight:700; margin-top:6px; background:var(--primary-glow); padding:2px 8px; border-radius:20px;">
                            Universal Recipient</div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div
            style="background:var(--accent-red-light); border-radius:var(--radius-lg); padding:22px 28px; display:flex; align-items:center; gap:18px; border-left:4px solid var(--accent-red);">
            <i class="fa-solid fa-circle-info" style="font-size:1.5rem; color:var(--accent-red); flex-shrink:0;"></i>
            <div>
                <strong style="color:var(--accent-red-dark);">Compatibility Tip:</strong>
                <span style="color:var(--text-body); font-size:0.92rem;"> O− is the universal donor (can give to all).
                    AB+ is the universal recipient. Hover a card above to see who each type can donate to.</span>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 6 — USER ROLES
═══════════════════════════════════════════════ -->
<section class="roles-section">
    <div class="section-inner">
        <div class="section-heading">
            <span class="section-label">Platform Roles</span>
            <h2>Built for Everyone in the<br>Blood Donation Chain</h2>
        </div>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:20px;">
            <?php
            $roles = [
                ['icon' => 'fa-gauge', 'color' => '#e8f4fd', 'icolor' => 'var(--info)', 'title' => 'Admin', 'desc' => 'System-wide control, user approvals, global reports & analytics'],
                ['icon' => 'fa-heart', 'color' => '#fff0f1', 'icolor' => 'var(--accent-red)', 'title' => 'Donor', 'desc' => 'Register, manage profile, view donation history & emergency alerts'],
                ['icon' => 'fa-bed-pulse', 'color' => '#fff8ec', 'icolor' => 'var(--warning)', 'title' => 'Recipient', 'desc' => 'Request blood, flag emergencies, track request status updates'],
                ['icon' => 'fa-hospital-user', 'color' => 'var(--primary-glow)', 'icolor' => 'var(--primary)', 'title' => 'Hospital', 'desc' => 'Manage blood inventory, review donations & blood issue requests'],
                ['icon' => 'fa-user-nurse', 'color' => '#f3e8ff', 'icolor' => '#8b5cf6', 'title' => 'Hospital Staff', 'desc' => 'Confirm donations, update inventory units & manage schedule'],
            ];
            foreach ($roles as $r):
                ?>
                <div class="card" style="text-align:center; padding:28px 20px; transition:var(--transition);"
                    onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='var(--shadow-md)'"
                    onmouseout="this.style.transform='';this.style.boxShadow=''">
                    <div
                        style="width:60px;height:60px;background:<?php echo $r['color']; ?>;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:<?php echo $r['icolor']; ?>;margin:0 auto 16px;">
                        <i class="fa-solid <?php echo $r['icon']; ?>"></i>
                    </div>
                    <h3 style="font-size:1rem; font-weight:700; margin-bottom:8px;"><?php echo $r['title']; ?></h3>
                    <p style="font-size:0.83rem; color:var(--text-muted); line-height:1.55;"><?php echo $r['desc']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 7 — CALL TO ACTION
═══════════════════════════════════════════════ -->
<section class="cta-section">
    <div class="cta-inner">
        <span
            style="background:rgba(255,255,255,0.15); color:white; padding:6px 18px; border-radius:20px; font-size:0.78rem; font-weight:700; display:inline-block; letter-spacing:0.08em; margin-bottom:20px;">
            🩸 JOIN THE MISSION
        </span>
        <h2>Every Drop Counts.<br>Be Someone's Lifeline.</h2>
        <p>Register today as a blood donor and help save lives across Wayanad. It only takes a few minutes to sign up —
            your donation could be the difference between life and death.</p>
        <div class="cta-btn-row">
            <a href="register.php" class="btn-cta-white">
                <i class="fa-solid fa-heart-pulse"></i> Register as Donor
            </a>
            <a href="login.php" class="btn-cta-outline">
                <i class="fa-solid fa-right-to-bracket"></i> Login to Portal
            </a>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════
     SECTION 8 — FOOTER
═══════════════════════════════════════════════ -->
<footer class="landing-footer">
    <div class="footer-inner">
        <div class="footer-top">
            <div class="footer-brand-col">
                <div class="brand-name">
                    <div
                        style="width:36px;height:36px;background:linear-gradient(135deg,var(--primary),var(--primary-dark));border-radius:10px;display:flex;align-items:center;justify-content:center;color:white;font-size:1rem;">
                        <i class="fa-solid fa-droplet"></i>
                    </div>
                    LifeDrop
                </div>
                <p>Online Blood Donation Management System for GPTC MANANTHAVADY College — Diploma in Computer
                    Engineering, 2024–25 Academic Project.</p>
                <div
                    style="margin-top:16px; display:flex; align-items:center; gap:8px; font-size:0.82rem; color:var(--primary);">
                    <i class="fa-solid fa-circle" style="font-size:7px;"></i>
                    <span>System Online &amp; Active</span>
                </div>
            </div>

            <div class="footer-col">
                <h4>Platform</h4>
                <a href="#features">Features</a>
                <a href="#how-it-works">How It Works</a>
                <a href="#blood-types">Blood Types</a>
                <a href="login.php">Login</a>
            </div>

            <div class="footer-col">
                <h4>Register As</h4>
                <a href="register.php">Blood Donor</a>
                <a href="register.php">Patient / Recipient</a>
                <a href="register.php">Hospital</a>
            </div>

            <div class="footer-col">
                <h4>Project Info</h4>
                <a href="#">GPTC MANANTHAVADY</a>
                <a href="#">Computer Engineering</a>
                <a href="#">Diploma 2024–25</a>
            </div>
        </div>

        <div class="footer-bottom">
            <span>&copy; <?php echo date('Y'); ?> LifeDrop · GPTC MANANTHAVADY College</span>
            <span style="font-size:0.8rem; color:rgba(255,255,255,0.4);">Built with ❤️ for the community</span>
        </div>
    </div>
</footer>

<script>
    // ── Smooth scroll helper ──────────────
    function smoothScrollTo(sectionId) {
        const el = document.getElementById(sectionId);
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // If URL has ?error or ?success
        if (window.location.search) {
            setTimeout(() => smoothScrollTo('features'), 200);
        }



        // Animated counters for stats
        function animateCount(el, target, suffix) {
            let current = 0;
            const step = Math.ceil(target / 30);
            const timer = setInterval(() => {
                current += step;
                if (current >= target) { current = target; clearInterval(timer); }
                el.textContent = current + (suffix || '');
            }, 40);
        }

        // Intersection observer to trigger counters when visible
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCount(document.getElementById('count-roles'), 5, '');
                    animateCount(document.getElementById('count-types'), 8, '+');
                    observer.disconnect();
                }
            });
        }, { threshold: 0.3 });

        const statsBar = document.querySelector('.hero-stats-bar');
        if (statsBar) observer.observe(statsBar);
    });
</script>

<?php require_once 'includes/footer.php'; ?>