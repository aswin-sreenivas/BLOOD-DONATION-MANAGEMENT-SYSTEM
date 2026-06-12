<?php
// hospital/blood_issues.php
session_start();
define('PAGE_TITLE', 'Blood Issues');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Hospital') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}
$hospital_id = $_SESSION['user_id'];

try {
    // Fulfilled requests = units that were issued
    $stmt = $pdo->prepare("SELECT r.*, rec.Name AS RecipientName, rec.Phone AS RecipientPhone
                           FROM BLOOD_REQUEST r
                           JOIN RECIPIENT rec ON r.Recipient_ID = rec.Recipient_ID
                           WHERE r.Status = 'Fulfilled'
                           ORDER BY r.Request_Date DESC");
    $stmt->execute();
    $issues = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-droplet" style="color:var(--accent-red);margin-right:10px;"></i>Blood Issues Log</h2>
        <p>All fulfilled blood requests (units issued from your hospital)</p>
    </div>
    <a href="dashboard.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-arrow-right-from-bracket"
                style="color:var(--accent-red);margin-right:8px;"></i>Issued Units History</span>
        <span class="badge badge-success">
            <?php echo count($issues); ?> Records
        </span>
    </div>
    <?php if (count($issues) > 0): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Request #</th>
                        <th>Date</th>
                        <th>Blood Group</th>
                        <th>Units</th>
                        <th>Recipient</th>
                        <th>Phone</th>
                        <th>District</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($issues as $i): ?>
                        <tr>
                            <td><strong>#
                                    <?php echo $i['Request_ID']; ?>
                                </strong></td>
                            <td>
                                <?php echo date('d M Y', strtotime($i['Request_Date'])); ?>
                            </td>
                            <td><span class="badge badge-danger">
                                    <?php echo htmlspecialchars($i['Blood_Group']); ?>
                                </span></td>
                            <td>
                                <?php echo $i['Quantity']; ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($i['RecipientName']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($i['RecipientPhone']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($i['District']); ?>
                            </td>
                            <td><span class="badge badge-success">Fulfilled</span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="padding:30px; text-align:center; color:var(--text-muted);">No blood issues recorded yet.</p>
    <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>