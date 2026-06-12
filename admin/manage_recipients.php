<?php
// admin/manage_recipients.php
session_start();
define('PAGE_TITLE', 'Manage Recipients');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Admin') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}

$message = '';

// Handle Add Recipient
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_recipient'])) {
    $name = trim($_POST['name']);
    $bg = $_POST['blood_group'];
    $district = $_POST['district'];
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $emergency = $_POST['emergency_flag'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO RECIPIENT (Name, Blood_Group, District, Phone, Email, Emergency_Flag, Password, Status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Approved')");
        $stmt->execute([$name, $bg, $district, $phone, $email, $emergency, $pass]);
        $message = "<div class='alert alert-success'>Recipient <strong>$name</strong> added successfully.</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-error'>Error adding recipient: " . $e->getMessage() . "</div>";
    }
}

// Handle Approval / Rejection Actions for Recipients
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($id) {
        try {
            if ($action === 'approve') {
                $stmt = $pdo->prepare("UPDATE RECIPIENT SET Status = 'Approved' WHERE Recipient_ID = ?");
                $stmt->execute([$id]);
                $message = "<div class='alert alert-success'>Recipient ID #$id approved successfully.</div>";
            } else if ($action === 'reject') {
                $stmt = $pdo->prepare("UPDATE RECIPIENT SET Status = 'Rejected' WHERE Recipient_ID = ?");
                $stmt->execute([$id]);
                $message = "<div class='alert alert-error'>Recipient ID #$id rejected limit.</div>";
            }
        } catch (PDOException $e) {
            $message = "<div class='alert alert-error'>Error updating status: " . $e->getMessage() . "</div>";
        }
    }
}

try {
    // Recipients don't have "pending" status usually, but we'll list them to show management ability
    $stmt = $pdo->query("SELECT * FROM RECIPIENT ORDER BY Created_At DESC");
    $recipients = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Manage Recipients</h2>
    <div style="display: flex; gap: 10px;">
        <button onclick="document.getElementById('addRecipientForm').style.display='block'" class="btn btn-primary">+
            Add Recipient</button>
        <a href="dashboard.php" class="btn">&larr; Back</a>
    </div>
</div>

<?php echo $message; ?>

<!-- Add Recipient Form (Hidden) -->
<div id="addRecipientForm" class="card" style="display: none; margin-bottom: 30px; border: 1px solid var(--primary);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h3 style="margin: 0;">Add New Recipient</h3>
        <button onclick="document.getElementById('addRecipientForm').style.display='none'" class="btn btn-sm"
            style="background:#eee;">&times; Close</button>
    </div>
    <form method="POST">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Blood Group</label>
                <select name="blood_group" class="form-control" required>
                    <?php foreach (['A+', 'A-', 'B+', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                        echo "<option value='$bg'>$bg</option>"; ?>
                </select>
            </div>
            <div class="form-group">
                <label>District</label>
                <input type="text" name="district" class="form-control" placeholder="e.g. Malappuram" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Emergency Flag</label>
                <select name="emergency_flag" class="form-control">
                    <option value="No">No</option>
                    <option value="Yes">Yes</option>
                </select>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="Recipient@123" required>
            </div>
        </div>
        <button type="submit" name="add_recipient" class="btn btn-primary">Register Recipient</button>
    </form>
</div>
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Blood Group</th>
                    <th>District</th>
                    <th>Contact</th>
                    <th>Emergency Flag</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($recipients) > 0): ?>
                    <?php foreach ($recipients as $recipient): ?>
                        <tr>
                            <td>#
                                <?php echo $recipient['Recipient_ID']; ?>
                            </td>
                            <td><strong>
                                    <?php echo htmlspecialchars($recipient['Name']); ?>
                                </strong></td>
                            <td><span class="badge badge-danger">
                                    <?php echo htmlspecialchars($recipient['Blood_Group']); ?>
                                </span></td>
                            <td>
                                <?php echo htmlspecialchars($recipient['District']); ?>
                            </td>
                            <td>
                                📞
                                <?php echo htmlspecialchars($recipient['Phone']); ?><br>
                                ✉️ <a href="mailto:<?php echo htmlspecialchars($recipient['Email']); ?>">
                                    <?php echo htmlspecialchars($recipient['Email']); ?>
                                </a>
                            </td>
                            <td>
                                <?php if ($recipient['Emergency_Flag'] === 'Yes'): ?>
                                    <span class="badge" style="background: red; color: white;">Yes</span>
                                <?php else: ?>
                                    <span class="badge" style="background: var(--primary); color: white;">No</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($recipient['Status'] === 'Pending'): ?>
                                    <span class="badge badge-pending">Pending</span>
                                <?php elseif ($recipient['Status'] === 'Approved'): ?>
                                    <span class="badge badge-success">Approved</span>
                                <?php else: ?>
                                    <span class="badge" style="background: #999; color: white;">Rejected</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($recipient['Status'] === 'Pending'): ?>
                                    <a href="?action=approve&id=<?php echo $recipient['Recipient_ID']; ?>" class="btn btn-success"
                                        style="padding: 4px 8px; font-size: 0.8em;"
                                        onclick="return confirm('Approve this recipient?');"><i class="fa-solid fa-check"></i>
                                        Approve</a>
                                    <a href="?action=reject&id=<?php echo $recipient['Recipient_ID']; ?>" class="btn btn-primary"
                                        style="padding: 4px 8px; font-size: 0.8em; background-color: var(--text-muted);"
                                        onclick="return confirm('Reject this recipient?');"><i class="fa-solid fa-xmark"></i>
                                        Reject</a>
                                <?php elseif ($recipient['Status'] === 'Approved'): ?>
                                    <a href="?action=reject&id=<?php echo $recipient['Recipient_ID']; ?>" class="btn btn-primary"
                                        style="padding: 4px 8px; font-size: 0.8em; background-color: var(--accent-red);"
                                        onclick="return confirm('Suspend this recipient?');"><i class="fa-solid fa-ban"></i>
                                        Suspend</a>
                                <?php else: ?>
                                    <a href="?action=approve&id=<?php echo $recipient['Recipient_ID']; ?>" class="btn btn-success"
                                        style="padding: 4px 8px; font-size: 0.8em;"
                                        onclick="return confirm('Restore this recipient?');"><i class="fa-solid fa-rotate-left"></i>
                                        Restore</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px;">No recipients registered yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>