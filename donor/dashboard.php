<?php
// donor/dashboard.php
session_start();
define('PAGE_TITLE', 'Donor Dashboard');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Donor') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}

$donor_id = $_SESSION['user_id'];
$message = '';

// Handle Availability Toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_availability'])) {
    $new_status = $_POST['availability_status'];
    try {
        $stmt = $pdo->prepare("UPDATE DONOR SET Availability_Status = ? WHERE Donor_ID = ?");
        $stmt->execute([$new_status, $donor_id]);
        $message = "<div class='alert alert-success'><i class='fa-solid fa-circle-check'></i> Availability updated to <strong>$new_status</strong>.</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-error'><i class='fa-solid fa-circle-exclamation'></i> Error updating availability.</div>";
    }
}

// Fetch Donor Data
try {
    $stmt = $pdo->prepare("SELECT * FROM DONOR WHERE Donor_ID = ?");
    $stmt->execute([$donor_id]);
    $donor = $stmt->fetch(PDO::FETCH_ASSOC);

    $hist_stmt = $pdo->prepare("SELECT d.Donation_ID, d.Donation_Date, d.Donation_Status, h.Hospital_Name
                                 FROM DONATION d
                                 LEFT JOIN HOSPITAL h ON d.Hospital_ID = h.Hospital_ID
                                 WHERE d.Donor_ID = ?
                                 ORDER BY d.Donation_Date DESC LIMIT 5");
    $hist_stmt->execute([$donor_id]);
    $history = $hist_stmt->fetchAll(PDO::FETCH_ASSOC);

    $emerg_stmt = $pdo->prepare("SELECT * FROM BLOOD_REQUEST
                                 WHERE Blood_Group = ? AND District = ? AND Emergency_Status = 'Critical' AND Status = 'Pending'
                                 ORDER BY Request_Date DESC LIMIT 3");
    $emerg_stmt->execute([$donor['Blood_Group'], $donor['District']]);
    $emergencies = $emerg_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}

// Donation Eligibility (90-day rule)
$eligible_to_donate = true;
$next_eligible_date = "Now";
if ($donor['Last_Donation_Date']) {
    $last = new DateTime($donor['Last_Donation_Date']);
    $now = new DateTime();
    if ($now->diff($last)->days < 90) {
        $eligible_to_donate = false;
        $last->add(new DateInterval('P90D'));
        $next_eligible_date = $last->format('M d, Y');
    }
}
?>

<!-- Page Header -->
<div class="page-header">
    <h2>
        <i class="fa-solid fa-heart" style="color:var(--accent-red); margin-right:10px;"></i>
        Welcome, <?php echo htmlspecialchars($donor['Name']); ?>!
    </h2>
    <p>Your donor dashboard — <?php echo date('l, d F Y'); ?></p>
</div>

<?php echo $message; ?>

<!-- KPI Cards -->
<div class="dashboard-grid">
    <div class="kpi-card">
        <div class="kpi-icon <?php echo $eligible_to_donate ? 'green' : 'orange'; ?>">
            <i class="fa-solid fa-<?php echo $eligible_to_donate ? 'check-circle' : 'clock'; ?>"></i>
        </div>
        <div class="kpi-info">
            <h3 style="font-size:1.3rem;"><?php echo $eligible_to_donate ? 'Eligible' : 'Wait'; ?></h3>
            <p>Next eligible: <strong><?php echo $next_eligible_date; ?></strong></p>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon red"><i class="fa-solid fa-droplet"></i></div>
        <div class="kpi-info">
            <h3><?php echo count($history); ?></h3>
            <p>Total Donations</p>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon <?php echo $donor['Availability_Status'] === 'Available' ? 'green' : 'orange'; ?>">
            <i
                class="fa-solid fa-<?php echo $donor['Availability_Status'] === 'Available' ? 'circle-check' : 'pause-circle'; ?>"></i>
        </div>
        <div class="kpi-info">
            <h3 style="font-size:1.1rem;"><?php echo htmlspecialchars($donor['Availability_Status']); ?></h3>
            <p>Current Status</p>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon red"><i class="fa-solid fa-tint"></i></div>
        <div class="kpi-info">
            <h3><?php echo htmlspecialchars($donor['Blood_Group']); ?></h3>
            <p>Blood Group · <?php echo htmlspecialchars($donor['District']); ?></p>
        </div>
    </div>
</div>

<!-- Content Grid -->
<div style="display:grid; grid-template-columns:2fr 1fr; gap:24px;">

    <!-- Recent Donations -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-hand-holding-droplet"
                    style="color:var(--accent-red);margin-right:8px;"></i>Recent Donations</span>
            <a href="profile.php" class="btn btn-outline btn-sm">View Full Profile</a>
        </div>

        <?php if (count($history) > 0): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Hospital</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $record): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($record['Donation_Date'])); ?></td>
                                <td><?php echo htmlspecialchars($record['Hospital_Name'] ?: 'System Record'); ?></td>
                                <td>
                                    <?php
                                    $s = $record['Donation_Status'];
                                    $cls = $s === 'Completed' ? 'badge-success' : ($s === 'Scheduled' ? 'badge-pending' : 'badge-info');
                                    echo "<span class='badge $cls'>$s</span>";
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="text-align:center; padding:30px; color:var(--text-muted);">
                <i class="fa-solid fa-heart-crack"
                    style="font-size:2rem; color:var(--accent-red); margin-bottom:10px; display:block; opacity:0.5;"></i>
                No donations yet. Be the first to make a difference!
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Panel -->
    <div style="display:flex; flex-direction:column; gap:20px;">

        <!-- Availability Toggle -->
        <div class="card" style="margin-bottom:0;">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-toggle-on"
                        style="color:var(--primary);margin-right:8px;"></i>Update Status</span>
            </div>
            <form method="POST">
                <div class="form-group">
                    <select name="availability_status" class="form-control">
                        <option value="Available" <?php echo $donor['Availability_Status'] === 'Available' ? 'selected' : ''; ?>>✅ Available to Donate</option>
                        <option value="Unavailable" <?php echo $donor['Availability_Status'] === 'Unavailable' ? 'selected' : ''; ?>>⏸ Temporarily Unavailable</option>
                    </select>
                </div>
                <button type="submit" name="toggle_availability" class="btn btn-primary" style="width:100%;">
                    <i class="fa-solid fa-save"></i> Update Availability
                </button>
            </form>
        </div>

        <!-- Emergency Matches -->
        <div class="card" style="margin-bottom:0; border-top:3px solid var(--accent-red);">
            <div class="card-header">
                <span class="card-title" style="color:var(--accent-red);">
                    <i class="fa-solid fa-siren-on" style="margin-right:8px;"></i>Emergency Matches
                </span>
            </div>
            <p style="font-size:0.82rem; color:var(--text-muted); margin-bottom:14px;">
                Critical requests for <strong
                    style="color:var(--accent-red);"><?php echo $donor['Blood_Group']; ?></strong> in your district.
            </p>

            <?php if (count($emergencies) > 0): ?>
                <?php foreach ($emergencies as $em): ?>
                    <div
                        style="padding:12px; background:var(--accent-red-light); border-radius:var(--radius-sm); margin-bottom:10px; border-left:3px solid var(--accent-red);">
                        <strong style="color:var(--accent-red); font-size:0.88rem;">🩸 <?php echo $em['Quantity']; ?> unit(s)
                            needed urgently</strong><br>
                        <span style="font-size:0.8rem; color:var(--text-muted);">
                            📍 <?php echo htmlspecialchars($em['District']); ?> &nbsp;·&nbsp;
                            <?php echo date('M d, g:i a', strtotime($em['Request_Date'])); ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-success" style="margin:0;">
                    <i class="fa-solid fa-circle-check"></i> No critical emergencies matching your profile right now.
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>