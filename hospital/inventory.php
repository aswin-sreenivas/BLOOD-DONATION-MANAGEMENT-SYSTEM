<?php
// hospital/inventory.php
session_start();
define('PAGE_TITLE', 'Blood Inventory');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Hospital') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}
$hospital_id = $_SESSION['user_id'];
$message = '';

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $bg = $_POST['blood_group'];
    $units = (int) $_POST['units'];
    try {
        // Upsert: if record exists update, else insert
        $check = $pdo->prepare("SELECT Inventory_ID FROM BLOOD_INVENTORY WHERE Hospital_ID = ? AND Blood_Group = ?");
        $check->execute([$hospital_id, $bg]);
        if ($check->fetch()) {
            $stmt = $pdo->prepare("UPDATE BLOOD_INVENTORY SET Units_Available = ? WHERE Hospital_ID = ? AND Blood_Group = ?");
            $stmt->execute([$units, $hospital_id, $bg]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO BLOOD_INVENTORY (Hospital_ID, Blood_Group, Units_Available) VALUES (?, ?, ?)");
            $stmt->execute([$hospital_id, $bg, $units]);
        }
        $message = "<div class='alert alert-success'><i class='fa-solid fa-circle-check'></i> Inventory updated for <strong>$bg</strong>.</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-error'>Error: " . $e->getMessage() . "</div>";
    }
}

try {
    $stmt = $pdo->prepare("SELECT * FROM BLOOD_INVENTORY WHERE Hospital_ID = ? ORDER BY Blood_Group");
    $stmt->execute([$hospital_id]);
    $inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total = array_sum(array_column($inventory, 'Units_Available'));
} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-boxes-stacked" style="color:var(--primary);margin-right:10px;"></i>Blood Inventory
        </h2>
        <p>Manage your hospital's live blood stock per blood group</p>
    </div>
    <a href="dashboard.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
</div>

<?php echo $message; ?>

<div style="display:grid; grid-template-columns:2fr 1fr; gap:24px;">

    <!-- Inventory Table -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-droplet"
                    style="color:var(--accent-red);margin-right:8px;"></i>Current Stock</span>
            <span style="font-size:0.85rem; color:var(--text-muted);">Total: <strong
                    style="color:var(--primary);"><?php echo $total; ?> units</strong></span>
        </div>
        <?php if (count($inventory) > 0): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Blood Group</th>
                            <th>Units Available</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inventory as $item): ?>
                            <tr>
                                <td><span class="badge badge-danger"
                                        style="font-size:0.95rem;"><?php echo htmlspecialchars($item['Blood_Group']); ?></span>
                                </td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <strong style="font-size:1.1rem;"><?php echo $item['Units_Available']; ?></strong>
                                        <div
                                            style="flex:1; background:#eee; border-radius:20px; height:8px; overflow:hidden; max-width:120px;">
                                            <div
                                                style="height:100%; width:<?php echo min(100, $item['Units_Available'] * 5); ?>%; background: <?php echo $item['Units_Available'] < 5 ? 'var(--accent-red)' : 'var(--primary)'; ?>; border-radius:20px;">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($item['Units_Available'] == 0): ?>
                                        <span class="badge" style="background:var(--accent-red); color:white;">Out of Stock</span>
                                    <?php elseif ($item['Units_Available'] < 5): ?>
                                        <span class="badge badge-danger">Low Stock</span>
                                    <?php elseif ($item['Units_Available'] < 10): ?>
                                        <span class="badge badge-pending">Moderate</span>
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
            <p style="padding:20px; text-align:center; color:var(--text-muted);">No inventory records. Add stock using the
                form on the right.</p>
        <?php endif; ?>
    </div>

    <!-- Update Stock Form -->
    <div class="card" style="margin-bottom:0; height:fit-content;">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-pen"
                    style="color:var(--primary);margin-right:8px;"></i>Update Stock</span>
        </div>
        <form method="POST">
            <div class="form-group">
                <label>Blood Group</label>
                <select name="blood_group" class="form-control" required>
                    <?php foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg): ?>
                        <option value="<?php echo $bg; ?>"><?php echo $bg; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Units Available</label>
                <input type="number" name="units" class="form-control" min="0" max="9999" required
                    placeholder="e.g. 25">
            </div>
            <button type="submit" name="update_stock" class="btn btn-primary" style="width:100%;">
                <i class="fa-solid fa-save"></i> Update Stock
            </button>
        </form>
        <hr class="divider">
        <div class="alert alert-error" style="font-size:0.8rem;">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <strong>Low stock (< 5 units)</strong> will trigger system alerts for matching blood requests.
        </div>
    </div>

</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>