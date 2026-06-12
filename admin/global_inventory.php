<?php
// admin/global_inventory.php
session_start();
define('PAGE_TITLE', 'Global Blood Inventory');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Admin') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}

try {
    // 1. Fetch Aggregated Global Inventory (Total per blood group)
    $stmt_summary = $pdo->query("
        SELECT Blood_Group, SUM(Units_Available) as Total_Units
        FROM BLOOD_INVENTORY
        GROUP BY Blood_Group
        ORDER BY Blood_Group
    ");
    $global_summary = $stmt_summary->fetchAll(PDO::FETCH_ASSOC);

    // Initialise array with 0 for all blood groups to ensure they all show up
    $blood_groups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    $summary_data = array_fill_keys($blood_groups, 0);
    foreach ($global_summary as $row) {
        if (array_key_exists($row['Blood_Group'], $summary_data)) {
            $summary_data[$row['Blood_Group']] = (int) $row['Total_Units'];
        }
    }

    // 2. Fetch Detailed Inventory per Hospital
    $stmt_details = $pdo->query("
        SELECT i.Blood_Group, i.Units_Available, i.Last_Updated,
               h.Hospital_Name, h.Location
        FROM BLOOD_INVENTORY i
        JOIN HOSPITAL h ON i.Hospital_ID = h.Hospital_ID
        WHERE i.Units_Available > 0
        ORDER BY h.Hospital_Name ASC, i.Blood_Group ASC
    ");
    $detailed_inventory = $stmt_details->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}
?>

<div style="display: flex; justify-content: space-between; align-items: center;">
    <h2>Global Blood Inventory</h2>
    <a href="dashboard.php" class="btn">&larr; Back to Dashboard</a>
</div>

<!-- Global Summary Cards -->
<div class="dashboard-grid" style="grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); margin-bottom: 30px;">
    <?php foreach ($summary_data as $bg => $total): ?>
        <div class="card"
            style="text-align: center; padding: 15px; <?php echo ($total < 10) ? 'border-left: 4px solid var(--danger);' : 'border-left: 4px solid var(--success);'; ?>">
            <h3 style="margin-bottom: 5px; color: var(--danger); font-size: 1.5em;">
                <?php echo htmlspecialchars($bg); ?>
            </h3>
            <div style="font-size: 1.8em; font-weight: bold; margin-bottom: 5px;">
                <?php echo $total; ?>
            </div>
            <div style="font-size: 0.85em; color: var(--text-muted);">Total Units</div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Detailed Hospital-wise Inventory -->
<div class="card">
    <h3>Inventory Breakdown by Hospital</h3>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Hospital Name</th>
                    <th>Location (District)</th>
                    <th>Blood Group</th>
                    <th>Units Available</th>
                    <th>Last Updated</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($detailed_inventory) > 0): ?>
                    <?php foreach ($detailed_inventory as $inv): ?>
                        <tr>
                            <td><strong>
                                    <?php echo htmlspecialchars($inv['Hospital_Name']); ?>
                                </strong></td>
                            <td>
                                <?php echo htmlspecialchars($inv['Location']); ?>
                            </td>
                            <td>
                                <span class="badge badge-danger">
                                    <?php echo htmlspecialchars($inv['Blood_Group']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($inv['Units_Available'] < 5): ?>
                                    <span style="color: var(--danger); font-weight: bold;">
                                        <?php echo $inv['Units_Available']; ?> (Low Stock)
                                    </span>
                                <?php else: ?>
                                    <?php echo $inv['Units_Available']; ?>
                                <?php endif; ?>
                            </td>
                            <td style="font-size: 0.9em; color: var(--text-muted);">
                                <?php echo date('M d, Y h:i A', strtotime($inv['Last_Updated'])); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px;">No inventory data recorded in any
                            hospital yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>