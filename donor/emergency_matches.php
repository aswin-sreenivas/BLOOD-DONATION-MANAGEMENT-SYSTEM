<?php
// donor/emergency_matches.php
session_start();
define('PAGE_TITLE', 'Emergency Matches');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Donor') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}
$donor_id = $_SESSION['user_id'];
try {
    $stmt = $pdo->prepare("SELECT * FROM DONOR WHERE Donor_ID = ?");
    $stmt->execute([$donor_id]);
    $donor = $stmt->fetch(PDO::FETCH_ASSOC);

    // All critical requests matching donor's blood group AND district
    $emerg = $pdo->prepare("SELECT r.*, rec.Name as RecipientName, rec.Phone as RecipientPhone
                             FROM BLOOD_REQUEST r
                             LEFT JOIN RECIPIENT rec ON r.Recipient_ID = rec.Recipient_ID
                             WHERE r.Blood_Group = ? AND r.District = ? AND r.Emergency_Status = 'Critical' AND r.Status = 'Pending'
                             ORDER BY r.Request_Date DESC");
    $emerg->execute([$donor['Blood_Group'], $donor['District']]);
    $emergencies = $emerg->fetchAll(PDO::FETCH_ASSOC);

    // Normal pending requests matching blood group + district too
    $normal = $pdo->prepare("SELECT r.*, rec.Name as RecipientName
                              FROM BLOOD_REQUEST r
                              LEFT JOIN RECIPIENT rec ON r.Recipient_ID = rec.Recipient_ID
                              WHERE r.Blood_Group = ? AND r.District = ? AND r.Emergency_Status != 'Critical' AND r.Status = 'Pending'
                              ORDER BY r.Request_Date DESC LIMIT 10");
    $normal->execute([$donor['Blood_Group'], $donor['District']]);
    $normal_requests = $normal->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}
?>

<div class="page-header">
    <div>
        <h2 style="color:var(--accent-red);"><i class="fa-solid fa-siren-on" style="margin-right:10px;"></i>Emergency
            Matches</h2>
        <p>Urgent blood requests matching your profile: <strong>
                <?php echo $donor['Blood_Group']; ?>
            </strong> ·
            <?php echo htmlspecialchars($donor['District']); ?>
        </p>
    </div>
    <a href="dashboard.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
</div>

<!-- Critical Emergencies -->
<div class="card" style="border-top:3px solid var(--accent-red); margin-bottom:24px;">
    <div class="card-header">
        <span class="card-title" style="color:var(--accent-red);"><i class="fa-solid fa-triangle-exclamation"></i>
            &nbsp;Critical Emergencies</span>
        <span class="badge badge-danger">
            <?php echo count($emergencies); ?> Active
        </span>
    </div>

    <?php if (count($emergencies) > 0): ?>
        <?php foreach ($emergencies as $em): ?>
            <div
                style="padding:18px; background:var(--accent-red-light); border-radius:var(--radius-md); margin-bottom:14px; border-left:4px solid var(--accent-red); display:flex; align-items:flex-start; gap:16px;">
                <div style="font-size:1.8rem; color:var(--accent-red); flex-shrink:0;">🩸</div>
                <div style="flex:1;">
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
                        <span class="badge badge-danger">
                            <?php echo htmlspecialchars($em['Blood_Group']); ?>
                        </span>
                        <strong style="color:var(--accent-red-dark); font-size:1rem;">
                            <?php echo $em['Quantity']; ?> Unit(s) Needed URGENTLY
                        </strong>
                        <span class="badge" style="background:var(--accent-red); color:white;">CRITICAL</span>
                    </div>
                    <p style="font-size:0.85rem; color:var(--text-body); margin-bottom:4px;">
                        📍
                        <?php echo htmlspecialchars($em['District']); ?> &nbsp;·&nbsp;
                        Requested by: <strong>
                            <?php echo htmlspecialchars($em['RecipientName'] ?? 'Anonymous'); ?>
                        </strong>
                    </p>
                    <p style="font-size:0.78rem; color:var(--text-muted);">
                        <i class="fa-solid fa-clock"></i>
                        <?php echo date('d M Y, g:i a', strtotime($em['Request_Date'])); ?>
                    </p>
                </div>
                <div style="flex-shrink:0; text-align:center;">
                    <p style="font-size:0.72rem; color:var(--text-muted); margin-bottom:6px;">Contact hospital to<br>fulfill
                        this request</p>
                    <span
                        style="font-size:0.75rem; font-weight:700; color:var(--primary); background:var(--primary-glow); padding:4px 10px; border-radius:20px;">Request
                        #
                        <?php echo $em['Request_ID']; ?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-success" style="margin:0;">
            <i class="fa-solid fa-circle-check"></i> No critical emergencies in your area right now. You're all clear!
        </div>
    <?php endif; ?>
</div>

<!-- Normal Pending Requests -->
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-clipboard-list"
                style="color:var(--primary);margin-right:8px;"></i>Other Pending Requests Near You</span>
        <span class="badge badge-pending">
            <?php echo count($normal_requests); ?>
        </span>
    </div>
    <?php if (count($normal_requests) > 0): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Request #</th>
                        <th>Blood Group</th>
                        <th>Units</th>
                        <th>District</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($normal_requests as $r): ?>
                        <tr>
                            <td><strong>#
                                    <?php echo $r['Request_ID']; ?>
                                </strong></td>
                            <td><span class="badge badge-danger">
                                    <?php echo htmlspecialchars($r['Blood_Group']); ?>
                                </span></td>
                            <td>
                                <?php echo $r['Quantity']; ?> unit(s)
                            </td>
                            <td>
                                <?php echo htmlspecialchars($r['District']); ?>
                            </td>
                            <td>
                                <?php echo date('d M Y', strtotime($r['Request_Date'])); ?>
                            </td>
                            <td><span class="badge badge-pending">Pending</span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="padding:20px; text-align:center; color:var(--text-muted);">No pending requests matching your blood group
            and district.</p>
    <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>