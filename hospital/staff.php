<?php
// hospital/staff.php
session_start();
define('PAGE_TITLE', 'Hospital Staff Management');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Hospital') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}

$hospital_id = $_SESSION['user_id'];
$message = '';

// Handle Staff Activation/Deactivation
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $staff_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($staff_id) {
        try {
            $status = ($action === 'activate') ? 'Active' : 'Inactive';
            $stmt = $pdo->prepare("UPDATE Staff SET Status = ? WHERE Staff_ID = ? AND Hospital_ID = ?");
            $stmt->execute([$status, $staff_id, $hospital_id]);
            $message = "<div class='alert alert-success'>Staff member " . ($action === 'activate' ? 'activated' : 'deactivated') . " successfully.</div>";
        } catch (PDOException $e) {
            $message = "<div class='alert alert-error'>Error updating status: " . $e->getMessage() . "</div>";
        }
    }
}

// Fetch Hospital Staff
try {
    $stmt = $pdo->prepare("SELECT * FROM Staff WHERE Hospital_ID = ? ORDER BY Name ASC");
    $stmt->execute([$hospital_id]);
    $staff_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}
?>

<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-user-nurse" style="color:var(--primary);margin-right:10px;"></i>Manage Staff</h2>
        <p>View and manage staff members registered under your hospital</p>
    </div>
    <a href="dashboard.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
</div>

<?php echo $message; ?>

<div class="card">
    <div class="card-header">
        <span class="card-title">Hospital Personnel</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Staff ID</th>
                    <th>Name</th>
                    <th>Designation</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($staff_list) > 0): ?>
                    <?php foreach ($staff_list as $staff): ?>
                        <tr>
                            <td>#
                                <?php echo $staff['Staff_ID']; ?>
                            </td>
                            <td><strong>
                                    <?php echo htmlspecialchars($staff['Name']); ?>
                                </strong></td>
                            <td>
                                <?php echo htmlspecialchars($staff['Role'] ?? 'Staff'); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($staff['Email']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($staff['Phone'] ?? '—'); ?>
                            </td>
                            <td>
                                <?php if ($staff['Status'] === 'Active'): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-info" style="background:var(--text-muted);">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($staff['Status'] === 'Active'): ?>
                                    <a href="?action=deactivate&id=<?php echo $staff['Staff_ID']; ?>" class="btn btn-sm btn-outline"
                                        style="border-color:var(--accent-red); color:var(--accent-red);"
                                        onclick="return confirm('Deactivate this staff member?');">Deactivate</a>
                                <?php else: ?>
                                    <a href="?action=activate&id=<?php echo $staff['Staff_ID']; ?>" class="btn btn-sm btn-success"
                                        onclick="return confirm('Activate this staff member?');">Activate</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center; padding:30px; color:var(--text-muted);">No staff
                            registered for your hospital yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>