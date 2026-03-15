<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// error_log("=== Admin Index Page Loaded ===");
// admin/index.php - Debug version
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/models/AdminModel.php';
require_once __DIR__ . '/models/UserModel.php';
require_once __DIR__ . '/models/PropertyModel.php';
require_once __DIR__ . '/models/PaymentModel.php';

// Initialize models with error checking
$adminModel = new AdminModel();
if (!$adminModel) {
    die("Failed to create AdminModel");
}

$userModel = new UserModel();
if (!$userModel) {
    die("Failed to create UserModel");
}

$propertyModel = new PropertyModel();
if (!$propertyModel) {
    die("Failed to create PropertyModel");
}

$paymentModel = new PaymentModel();
if (!$paymentModel) {
    die("Failed to create PaymentModel");
}

// Get dashboard statistics with error checking
$stats = $adminModel->getDashboardStats();
if ($stats === false) {
    echo "<!-- Debug: getDashboardStats returned false -->";
    $stats = [];
}

$recentActivities = $adminModel->getRecentActivities(10);
if ($recentActivities === false) {
    echo "<!-- Debug: getRecentActivities returned false -->";
    $recentActivities = [];
}

// Get recent users
$recentUsers = $userModel->getRecentUsers(5);
if ($recentUsers === false) {
    echo "<!-- Debug: getRecentUsers returned false -->";
    $recentUsers = [];
}

// Get recent properties
$recentProperties = $propertyModel->getRecentProperties(5);
if ($recentProperties === false) {
    echo "<!-- Debug: getRecentProperties returned false -->";
    $recentProperties = [];
}

// Get recent payments
$recentPayments = $paymentModel->getRecentPayments(5);
if ($recentPayments === false) {
    echo "<!-- Debug: getRecentPayments returned false -->";
    $recentPayments = [];
}

// Debug output
echo "<!-- Debug: stats = " . print_r($stats, true) . " -->";
echo "<!-- Debug: recentUsers count = " . count($recentUsers) . " -->";
echo "<!-- Debug: recentProperties count = " . count($recentProperties) . " -->";
echo "<!-- Debug: recentPayments count = " . count($recentPayments) . " -->";

$page_title = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="dashboard-stats">
    <div class="stat-card">
        <div class="stat-icon" style="background: #4e73df20; color: #4e73df;">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-details">
            <h3>Total Users</h3>
            <p class="stat-value"><?php echo number_format($stats['total_users'] ?? 0); ?></p>
            <p class="stat-change">
                <span class="text-success">+<?php echo $stats['new_users_week'] ?? 0; ?></span> this week
            </p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #1cc88a20; color: #1cc88a;">
            <i class="fas fa-home"></i>
        </div>
        <div class="stat-details">
            <h3>Total Properties</h3>
            <p class="stat-value"><?php echo number_format($stats['total_properties'] ?? 0); ?></p>
            <p class="stat-change">
                <span class="text-success"><?php echo $stats['available_properties'] ?? 0; ?> Available</span>
            </p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #f6c23e20; color: #f6c23e;">
            <i class="fas fa-credit-card"></i>
        </div>
        <div class="stat-details">
            <h3>Total Revenue</h3>
            <p class="stat-value">KES <?php echo number_format($stats['total_revenue'] ?? 0); ?></p>
            <p class="stat-change">
                <span class="text-warning"><?php echo $stats['pending_payments'] ?? 0; ?> Pending</span>
            </p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #e74a3b20; color: #e74a3b;">
            <i class="fas fa-building"></i>
        </div>
        <div class="stat-details">
            <h3>Landlords</h3>
            <p class="stat-value"><?php echo number_format($stats['total_landlords'] ?? 0); ?></p>
            <p class="stat-change">
                <span class="text-info"><?php echo $stats['total_students'] ?? 0; ?> Students</span>
            </p>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <!-- Recent Users -->
    <div class="dashboard-card">
        <div class="card-header">
            <h3><i class="fas fa-users"></i> Recent Users</h3>
            <a href="users.php" class="btn-link">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Type</th>
                        <th>Joined</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentUsers)): ?>
                        <tr>
                            <td colspan="4" class="text-center">No recent users</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentUsers as $user): ?>
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">
                                        <?php echo strtoupper(substr($user['username'] ?? 'U', 0, 1)); ?>
                                    </div>
                                    <div>
                                        <strong><?php echo htmlspecialchars($user['username'] ?? 'N/A'); ?></strong>
                                        <br><small><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo ucfirst($user['user_type'] ?? 'Student'); ?></td>
                            <td><?php echo isset($user['created_at']) ? date('M d, Y', strtotime($user['created_at'])) : 'N/A'; ?></td>
                            <td>
                                <?php if (isset($user['is_verified']) && $user['is_verified']): ?>
                                    <span class="badge badge-success">Verified</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Pending</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Properties -->
    <div class="dashboard-card">
        <div class="card-header">
            <h3><i class="fas fa-home"></i> Recent Properties</h3>
            <a href="properties.php" class="btn-link">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Property</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Listed</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentProperties)): ?>
                        <tr>
                            <td colspan="4" class="text-center">No recent properties</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentProperties as $property): ?>
                        <tr>
                            <td>
                                <div class="property-info">
                                    <strong><?php echo htmlspecialchars($property['property_name'] ?? 'N/A'); ?></strong>
                                    <br><small><?php echo htmlspecialchars($property['city'] ?? 'N/A'); ?></small>
                                </div>
                            </td>
                            <td>KES <?php echo number_format($property['monthly_rent'] ?? 0); ?></td>
                            <td>
                                <?php
                                $status = $property['status'] ?? 'unknown';
                                $badgeClass = 'badge-secondary';
                                if ($status == 'available') $badgeClass = 'badge-success';
                                elseif ($status == 'occupied') $badgeClass = 'badge-warning';
                                elseif ($status == 'maintenance') $badgeClass = 'badge-danger';
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst($status); ?></span>
                            </td>
                            <td><?php echo isset($property['created_at']) ? date('M d', strtotime($property['created_at'])) : 'N/A'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="dashboard-card">
        <div class="card-header">
            <h3><i class="fas fa-credit-card"></i> Recent Payments</h3>
            <a href="payments.php" class="btn-link">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Transaction</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentPayments)): ?>
                        <tr>
                            <td colspan="4" class="text-center">No recent payments</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentPayments as $payment): ?>
                        <tr>
                            <td>
                                <div class="payment-info">
                                    <strong><?php echo htmlspecialchars($payment['transaction_reference'] ?? 'N/A'); ?></strong>
                                    <br><small><?php echo htmlspecialchars($payment['property_name'] ?? 'N/A'); ?></small>
                                </div>
                            </td>
                            <td>KES <?php echo number_format($payment['amount_paid'] ?? 0); ?></td>
                            <td>
                                <?php
                                $status = $payment['status'] ?? 'pending';
                                $badgeClass = $status == 'completed' ? 'badge-success' : 
                                             ($status == 'pending' ? 'badge-warning' : 'badge-danger');
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst($status); ?></span>
                            </td>
                            <td><?php echo isset($payment['created_at']) ? date('M d', strtotime($payment['created_at'])) : 'N/A'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="dashboard-card">
        <div class="card-header">
            <h3><i class="fas fa-history"></i> Recent Activity</h3>
        </div>
        <div class="card-body">
            <div class="activity-list">
                <?php if (empty($recentActivities)): ?>
                    <p class="text-center">No recent activity</p>
                <?php else: ?>
                    <?php foreach ($recentActivities as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <?php if (isset($activity['type']) && $activity['type'] == 'user'): ?>
                                <i class="fas fa-user-plus" style="color: #4e73df;"></i>
                            <?php elseif (isset($activity['type']) && $activity['type'] == 'property'): ?>
                                <i class="fas fa-home" style="color: #1cc88a;"></i>
                            <?php else: ?>
                                <i class="fas fa-credit-card" style="color: #f6c23e;"></i>
                            <?php endif; ?>
                        </div>
                        <div class="activity-details">
                            <p class="activity-text">
                                <?php if (isset($activity['type']) && $activity['type'] == 'user'): ?>
                                    New user registered: <strong><?php echo htmlspecialchars($activity['name'] ?? ''); ?></strong>
                                <?php elseif (isset($activity['type']) && $activity['type'] == 'property'): ?>
                                    New property listed: <strong><?php echo htmlspecialchars($activity['name'] ?? ''); ?></strong>
                                <?php else: ?>
                                    New payment received
                                <?php endif; ?>
                            </p>
                            <p class="activity-time"><?php echo isset($activity['created_at']) ? date('M d, Y H:i', strtotime($activity['created_at'])) : 'N/A'; ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>