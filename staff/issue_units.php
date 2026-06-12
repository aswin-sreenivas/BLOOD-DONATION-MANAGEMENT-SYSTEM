<?php
// staff/issue_units.php
session_start();
define('PAGE_TITLE', 'Issue Blood Units');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Staff') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}
$hospital_id = $_SESSION['hospital_id'] ?? 0;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['issue_units'])) {
    $blood_group = $_POST['blood_group'];
    $units = (int) $_POST['units'];
    $request_id = (int) $_POST['request_id'];
    try {
        // Deduct from inventory
        $stmt = $pdo->prepare("UPDATE BLOOD_INVENTORY SET Units_Available = Units_Available - ? WHERE Hospital_ID = ? AND Blood_Group = ? AND Units_Available >= ?");
        $stmt->execute([$units, $hospital_id, $blood_group, $units]);

        if ($stmt->rowCount() > 0) {
            // Mark request fulfilled if an ID was provided
            if ($request_id > 0) {
                $stmt2 = $pdo->prepare("UPDATE BLOOD_REQUEST SET Status = 'Fulfilled' WHERE Request_ID = ?");
                $stmt2->execute([$request_id]);
            }
            $message = "<div class='alert alert-success'><i class='fa-solid fa-circle-check'></i> Issued $units unit(s) of <strong>$blood_group</strong> successfully.</div>";
        } else {
            $message = "<div class='alert alert-error'><i class='fa-solid fa-triangle-exclamation'></i> Insufficient stock of <strong>$blood_group</strong> to issue $units unit(s).</div>";
        }
    } catch (PDOException $e) {
        $message = "<div class='alert alert-error'>Error: " . $e->getMessage() . "</div>";
    }
}

try {
    $inv = $pdo->prepare("SELECT * FROM BLOOD_INVENTORY WHERE Hospital_ID = ? ORDER BY Blood_Group");
    $inv->execute([$hospital_id]);
    $inventory = $inv->fetchAll(PDO::FETCH_ASSOC);

    // Pending matched requests for this hospital
    $req = $pdo->query("SELECT r.Request_ID, r.Blood_Group, r.Quantity, r.District, rec.Name AS RecipientName
                         FROM BLOOD_REQUEST r JOIN RECIPIENT rec ON r.Recipient_ID = rec.Recipient_ID
                         WHERE r.Status = 'Matched' ORDER BY r.Request_Date ASC LIMIT 10");
    $requests = $req ? $req->fetchAll(PDO::FETCH_ASSOC) : [];
} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-droplet" style="color:var(--accent-red);margin-right:10px;"></i>Issue Blood Units</h2>
        <p>Dispense blood units from hospital inventory to matched requests</p>
    </div>
    <a href="dashboard.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
</div>

<?php echo $message; ?>

<div style="display:grid; grid-template-columns:1fr 2fr; gap:24px;">

    <!-- Issue Form -->
    <div style="display:flex; flex-direction:column; gap:20px;">
        <div class="card" style="margin-bottom:0;">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-arrow-right-from-bracket"
                        style="color:var(--accent-red);margin-right:8px;"></i>Issue Units</span>
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
                    <label>Units to Issue</label>
                    <input type="number" name="units" class="form-control" min="1" max="20" required placeholder="1">
                </div>
                <div class="form-group">
                    <label>Linked Request # <small style="color:var(--text-muted);">(optional)</small></label>
                    <input type="number" name="request_id" class="form-control" min="0" placeholder="e.g. 42">
                </div>
                <button type="submit" name="issue_units" class="btn btn-primary" style="width:100%;">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Issue Now
                </button>
            </form>
        </div>

        <!-- Current Inventory Mini -->
        <div class="card" style="margin-bottom:0;">
            <div class="card-header"><span class="card-title">Available Stock</span></div>
            <?php if ($inventory): ?>
                <?php foreach ($inventory as $i): ?>
                    <div
                        style="display:flex; align-items:center; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--border);">
                        <span class="badge badge-danger"><?php echo htmlspecialchars($i['Blood_Group']); ?></span>
                        <strong style="color:<?php echo $i['Units_Available'] < 5 ? 'var(--accent-red)' : 'var(--primary)'; ?>">
                            <?php echo $i['Units_Available']; ?> units
                        </strong>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color:var(--text-muted); font-size:0.85rem; padding:10px 0;">No inventory recorded yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Matched Requests -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-link"
                    style="color:var(--primary);margin-right:8px;"></i>Matched Requests</span>
            <span class="badge badge-info"><?php echo count($requests); ?> Awaiting Issue</span>
        </div>
        <?php if ($requests): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Request #</th>
                            <th>Blood Group</th>
                            <th>Units</th>
                            <th>Recipient</th>
                            <th>District</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $r): ?>
                            <tr>
                                <td><strong>#<?php echo $r['Request_ID']; ?></strong></td>
                                <td><span class="badge badge-danger"><?php echo htmlspecialchars($r['Blood_Group']); ?></span>
                                </td>
                                <td><?php echo $r['Quantity']; ?></td>
                                <td><?php echo htmlspecialchars($r['RecipientName']); ?></td>
                                <td><?php echo htmlspecialchars($r['District']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p style="padding:30px; text-align:center; color:var(--text-muted);">No matched requests pending issue.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>