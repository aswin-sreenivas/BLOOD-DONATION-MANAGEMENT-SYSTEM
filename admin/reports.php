<?php
// admin/reports.php
session_start();
define('PAGE_TITLE', 'System Reports');
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SESSION['role'] !== 'Admin') {
    header("Location: " . SITE_URL . "/index.php?error=unauthorized");
    exit();
}

try {
    // 1. Overall System Metrics
    $metrics = [];
    
    $metrics['Total Donors'] = $pdo->query("SELECT COUNT(*) FROM DONOR")->fetchColumn();
    $metrics['Total Recipients'] = $pdo->query("SELECT COUNT(*) FROM RECIPIENT")->fetchColumn();
    $metrics['Total Hospitals'] = $pdo->query("SELECT COUNT(*) FROM HOSPITAL")->fetchColumn();
    
    $metrics['Completed Donations'] = $pdo->query("SELECT COUNT(*) FROM DONATION WHERE Donation_Status = 'Completed'")->fetchColumn();
    $metrics['Total Issued Units'] = $pdo->query("SELECT COUNT(*) FROM BloodIssue")->fetchColumn();
    $metrics['Pending Requests'] = $pdo->query("SELECT COUNT(*) FROM BLOOD_REQUEST WHERE Status = 'Pending'")->fetchColumn();
    
    // 2. Blood Stock by Group (Across all hospitals)
    $stmt = $pdo->query("SELECT Blood_Group, SUM(Units_Available) as Total_Stock FROM BLOOD_INVENTORY GROUP BY Blood_Group ORDER BY Total_Stock DESC");
    $stock_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Recent Activity Log (Pseudo-log from multiple tables for demonstration)
    $log_query = "
        (SELECT 'New Donor Registered' AS Action, Name AS Detail, Created_At AS `Date` FROM DONOR)
        UNION
        (SELECT 'Hospital Registered', Hospital_Name, Created_At FROM HOSPITAL)
        UNION
        (SELECT 'Blood Request Made', CONCAT(Quantity, ' units of ', Blood_Group), Request_Date FROM BLOOD_REQUEST)
        UNION
        (SELECT 'Blood Unit Issued', Unit_ID, Issue_Date FROM BloodIssue)
        ORDER BY `Date` DESC LIMIT 15
    ";
    $recent_logs = $pdo->query($log_query)->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database Error: A system error occurred. Please try again later.");
}
?>

<div style="display: flex; justify-content: space-between; align-items: center;">
    <h2>System Reports & Analytics</h2>
    <a href="dashboard.php" class="btn">&larr; Back to Dashboard</a>
</div>

<div class="dashboard-grid">
    <?php foreach($metrics as $label => $value): ?>
    <div class="card stat-card" style="padding: 15px;">
        <h4 style="margin: 0 0 10px 0; color: var(--text-muted); font-size: 0.9em;"><?php echo htmlspecialchars($label); ?></h4>
        <div class="stat-value" style="font-size: 1.8em; color: var(--text-color);"><?php echo $value; ?></div>
    </div>
    <?php endforeach; ?>
</div>

<div class="dashboard-grid" style="grid-template-columns: 1fr 2fr;">
    <!-- Blood Stock Summary -->
    <div class="card">
        <h3>Global Blood Stock</h3>
        <p style="font-size: 0.85em; color: var(--text-muted); margin-bottom: 15px;">Aggregated across all registered hospitals.</p>
        
        <?php if(count($stock_data) > 0): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Group</th>
                            <th>Total Units</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($stock_data as $row): ?>
                        <tr>
                            <td><strong><span class="badge badge-danger"><?php echo htmlspecialchars($row['Blood_Group']); ?></span></strong></td>
                            <td style="font-size: 1.2em;"><?php echo $row['Total_Stock']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div style="margin-top: 20px;">
                <button class="btn btn-primary" style="width: 100%;" onclick="window.print()">Print Report / Export PDF</button>
            </div>
        <?php else: ?>
            <p>No inventory data available yet.</p>
        <?php endif; ?>
    </div>

    <!-- Activity Log -->
    <div class="card">
        <h3>Recent System Activity</h3>
        <p style="font-size: 0.85em; color: var(--text-muted); margin-bottom: 15px;">Latest automated trail of actions across the platform.</p>
        
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Action</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($recent_logs) > 0): ?>
                        <?php foreach($recent_logs as $log): ?>
                        <tr>
                            <td style="font-size: 0.85em; color: var(--text-muted);"><?php echo date('M d, Y H:i:s', strtotime($log['Date'])); ?></td>
                            <td><strong><?php echo htmlspecialchars($log['Action']); ?></strong></td>
                            <td><?php echo htmlspecialchars($log['Detail']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" style="text-align: center;">No activity recorded yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
