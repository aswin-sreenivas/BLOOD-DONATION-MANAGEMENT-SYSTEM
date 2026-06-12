<?php
// hospital/leave_appointments.php
session_start();
define('PAGE_TITLE', 'Manage Leaves & Appointments');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Hospital') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}

$hospital_id = $_SESSION['user_id'];
$message = '';

// Handle Leave Approval/Rejection
if (isset($_GET['action']) && isset($_GET['leave_id'])) {
    $action = $_GET['action'];
    $leave_id = filter_var($_GET['leave_id'], FILTER_VALIDATE_INT);
    
    if ($leave_id) {
        try {
            $status = ($action === 'approve') ? 'Approved' : 'Rejected';
            $stmt = $pdo->prepare("UPDATE STAFF_LEAVE sl 
                                   JOIN Staff s ON sl.Staff_ID = s.Staff_ID
                                   SET sl.Status = ? 
                                   WHERE sl.Leave_ID = ? AND s.Hospital_ID = ?");
            $stmt->execute([$status, $leave_id, $hospital_id]);
            $message = "<div class='alert alert-success'>Leave request #$leave_id " . ($action === 'approve' ? 'approved' : 'rejected') . " successfully.</div>";
        } catch (PDOException $e) {
            $message = "<div class='alert alert-error'>Error: " . $e->getMessage() . "</div>";
        }
    }
}

// Fetch Staff Leave Requests
try {
    $stmt = $pdo->prepare("SELECT sl.*, s.Name AS StaffName, s.Role
                           FROM STAFF_LEAVE sl
                           JOIN Staff s ON sl.Staff_ID = s.Staff_ID
                           WHERE s.Hospital_ID = ?
                           ORDER BY sl.Status = 'Pending' DESC, sl.From_Date ASC");
    $stmt->execute([$hospital_id]);
    $leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch Scheduled Donor Appointments
    $stmt2 = $pdo->prepare("SELECT d.Donation_ID, d.Donation_Date, do.Name AS DonorName, do.Blood_Group, d.Donation_Status
                            FROM DONATION d
                            JOIN DONOR do ON d.Donor_ID = do.Donor_ID
                            WHERE d.Hospital_ID = ? AND d.Donation_Status IN ('Scheduled', 'Pending Verification')
                            ORDER BY d.Donation_Date ASC");
    $stmt2->execute([$hospital_id]);
    $appointments = $stmt2->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-calendar-days" style="color:var(--primary);margin-right:10px;"></i>Leaves & Appointments</h2>
        <p>Review staff leave requests and manage donor collection schedule</p>
    </div>
    <a href="dashboard.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
</div>

<?php echo $message; ?>

<div style="display:grid; grid-template-columns: 1.5fr 1fr; gap:24px;">
    <!-- Staff Leaves -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-user-clock" style="color:var(--primary);margin-right:8px;"></i>Staff Leave Requests</span>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Staff</th>
                        <th>Period</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($leaves) > 0): ?>
                        <?php foreach ($leaves as $l): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($l['StaffName']); ?></strong><br>
                                    <span style="font-size:0.8em; color:var(--text-muted);"><?php echo htmlspecialchars($l['Role']); ?></span>
                                </td>
                                <td>
                                    <?php echo date('d M', strtotime($l['From_Date'])); ?> - <?php echo date('d M', strtotime($l['To_Date'])); ?>
                                </td>
                                <td><span title="<?php echo htmlspecialchars($l['Reason']); ?>"><?php echo htmlspecialchars(substr($l['Reason'], 0, 15)); ?>...</span></td>
                                <td>
                                    <?php
                                    $s = $l['Status'];
                                    $cls = $s === 'Approved' ? 'badge-success' : ($s === 'Rejected' ? 'badge-danger' : 'badge-pending');
                                    echo "<span class='badge $cls'>$s</span>";
                                    ?>
                                </td>
                                <td>
                                    <?php if ($l['Status'] === 'Pending'): ?>
                                        <div style="display:flex; gap:5px;">
                                            <a href="?action=approve&leave_id=<?php echo $l['Leave_ID']; ?>" class="btn btn-sm btn-success" style="padding:4px 8px;"><i class="fa-solid fa-check"></i></a>
                                            <a href="?action=reject&leave_id=<?php echo $l['Leave_ID']; ?>" class="btn btn-sm btn-info" style="padding:4px 8px; background:var(--text-muted); border:none;"><i class="fa-solid fa-xmark"></i></a>
                                        </div>
                                    <?php else: ?>
                                        <span style="font-size:0.8em; color:var(--text-muted);">Resolved</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center; padding:20px;">No leave requests found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Donor Appointments -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-hand-holding-heart" style="color:var(--accent-red);margin-right:8px;"></i>Upcoming Donations</span>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Donor</th>
                        <th>Date</th>
                        <th>Group</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($appointments) > 0): ?>
                        <?php foreach ($appointments as $a): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($a['DonorName']); ?></strong></td>
                                <td><?php echo date('d M, Y', strtotime($a['Donation_Date'])); ?></td>
                                <td><span class="badge badge-danger"><?php echo $a['Blood_Group']; ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" style="text-align:center; padding:20px;">No upcoming appointments.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div style="margin-top:15px; text-align:center;">
            <a href="confirm_donations.php" class="btn btn-outline btn-sm" style="width:100%;">Process Appointments</a>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
