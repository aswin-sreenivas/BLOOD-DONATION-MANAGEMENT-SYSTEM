<?php
// donor/profile.php
session_start();
define('PAGE_TITLE', 'My Profile');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Donor') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}

$donor_id = $_SESSION['user_id'];
$message = '';

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $district = trim($_POST['district']);

    try {
        $stmt = $pdo->prepare("UPDATE DONOR SET Phone = ?, Email = ?, District = ? WHERE Donor_ID = ?");
        $stmt->execute([$phone, $email, $district, $donor_id]);
        $message = "<div class='alert alert-success'>Profile updated successfully.</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-error'>Error updating profile: " . $e->getMessage() . "</div>";
    }
}

// Fetch current details
try {
    $stmt = $pdo->prepare("SELECT * FROM DONOR WHERE Donor_ID = ?");
    $stmt->execute([$donor_id]);
    $donor = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch Full History
    $hist_stmt = $pdo->prepare("SELECT d.Donation_ID, d.Donation_Date, d.Donation_Status, h.Hospital_Name 
                                FROM DONATION d 
                                LEFT JOIN HOSPITAL h ON d.Hospital_ID = h.Hospital_ID 
                                WHERE d.Donor_ID = ? 
                                ORDER BY d.Donation_Date DESC");
    $hist_stmt->execute([$donor_id]);
    $history = $hist_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}
?>

<div style="display: flex; justify-content: space-between; align-items: center;">
    <h2>My Profile & History</h2>
    <a href="dashboard.php" class="btn">&larr; Back to Dashboard</a>
</div>

<?php echo $message; ?>

<div class="dashboard-grid" style="grid-template-columns: 1fr 2fr;">
    <!-- Profile Edit Form -->
    <div class="card">
        <h3>Update Contact Details</h3>
        <p style="font-size: 0.85em; color: var(--text-muted); margin-bottom: 15px;">Keep your contact information and
            district updated so we can reach you for emergency requests.</p>
        <form method="POST" action="">
            <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($donor['Name']); ?>" disabled
                    style="background:#f0f0f0;">
            </div>
            <div class="form-group" style="display: flex; gap: 10px;">
                <div style="flex:1;">
                    <label>Age</label>
                    <input type="text" class="form-control" value="<?php echo $donor['Age']; ?>" disabled
                        style="background:#f0f0f0;">
                </div>
                <div style="flex:1;">
                    <label>Blood Group</label>
                    <input type="text" class="form-control"
                        value="<?php echo htmlspecialchars($donor['Blood_Group']); ?>" disabled
                        style="background:#f0f0f0; color:var(--primary-color); font-weight:bold;">
                </div>
            </div>

            <hr style="margin: 20px 0; border:0; border-top:1px solid var(--border-color);">

            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" class="form-control"
                    value="<?php echo htmlspecialchars($donor['Phone']); ?>" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control"
                    value="<?php echo htmlspecialchars($donor['Email']); ?>" required>
            </div>
            <div class="form-group">
                <label>District</label>
                <select name="district" class="form-control" required>
                    <?php
                    $districts = ['Alappuzha', 'Ernakulam', 'Idukki', 'Kannur', 'Kasaragod', 'Kollam', 'Kottayam', 'Kozhikode', 'Malappuram', 'Palakkad', 'Pathanamthitta', 'Thiruvananthapuram', 'Thrissur', 'Wayanad'];
                    foreach ($districts as $d) {
                        $sel = ($donor['District'] === $d) ? 'selected' : '';
                        echo "<option value=\"$d\" $sel>$d</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit" name="update_profile" class="btn btn-primary"
                style="width: 100%; margin-top: 10px;">Save Changes</button>
        </form>
    </div>

    <!-- Complete Donation History -->
    <div class="card">
        <h3 style="margin-bottom: 20px;">Complete Donation History</h3>

        <?php if (count($history) > 0): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Donation ID</th>
                            <th>Date</th>
                            <th>Location / Hospital</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $record): ?>
                            <tr>
                                <td>#
                                    <?php echo $record['Donation_ID']; ?>
                                </td>
                                <td>
                                    <?php echo date('F d, Y', strtotime($record['Donation_Date'])); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($record['Hospital_Name'] ?: 'Recorded by Admin'); ?>
                                </td>
                                <td>
                                    <?php if ($record['Donation_Status'] === 'Completed'): ?>
                                        <span class="badge badge-success">Completed</span>
                                    <?php elseif ($record['Donation_Status'] === 'Scheduled'): ?>
                                        <span class="badge badge-pending">Scheduled</span>
                                    <?php else: ?>
                                        <span class="badge" style="background:#999; color:white;">
                                            <?php echo htmlspecialchars($record['Donation_Status']); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p
                style="color: var(--text-muted); padding: 20px; border: 1px dashed var(--border-color); text-align: center; border-radius: 5px;">
                You haven't made any donations yet. Your history will appear here once a hospital staff confirms your
                completed donation.
            </p>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>