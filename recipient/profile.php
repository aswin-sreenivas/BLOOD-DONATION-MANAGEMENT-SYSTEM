<?php
// recipient/profile.php
session_start();
define('PAGE_TITLE', 'My Profile');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Recipient') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}

$recipient_id = $_SESSION['user_id'];
$message = '';

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $district = trim($_POST['district']);

    try {
        $stmt = $pdo->prepare("UPDATE RECIPIENT SET Phone = ?, Email = ?, District = ? WHERE Recipient_ID = ?");
        $stmt->execute([$phone, $email, $district, $recipient_id]);
        $message = "<div class='alert alert-success'>Profile updated successfully.</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-error'>Error updating profile: " . $e->getMessage() . "</div>";
    }
}

// Fetch current details
try {
    $stmt = $pdo->prepare("SELECT * FROM RECIPIENT WHERE Recipient_ID = ?");
    $stmt->execute([$recipient_id]);
    $recipient = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch Request History
    $hist_stmt = $pdo->prepare("SELECT * FROM BLOOD_REQUEST WHERE Recipient_ID = ? ORDER BY Request_Date DESC");
    $hist_stmt->execute([$recipient_id]);
    $history = $hist_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}
?>

<div style="display: flex; justify-content: space-between; align-items: center;">
    <h2>My Profile & Request History</h2>
    <a href="dashboard.php" class="btn">&larr; Back to Dashboard</a>
</div>

<?php echo $message; ?>

<div class="dashboard-grid" style="grid-template-columns: 1fr 2fr;">
    <!-- Profile Edit Form -->
    <div class="card">
        <h3>Update Contact Details</h3>
        <p style="font-size: 0.85em; color: var(--text-muted); margin-bottom: 15px;">Keep your contact information and
            district updated so we can reach you when blood is available.</p>
        <form method="POST" action="">
            <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($recipient['Name']); ?>"
                    disabled style="background:#f0f0f0;">
            </div>
            <div class="form-group">
                <label>Blood Group</label>
                <input type="text" class="form-control"
                    value="<?php echo htmlspecialchars($recipient['Blood_Group']); ?>" disabled
                    style="background:#f0f0f0; color:var(--primary-color); font-weight:bold;">
            </div>

            <hr style="margin: 20px 0; border:0; border-top:1px solid var(--border-color);">

            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" class="form-control"
                    value="<?php echo htmlspecialchars($recipient['Phone']); ?>" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control"
                    value="<?php echo htmlspecialchars($recipient['Email']); ?>" required>
            </div>
            <div class="form-group">
                <label>District</label>
                <select name="district" class="form-control" required>
                    <?php
                    $districts = ['Alappuzha', 'Ernakulam', 'Idukki', 'Kannur', 'Kasaragod', 'Kollam', 'Kottayam', 'Kozhikode', 'Malappuram', 'Palakkad', 'Pathanamthitta', 'Thiruvananthapuram', 'Thrissur', 'Wayanad'];
                    foreach ($districts as $d) {
                        $sel = ($recipient['District'] === $d) ? 'selected' : '';
                        echo "<option value=\"$d\" $sel>$d</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit" name="update_profile" class="btn btn-primary"
                style="width: 100%; margin-top: 10px;">Save Changes</button>
        </form>
    </div>

    <!-- Complete Request History -->
    <div class="card">
        <h3 style="margin-bottom: 20px;">Complete Request History</h3>

        <?php if (count($history) > 0): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Date</th>
                            <th>Units</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $record): ?>
                            <tr>
                                <td>#
                                    <?php echo $record['Request_ID']; ?>
                                </td>
                                <td>
                                    <?php echo date('F d, Y', strtotime($record['Request_Date'])); ?>
                                </td>
                                <td>
                                    <?php echo $record['Quantity']; ?>
                                </td>
                                <td>
                                    <?php if ($record['Status'] === 'Fulfilled'): ?>
                                        <span class="badge badge-success">Fulfilled</span>
                                    <?php elseif (in_array($record['Status'], ['Matched', 'Pending'])): ?>
                                        <span class="badge badge-pending">
                                            <?php echo htmlspecialchars($record['Status']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge" style="background:#999; color:white;">
                                            <?php echo htmlspecialchars($record['Status']); ?>
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
                You haven't made any blood requests yet.
            </p>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>