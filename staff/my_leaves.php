<?php
// staff/my_leaves.php
session_start();
define('PAGE_TITLE', 'My Leaves');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Staff') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}
$staff_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_leave'])) {
    $from = $_POST['from_date'];
    $to = $_POST['to_date'];
    $reason = trim($_POST['reason']);
    try {
        $stmt = $pdo->prepare("INSERT INTO STAFF_LEAVE (Staff_ID, From_Date, To_Date, Reason, Status) VALUES (?, ?, ?, ?, 'Pending')");
        $stmt->execute([$staff_id, $from, $to, $reason]);
        $message = "<div class='alert alert-success'><i class='fa-solid fa-circle-check'></i> Leave request submitted successfully.</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-error'>Error: " . $e->getMessage() . "</div>";
    }
}

try {
    $stmt = $pdo->prepare("SELECT * FROM STAFF_LEAVE WHERE Staff_ID = ? ORDER BY From_Date DESC");
    $stmt->execute([$staff_id]);
    $leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Table may not exist yet
    $leaves = [];
}
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-calendar-days" style="color:var(--primary);margin-right:10px;"></i>My Leaves</h2>
        <p>Apply for leave and track your leave applications</p>
    </div>
    <a href="dashboard.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
</div>

<?php echo $message; ?>

<div style="display:grid; grid-template-columns:1fr 2fr; gap:24px;">
    <!-- Apply Form -->
    <div class="card" style="margin-bottom:0; height:fit-content;">
        <div class="card-header"><span class="card-title"><i class="fa-solid fa-plus"
                    style="color:var(--primary);margin-right:8px;"></i>Apply for Leave</span></div>
        <form method="POST">
            <div class="form-group">
                <label>From Date</label>
                <input type="date" name="from_date" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-group">
                <label>To Date</label>
                <input type="date" name="to_date" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-group">
                <label>Reason</label>
                <textarea name="reason" class="form-control" rows="4" required
                    placeholder="Briefly state the reason…"></textarea>
            </div>
            <button type="submit" name="apply_leave" class="btn btn-primary" style="width:100%;">
                <i class="fa-solid fa-paper-plane"></i> Submit Request
            </button>
        </form>
    </div>

    <!-- Leave History -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-list"
                    style="color:var(--primary);margin-right:8px;"></i>Leave History</span>
        </div>
        <?php if (count($leaves) > 0): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Days</th>
                            <th>Reason</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leaves as $l): ?>
                            <?php
                            $from_ts = strtotime($l['From_Date']);
                            $to_ts = strtotime($l['To_Date']);
                            $days = max(1, (int) (($to_ts - $from_ts) / 86400) + 1);
                            ?>
                            <tr>
                                <td>#
                                    <?php echo $l['Leave_ID']; ?>
                                </td>
                                <td>
                                    <?php echo date('d M Y', strtotime($l['From_Date'])); ?>
                                </td>
                                <td>
                                    <?php echo date('d M Y', strtotime($l['To_Date'])); ?>
                                </td>
                                <td>
                                    <?php echo $days; ?> day(s)
                                </td>
                                <td>
                                    <?php echo htmlspecialchars(substr($l['Reason'], 0, 30)); ?>…
                                </td>
                                <td>
                                    <?php
                                    $s = $l['Status'];
                                    $cls = $s === 'Approved' ? 'badge-success' : ($s === 'Rejected' ? 'badge-danger' : 'badge-pending');
                                    echo "<span class='badge $cls'>$s</span>";
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p style="text-align:center; padding:30px; color:var(--text-muted);">No leave applications found.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>