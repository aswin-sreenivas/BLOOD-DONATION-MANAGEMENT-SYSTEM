<?php
// hospital/donation_records.php
session_start();
define('PAGE_TITLE', 'Donation Records');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Hospital') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}
$hospital_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT d.*, do.Name AS DonorName, do.Blood_Group, do.Phone AS DonorPhone
                           FROM DONATION d
                           JOIN DONOR do ON d.Donor_ID = do.Donor_ID
                           WHERE d.Hospital_ID = ?
                           ORDER BY d.Donation_Date DESC");
    $stmt->execute([$hospital_id]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $completed = array_filter($records, fn($r) => $r['Donation_Status'] === 'Completed');
    $total_units = array_sum(array_map(fn($r) => $r['Units_Donated'] ?? 1, $completed));
} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-file-medical" style="color:var(--primary);margin-right:10px;"></i>Donation Records
        </h2>
        <p>Full history of all donations received at your hospital</p>
    </div>
    <a href="dashboard.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
</div>

<div class="dashboard-grid" style="margin-bottom:24px;">
    <div class="kpi-card">
        <div class="kpi-icon blue"><i class="fa-solid fa-list"></i></div>
        <div class="kpi-info">
            <h3>
                <?php echo count($records); ?>
            </h3>
            <p>Total Records</p>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon green"><i class="fa-solid fa-circle-check"></i></div>
        <div class="kpi-info">
            <h3>
                <?php echo count($completed); ?>
            </h3>
            <p>Completed</p>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon red"><i class="fa-solid fa-droplet"></i></div>
        <div class="kpi-info">
            <h3>
                <?php echo $total_units; ?>
            </h3>
            <p>Units Collected</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-hand-holding-droplet"
                style="color:var(--accent-red);margin-right:8px;"></i>All Donations</span>
    </div>
    <?php if (count($records) > 0): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Donor</th>
                        <th>Blood Group</th>
                        <th>Phone</th>
                        <th>Units</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $r): ?>
                        <tr>
                            <td><strong>#
                                    <?php echo $r['Donation_ID']; ?>
                                </strong></td>
                            <td>
                                <?php echo date('d M Y', strtotime($r['Donation_Date'])); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($r['DonorName']); ?>
                            </td>
                            <td><span class="badge badge-danger">
                                    <?php echo htmlspecialchars($r['Blood_Group']); ?>
                                </span></td>
                            <td>
                                <?php echo htmlspecialchars($r['DonorPhone']); ?>
                            </td>
                            <td>
                                <?php echo $r['Units_Donated'] ?? 1; ?>
                            </td>
                            <td>
                                <?php $s = $r['Donation_Status'];
                                $c = $s === 'Completed' ? 'badge-success' : ($s === 'Cancelled' ? 'badge-danger' : 'badge-pending'); ?>
                                <span class="badge <?php echo $c; ?>">
                                    <?php echo $s; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="padding:30px; text-align:center; color:var(--text-muted);">No donation records for your hospital yet.</p>
    <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>