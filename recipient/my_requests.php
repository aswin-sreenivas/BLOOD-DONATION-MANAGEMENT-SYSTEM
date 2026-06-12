<?php
// recipient/my_requests.php
session_start();
define('PAGE_TITLE', 'My Requests');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Recipient') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}
$recipient_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM BLOOD_REQUEST WHERE Recipient_ID = ? ORDER BY Request_Date DESC");
    $stmt->execute([$recipient_id]);
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $active = array_filter($requests, fn($r) => in_array($r['Status'], ['Pending', 'Matched']));
    $fulfilled = array_filter($requests, fn($r) => $r['Status'] === 'Fulfilled');
} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-clipboard-list" style="color:var(--primary);margin-right:10px;"></i>My Blood Requests
        </h2>
        <p>All your blood requests and their current status</p>
    </div>
    <a href="new_request.php" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> New Request</a>
</div>

<div class="dashboard-grid" style="margin-bottom:24px;">
    <div class="kpi-card">
        <div class="kpi-icon orange"><i class="fa-solid fa-clock"></i></div>
        <div class="kpi-info">
            <h3>
                <?php echo count($active); ?>
            </h3>
            <p>Active / Pending</p>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon green"><i class="fa-solid fa-circle-check"></i></div>
        <div class="kpi-info">
            <h3>
                <?php echo count($fulfilled); ?>
            </h3>
            <p>Fulfilled</p>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon blue"><i class="fa-solid fa-list"></i></div>
        <div class="kpi-info">
            <h3>
                <?php echo count($requests); ?>
            </h3>
            <p>Total Requests</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-droplet"
                style="color:var(--accent-red);margin-right:8px;"></i>All Requests</span>
    </div>
    <?php if (count($requests) > 0): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Request #</th>
                        <th>Date</th>
                        <th>Blood Group</th>
                        <th>Units</th>
                        <th>District</th>
                        <th>Priority</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $r): ?>
                        <tr>
                            <td><strong>#
                                    <?php echo $r['Request_ID']; ?>
                                </strong></td>
                            <td>
                                <?php echo date('d M Y', strtotime($r['Request_Date'])); ?>
                            </td>
                            <td><span class="badge badge-danger">
                                    <?php echo htmlspecialchars($r['Blood_Group']); ?>
                                </span></td>
                            <td>
                                <?php echo $r['Quantity']; ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($r['District']); ?>
                            </td>
                            <td>
                                <?php if ($r['Emergency_Status'] === 'Critical'): ?>
                                    <span class="badge" style="background:var(--accent-red); color:white;">CRITICAL</span>
                                <?php else: ?>
                                    <span class="badge badge-info">Normal</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $s = $r['Status'];
                                $cls = $s === 'Fulfilled' ? 'badge-success' : ($s === 'Matched' ? 'badge-info' : 'badge-pending');
                                echo "<span class='badge $cls'>$s</span>";
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div style="text-align:center; padding:50px; color:var(--text-muted);">
            <i class="fa-solid fa-clipboard" style="font-size:3rem; opacity:0.3; display:block; margin-bottom:12px;"></i>
            <p>No requests yet. Submit your first blood request when needed.</p>
            <a href="new_request.php" class="btn btn-primary" style="margin-top:16px;">
                <i class="fa-solid fa-plus"></i> Create Request
            </a>
        </div>
    <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>