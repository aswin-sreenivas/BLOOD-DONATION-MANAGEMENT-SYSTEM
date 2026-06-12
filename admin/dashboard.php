<?php
// admin/dashboard.php
session_start();
define('PAGE_TITLE', 'Admin Dashboard');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Admin') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}

// Fetch KPI Data
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM DONOR WHERE Status = 'Approved'");
    $total_donors = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM HOSPITAL WHERE Status = 'Approved'");
    $total_hospitals = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM BLOOD_REQUEST WHERE Status = 'Pending' OR Status = 'Matched'");
    $active_requests = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT
        (SELECT COUNT(*) FROM DONOR WHERE Status = 'Pending') +
        (SELECT COUNT(*) FROM RECIPIENT WHERE Status = 'Pending') +
        (SELECT COUNT(*) FROM HOSPITAL WHERE Status = 'Pending')
    ");
    $pending_approvals = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT Donor_ID, Name, Blood_Group, District FROM DONOR WHERE Status = 'Pending' LIMIT 5");
    $pending_donors_list = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}
?>

<!-- Page Header -->
<div class="page-header">
    <h2><i class="fa-solid fa-gauge" style="color:var(--primary); margin-right:10px;"></i>Admin Dashboard</h2>
    <p>System overview and quick actions — <?php echo date('l, d F Y'); ?></p>
</div>

<!-- KPI Cards -->
<div class="dashboard-grid">
    <div class="kpi-card">
        <div class="kpi-icon red"><i class="fa-solid fa-heart"></i></div>
        <div class="kpi-info">
            <h3><?php echo number_format($total_donors); ?></h3>
            <p>Approved Donors</p>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon blue"><i class="fa-solid fa-hospital"></i></div>
        <div class="kpi-info">
            <h3><?php echo number_format($total_hospitals); ?></h3>
            <p>Active Hospitals</p>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon orange"><i class="fa-solid fa-clipboard-list"></i></div>
        <div class="kpi-info">
            <h3><?php echo number_format($active_requests); ?></h3>
            <p>Active Requests</p>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon purple"><i class="fa-solid fa-user-clock"></i></div>
        <div class="kpi-info">
            <h3><?php echo number_format($pending_approvals); ?></h3>
            <p>Pending Approvals</p>
        </div>
    </div>
</div>

<!-- Content Grid -->
<div style="display:grid; grid-template-columns:2fr 1fr; gap:24px;">

    <!-- Pending Donors Widget -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-heart"
                    style="color:var(--accent-red);margin-right:8px;"></i>Pending Donor Approvals</span>
            <a href="manage_donors.php" class="btn btn-outline btn-sm">View All</a>
        </div>

        <?php if (count($pending_donors_list) > 0): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Blood Group</th>
                            <th>District</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending_donors_list as $donor): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($donor['Name']); ?></strong></td>
                                <td><span
                                        class="badge badge-danger"><?php echo htmlspecialchars($donor['Blood_Group']); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($donor['District']); ?></td>
                                <td>
                                    <a href="manage_donors.php?action=approve&id=<?php echo $donor['Donor_ID']; ?>"
                                        class="btn btn-success btn-sm">
                                        <i class="fa-solid fa-check"></i> Approve
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="text-align:center; padding:30px; color:var(--text-muted);">
                <i class="fa-solid fa-circle-check"
                    style="font-size:2rem; color:var(--primary); margin-bottom:10px; display:block;"></i>
                No pending donor approvals at this time.
            </div>
        <?php endif; ?>
    </div>

    <!-- System Status Widget -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-server"
                    style="color:var(--primary); margin-right:8px;"></i>System Status</span>
        </div>

        <div class="alert alert-success" style="margin-bottom:12px;">
            <i class="fa-solid fa-circle-check"></i> Database Online
        </div>
        <div class="alert alert-success" style="margin-bottom:12px;">
            <i class="fa-solid fa-shield-check"></i> Auth System Active
        </div>

        <hr class="divider">

        <div style="font-size:0.85rem; color:var(--text-muted);">
            <p style="margin-bottom:8px;"><i class="fa-solid fa-clock"
                    style="color:var(--primary);margin-right:6px;"></i>
                Server Time: <?php echo date('H:i:s'); ?>
            </p>
            <p><i class="fa-solid fa-calendar" style="color:var(--primary);margin-right:6px;"></i>
                <?php echo date('d M Y'); ?>
            </p>
        </div>

        <hr class="divider">

        <p style="font-size:0.8rem; color:var(--text-muted); line-height:1.55;">
            <i class="fa-solid fa-info-circle" style="color:var(--info);"></i>
            Low stock alerts will appear here when configured by hospital data.
        </p>

        <div style="margin-top:16px;">
            <a href="reports.php" class="btn btn-primary btn-sm" style="width:100%; justify-content:center;">
                <i class="fa-solid fa-chart-column"></i> View Reports
            </a>
        </div>
    </div>

</div>

<!-- Quick Links Row -->
<div class="dashboard-grid" style="margin-top:4px;">
    <?php
    $quick_links = [
        ['url' => 'manage_donors.php', 'icon' => 'fa-heart', 'color' => 'red', 'label' => 'Manage Donors'],
        ['url' => 'manage_recipients.php', 'icon' => 'fa-bed-pulse', 'color' => 'orange', 'label' => 'Recipients'],
        ['url' => 'manage_hospitals.php', 'icon' => 'fa-hospital', 'color' => 'blue', 'label' => 'Hospitals'],
        ['url' => 'manage_staff.php', 'icon' => 'fa-user-nurse', 'color' => 'purple', 'label' => 'Staff'],
        ['url' => 'all_requests.php', 'icon' => 'fa-clipboard-list', 'color' => 'orange', 'label' => 'Blood Requests'],
        ['url' => 'global_inventory.php', 'icon' => 'fa-boxes-stacked', 'color' => 'green', 'label' => 'Inventory'],
    ];
    foreach ($quick_links as $ql):
        ?>
        <a href="<?php echo $ql['url']; ?>"
            style="text-decoration:none; display:flex; align-items:center; gap:12px; padding:16px 18px; background:white; border-radius:var(--radius-md); border:1px solid var(--border); transition:var(--transition); color:var(--text-dark);"
            onmouseover="this.style.boxShadow='var(--shadow-md)';this.style.transform='translateY(-2px)'"
            onmouseout="this.style.boxShadow='';this.style.transform=''">
            <div class="kpi-icon <?php echo $ql['color']; ?>" style="width:40px;height:40px;flex-shrink:0;">
                <i class="fa-solid <?php echo $ql['icon']; ?>"></i>
            </div>
            <span style="font-weight:600; font-size:0.88rem;"><?php echo $ql['label']; ?></span>
            <i class="fa-solid fa-chevron-right" style="margin-left:auto; color:var(--text-muted); font-size:0.75rem;"></i>
        </a>
    <?php endforeach; ?>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>