<?php
// hospital/dashboard.php
session_start();
define('PAGE_TITLE', 'Hospital Dashboard');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Hospital') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}

$hospital_id = $_SESSION['user_id'];

// Get KPI Metrics
try {
    // 1. Live Inventory Count
    $stmt = $pdo->prepare("SELECT SUM(Units_Available) as Total_Units FROM BLOOD_INVENTORY WHERE Hospital_ID = ?");
    $stmt->execute([$hospital_id]);
    $total_units = $stmt->fetchColumn() ?: 0;

    // 2. Pending Donations to Confirm
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM DONATION WHERE Hospital_ID = ? AND Donation_Status = 'Scheduled'");
    $stmt->execute([$hospital_id]);
    $pending_donations = $stmt->fetchColumn();

    // 3. Open Requests fulfilled by this hospital (or pending action)
    // Assuming if donation is tied to request, we can track it. For simplicity, just count recent activity.
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM DONATION WHERE Hospital_ID = ? AND Donation_Status = 'Completed'");
    $stmt->execute([$hospital_id]);
    $fulfilled_donations = $stmt->fetchColumn();

    // 4. Pending Local Requests
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM BLOOD_REQUEST WHERE District = (SELECT Location FROM HOSPITAL WHERE Hospital_ID = ?) AND Status = 'Pending'");
    $stmt->execute([$hospital_id]);
    $pending_local_requests = $stmt->fetchColumn();

    // Inventory by Group (for chart conceptualization)
    $stmt = $pdo->prepare("SELECT Blood_Group, Units_Available FROM BLOOD_INVENTORY WHERE Hospital_ID = ? ORDER BY Blood_Group");
    $stmt->execute([$hospital_id]);
    $inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}
?>

<div class="dashboard-grid">
    <div class="card stat-card">
        <h3>Live Inventory</h3>
        <div class="stat-value">
            <?php echo $total_units; ?> <span style="font-size:0.4em; color:var(--text-muted);">Units</span>
        </div>
    </div>

    <div class="card stat-card" style="border-left: 4px solid #f39c12;">
        <h3>Pending Confirmations</h3>
        <div class="stat-value" style="color: #f39c12;">
            <?php echo $pending_donations; ?>
        </div>
    </div>

    <div class="card stat-card" style="border-left: 4px solid var(--accent-red);">
        <h3>Pending Local Requests</h3>
        <div class="stat-value" style="color: var(--accent-red);">
            <?php echo $pending_local_requests; ?>
        </div>
    </div>

    <div class="card stat-card" style="border-left: 4px solid var(--success-color);">
        <h3>Completed Donations</h3>
        <div class="stat-value" style="color: var(--success-color);">
            <?php echo $fulfilled_donations; ?>
        </div>
    </div>
</div>

<div class="dashboard-grid" style="grid-template-columns: 2fr 1fr;">
    <div class="card">
        <h3>Current Blood Stock</h3>
        <?php if (count($inventory) > 0): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Blood Group</th>
                            <th>Units Available</th>
                            <th>Status / Alert</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inventory as $item): ?>
                            <tr>
                                <td><strong><span class="badge badge-danger">
                                            <?php echo htmlspecialchars($item['Blood_Group']); ?>
                                        </span></strong></td>
                                <td>
                                    <?php echo $item['Units_Available']; ?> Units
                                </td>
                                <td>
                                    <?php if ($item['Units_Available'] < 5): ?>
                                        <span class="badge" style="background: red; color:white;">Low Stock</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Sufficient</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p style="color: var(--text-muted);">No inventory records found. Manage inventory to add stock.</p>
        <?php endif; ?>
        <br>
        <a href="inventory.php" class="btn">Manage Inventory &rarr;</a>
    </div>

    <div class="card" style="height:fit-content;">
        <div class="card-header" style="border-bottom:1px solid var(--border);">
            <span class="card-title"><i class="fa-solid fa-bolt"
                    style="color:var(--primary);margin-right:8px;"></i>Quick Actions</span>
        </div>

        <div style="padding:4px 0; display:flex; flex-direction:column; gap:10px;">
            <!-- Review Pending -->
            <a href="confirm_donations.php" class="btn btn-primary"
                style="display:flex; align-items:center; gap:10px; width:100%; padding:14px 18px; border-radius:var(--radius-md); font-size:0.9rem; justify-content:center;">
                <i class="fa-solid fa-clock-rotate-left"></i> Review Pending Donations
                <?php if ($pending_donations > 0): ?>
                    <span
                        style="background:rgba(255,255,255,0.25); padding:2px 8px; border-radius:12px; font-size:0.75rem; font-weight:700;"><?php echo $pending_donations; ?></span>
                <?php endif; ?>
            </a>

            <!-- Pending Local Requests -->
            <a href="pending_requests.php" class="btn"
                style="display:flex; align-items:center; gap:10px; width:100%; padding:14px 18px; border-radius:var(--radius-md); font-size:0.9rem; justify-content:center; background:var(--accent-red); color:white;">
                <i class="fa-solid fa-hand-holding-droplet"></i> Verify Local Requests
                <?php if ($pending_local_requests > 0): ?>
                    <span
                        style="background:rgba(255,255,255,0.25); padding:2px 8px; border-radius:12px; font-size:0.75rem; font-weight:700;"><?php echo $pending_local_requests; ?></span>
                <?php endif; ?>
            </a>

            <!-- Update Stock -->
            <a href="inventory.php" class="btn btn-outline"
                style="display:flex; align-items:center; gap:10px; width:100%; padding:12px 18px; border-radius:var(--radius-md); font-size:0.9rem; justify-content:center;">
                <i class="fa-solid fa-boxes-stacked"></i> Update Stock
            </a>

            <!-- Donation Records -->
            <a href="donation_records.php" class="btn btn-outline"
                style="display:flex; align-items:center; gap:10px; width:100%; padding:12px 18px; border-radius:var(--radius-md); font-size:0.9rem; justify-content:center;">
                <i class="fa-solid fa-file-medical"></i> Donation Records
            </a>

            <!-- Blood Issues -->
            <a href="blood_issues.php" class="btn btn-outline"
                style="display:flex; align-items:center; gap:10px; width:100%; padding:12px 18px; border-radius:var(--radius-md); font-size:0.9rem; justify-content:center; border-color:var(--accent-red); color:var(--accent-red);">
                <i class="fa-solid fa-droplet"></i> Issue Blood Unit
            </a>
        </div>

        <hr style="border:none; border-top:1px solid var(--border); margin:16px 0;">

        <!-- Alerts -->
        <div>
            <h4
                style="font-size:0.85rem; font-weight:700; color:var(--warning); margin-bottom:12px; display:flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-triangle-exclamation"></i> Alerts
            </h4>

            <?php
            $low_stock_count = 0;
            foreach ($inventory as $item) {
                if ($item['Units_Available'] < 5)
                    $low_stock_count++;
            }
            ?>

            <?php if ($low_stock_count > 0): ?>
                <div
                    style="background:var(--accent-red-light); color:var(--accent-red-dark); padding:14px 16px; border-radius:var(--radius-md); border-left:4px solid var(--accent-red); font-size:0.85rem; margin-bottom:10px; line-height:1.6;">
                    <strong><i class="fa-solid fa-circle-exclamation"></i> Low Stock Warning</strong><br>
                    <?php echo $low_stock_count; ?> blood group(s) have fewer than 5 units.
                    <a href="inventory.php"
                        style="color:var(--accent-red); font-weight:700; text-decoration:underline;">Restock now →</a>
                </div>
            <?php endif; ?>

            <div
                style="background:#fff8ec; color:#856404; padding:14px 16px; border-radius:var(--radius-md); border-left:4px solid #f39c12; font-size:0.85rem; line-height:1.6;">
                <strong><i class="fa-solid fa-clock"></i> Expiry Tracking</strong><br>
                Check expiring units in <a href="expiring_units.php"
                    style="color:#856404; font-weight:700; text-decoration:underline;">Expiring Units</a>.
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>