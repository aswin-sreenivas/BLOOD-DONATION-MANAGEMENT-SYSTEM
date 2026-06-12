<?php
// donor/my_donations.php
session_start();
define('PAGE_TITLE', 'My Donations');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Donor') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}
$donor_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT d.Donation_ID, d.Donation_Date, d.Donation_Status, Units_Donated, h.Hospital_Name, h.Location
                           FROM DONATION d
                           LEFT JOIN HOSPITAL h ON d.Hospital_ID = h.Hospital_ID
                           WHERE d.Donor_ID = ?
                           ORDER BY d.Donation_Date DESC");
    $stmt->execute([$donor_id]);
    $donations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM DONATION WHERE Donor_ID = ? AND Donation_Status = 'Completed'");
    $stmt2->execute([$donor_id]);
    $completed_count = $stmt2->fetchColumn();
} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-hand-holding-droplet" style="color:var(--accent-red);margin-right:10px;"></i>My
            Donation History</h2>
        <p>Complete record of all your blood donations</p>
    </div>
    <a href="dashboard.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
</div>

<!-- KPI Row -->
<div class="dashboard-grid" style="margin-bottom:24px;">
    <div class="kpi-card">
        <div class="kpi-icon red"><i class="fa-solid fa-droplet"></i></div>
        <div class="kpi-info">
            <h3>
                <?php echo count($donations); ?>
            </h3>
            <p>Total Donations</p>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon green"><i class="fa-solid fa-circle-check"></i></div>
        <div class="kpi-info">
            <h3>
                <?php echo $completed_count; ?>
            </h3>
            <p>Completed</p>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon orange"><i class="fa-solid fa-tint"></i></div>
        <div class="kpi-info">
            <h3>
                <?php echo $completed_count * 450; ?> ml
            </h3>
            <p>Est. Blood Donated</p>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon purple"><i class="fa-solid fa-heart"></i></div>
        <div class="kpi-info">
            <h3>~
                <?php echo $completed_count * 3; ?>
            </h3>
            <p>Lives Impacted</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-list" style="color:var(--primary);margin-right:8px;"></i>Donation
            Records</span>
    </div>

    <?php if (count($donations) > 0): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Donation #</th>
                        <th>Date</th>
                        <th>Hospital</th>
                        <th>Location</th>
                        <th>Units</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donations as $d): ?>
                        <tr>
                            <td><strong>#
                                    <?php echo $d['Donation_ID']; ?>
                                </strong></td>
                            <td>
                                <?php echo date('d M Y', strtotime($d['Donation_Date'])); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($d['Hospital_Name'] ?? 'System Record'); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($d['Location'] ?? '—'); ?>
                            </td>
                            <td>
                                <?php echo $d['Units_Donated'] ?? '1'; ?> unit(s)
                            </td>
                            <td>
                                <?php
                                $s = $d['Donation_Status'];
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
        <div style="text-align:center; padding:50px; color:var(--text-muted);">
            <i class="fa-solid fa-heart-crack"
                style="font-size:3rem; color:var(--accent-red); opacity:0.3; display:block; margin-bottom:12px;"></i>
            <p>No donations recorded yet. Your first donation could save up to 3 lives!</p>
            <a href="profile.php" class="btn btn-primary" style="margin-top:16px;">Update Profile to Stay Ready</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>