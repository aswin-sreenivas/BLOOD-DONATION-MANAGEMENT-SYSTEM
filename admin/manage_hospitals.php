<?php
// admin/manage_hospitals.php
session_start();
define('PAGE_TITLE', 'Manage Hospitals');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Admin') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}

$message = '';

// Handle Add Hospital
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_hospital'])) {
    $name = trim($_POST['hospital_name']);
    $loc = trim($_POST['location']);
    $contact = trim($_POST['contact']);
    $email = trim($_POST['email']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO HOSPITAL (Hospital_Name, Location, Contact_Number, Email, Password, Status) VALUES (?, ?, ?, ?, ?, 'Approved')");
        $stmt->execute([$name, $loc, $contact, $email, $pass]);
        $message = "<div class='alert alert-success'>Hospital <strong>$name</strong> added successfully.</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-error'>Error adding hospital: " . $e->getMessage() . "</div>";
    }
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    
    if ($id) {
        try {
            if ($action === 'approve') {
                $stmt = $pdo->prepare("UPDATE HOSPITAL SET Status = 'Approved' WHERE Hospital_ID = ?");
                $stmt->execute([$id]);
                $message = "<div class='alert alert-success'>Hospital ID #$id approved successfully.</div>";
            } else if ($action === 'reject') {
                $stmt = $pdo->prepare("UPDATE HOSPITAL SET Status = 'Rejected' WHERE Hospital_ID = ?");
                $stmt->execute([$id]);
                $message = "<div class='alert alert-error'>Hospital ID #$id rejected.</div>";
            }
        } catch (PDOException $e) {
            $message = "<div class='alert alert-error'>Error updating status: " . $e->getMessage() . "</div>";
        }
    }
}

try {
    $stmt = $pdo->query("SELECT * FROM HOSPITAL ORDER BY Status = 'Pending' DESC, Created_At DESC");
    $hospitals = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Manage Hospitals</h2>
    <div style="display: flex; gap: 10px;">
        <button onclick="document.getElementById('addHospitalForm').style.display='block'" class="btn btn-primary">+ Add Hospital</button>
        <a href="dashboard.php" class="btn">&larr; Back</a>
    </div>
</div>

<?php echo $message; ?>

<!-- Add Hospital Form (Hidden by default) -->
<div id="addHospitalForm" class="card" style="display: none; margin-bottom: 30px; border: 1px solid var(--primary);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h3 style="margin: 0;">Add New Hospital</h3>
        <button onclick="document.getElementById('addHospitalForm').style.display='none'" class="btn btn-sm" style="background:#eee;">&times; Close</button>
    </div>
    <form method="POST">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>Hospital Name</label>
                <input type="text" name="hospital_name" class="form-control" placeholder="e.g. Life Care Clinic" required>
            </div>
            <div class="form-group">
                <label>District / Location</label>
                <input type="text" name="location" class="form-control" placeholder="e.g. Wayanad" required>
            </div>
            <div class="form-group">
                <label>Contact Number</label>
                <input type="text" name="contact" class="form-control" placeholder="e.g. +91 98765..." required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="hospital@example.com" required>
            </div>
            <div class="form-group">
                <label>Default Password</label>
                <input type="password" name="password" class="form-control" value="Hospital@123" required>
            </div>
        </div>
        <button type="submit" name="add_hospital" class="btn btn-primary" style="margin-top: 10px;">Register Hospital</button>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Hospital Name</th>
                    <th>Location</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($hospitals) > 0): ?>
                    <?php foreach($hospitals as $hospital): ?>
                    <tr>
                        <td>#<?php echo $hospital['Hospital_ID']; ?></td>
                        <td><strong><?php echo htmlspecialchars($hospital['Hospital_Name']); ?></strong></td>
                        <td><?php echo nl2br(htmlspecialchars($hospital['Location'])); ?></td>
                        <td>
                            📞 <?php echo htmlspecialchars($hospital['Contact_Number']); ?><br>
                            ✉️ <a href="mailto:<?php echo htmlspecialchars($hospital['Email']); ?>"><?php echo htmlspecialchars($hospital['Email']); ?></a>
                        </td>
                        <td>
                            <?php if($hospital['Status'] === 'Pending'): ?>
                                <span class="badge badge-pending">Pending</span>
                            <?php elseif($hospital['Status'] === 'Approved'): ?>
                                <span class="badge badge-success">Approved</span>
                            <?php else: ?>
                                <span class="badge" style="background: #999; color: white;">Rejected</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($hospital['Status'] === 'Pending'): ?>
                                <a href="?action=approve&id=<?php echo $hospital['Hospital_ID']; ?>" class="btn btn-success" style="padding: 4px 8px; font-size: 0.8em;" onclick="return confirm('Approve this hospital?');"><i class="fa-solid fa-check"></i> Approve</a>
                                <a href="?action=reject&id=<?php echo $hospital['Hospital_ID']; ?>" class="btn btn-primary" style="padding: 4px 8px; font-size: 0.8em; background-color: var(--text-muted);" onclick="return confirm('Reject this hospital?');"><i class="fa-solid fa-xmark"></i> Reject</a>
                            <?php elseif($hospital['Status'] === 'Approved'): ?>
                                <a href="?action=reject&id=<?php echo $hospital['Hospital_ID']; ?>" class="btn btn-primary" style="padding: 4px 8px; font-size: 0.8em; background-color: var(--accent-red);" onclick="return confirm('Suspend this hospital?');"><i class="fa-solid fa-ban"></i> Suspend</a>
                            <?php else: ?>
                                <a href="?action=approve&id=<?php echo $hospital['Hospital_ID']; ?>" class="btn btn-success" style="padding: 4px 8px; font-size: 0.8em;" onclick="return confirm('Restore this hospital?');"><i class="fa-solid fa-rotate-left"></i> Restore</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align: center; padding: 20px;">No hospitals registered yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
