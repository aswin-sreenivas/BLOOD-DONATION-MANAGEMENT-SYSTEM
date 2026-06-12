<?php
// staff/dashboard.php
session_start();
define('PAGE_TITLE', 'Staff Dashboard');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Staff') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}

$staff_id = $_SESSION['user_id'];
$hospital_id = $_SESSION['hospital_id'] ?? 0;

try {
    // KPI Metrics
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM DONATION WHERE Hospital_ID = ? AND Donation_Date = CURRENT_DATE");
    $stmt->execute([$hospital_id]);
    $appointments_today = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM BloodUnit WHERE Collection_Date = CURRENT_DATE AND Status = 'Available' AND Inventory_ID IN (SELECT Inventory_ID FROM BLOOD_INVENTORY WHERE Hospital_ID = ?)");
    $stmt->execute([$hospital_id]);
    $collections_today = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM BLOOD_REQUEST WHERE Status = 'Matched' AND District = (SELECT Location FROM HOSPITAL WHERE Hospital_ID = ? LIMIT 1)");
    $stmt->execute([$hospital_id]);
    $pending_issues = $stmt->fetchColumn();

    // Today's appointments
    $stmt = $pdo->prepare("SELECT d.Donation_ID, do.Name AS DonorName, do.Blood_Group, d.Donation_Status 
                           FROM DONATION d JOIN DONOR do ON d.Donor_ID = do.Donor_ID 
                           WHERE d.Hospital_ID = ? AND d.Donation_Date = CURRENT_DATE");
    $stmt->execute([$hospital_id]);
    $schedule = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}
?>

<div class="dashboard-grid">
    <div class="card stat-card">
        <h3>Appointments Today</h3>
        <div class="stat-value">
            <?php echo $appointments_today; ?>
        </div>
    </div>

    <div class="card stat-card" style="border-left: 4px solid var(--success-color);">
        <h3>Units Collected Today</h3>
        <div class="stat-value" style="color: var(--success-color);">
            <?php echo $collections_today; ?>
        </div>
    </div>

    <div class="card stat-card" style="border-left: 4px solid #f39c12;">
        <h3>Pending Issues</h3>
        <div class="stat-value" style="color: #f39c12;">
            <?php echo $pending_issues; ?>
        </div>
    </div>
</div>

<div class="dashboard-grid" style="grid-template-columns: 2fr 1fr;">
    <div class="card">
        <h3>Today's Schedule (
            <?php echo date('M d, Y'); ?>)
        </h3>
        <?php if (count($schedule) > 0): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Donation #</th>
                            <th>Donor Name</th>
                            <th>Blood Group</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedule as $apt): ?>
                            <tr>
                                <td>#
                                    <?php echo $apt['Donation_ID']; ?>
                                </td>
                                <td><strong>
                                        <?php echo htmlspecialchars($apt['DonorName']); ?>
                                    </strong></td>
                                <td><span class="badge badge-danger">
                                        <?php echo htmlspecialchars($apt['Blood_Group']); ?>
                                    </span></td>
                                <td>
                                    <?php if ($apt['Donation_Status'] === 'Completed'): ?>
                                        <span class="badge badge-success">Completed</span>
                                    <?php else: ?>
                                        <span class="badge badge-pending">
                                            <?php echo htmlspecialchars($apt['Donation_Status']); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p style="color: var(--text-muted); text-align: center; padding: 20px;">No appointments scheduled for today.</p>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>Quick Actions</h3>
        <ul style="list-style: none; padding: 0;">
            <li style="margin-bottom: 15px;"><a href="process_donation.php" class="btn btn-primary"
                    style="display:block; text-align:center;">Process Donation</a></li>
            <li style="margin-bottom: 15px;"><a href="issue_units.php" class="btn"
                    style="display:block; text-align:center;">Issue Blood Units</a></li>
        </ul>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>