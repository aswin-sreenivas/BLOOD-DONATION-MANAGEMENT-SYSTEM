<?php
// admin/all_donations.php
session_start();
define('PAGE_TITLE', 'All Donations History');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Admin') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}

try {
    // Fetch all donations along with donor and hospital info
    $stmt = $pdo->query("
        SELECT d.Donation_ID, d.Donation_Date, d.Donation_Status, Units_Donated, 
               don.Name AS DonorName, don.Blood_Group, don.Phone AS DonorPhone,
               h.Hospital_Name
        FROM DONATION d
        JOIN DONOR don ON d.Donor_ID = don.Donor_ID
        LEFT JOIN HOSPITAL h ON d.Hospital_ID = h.Hospital_ID
        ORDER BY d.Donation_Date DESC
    ");
    $donations = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}
?>

<div style="display: flex; justify-content: space-between; align-items: center;">
    <h2>All Blood Donations (Global History)</h2>
    <a href="dashboard.php" class="btn">&larr; Back to Dashboard</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Donation #</th>
                    <th>Date</th>
                    <th>Donor</th>
                    <th>Blood Group</th>
                    <th>Hospital (Location)</th>
                    <th>Units</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($donations) > 0): ?>
                    <?php foreach ($donations as $don): ?>
                        <tr>
                            <td><strong>#
                                    <?php echo $don['Donation_ID']; ?>
                                </strong></td>
                            <td>
                                <?php echo date('M d, Y', strtotime($don['Donation_Date'])); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($don['DonorName']); ?><br>
                                <span style="font-size: 0.85em; color: var(--text-muted);">📞
                                    <?php echo htmlspecialchars($don['DonorPhone']); ?>
                                </span>
                            </td>
                            <td><span class="badge badge-danger">
                                    <?php echo htmlspecialchars($don['Blood_Group']); ?>
                                </span></td>
                            <td>
                                <?php echo htmlspecialchars($don['Hospital_Name'] ?? 'General/Camp'); ?>
                            </td>
                            <td>
                                <?php echo $don['Units_Donated'] ?? '-'; ?>
                            </td>
                            <td>
                                <?php if ($don['Donation_Status'] === 'Completed'): ?>
                                    <span class="badge badge-success">Completed</span>
                                <?php else: ?>
                                    <span class="badge badge-pending">
                                        <?php echo htmlspecialchars($don['Donation_Status']); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px;">No donations recorded in the system.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>