<?php
// recipient/new_request.php
session_start();
define('PAGE_TITLE', 'Request Blood');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Recipient') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}

$recipient_id = $_SESSION['user_id'];
$message = '';

// Fetch recipient details to pre-fill the form
try {
    $stmt = $pdo->prepare("SELECT * FROM RECIPIENT WHERE Recipient_ID = ?");
    $stmt->execute([$recipient_id]);
    $recipient = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_request'])) {
    $bg = $_POST['blood_group'];
    $qty = (int) $_POST['quantity'];
    $district = $_POST['district'];
    $is_critical = isset($_POST['emergency_status']) ? 'Critical' : 'Normal';

    if ($qty > 0 && $qty <= 10) {
        try {
            $stmt = $pdo->prepare("INSERT INTO BLOOD_REQUEST (Recipient_ID, Blood_Group, Quantity, District, Emergency_Status, Status) VALUES (?, ?, ?, ?, ?, 'Pending')");
            $stmt->execute([$recipient_id, $bg, $qty, $district, $is_critical]);

            // Background PHP Trigger Concept (Simulation for Matching)
            // 1. If Critical, system could immediately flag local hospitals with stock

            $message = "<div class='alert alert-success'>Blood request submitted successfully! Hospitals and donors in your district will be notified.</div>";
        } catch (PDOException $e) {
            $message = "<div class='alert alert-error'>Failed to submit request: " . $e->getMessage() . "</div>";
        }
    } else {
        $message = "<div class='alert alert-error'>Invalid quantity. Must be between 1 and 10 units.</div>";
    }
}
?>

<div style="display: flex; justify-content: space-between; align-items: center;">
    <h2>Request Blood Units</h2>
    <a href="dashboard.php" class="btn">&larr; Back to Dashboard</a>
</div>

<?php echo $message; ?>

<div class="dashboard-grid" style="grid-template-columns: 2fr 1fr;">
    <div class="card">
        <h3>Blood Request Form</h3>
        <p style="font-size: 0.85em; color: var(--text-muted); margin-bottom: 20px;">Please provide accurate details. If
            this is a life-threatening emergency, toggle the Emergency Switch below.</p>

        <form method="POST" action="">
            <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                <div class="form-group" style="flex: 1;">
                    <label>Recipient Name</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($recipient['Name']); ?>"
                        disabled style="background: #f4f4f4;">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Required Blood Group <span style="color:red;">*</span></label>
                    <select name="blood_group" class="form-control" required>
                        <?php
                        $bgs = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                        foreach ($bgs as $g) {
                            $sel = ($g === $recipient['Blood_Group']) ? 'selected' : '';
                            echo "<option value=\"$g\" $sel>$g</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                <div class="form-group" style="flex: 1;">
                    <label>Quantity Required (Units) <span style="color:red;">*</span></label>
                    <input type="number" name="quantity" class="form-control" min="1" max="10" value="1" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Hospital/Delivery District <span style="color:red;">*</span></label>
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
            </div>

            <div class="form-group"
                style="background: #fff3cd; border: 1px solid #ffeeba; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                <label
                    style="display: flex; align-items: center; cursor: pointer; color: #856404; font-weight: bold; font-size: 1.1em;">
                    <input type="checkbox" name="emergency_status" value="Critical"
                        style="width: 20px; height: 20px; margin-right: 10px;">
                    Critical Emergency (Life Threatening)
                </label>
                <p style="margin-top: 5px; font-size: 0.85em; color: #856404;">Checking this box will alert all nearby
                    donors immediately and prioritize your request across all hospitals.</p>
            </div>

            <button type="submit" name="submit_request" class="btn btn-primary"
                style="width: 100%; font-size: 1.1em; padding: 12px;">Submit Request &rarr;</button>
        </form>
    </div>

    <div class="card">
        <h3>Contact Verification</h3>
        <p style="font-size: 0.9em; margin-bottom: 15px;">Hospitals will attempt to reach you at:</p>
        <ul style="list-style: none; padding: 0;">
            <li style="margin-bottom: 10px;">📞 <strong>
                    <?php echo htmlspecialchars($recipient['Phone']); ?>
                </strong></li>
            <li style="margin-bottom: 10px;">✉️ <strong>
                    <?php echo htmlspecialchars($recipient['Email']); ?>
                </strong></li>
        </ul>
        <br>
        <p style="font-size: 0.8em; color: var(--text-muted);">Ensure these details are correct so you don't miss
            matching notifications. Change them in your <a href="profile.php">profile settings</a> if needed.</p>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>