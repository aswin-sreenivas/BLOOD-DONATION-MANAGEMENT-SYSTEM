<?php
// staff/process_donation.php
session_start();
define('PAGE_TITLE', 'Process Donation');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Staff') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}
$hospital_id = $_SESSION['hospital_id'] ?? 0;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['record_donation'])) {
    $donor_id = (int) $_POST['donor_id'];
    $date = $_POST['donation_date'];
    $units = (int) $_POST['units'];
    $status = $_POST['status'];
    try {
        $stmt = $pdo->prepare("INSERT INTO DONATION (Donor_ID, Hospital_ID, Donation_Date, Units_Donated, Donation_Status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$donor_id, $hospital_id, $date, $units, $status]);

        // Update donor last donation date if confirmed
        if ($status === 'Completed') {
            $upd = $pdo->prepare("UPDATE DONOR SET Last_Donation_Date = ? WHERE Donor_ID = ?");
            $upd->execute([$date, $donor_id]);
        }
        $message = "<div class='alert alert-success'><i class='fa-solid fa-circle-check'></i> Donation recorded successfully.</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-error'>Error: " . $e->getMessage() . "</div>";
    }
}

try {
    // Fetch approved donors for the dropdown
    $donors = $pdo->query("SELECT Donor_ID, Name, Blood_Group FROM DONOR WHERE Status = 'Approved' AND Availability_Status = 'Available' ORDER BY Name")->fetchAll(PDO::FETCH_ASSOC);

    // Today's donations at this hospital
    $stmt = $pdo->prepare("SELECT d.*, do.Name AS DonorName, do.Blood_Group FROM DONATION d JOIN DONOR do ON d.Donor_ID = do.Donor_ID WHERE d.Hospital_ID = ? AND d.Donation_Date = CURRENT_DATE ORDER BY d.Donation_ID DESC");
    $stmt->execute([$hospital_id]);
    $today = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-heart-pulse" style="color:var(--accent-red);margin-right:10px;"></i>Process Donation
        </h2>
        <p>Record a new blood donation entry for today</p>
    </div>
    <a href="dashboard.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
</div>

<?php echo $message; ?>

<div style="display:grid; grid-template-columns:1fr 2fr; gap:24px;">

    <!-- Record Form -->
    <div class="card" style="margin-bottom:0; height:fit-content;">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-plus" style="color:var(--primary);margin-right:8px;"></i>New
                Entry</span>
        </div>
        <form method="POST">
            <div class="form-group">
                <label>Donor</label>
                <select name="donor_id" class="form-control" required>
                    <option value="">Select Donor…</option>
                    <?php foreach ($donors as $d): ?>
                        <option value="<?php echo $d['Donor_ID']; ?>"><?php echo htmlspecialchars($d['Name']); ?>
                            (<?php echo $d['Blood_Group']; ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Donation Date</label>
                <input type="date" name="donation_date" class="form-control" value="<?php echo date('Y-m-d'); ?>"
                    required>
            </div>
            <div class="form-group">
                <label>Units Donated</label>
                <input type="number" name="units" class="form-control" value="1" min="1" max="5" required>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="Completed">Completed</option>
                    <option value="Scheduled">Scheduled</option>
                </select>
            </div>
            <button type="submit" name="record_donation" class="btn btn-primary" style="width:100%;">
                <i class="fa-solid fa-save"></i> Record Donation
            </button>
        </form>
    </div>

    <!-- Today's Records -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-calendar-day"
                    style="color:var(--primary);margin-right:8px;"></i>Today's Donations
                (<?php echo date('d M Y'); ?>)</span>
            <span class="badge badge-success"><?php echo count($today); ?> Records</span>
        </div>
        <?php if (count($today) > 0): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Donor</th>
                            <th>Blood Group</th>
                            <th>Units</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($today as $t): ?>
                            <tr>
                                <td>#<?php echo $t['Donation_ID']; ?></td>
                                <td><strong><?php echo htmlspecialchars($t['DonorName']); ?></strong></td>
                                <td><span class="badge badge-danger"><?php echo htmlspecialchars($t['Blood_Group']); ?></span>
                                </td>
                                <td><?php echo $t['Units_Donated']; ?></td>
                                <td>
                                    <?php $s = $t['Donation_Status'];
                                    $c = $s === 'Completed' ? 'badge-success' : 'badge-pending'; ?>
                                    <span class="badge <?php echo $c; ?>"><?php echo $s; ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p style="text-align:center; padding:30px; color:var(--text-muted);">No donations processed today yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>