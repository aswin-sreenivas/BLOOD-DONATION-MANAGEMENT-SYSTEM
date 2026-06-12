<?php
// hospital/pending_requests.php
session_start();
define('PAGE_TITLE', 'Pending Local Requests');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Hospital') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}

$hospital_id = $_SESSION['user_id'];
$message = '';

// Get hospital details (specifically district/location)
try {
    $stmt = $pdo->prepare("SELECT Location FROM HOSPITAL WHERE Hospital_ID = ?");
    $stmt->execute([$hospital_id]);
    $hospital_district = $stmt->fetchColumn();
} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}

// Handle 'Accept Request' Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_request'])) {
    $request_id = (int) $_POST['request_id'];
    try {
        // Change status to 'Matched'. This allows staff to issue units for it.
        $stmt = $pdo->prepare("UPDATE BLOOD_REQUEST SET Status = 'Matched' WHERE Request_ID = ? AND Status = 'Pending'");
        $stmt->execute([$request_id]);

        if ($stmt->rowCount() > 0) {
            $message = "<div class='alert alert-success'><i class='fa-solid fa-circle-check'></i> Request #$request_id accepted successfully! Tell your staff to issue the units.</div>";
        } else {
            $message = "<div class='alert alert-error'><i class='fa-solid fa-triangle-exclamation'></i> Request could not be accepted. It may have already been matched or cancelled.</div>";
        }
    } catch (PDOException $e) {
        $message = "<div class='alert alert-error'>Error: " . $e->getMessage() . "</div>";
    }
}

// Fetch Pending Requests for this District
try {
    $stmt = $pdo->prepare("
        SELECT r.Request_ID, r.Blood_Group, r.Quantity, r.Emergency_Status, r.Request_Date,
               rec.Name AS RecipientName, rec.Phone AS RecipientPhone
        FROM BLOOD_REQUEST r
        JOIN RECIPIENT rec ON r.Recipient_ID = rec.Recipient_ID
        WHERE r.District = ? AND r.Status = 'Pending'
        ORDER BY CASE WHEN r.Emergency_Status = 'Critical' THEN 1 ELSE 2 END, r.Request_Date DESC
    ");
    $stmt->execute([$hospital_district]);
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-hand-holding-droplet" style="color:var(--primary);margin-right:10px;"></i>Pending
            Local Requests</h2>
        <p>Unfulfilled blood requests in <strong>
                <?php echo htmlspecialchars($hospital_district); ?>
            </strong> district</p>
    </div>
    <a href="dashboard.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
</div>

<?php echo $message; ?>

<div class="card">
    <div class="card-header">
        <span class="card-title">
            <i class="fa-solid fa-users" style="color:var(--primary);margin-right:8px;"></i>Available Requests to
            Fulfill
        </span>
        <span class="badge badge-info">
            <?php echo count($requests); ?> Pending
        </span>
    </div>

    <?php if (count($requests) > 0): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Req #</th>
                        <th>Date Received</th>
                        <th>Required Type</th>
                        <th>Units</th>
                        <th>Recipient Info</th>
                        <th>Priority</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $r): ?>
                        <tr style="<?php echo ($r['Emergency_Status'] == 'Critical') ? 'background:#fff1f0;' : ''; ?>">
                            <td><strong>#
                                    <?php echo $r['Request_ID']; ?>
                                </strong></td>
                            <td>
                                <?php echo date('M d, Y', strtotime($r['Request_Date'])); ?>
                            </td>
                            <td><span class="badge badge-danger">
                                    <?php echo htmlspecialchars($r['Blood_Group']); ?>
                                </span></td>
                            <td><strong>
                                    <?php echo $r['Quantity']; ?>
                                </strong></td>
                            <td>
                                <?php echo htmlspecialchars($r['RecipientName']); ?><br>
                                <span style="font-size:0.85em; color:var(--text-muted);">
                                    <i class="fa-solid fa-phone" style="font-size:0.8em;"></i>
                                    <?php echo htmlspecialchars($r['RecipientPhone']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($r['Emergency_Status'] === 'Critical'): ?>
                                    <span class="badge" style="background:var(--accent-red); color:white;">CRITICAL</span>
                                <?php else: ?>
                                    <span class="badge" style="background:var(--border); color:var(--text-body);">Normal</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="POST" style="margin:0;">
                                    <input type="hidden" name="request_id" value="<?php echo $r['Request_ID']; ?>">
                                    <button type="submit" name="accept_request" class="btn btn-primary btn-sm"
                                        onclick="return confirm('Are you sure you want to accept this request? Your staff will need to issue these units.');">
                                        Match Request
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div style="text-align:center; padding:40px 20px;">
            <i class="fa-solid fa-check-circle"
                style="font-size:3rem; color:var(--success); margin-bottom:15px; opacity:0.8;"></i>
            <h3 style="color:var(--text-main); margin-bottom:8px;">All Clear!</h3>
            <p style="color:var(--text-muted);">There are currently no pending blood requests in the
                <?php echo htmlspecialchars($hospital_district); ?> district.
            </p>
        </div>
    <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>