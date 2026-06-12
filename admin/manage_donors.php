<?php
// admin/manage_donors.php
session_start();
define('PAGE_TITLE', 'Manage Donors');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Admin') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}

$message = '';

// Handle Add Donor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_donor'])) {
    $name = trim($_POST['name']);
    $age = (int) $_POST['age'];
    $gender = $_POST['gender'];
    $bg = $_POST['blood_group'];
    $district = $_POST['district'];
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO DONOR (Name, Age, Gender, Blood_Group, District, Phone, Email, Password, Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Approved')");
        $stmt->execute([$name, $age, $gender, $bg, $district, $phone, $email, $pass]);
        $message = "<div class='alert alert-success'>Donor <strong>$name</strong> added successfully.</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-error'>Error adding donor: " . $e->getMessage() . "</div>";
    }
}

// Handle Approval / Rejection Actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($id) {
        try {
            if ($action === 'approve') {
                $stmt = $pdo->prepare("UPDATE DONOR SET Status = 'Approved' WHERE Donor_ID = ?");
                $stmt->execute([$id]);
                $message = "<div class='alert alert-success'>Donor ID #$id approved successfully.</div>";
            } else if ($action === 'reject') {
                $stmt = $pdo->prepare("UPDATE DONOR SET Status = 'Rejected' WHERE Donor_ID = ?");
                $stmt->execute([$id]);
                $message = "<div class='alert alert-error'>Donor ID #$id rejected.</div>";
            }
        } catch (PDOException $e) {
            $message = "<div class='alert alert-error'>Error updating status: " . $e->getMessage() . "</div>";
        }
    }
}

// Fetch Donors
try {
    $stmt = $pdo->query("SELECT * FROM DONOR ORDER BY Status = 'Pending' DESC, Created_At DESC");
    $donors = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Manage Donors</h2>
    <div style="display: flex; gap: 10px;">
        <button onclick="document.getElementById('addDonorForm').style.display='block'" class="btn btn-primary">+ Add
            Donor</button>
        <a href="dashboard.php" class="btn">&larr; Back</a>
    </div>
</div>

<?php echo $message; ?>

<!-- Add Donor Form (Hidden) -->
<div id="addDonorForm" class="card" style="display: none; margin-bottom: 30px; border: 1px solid var(--primary);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h3 style="margin: 0;">Add New Donor</h3>
        <button onclick="document.getElementById('addDonorForm').style.display='none'" class="btn btn-sm"
            style="background:#eee;">&times; Close</button>
    </div>
    <form method="POST">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Age</label>
                <input type="number" name="age" class="form-control" required min="18" max="65">
            </div>
            <div class="form-group">
                <label>Gender</label>
                <select name="gender" class="form-control" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label>Blood Group</label>
                <select name="blood_group" class="form-control" required>
                    <?php foreach (['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                        echo "<option value='$bg'>$bg</option>"; ?>
                </select>
            </div>
            <div class="form-group">
                <label>District</label>
                <input type="text" name="district" class="form-control" placeholder="e.g. Kozhikode" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" class="form-control" required>
            </div>
            <div class="form-group" style="grid-column: span 2;">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="Donor@123" required>
            </div>
        </div>
        <button type="submit" name="add_donor" class="btn btn-primary">Register Donor</button>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Group</th>
                    <th>District</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($donors) > 0): ?>
                    <?php foreach ($donors as $donor): ?>
                        <tr>
                            <td>#<?php echo $donor['Donor_ID']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($donor['Name']); ?></strong><br>
                                <span style="font-size: 0.8em; color: var(--text-muted);"><?php echo $donor['Gender']; ?>, Age
                                    <?php echo $donor['Age']; ?></span>
                            </td>
                            <td><span class="badge badge-danger"><?php echo htmlspecialchars($donor['Blood_Group']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($donor['District']); ?></td>
                            <td>
                                📞 <?php echo htmlspecialchars($donor['Phone']); ?><br>
                                ✉️ <a
                                    href="mailto:<?php echo htmlspecialchars($donor['Email']); ?>"><?php echo htmlspecialchars($donor['Email']); ?></a>
                            </td>
                            <td>
                                <?php if ($donor['Status'] === 'Pending'): ?>
                                    <span class="badge badge-pending">Pending</span>
                                <?php elseif ($donor['Status'] === 'Approved'): ?>
                                    <span class="badge badge-success">Approved</span>
                                <?php else: ?>
                                    <span class="badge" style="background: #999; color: white;">Rejected</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($donor['Status'] === 'Pending'): ?>
                                    <a href="?action=approve&id=<?php echo $donor['Donor_ID']; ?>" class="btn btn-success"
                                        style="padding: 4px 8px; font-size: 0.8em;"
                                        onclick="return confirm('Approve this donor?');"><i class="fa-solid fa-check"></i>
                                        Approve</a>
                                    <a href="?action=reject&id=<?php echo $donor['Donor_ID']; ?>" class="btn btn-primary"
                                        style="padding: 4px 8px; font-size: 0.8em; background-color: var(--text-muted);"
                                        onclick="return confirm('Reject this donor?');"><i class="fa-solid fa-xmark"></i> Reject</a>
                                <?php elseif ($donor['Status'] === 'Approved'): ?>
                                    <a href="?action=reject&id=<?php echo $donor['Donor_ID']; ?>" class="btn btn-primary"
                                        style="padding: 4px 8px; font-size: 0.8em; background-color: var(--accent-red);"
                                        onclick="return confirm('Suspend this donor?');"><i class="fa-solid fa-ban"></i> Suspend</a>
                                <?php else: ?>
                                    <a href="?action=approve&id=<?php echo $donor['Donor_ID']; ?>" class="btn btn-success"
                                        style="padding: 4px 8px; font-size: 0.8em;"
                                        onclick="return confirm('Restore this donor?');"><i class="fa-solid fa-rotate-left"></i>
                                        Restore</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px;">No donors registered yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>