<?php
// admin/all_requests.php
session_start();
define('PAGE_TITLE', 'All Blood Requests');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Admin') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cancel_req'])) {
        $req_id = (int) $_POST['req_id'];
        try {
            $pdo->prepare("UPDATE BLOOD_REQUEST SET Status = 'Cancelled' WHERE Request_ID = ? AND Status IN ('Pending', 'Matched')")->execute([$req_id]);
            $message = "<div class='alert alert-success'><i class='fa-solid fa-ban'></i> Request #$req_id cancelled.</div>";
        } catch (PDOException $e) { /* ignore */
        }
    } elseif (isset($_POST['del_req'])) {
        $req_id = (int) $_POST['req_id'];
        try {
            $pdo->prepare("DELETE FROM BLOOD_REQUEST WHERE Request_ID = ?")->execute([$req_id]);
            $message = "<div class='alert alert-warning'><i class='fa-solid fa-trash'></i> Request #$req_id deleted permanently.</div>";
        } catch (PDOException $e) { /* ignore */
        }
    }
}

try {
    // Fetch all requests along with recipient info
    $stmt = $pdo->query("
        SELECT r.Request_ID, r.Blood_Group, r.Quantity, r.District, r.Emergency_Status, r.Request_Date, r.Status,
               rec.Name AS RecipientName, rec.Phone AS RecipientPhone
        FROM BLOOD_REQUEST r
        JOIN RECIPIENT rec ON r.Recipient_ID = rec.Recipient_ID
        ORDER BY r.Request_Date DESC
    ");
    $requests = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}
?>

<div style="display: flex; justify-content: space-between; align-items: center;">
    <h2>All Blood Requests (Global)</h2>
    <a href="dashboard.php" class="btn">&larr; Back to Dashboard</a>
</div>

<?php if (isset($message))
    echo $message; ?>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Req #</th>
                    <th>Date</th>
                    <th>Recipient</th>
                    <th>Blood Group</th>
                    <th>Qty</th>
                    <th>District</th>
                    <th>Emergency</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($requests) > 0): ?>
                    <?php foreach ($requests as $req): ?>
                        <tr style="<?php echo ($req['Emergency_Status'] == 'Critical') ? 'background:#fff1f0;' : ''; ?>">
                            <td><strong>#
                                    <?php echo $req['Request_ID']; ?>
                                </strong></td>
                            <td>
                                <?php echo date('M d, Y', strtotime($req['Request_Date'])); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($req['RecipientName']); ?><br>
                                <span style="font-size: 0.85em; color: var(--text-muted);">📞
                                    <?php echo htmlspecialchars($req['RecipientPhone']); ?>
                                </span>
                            </td>
                            <td><span class="badge badge-danger">
                                    <?php echo htmlspecialchars($req['Blood_Group']); ?>
                                </span></td>
                            <td>
                                <?php echo $req['Quantity']; ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($req['District']); ?>
                            </td>
                            <td>
                                <?php if ($req['Emergency_Status'] === 'Critical'): ?>
                                    <span class="badge" style="background:red; color:white;">CRITICAL</span>
                                <?php else: ?>
                                    <span class="badge" style="background:#ccc; color:#333;">Normal</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($req['Status'] === 'Fulfilled'): ?>
                                    <span class="badge badge-success">Fulfilled</span>
                                <?php elseif (in_array($req['Status'], ['Matched', 'Pending'])): ?>
                                    <span class="badge badge-pending">
                                        <?php echo htmlspecialchars($req['Status']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge" style="background:#999; color:white;">
                                        <?php echo htmlspecialchars($req['Status']); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="POST" style="display:inline-block; margin:0;"
                                    onsubmit="return confirm('Are you sure?');">
                                    <input type="hidden" name="req_id" value="<?php echo $req['Request_ID']; ?>">
                                    <?php if (in_array($req['Status'], ['Pending', 'Matched'])): ?>
                                        <button type="submit" name="cancel_req" class="btn btn-outline btn-sm"
                                            title="Cancel Request"><i class="fa-solid fa-ban"></i></button>
                                    <?php endif; ?>
                                    <button type="submit" name="del_req" class="btn btn-outline btn-sm"
                                        style="color:var(--accent-red);border-color:var(--accent-red);" title="Delete Record"><i
                                            class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px;">No blood requests found in the system.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>