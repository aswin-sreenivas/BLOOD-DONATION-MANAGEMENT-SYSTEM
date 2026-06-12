<?php
// hospital/confirm_donations.php
session_start();
define('PAGE_TITLE', 'Confirm Donations');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Hospital') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}
$hospital_id = $_SESSION['user_id'];
$message = '';

// Handle confirm/reject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donation_id = (int) $_POST['donation_id'];
    $action = $_POST['action'];
    try {
        if ($action === 'confirm') {
            $stmt = $pdo->prepare("UPDATE DONATION SET Donation_Status = 'Completed' WHERE Donation_ID = ? AND Hospital_ID = ?");
            $stmt->execute([$donation_id, $hospital_id]);
            $message = "<div class='alert alert-success'><i class='fa-solid fa-circle-check'></i> Donation #$donation_id confirmed.</div>";
        } elseif ($action === 'cancel') {
            $stmt = $pdo->prepare("UPDATE DONATION SET Donation_Status = 'Cancelled' WHERE Donation_ID = ? AND Hospital_ID = ?");
            $stmt->execute([$donation_id, $hospital_id]);
            $message = "<div class='alert alert-error'><i class='fa-solid fa-times'></i> Donation #$donation_id cancelled.</div>";
        }
    } catch (PDOException $e) {
        $message = "<div class='alert alert-error'>Error: " . $e->getMessage() . "</div>";
    }
}

try {
    $stmt = $pdo->prepare("SELECT d.*, do.Name AS DonorName, do.Blood_Group, do.Phone AS DonorPhone
                           FROM DONATION d
                           JOIN DONOR do ON d.Donor_ID = do.Donor_ID
                           WHERE d.Hospital_ID = ? AND d.Donation_Status = 'Scheduled'
                           ORDER BY d.Donation_Date ASC");
    $stmt->execute([$hospital_id]);
    $pending = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-circle-check" style="color:var(--primary);margin-right:10px;"></i>Confirm Donations
        </h2>
        <p>Review &amp; confirm scheduled donor appointments below</p>
    </div>
    <a href="dashboard.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
</div>

<?php echo $message; ?>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-clock" style="color:var(--warning);margin-right:8px;"></i>Pending
            Confirmations</span>
        <span class="badge badge-pending"><?php echo count($pending); ?> Awaiting</span>
    </div>

    <?php if (count($pending) > 0): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Donor</th>
                        <th>Blood Group</th>
                        <th>Phone</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending as $d): ?>
                        <tr>
                            <td><strong>#<?php echo $d['Donation_ID']; ?></strong></td>
                            <td><strong><?php echo htmlspecialchars($d['DonorName']); ?></strong></td>
                            <td><span class="badge badge-danger"><?php echo htmlspecialchars($d['Blood_Group']); ?></span></td>
                            <td><?php echo htmlspecialchars($d['DonorPhone']); ?></td>
                            <td><?php echo date('d M Y', strtotime($d['Donation_Date'])); ?></td>
                            <td>
                                <form method="POST" style="display:inline; gap:8px;">
                                    <input type="hidden" name="donation_id" value="<?php echo $d['Donation_ID']; ?>">
                                    <button type="submit" name="action" value="confirm" class="btn btn-success btn-sm">
                                        <i class="fa-solid fa-check"></i> Confirm
                                    </button>
                                    <button type="submit" name="action" value="cancel" class="btn btn-sm"
                                        style="background:var(--accent-red);color:white;margin-left:6px;">
                                        <i class="fa-solid fa-times"></i> Cancel
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div style="text-align:center; padding:40px; color:var(--text-muted);">
            <i class="fa-solid fa-circle-check"
                style="font-size:2.5rem; color:var(--primary); display:block; margin-bottom:12px; opacity:0.5;"></i>
            No pending donations to confirm. All is up to date!
        </div>
    <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>