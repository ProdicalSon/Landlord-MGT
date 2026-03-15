<?php
// admin/tenancies.php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/models/TenancyModel.php';
require_once __DIR__ . '/models/UserModel.php';
require_once __DIR__ . '/models/PropertyModel.php';
require_once __DIR__ . '/models/PaymentModel.php';

$tenancyModel = new TenancyModel();
$userModel = new UserModel();
$propertyModel = new PropertyModel();
$paymentModel = new PaymentModel();

// Handle actions
$message = '';
$error = '';

// Delete tenancy
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($tenancyModel->deleteTenancy($id)) {
        $message = "Tenancy deleted successfully";
    } else {
        $error = "Failed to delete tenancy";
    }
}

// Terminate tenancy
if (isset($_GET['terminate'])) {
    $id = intval($_GET['terminate']);
    if ($tenancyModel->terminateTenancy($id)) {
        $message = "Tenancy terminated successfully";
    } else {
        $error = "Failed to terminate tenancy";
    }
}

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$landlord_filter = $_GET['landlord'] ?? '';
$property_filter = $_GET['property'] ?? '';
$search = $_GET['search'] ?? '';

// Get all tenancies with pagination
$tenancies = $tenancyModel->getAllTenancies($limit, $offset);
$totalTenancies = $tenancyModel->countTenancies();
$totalPages = ceil($totalTenancies / $limit);

// Get statistics
$stats = [
    'total' => $tenancyModel->countTenancies(),
    'active' => $tenancyModel->countActiveTenancies(),
    'expired' => $tenancyModel->countExpiredTenancies(),
    'terminated' => $tenancyModel->countTerminatedTenancies()
];

// Get expiring tenancies (next 30 days)
$expiringTenancies = $tenancyModel->getExpiringTenancies(30);

// Get all landlords for filter
$landlords = $userModel->getUsersByType('landlord', 100, 0);

// Get all properties for filter
$properties = $propertyModel->getAllProperties(100, 0);

$page_title = 'Manage Tenancies';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="tenancies-wrapper">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h1><i class="fas fa-handshake"></i> Tenancy Management</h1>
            <p>Manage all rental agreements between landlords and students</p>
        </div>
        <div class="header-actions">
            <a href="add_tenancy.php" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> New Tenancy
            </a>
            <a href="export_tenancies.php" class="btn btn-secondary">
                <i class="fas fa-download"></i> Export
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if ($message): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span><?php echo htmlspecialchars($message); ?></span>
            <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">&times;</button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <span><?php echo htmlspecialchars($error); ?></span>
            <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">&times;</button>
        </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #4361ee20; color: #4361ee;">
                <i class="fas fa-handshake"></i>
            </div>
            <div class="stat-details">
                <h3>Total Tenancies</h3>
                <p class="stat-value"><?php echo $stats['total']; ?></p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: #06d6a020; color: #06d6a0;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-details">
                <h3>Active</h3>
                <p class="stat-value"><?php echo $stats['active']; ?></p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: #ffd16620; color: #ffd166;">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-details">
                <h3>Expiring Soon</h3>
                <p class="stat-value"><?php echo count($expiringTenancies); ?></p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: #ef476f20; color: #ef476f;">
                <i class="fas fa-hourglass-end"></i>
            </div>
            <div class="stat-details">
                <h3>Expired</h3>
                <p class="stat-value"><?php echo $stats['expired']; ?></p>
            </div>
        </div>
    </div>

    <!-- Expiring Soon Alert -->
    <?php if (count($expiringTenancies) > 0): ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <span><strong><?php echo count($expiringTenancies); ?> tenancies</strong> are expiring within the next 30 days.</span>
        <a href="#expiring-section" class="alert-link">View Details →</a>
    </div>
    <?php endif; ?>

    <!-- Search and Filters -->
    <div class="filters-section">
        <form method="GET" action="" class="filters-form">
            <div class="filters-grid">
                <div class="filter-group">
                    <label for="search">Search</label>
                    <input type="text" id="search" name="search" placeholder="Student, property, landlord..." 
                           value="<?php echo htmlspecialchars($search); ?>" class="form-control">
                </div>
                
                <div class="filter-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="active" <?php echo $status_filter == 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="expired" <?php echo $status_filter == 'expired' ? 'selected' : ''; ?>>Expired</option>
                        <option value="terminated" <?php echo $status_filter == 'terminated' ? 'selected' : ''; ?>>Terminated</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="landlord">Landlord</label>
                    <select id="landlord" name="landlord" class="form-control">
                        <option value="">All Landlords</option>
                        <?php foreach ($landlords as $landlord): ?>
                            <option value="<?php echo $landlord['id']; ?>" <?php echo $landlord_filter == $landlord['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($landlord['first_name'] ?? '') . ' ' . htmlspecialchars($landlord['last_name'] ?? '') . ' (@' . $landlord['username'] . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="property">Property</label>
                    <select id="property" name="property" class="form-control">
                        <option value="">All Properties</option>
                        <?php foreach ($properties as $property): ?>
                            <option value="<?php echo $property['id']; ?>" <?php echo $property_filter == $property['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($property['property_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Apply Filters
                    </button>
                    <a href="tenancies.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Tenancies Table -->
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Property</th>
                    <th>Landlord</th>
                    <th>Period</th>
                    <th>Rent</th>
                    <th>Status</th>
                    <th>Payments</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tenancies)): ?>
                    <tr>
                        <td colspan="9" class="text-center">No tenancies found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tenancies as $tenancy): 
                        // Calculate days remaining if active
                        $daysRemaining = null;
                        $statusClass = 'badge-secondary';
                        
                        if ($tenancy['status'] == 'active') {
                            $endDate = new DateTime($tenancy['end_date']);
                            $now = new DateTime();
                            $daysRemaining = $now->diff($endDate)->days;
                            
                            if ($daysRemaining < 0) {
                                $statusClass = 'badge-danger';
                                $statusText = 'Expired';
                            } elseif ($daysRemaining <= 30) {
                                $statusClass = 'badge-warning';
                                $statusText = 'Expiring Soon';
                            } else {
                                $statusClass = 'badge-success';
                                $statusText = 'Active';
                            }
                        } elseif ($tenancy['status'] == 'terminated') {
                            $statusClass = 'badge-danger';
                            $statusText = 'Terminated';
                        } else {
                            $statusClass = 'badge-secondary';
                            $statusText = ucfirst($tenancy['status']);
                        }
                        
                        // Get payment info for this tenancy
                        $paymentCount = $paymentModel->countPaymentsByTenancy($tenancy['id'] ?? 0);
                        $totalPaid = $paymentModel->getTotalPaidByTenancy($tenancy['id'] ?? 0);
                    ?>
                    <tr>
                        <td>#<?php echo $tenancy['id']; ?></td>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar small">
                                    <?php echo strtoupper(substr($tenancy['student_username'] ?? 'S', 0, 1)); ?>
                                </div>
                                <div>
                                    <strong><?php echo htmlspecialchars($tenancy['student_first_name'] ?? '') . ' ' . htmlspecialchars($tenancy['student_last_name'] ?? ''); ?></strong>
                                    <br>
                                    <small>@<?php echo htmlspecialchars($tenancy['student_username'] ?? 'N/A'); ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($tenancy['property_name'] ?? 'N/A'); ?></strong>
                            <br>
                            <small><?php echo htmlspecialchars($tenancy['address'] ?? ''); ?></small>
                        </td>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar small" style="background: #4cc9f0;">
                                    <?php echo strtoupper(substr($tenancy['landlord_username'] ?? 'L', 0, 1)); ?>
                                </div>
                                <div>
                                    <strong><?php echo htmlspecialchars($tenancy['landlord_first_name'] ?? '') . ' ' . htmlspecialchars($tenancy['landlord_last_name'] ?? ''); ?></strong>
                                    <br>
                                    <small>@<?php echo htmlspecialchars($tenancy['landlord_username'] ?? 'N/A'); ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div><i class="fas fa-calendar-alt"></i> <?php echo date('M d, Y', strtotime($tenancy['start_date'])); ?></div>
                            <div><i class="fas fa-calendar-check"></i> <?php echo date('M d, Y', strtotime($tenancy['end_date'])); ?></div>
                            <?php if ($daysRemaining !== null && $daysRemaining > 0): ?>
                                <small class="days-remaining"><?php echo $daysRemaining; ?> days left</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong>KES <?php echo number_format($tenancy['monthly_rent']); ?></strong>
                            <br>
                            <?php if ($tenancy['deposit_paid']): ?>
                                <span class="badge badge-success">Deposit Paid</span>
                            <?php else: ?>
                                <span class="badge badge-warning">Deposit Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                            <br>
                            <?php if ($paymentCount > 0): ?>
                                <small class="payment-info">
                                    <i class="fas fa-credit-card"></i> <?php echo $paymentCount; ?> payments
                                    <br>
                                    <strong>KES <?php echo number_format($totalPaid); ?></strong>
                                </small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="view_tenancy.php?id=<?php echo $tenancy['id']; ?>" class="btn btn-sm btn-info" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit_tenancy.php?id=<?php echo $tenancy['id']; ?>" class="btn btn-sm btn-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($tenancy['status'] == 'active'): ?>
                                    <a href="?terminate=<?php echo $tenancy['id']; ?>" class="btn btn-sm btn-warning" title="Terminate" 
                                       onclick="return confirm('Are you sure you want to terminate this tenancy?')">
                                        <i class="fas fa-ban"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="payments.php?tenancy=<?php echo $tenancy['id']; ?>" class="btn btn-sm btn-success" title="View Payments">
                                    <i class="fas fa-credit-card"></i>
                                </a>
                                <a href="?delete=<?php echo $tenancy['id']; ?>" class="btn btn-sm btn-danger" title="Delete" 
                                   onclick="return confirmDelete('Delete this tenancy? This action cannot be undone.')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo ($page - 1); ?><?php echo $status_filter ? '&status='.$status_filter : ''; ?><?php echo $landlord_filter ? '&landlord='.$landlord_filter : ''; ?><?php echo $property_filter ? '&property='.$property_filter : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" class="page-link">
                    <i class="fas fa-chevron-left"></i> Previous
                </a>
            <?php endif; ?>
            
            <?php
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
            for ($i = $start; $i <= $end; $i++):
            ?>
                <a href="?page=<?php echo $i; ?><?php echo $status_filter ? '&status='.$status_filter : ''; ?><?php echo $landlord_filter ? '&landlord='.$landlord_filter : ''; ?><?php echo $property_filter ? '&property='.$property_filter : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" 
                   class="page-link <?php echo $i == $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo ($page + 1); ?><?php echo $status_filter ? '&status='.$status_filter : ''; ?><?php echo $landlord_filter ? '&landlord='.$landlord_filter : ''; ?><?php echo $property_filter ? '&property='.$property_filter : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" class="page-link">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Expiring Tenancies Section -->
    <?php if (!empty($expiringTenancies)): ?>
    <div id="expiring-section" class="expiring-section">
        <h2><i class="fas fa-clock"></i> Tenancies Expiring Soon (Next 30 Days)</h2>
        <div class="expiring-grid">
            <?php foreach ($expiringTenancies as $tenancy): ?>
                <div class="expiring-card">
                    <div class="expiring-header">
                        <span class="badge badge-warning"><?php echo $tenancy['days_remaining']; ?> days left</span>
                        <h3><?php echo htmlspecialchars($tenancy['property_name']); ?></h3>
                    </div>
                    <div class="expiring-body">
                        <p><i class="fas fa-user-graduate"></i> <?php echo htmlspecialchars($tenancy['student_first_name'] ?? '') . ' ' . htmlspecialchars($tenancy['student_last_name'] ?? ''); ?></p>
                        <p><i class="fas fa-calendar-alt"></i> Expires: <?php echo date('M d, Y', strtotime($tenancy['end_date'])); ?></p>
                        <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($tenancy['student_phone'] ?? 'No phone'); ?></p>
                        <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($tenancy['student_email'] ?? 'No email'); ?></p>
                    </div>
                    <div class="expiring-footer">
                        <a href="send_reminder.php?tenancy=<?php echo $tenancy['id']; ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-bell"></i> Send Reminder
                        </a>
                        <a href="extend_tenancy.php?id=<?php echo $tenancy['id']; ?>" class="btn btn-sm btn-success">
                            <i class="fas fa-calendar-plus"></i> Extend
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
/* Tenancies Page Styles */
.tenancies-wrapper {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

/* Page Header */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    flex-wrap: wrap;
    gap: 15px;
}

.header-content h1 {
    font-size: 28px;
    margin-bottom: 5px;
    color: #2b2d42;
}

.header-content p {
    color: #8d99ae;
    font-size: 14px;
}

.header-actions {
    display: flex;
    gap: 10px;
}

/* Alert Styles */
.alert {
    position: relative;
    padding: 16px 20px;
    border-radius: 10px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 12px;
    animation: slideDown 0.3s ease;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border-left: 4px solid #06d6a0;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border-left: 4px solid #ef476f;
}

.alert-warning {
    background: #fff3cd;
    color: #856404;
    border-left: 4px solid #ffd166;
}

.alert-link {
    margin-left: auto;
    color: inherit;
    font-weight: 600;
    text-decoration: underline;
}

.alert-close {
    margin-left: auto;
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: inherit;
    opacity: 0.5;
    transition: opacity 0.3s;
}

.alert-close:hover {
    opacity: 1;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.stat-details h3 {
    font-size: 14px;
    color: #8d99ae;
    margin-bottom: 5px;
}

.stat-value {
    font-size: 24px;
    font-weight: 700;
    color: #2b2d42;
    margin: 0;
}

/* Filters Section */
.filters-section {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    align-items: end;
}

.filter-group label {
    display: block;
    margin-bottom: 5px;
    font-size: 13px;
    font-weight: 500;
    color: #2b2d42;
}

.filter-actions {
    display: flex;
    gap: 10px;
}

.filter-actions .btn {
    flex: 1;
}

/* Table Styles */
.table-responsive {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th {
    text-align: left;
    padding: 12px;
    background: #f8f9fa;
    font-weight: 600;
    color: #2b2d42;
    border-bottom: 2px solid #e9ecef;
    white-space: nowrap;
}

.table td {
    padding: 15px 12px;
    border-bottom: 1px solid #e9ecef;
    vertical-align: middle;
}

/* User Info */
.user-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    background: #4361ee;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
}

.user-avatar.small {
    width: 30px;
    height: 30px;
    font-size: 12px;
}

/* Badges */
.badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 30px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}

.badge-success {
    background: #d4edda;
    color: #155724;
}

.badge-warning {
    background: #fff3cd;
    color: #856404;
}

.badge-danger {
    background: #f8d7da;
    color: #721c24;
}

.badge-info {
    background: #d1ecf1;
    color: #0c5460;
}

.badge-secondary {
    background: #e2e3e5;
    color: #383d41;
}

/* Days Remaining */
.days-remaining {
    display: inline-block;
    padding: 2px 8px;
    background: #4361ee;
    color: white;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

/* Payment Info */
.payment-info {
    display: inline-block;
    margin-top: 5px;
    padding: 3px 8px;
    background: #f8f9fa;
    border-radius: 4px;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

.btn-sm {
    padding: 6px 10px;
    font-size: 12px;
    border-radius: 6px;
}

.btn-info {
    background: #4cc9f0;
    color: white;
}

.btn-info:hover {
    background: #3aa8d6;
}

.btn-warning {
    background: #ffd166;
    color: #2b2d42;
}

.btn-warning:hover {
    background: #e6b94d;
}

.btn-success {
    background: #06d6a0;
    color: white;
}

.btn-success:hover {
    background: #05b585;
}

/* Expiring Section */
.expiring-section {
    margin-top: 30px;
}

.expiring-section h2 {
    font-size: 20px;
    margin-bottom: 20px;
    color: #2b2d42;
}

.expiring-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.expiring-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
    border-left: 4px solid #ffd166;
}

.expiring-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.expiring-header {
    padding: 15px;
    background: linear-gradient(135deg, #fff3cd, #fff9e6);
    border-bottom: 1px solid #ffeeba;
}

.expiring-header h3 {
    font-size: 16px;
    margin: 10px 0 0;
    color: #856404;
}

.expiring-body {
    padding: 15px;
}

.expiring-body p {
    margin: 8px 0;
    font-size: 13px;
    color: #2b2d42;
}

.expiring-body i {
    width: 20px;
    color: #ffd166;
}

.expiring-footer {
    padding: 15px;
    background: #f8f9fa;
    display: flex;
    gap: 10px;
}

.expiring-footer .btn {
    flex: 1;
}

/* Pagination */
.pagination {
    margin-top: 25px;
    display: flex;
    gap: 5px;
    justify-content: center;
    flex-wrap: wrap;
}

.page-link {
    display: inline-block;
    padding: 8px 12px;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    color: #4361ee;
    text-decoration: none;
    transition: all 0.3s;
}

.page-link:hover {
    background: #f8f9fa;
    border-color: #4361ee;
}

.page-link.active {
    background: #4361ee;
    color: white;
    border-color: #4361ee;
}

/* Responsive */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .header-actions {
        width: 100%;
    }
    
    .header-actions .btn {
        flex: 1;
    }
    
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-actions {
        grid-column: auto;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .expiring-grid {
        grid-template-columns: 1fr;
    }
}

/* Animations */
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
function confirmDelete(message) {
    return confirm(message);
}

// Auto-hide alerts after 5 seconds
setTimeout(() => {
    document.querySelectorAll('.alert').forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>