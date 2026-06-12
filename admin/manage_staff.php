<?php
// admin/manage_staff.php
session_start();
define('PAGE_TITLE', 'Manage Staff');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Admin') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}

$message = '';

// Handle Add / Edit / Update Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_staff'])) {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $hospital_id = (int) $_POST['hospital_id'];
        $role = trim($_POST['staff_role']); // e.g., 'Nurse', 'Doctor', 'Lab Tech'
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO Staff (Hospital_ID, Name, Role, Email, Password, Status) VALUES (?, ?, ?, ?, ?, 'Active')");
            $stmt->execute([$hospital_id, $name, $role, $email, $password]);
            $message = "<div class='alert alert-success'>Staff member '$name' added successfully.</div>";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate email
                $message = "<div class='alert alert-error'>A staff member with this email already exists.</div>";
            } else {
                $message = "<div class='alert alert-error'>Error adding staff: " . $e->getMessage() . "</div>";
            }
        }
    }
}

// Handle Toggle Status Action
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($id) {
        try {
            if ($action === 'activate') {
                $stmt = $pdo->prepare("UPDATE Staff SET Status = 'Active' WHERE Staff_ID = ?");
                $stmt->execute([$id]);
                $message = "<div class='alert alert-success'>Staff ID #$id activated.</div>";
            } else if ($action === 'deactivate') {
                $stmt = $pdo->prepare("UPDATE Staff SET Status = 'Inactive' WHERE Staff_ID = ?");
                $stmt->execute([$id]);
                $message = "<div class='alert alert-success'>Staff ID #$id deactivated.</div>";
            }
        } catch (PDOException $e) {
            $message = "<div class='alert alert-error'>Error updating status: " . $e->getMessage() . "</div>";
        }
    }
}

// Fetch Staff with their associated Hospitals
try {
    $stmt = $pdo->query("
        SELECT s.*, h.Hospital_Name 
        FROM Staff s 
        LEFT JOIN HOSPITAL h ON s.Hospital_ID = h.Hospital_ID 
        ORDER BY s.Created_At DESC
    ");
    $staff_members = $stmt->fetchAll();

    // Fetch Approved Hospitals for the dropdown
    $h_stmt = $pdo->query("SELECT Hospital_ID, Hospital_Name FROM HOSPITAL WHERE Status = 'Approved' ORDER BY Hospital_Name ASC");
    $active_hospitals = $h_stmt->fetchAll();

} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}
?>

<div style="display: flex; justify-content: space-between; align-items: center;">
    <h2>Manage Hospital Staff</h2>
    <a href="dashboard.php" class="btn">&larr; Back to Dashboard</a>
</div>

<?php echo $message; ?>

<div class="dashboard-grid" style="grid-template-columns: 2fr 1fr;">
    <!-- Staff List Table -->
    <div class="card">
        <h3>Registered Staff</h3>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Hospital</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($staff_members) > 0): ?>
                        <?php foreach ($staff_members as $staff): ?>
                            <tr>
                                <td>#
                                    <?php echo $staff['Staff_ID']; ?>
                                </td>
                                <td>
                                    <strong>
                                        <?php echo htmlspecialchars($staff['Name']); ?>
                                    </strong><br>
                                    <span style="font-size: 0.85em; color: var(--text-muted);">✉️
                                        <?php echo htmlspecialchars($staff['Email']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($staff['Hospital_Name'] ?? 'Unassigned / Error'); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($staff['Role']); ?>
                                </td>
                                <td>
                                    <?php if ($staff['Status'] === 'Active'): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge" style="background: var(--text-muted); color: white;">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($staff['Status'] === 'Active'): ?>
                                        <a href="?action=deactivate&id=<?php echo $staff['Staff_ID']; ?>" class="btn btn-primary"
                                            style="padding: 4px 8px; font-size: 0.8em; background-color: var(--text-muted);"
                                            onclick="return confirm('Deactivate this staff member? They will not be able to log in.');">Deactivate</a>
                                    <?php else: ?>
                                        <a href="?action=activate&id=<?php echo $staff['Staff_ID']; ?>" class="btn btn-success"
                                            style="padding: 4px 8px; font-size: 0.8em;"
                                            onclick="return confirm('Activate this staff member?');">Activate</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 20px;">No staff accounts created yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Staff Form -->
    <div class="card">
        <h3>Add New Staff Account</h3>
        <p style="font-size: 0.85em; color: var(--text-muted); margin-bottom: 15px;">Create a login for a hospital staff
            member (e.g., Nurse, Admin) so they can process donations and issue units.</p>

        <?php if (count($active_hospitals) === 0): ?>
            <div class="alert alert-error">You must approve at least one hospital before you can create staff accounts.
            </div>
        <?php else: ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Staff Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Jane Doe">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" required placeholder="e.g. jane@hospital.com">
                </div>
                <div class="form-group">
                    <label>Temporary Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Assign to Hospital</label>
                    <select name="hospital_id" class="form-control" required>
                        <option value="">Select Hospital...</option>
                        <?php foreach ($active_hospitals as $h): ?>
                            <option value="<?php echo $h['Hospital_ID']; ?>">
                                <?php echo htmlspecialchars($h['Hospital_Name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Job Role / Title</label>
                    <input type="text" name="staff_role" class="form-control" required placeholder="e.g. Head Nurse">
                </div>

                <button type="submit" name="add_staff" class="btn btn-primary" style="width: 100%; margin-top: 10px;">Create
                    Account</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>