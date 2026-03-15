<?php
// admin/clients.php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/models/UserModel.php';
require_once __DIR__ . '/models/PaymentModel.php';
require_once __DIR__ . '/models/TenancyModel.php';

$userModel = new UserModel();
$paymentModel = new PaymentModel();
$tenancyModel = new TenancyModel();

// Handle actions
$message = '';
$error = '';

// Delete client
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($userModel->deleteUser($id)) {
        $message = "Client deleted successfully";
    } else {
        $error = "Failed to delete client";
    }
}

// Toggle verification status
if (isset($_GET['verify'])) {
    $id = intval($_GET['verify']);
    $status = intval($_GET['status'] ?? 1);
    if ($userModel->updateUserStatus($id, $status)) {
        $message = "Client status updated successfully";
    } else {
        $error = "Failed to update client status";
    }
}

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Get clients (students)
$clients = $userModel->getUsersByType('student', $limit, $offset);
$totalClients = $userModel->countUsersByType('student');
$totalPages = ceil($totalClients / $limit);

// Get payment statistics
$paymentStats = $paymentModel->getPaymentStats();

$page_title = 'Manage Clients';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="content-header">
    <div class="header-actions">
        <a href="add_client.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Client
        </a>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: #4e73df20; color: #4e73df;">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-details">
            <h3>Total Clients</h3>
            <p class="stat-value"><?php echo $totalClients; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #1cc88a20; color: #1cc88a;">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-details">
            <h3>Verified</h3>
            <p class="stat-value"><?php echo $userModel->countVerifiedUsers('student'); ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #f6c23e20; color: #f6c23e;">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-details">
            <h3>Pending</h3>
            <p class="stat-value"><?php echo $userModel->countPendingUsers('student'); ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #e74a3b20; color: #e74a3b;">
            <i class="fas fa-credit-card"></i>
        </div>
        <div class="stat-details">
            <h3>Total Payments</h3>
            <p class="stat-value">KES <?php echo number_format($paymentStats['total_amount'] ?? 0); ?></p>
        </div>
    </div>
</div>

<!-- Search and Filters -->
<div class="search-section">
    <form method="GET" action="" class="search-form">
        <div class="search-box">
            <input type="text" name="search" placeholder="Search clients by name, email or phone..." 
                   value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Search
            </button>
        </div>
        <div class="filter-options">
            <select name="status" class="form-control">
                <option value="">All Status</option>
                <option value="verified" <?php echo (isset($_GET['status']) && $_GET['status'] == 'verified') ? 'selected' : ''; ?>>Verified</option>
                <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="active" <?php echo (isset($_GET['status']) && $_GET['status'] == 'active') ? 'selected' : ''; ?>>Active Tenancy</option>
            </select>
        </div>
    </form>
</div>

<!-- Clients Table -->
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Client</th>
                <th>Contact</th>
                <th>Tenancy</th>
                <th>Payments</th>
                <th>Joined</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($clients)): ?>
                <tr>
                    <td colspan="8" class="text-center">No clients found</td>
                </tr>
            <?php else: ?>
                <?php foreach ($clients as $client): 
                    $activeTenancy = $tenancyModel->getActiveTenancyByStudent($client['id']);
                    $paymentCount = $paymentModel->countPaymentsByStudent($client['id']);
                    $totalPaid = $paymentModel->getTotalPaidByStudent($client['id']);
                ?>
                <tr>
                    <td>#<?php echo $client['id']; ?></td>
                    <td>
                        <div class="user-info">
                            <div class="user-avatar">
                                <?php echo strtoupper(substr($client['username'] ?? 'S', 0, 1)); ?>
                            </div>
                            <div>
                                <strong><?php echo htmlspecialchars($client['first_name'] ?? '') . ' ' . htmlspecialchars($client['last_name'] ?? ''); ?></strong>
                                <br>
                                <small>@<?php echo htmlspecialchars($client['username']); ?></small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($client['email']); ?></div>
                        <?php if (!empty($client['phone_number'])): ?>
                            <div><i class="fas fa-phone"></i> <?php echo htmlspecialchars($client['phone_number']); ?></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($activeTenancy): ?>
                            <span class="badge badge-success">Active</span>
                            <br>
                            <small>Until <?php echo date('M d, Y', strtotime($activeTenancy['end_date'])); ?></small>
                        <?php else: ?>
                            <span class="badge badge-secondary">No Active Tenancy</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div><span class="badge badge-info"><?php echo $paymentCount; ?> Payments</span></div>
                        <?php if ($totalPaid > 0): ?>
                            <small>KES <?php echo number_format($totalPaid); ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($client['created_at'])); ?></td>
                    <td>
                        <?php if ($client['is_verified']): ?>
                            <span class="badge badge-success">Verified</span>
                        <?php else: ?>
                            <span class="badge badge-warning">Pending</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="view_client.php?id=<?php echo $client['id']; ?>" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="edit_client.php?id=<?php echo $client['id']; ?>" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if (!$client['is_verified']): ?>
                                <a href="?verify=<?php echo $client['id']; ?>&status=1" class="btn btn-sm btn-success" title="Verify" onclick="return confirm('Verify this client?')">
                                    <i class="fas fa-check-circle"></i>
                                </a>
                            <?php else: ?>
                                <a href="?verify=<?php echo $client['id']; ?>&status=0" class="btn btn-sm btn-warning" title="Unverify" onclick="return confirm('Unverify this client?')">
                                    <i class="fas fa-times-circle"></i>
                                </a>
                            <?php endif; ?>
                            <a href="?delete=<?php echo $client['id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirmDelete('Delete this client? All their records will be lost.')">
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
            <a href="?page=<?php echo ($page - 1); ?>" class="page-link">&laquo; Previous</a>
        <?php endif; ?>
        
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="page-link <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
        
        <?php if ($page < $totalPages): ?>
            <a href="?page=<?php echo ($page + 1); ?>" class="page-link">Next &raquo;</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<style>
/* Add to existing styles */
.btn-secondary {
    background: #858796;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.btn-secondary:hover {
    background: #6b6d7d;
}

.badge-secondary {
    background: #e2e3e5;
    color: #383d41;
}
</style>

<?php
require_once __DIR__ . '/includes/footer.php';
?>