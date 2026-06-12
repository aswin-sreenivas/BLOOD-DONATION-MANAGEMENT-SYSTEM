<?php
// recipient/dashboard.php
session_start();
define('PAGE_TITLE', 'Recipient Dashboard');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Recipient') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}

$recipient_id = $_SESSION['user_id'];

// Get KPI Metrics
try {
    $stmt = $pdo->prepare("SELECT 
        SUM(CASE WHEN Status IN ('Pending', 'Matched') THEN 1 ELSE 0 END) as Active_Requests,
        SUM(CASE WHEN Status = 'Fulfilled' THEN 1 ELSE 0 END) as Fulfilled_Requests
        FROM BLOOD_REQUEST WHERE Recipient_ID = ?");
    $stmt->execute([$recipient_id]);
    $metrics = $stmt->fetch(PDO::FETCH_ASSOC);

    $active_reqs = $metrics['Active_Requests'] ?: 0;
    $fulfilled_reqs = $metrics['Fulfilled_Requests'] ?: 0;

    // Fetch Active Requests for Tracker
    $stmt = $pdo->prepare("SELECT * FROM BLOOD_REQUEST WHERE Recipient_ID = ? ORDER BY Request_Date DESC LIMIT 5");
    $stmt->execute([$recipient_id]);
    $recent_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}
?>

<div class="dashboard-grid">
    <div class="card stat-card" style="border-left: 4px solid var(--primary-color);">
        <h3>Active Requests</h3>
        <div class="stat-value" style="color: var(--primary-color);">
            <?php echo $active_reqs; ?>
        </div>
    </div>

    <div class="card stat-card" style="border-left: 4px solid var(--success-color);">
        <h3>Fulfilled Requests</h3>
        <div class="stat-value" style="color: var(--success-color);">
            <?php echo $fulfilled_reqs; ?>
        </div>
    </div>
</div>

<div class="dashboard-grid" style="grid-template-columns: 2fr 1fr;">
    <!-- Request Status Tracker -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3>Request Status Tracker</h3>
            <a href="new_request.php" class="btn btn-primary">Create New Request</a>
        </div>

        <?php if (count($recent_requests) > 0): ?>
            <ul style="list-style: none; padding: 0;">
                <?php foreach ($recent_requests as $req): ?>
                    <li style="border: 1px solid var(--border-color); border-radius: 8px; padding: 15px; margin-bottom: 15px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <strong>Request #
                                <?php echo $req['Request_ID']; ?>
                            </strong>
                            <span style="font-size: 0.85em; color: var(--text-muted);">
                                <?php echo date('M d, Y', strtotime($req['Request_Date'])); ?>
                            </span>
                        </div>

                        <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                            <span class="badge badge-danger">
                                <?php echo htmlspecialchars($req['Blood_Group']); ?>
                            </span>
                            <span class="badge" style="background:var(--text-muted); color:white;">
                                <?php echo $req['Quantity']; ?> Unit(s)
                            </span>
                            <?php if ($req['Emergency_Status'] === 'Critical'): ?>
                                <span class="badge" style="background:red; color:white;">CRITICAL</span>
                            <?php endif; ?>
                        </div>

                        <!-- Visual Timeline -->
                        <div style="display: flex; justify-content: space-between; position: relative; margin-top:20px;">
                            <div
                                style="position: absolute; top: 12px; left: 10%; right: 10%; height: 3px; background: #eee; z-index: 1;">
                            </div>

                            <?php
                            $status = $req['Status'];
                            $is_pending = true;
                            $is_matched = in_array($status, ['Matched', 'Fulfilled']);
                            $is_fulfilled = $status === 'Fulfilled';
                            ?>

                            <!-- Step 1 -->
                            <div style="text-align: center; z-index: 2; flex:1;">
                                <div
                                    style="width: 25px; height: 25px; border-radius: 50%; background: var(--success-color); color: white; line-height: 25px; margin: 0 auto 5px;">
                                    ✓</div>
                                <div style="font-size: 0.8em; font-weight:bold;">Submitted</div>
                            </div>

                            <!-- Step 2 -->
                            <div style="text-align: center; z-index: 2; flex:1;">
                                <div
                                    style="width: 25px; height: 25px; border-radius: 50%; background: <?php echo $is_matched ? 'var(--success-color)' : '#ccc'; ?>; color: white; line-height: 25px; margin: 0 auto 5px;">
                                    <?php echo $is_matched ? '✓' : '2'; ?>
                                </div>
                                <div style="font-size: 0.8em; <?php echo $is_matched ? 'font-weight:bold;' : 'color:#999;'; ?>">
                                    Matched</div>
                            </div>

                            <!-- Step 3 -->
                            <div style="text-align: center; z-index: 2; flex:1;">
                                <div
                                    style="width: 25px; height: 25px; border-radius: 50%; background: <?php echo $is_fulfilled ? 'var(--success-color)' : '#ccc'; ?>; color: white; line-height: 25px; margin: 0 auto 5px;">
                                    <?php echo $is_fulfilled ? '✓' : '3'; ?>
                                </div>
                                <div
                                    style="font-size: 0.8em; <?php echo $is_fulfilled ? 'font-weight:bold;' : 'color:#999;'; ?>">
                                    Fulfilled</div>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div
                style="text-align: center; padding: 40px; color: var(--text-muted); border: 2px dashed var(--border-color); border-radius: 8px;">
                <p>You have no active or recent blood requests.</p>
                <a href="new_request.php" class="btn btn-primary" style="margin-top: 10px;">Create First Request</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Info / Matches Widget -->
    <div class="card">
        <h3>Matches & Instructions</h3>
        <p style="font-size:0.9em; color:var(--text-color); line-height:1.5;">
            Once your request reaches the <strong>Matched</strong> stage, you will be contacted by the hospital or
            assigned donors.
        </p>
        <hr style="margin: 15px 0; border:0; border-top:1px solid var(--border-color);">
        <ul style="font-size:0.9em; padding-left: 20px; color:var(--text-muted);">
            <li style="margin-bottom: 5px;">Ensure your contact number is reachable.</li>
            <li style="margin-bottom: 5px;">Hospitals need the Request ID for verification.</li>
            <li>In case of critical emergencies, the system prioritizes contacting all eligible donors in your district.
            </li>
        </ul>
        <br>
        <a href="profile.php" class="btn" style="width:100%; text-align:center;">Update Contact Info</a>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>