<?php
// hospital/expiring_units.php
session_start();
define('PAGE_TITLE', 'Expiring Units');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Hospital') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}
$hospital_id = $_SESSION['user_id'];

try {
    // Blood units expiring within 7 days (if Expiry_Date column exists)
    try {
        $stmt = $pdo->prepare("SELECT * FROM BLOOD_INVENTORY WHERE Hospital_ID = ? AND Expiry_Date IS NOT NULL AND Expiry_Date <= DATE_ADD(CURRENT_DATE, INTERVAL 7 DAY) ORDER BY Expiry_Date ASC");
        $stmt->execute([$hospital_id]);
        $expiring = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $expiring = []; // column may not exist
    }

    $inv = $pdo->prepare("SELECT * FROM BLOOD_INVENTORY WHERE Hospital_ID = ? ORDER BY Blood_Group");
    $inv->execute([$hospital_id]);
    $inventory = $inv->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-clock-rotate-left" style="color:var(--warning);margin-right:10px;"></i>Expiring Units
        </h2>
        <p>Monitor blood units nearing expiry dates to prevent wastage</p>
    </div>
    <a href="dashboard.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
</div>

<?php if (count($expiring) > 0): ?>
    <div class="alert alert-error" style="margin-bottom:20px;">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <strong>
            <?php echo count($expiring); ?> batch(es)
        </strong> expiring within 7 days. Please take immediate action to avoid wastage.
    </div>

    <div class="card" style="margin-bottom:24px; border-top:3px solid var(--warning);">
        <div class="card-header">
            <span class="card-title" style="color:var(--warning);"><i class="fa-solid fa-triangle-exclamation"></i> Expiring
                Soon</span>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Blood Group</th>
                        <th>Units</th>
                        <th>Expiry Date</th>
                        <th>Days Left</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($expiring as $e): ?>
                        <?php $days_left = (int) ((strtotime($e['Expiry_Date']) - time()) / 86400); ?>
                        <tr>
                            <td><span class="badge badge-danger">
                                    <?php echo htmlspecialchars($e['Blood_Group']); ?>
                                </span></td>
                            <td>
                                <?php echo $e['Units_Available']; ?>
                            </td>
                            <td>
                                <?php echo date('d M Y', strtotime($e['Expiry_Date'])); ?>
                            </td>
                            <td><span class="badge"
                                    style="background:<?php echo $days_left <= 2 ? 'var(--accent-red)' : 'var(--warning)'; ?>;color:white;">
                                    <?php echo $days_left; ?> day(s)
                                </span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-success" style="margin-bottom:20px;">
        <i class="fa-solid fa-circle-check"></i> No units expiring in the next 7 days. Inventory looks healthy.
    </div>
<?php endif; ?>

<!-- Full Inventory for context -->
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-boxes-stacked"
                style="color:var(--primary);margin-right:8px;"></i>Full Inventory Overview</span>
    </div>
    <?php if ($inventory): ?>
        <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(140px,1fr)); gap:16px; padding:4px 0;">
            <?php foreach ($inventory as $item): ?>
                <div
                    style="text-align:center; background:<?php echo $item['Units_Available'] < 5 ? 'var(--accent-red-light)' : 'var(--primary-light)'; ?>; border-radius:var(--radius-md); padding:20px 14px; border:1.5px solid <?php echo $item['Units_Available'] < 5 ? 'var(--accent-red)' : 'var(--border)'; ?>;">
                    <div
                        style="font-size:1.6rem; font-weight:900; color:<?php echo $item['Units_Available'] < 5 ? 'var(--accent-red)' : 'var(--primary)'; ?>;">
                        <?php echo $item['Blood_Group']; ?>
                    </div>
                    <div style="font-size:1.2rem; font-weight:700; margin:6px 0;">
                        <?php echo $item['Units_Available']; ?>
                    </div>
                    <div style="font-size:0.72rem; color:var(--text-muted);">UNITS</div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p style="padding:20px; text-align:center; color:var(--text-muted);">No inventory data available.</p>
    <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>