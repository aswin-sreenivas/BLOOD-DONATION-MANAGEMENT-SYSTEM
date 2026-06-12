<?php
// login.php — LifeDrop Login Page
require_once 'includes/header.php';

// Redirect logged-in users to their dashboard
if (isset($_SESSION['user_id'])) {
    $role = strtolower($_SESSION['role']);
    header("Location: {$role}/dashboard.php");
    exit();
}
?>

<section class="login-section" id="login-section">
    <div class="login-inner"
        style="max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; padding: 60px 20px;">

        <!-- Left: Role Info -->
        <div>
            <span class="section-label"><i class="fa-solid fa-right-to-bracket"></i> &nbsp;Portal Access</span>
            <h2 style="font-size:2rem; font-weight:800; margin-bottom:16px;">Access Your Dashboard</h2>
            <p style="color:var(--text-muted); line-height:1.75; margin-bottom:28px; font-size:0.97rem;">
                Log in to manage donations, blood requests, hospital inventory, or system administration. Each role has
                a dedicated workspace built for efficiency.
            </p>

            <div style="display:flex; flex-direction:column; gap:12px;">
                <?php
                $role_info = [
                    ['icon' => 'fa-gauge', 'bg' => '#e8f4fd', 'ic' => 'var(--info)', 'title' => 'Admin', 'desc' => 'Full system control · user approvals · global reports'],
                    ['icon' => 'fa-heart', 'bg' => '#fff0f1', 'ic' => 'var(--accent-red)', 'title' => 'Donor', 'desc' => 'Manage your profile, donation history & availability'],
                    ['icon' => 'fa-bed-pulse', 'bg' => '#fff8ec', 'ic' => 'var(--warning)', 'title' => 'Recipient', 'desc' => 'Submit blood requests & track their fulfillment status'],
                    ['icon' => 'fa-hospital', 'bg' => 'var(--primary-glow)', 'ic' => 'var(--primary)', 'title' => 'Hospital', 'desc' => 'Track blood inventory, confirm donations & issues'],
                    ['icon' => 'fa-user-nurse', 'bg' => '#f3e8ff', 'ic' => '#8b5cf6', 'title' => 'Staff', 'desc' => 'Process daily donations, schedule & issue blood units'],
                ];
                foreach ($role_info as $r):
                    ?>
                    <div class="role-info-card"
                        style="display: flex; align-items: center; gap: 12px; padding: 14px 18px; background: white; border-radius: var(--radius-md); box-shadow: var(--shadow-xs); border: 1px solid var(--border);">
                        <div
                            style="width:38px;height:38px;background:<?php echo $r['bg']; ?>;border-radius:10px;display:flex;align-items:center;justify-content:center;color:<?php echo $r['ic']; ?>;flex-shrink:0;">
                            <i class="fa-solid <?php echo $r['icon']; ?>"></i>
                        </div>
                        <div>
                            <strong style="font-size:0.88rem; color:var(--text-dark);">
                                <?php echo $r['title']; ?>
                            </strong>
                            <p style="font-size:0.76rem; color:var(--text-muted); margin:0;">
                                <?php echo $r['desc']; ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Right: Login Form -->
        <div>
            <div class="auth-card"
                style="background: white; border-radius: var(--radius-lg); padding: 38px; box-shadow: var(--shadow-lg); border: 1px solid var(--border);">
                <div class="auth-logo" style="text-align: center; margin-bottom: 28px;">
                    <div class="logo-icon-big"
                        style="width: 64px; height: 64px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); border-radius: 18px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.6rem; margin: 0 auto 14px; box-shadow: var(--shadow-primary);">
                        <i class="fa-solid fa-droplet"></i>
                    </div>
                    <h2 style="font-size: 1.4rem; font-weight: 800;">Welcome Back</h2>
                    <p style="color: var(--text-muted); font-size: 0.88rem;">Sign in to your LifeDrop account</p>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <?php
                        match ($_GET['error']) {
                            'invalid_credentials' => print 'Invalid email or password.',
                            'unauthorized' => print 'Please log in to access this page.',
                            'pending_approval' => print 'Your account is pending admin approval.',
                            'inactive' => print 'Your account has been deactivated.',
                            default => print 'An error occurred. Please try again.',
                        };
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['success']) && $_GET['success'] === 'registered'): ?>
                    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Account created! Please log
                        in.</div>
                <?php endif; ?>
                <?php if (isset($_GET['success']) && $_GET['success'] === 'logged_out'): ?>
                    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> You have been logged out.
                    </div>
                <?php endif; ?>

                <form action="login_action.php" method="POST">
                    <div class="form-group" style="margin-bottom: 18px;">
                        <label for="email"
                            style="display: block; margin-bottom: 7px; font-weight: 600; font-size: 0.87rem; color: var(--text-dark);">
                            <i class="fa-solid fa-envelope" style="color:var(--primary);margin-right:6px;"></i>Email
                            Address
                        </label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="your@email.com"
                            required autocomplete="email"
                            style="width: 100%; padding: 11px 14px; border: 1.5px solid var(--border); border-radius: var(--radius-sm); font-size: 0.95rem;">
                    </div>
                    <div class="form-group" style="margin-bottom: 18px;">
                        <label for="password"
                            style="display: block; margin-bottom: 7px; font-weight: 600; font-size: 0.87rem; color: var(--text-dark);">
                            <i class="fa-solid fa-lock" style="color:var(--primary);margin-right:6px;"></i>Password
                        </label>
                        <div style="position:relative;">
                            <input type="password" id="password" name="password" class="form-control"
                                placeholder="Enter your password" required autocomplete="current-password"
                                style="width: 100%; padding: 11px 14px; border: 1.5px solid var(--border); border-radius: var(--radius-sm); font-size: 0.95rem; padding-right:44px;">
                            <button type="button" onclick="togglePwd()"
                                style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:1rem;">
                                <i class="fa-solid fa-eye" id="pwd-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom: 18px;">
                        <label for="role"
                            style="display: block; margin-bottom: 7px; font-weight: 600; font-size: 0.87rem; color: var(--text-dark);">
                            <i class="fa-solid fa-user-tag" style="color:var(--primary);margin-right:6px;"></i>Login As
                        </label>
                        <select id="role" name="role" class="form-control" required
                            style="width: 100%; padding: 11px 14px; border: 1.5px solid var(--border); border-radius: var(--radius-sm); font-size: 0.95rem; background: white; cursor: pointer;">
                            <option value="" disabled selected>Select your role…</option>
                            <option value="Admin">Admin</option>
                            <option value="Donor">Donor</option>
                            <option value="Recipient">Recipient / Patient</option>
                            <option value="Hospital">Hospital</option>
                            <option value="Staff">Hospital Staff</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary"
                        style="width:100%; margin-top:6px; padding:14px; font-size:0.97rem; border-radius:var(--radius-md);">
                        <i class="fa-solid fa-right-to-bracket"></i> Sign In to Dashboard
                    </button>
                </form>

                <div style="text-align:center; margin-top:20px; font-size:0.87rem; color:var(--text-muted);">
                    New here? <a href="register.php" style="color:var(--primary); font-weight:700;">Create an account
                        →</a>
                </div>
            </div>
        </div>

    </div>
</section>

<script>
    window.togglePwd = function () {
        const inp = document.getElementById('password');
        const eye = document.getElementById('pwd-eye');
        if (inp.type === 'password') {
            inp.type = 'text';
            eye.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            inp.type = 'password';
            eye.classList.replace('fa-eye-slash', 'fa-eye');
        }
    };
</script>

<?php require_once 'includes/footer.php'; ?>