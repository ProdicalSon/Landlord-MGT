<?php
// admin/landlords.php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/models/UserModel.php';
require_once __DIR__ . '/models/PropertyModel.php';

$userModel = new UserModel();
$propertyModel = new PropertyModel();

// Handle actions
$message = '';
$error = '';

// Delete landlord
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($userModel->deleteUser($id)) {
        $message = "Landlord deleted successfully";
    } else {
        $error = "Failed to delete landlord";
    }
}

// Toggle verification status
if (isset($_GET['verify'])) {
    $id = intval($_GET['verify']);
    $status = intval($_GET['status'] ?? 1);
    if ($userModel->updateUserStatus($id, $status)) {
        $message = "Landlord status updated successfully";
    } else {
        $error = "Failed to update landlord status";
    }
}

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Get landlords
$landlords = $userModel->getUsersByType('landlord', $limit, $offset);
$totalLandlords = $userModel->countUsersByType('landlord');
$totalPages = ceil($totalLandlords / $limit);

// Get statistics
$totalProperties = $propertyModel->countProperties();
$activeProperties = $propertyModel->countPropertiesByStatus('available');
$occupiedProperties = $propertyModel->countPropertiesByStatus('occupied');

$page_title = 'Manage Landlords';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="content-header">
    <h1><i class="fas fa-building"></i> Manage Landlords</h1>
    <div class="header-actions">
        <a href="add_landlord.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Landlord
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
            <i class="fas fa-building"></i>
        </div>
        <div class="stat-details">
            <h3>Total Landlords</h3>
            <p class="stat-value"><?php echo $totalLandlords; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #1cc88a20; color: #1cc88a;">
            <i class="fas fa-home"></i>
        </div>
        <div class="stat-details">
            <h3>Total Properties</h3>
            <p class="stat-value"><?php echo $totalProperties; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #f6c23e20; color: #f6c23e;">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-details">
            <h3>Available</h3>
            <p class="stat-value"><?php echo $activeProperties; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #e74a3b20; color: #e74a3b;">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-details">
            <h3>Occupied</h3>
            <p class="stat-value"><?php echo $occupiedProperties; ?></p>
        </div>
    </div>
</div>

<!-- Search and Filters -->
<div class="search-section">
    <form method="GET" action="" class="search-form">
        <div class="search-box">
            <input type="text" name="search" placeholder="Search landlords..." 
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
            </select>
        </div>
    </form>
</div>

<!-- Landlords Table -->
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Landlord</th>
                <th>Contact</th>
                <th>Properties</th>
                <th>Joined</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($landlords)): ?>
                <tr>
                    <td colspan="7" class="text-center">No landlords found</td>
                </tr>
            <?php else: ?>
                <?php foreach ($landlords as $landlord): 
                    $propertyCount = $propertyModel->countPropertiesByLandlord($landlord['id']);
                ?>
                <tr>
                    <td>#<?php echo $landlord['id']; ?></td>
                    <td>
                        <div class="user-info">
                            <div class="user-avatar">
                                <?php echo strtoupper(substr($landlord['username'] ?? 'L', 0, 1)); ?>
                            </div>
                            <div>
                                <strong><?php echo htmlspecialchars($landlord['first_name'] ?? '') . ' ' . htmlspecialchars($landlord['last_name'] ?? ''); ?></strong>
                                <br>
                                <small>@<?php echo htmlspecialchars($landlord['username']); ?></small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($landlord['email']); ?></div>
                        <?php if (!empty($landlord['phone_number'])): ?>
                            <div><i class="fas fa-phone"></i> <?php echo htmlspecialchars($landlord['phone_number']); ?></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge badge-info"><?php echo $propertyCount; ?> Properties</span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($landlord['created_at'])); ?></td>
                    <td>
                        <?php if ($landlord['is_verified']): ?>
                            <span class="badge badge-success">Verified</span>
                        <?php else: ?>
                            <span class="badge badge-warning">Pending</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="view_landlord.php?id=<?php echo $landlord['id']; ?>" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="edit_landlord.php?id=<?php echo $landlord['id']; ?>" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if (!$landlord['is_verified']): ?>
                                <a href="?verify=<?php echo $landlord['id']; ?>&status=1" class="btn btn-sm btn-success" title="Verify" onclick="return confirm('Verify this landlord?')">
                                    <i class="fas fa-check-circle"></i>
                                </a>
                            <?php else: ?>
                                <a href="?verify=<?php echo $landlord['id']; ?>&status=0" class="btn btn-sm btn-warning" title="Unverify" onclick="return confirm('Unverify this landlord?')">
                                    <i class="fas fa-times-circle"></i>
                                </a>
                            <?php endif; ?>
                            <a href="?delete=<?php echo $landlord['id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirmDelete('Delete this landlord? All their properties will also be deleted.')">
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
.content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.search-section {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.search-form {
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap;
}

.search-box {
    flex: 1;
    display: flex;
    gap: 10px;
}

.search-box input {
    flex: 1;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.filter-options select {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    min-width: 150px;
}

.table-responsive {
    background: white;
    border-radius: 8px;
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
    color: #333;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    padding: 12px;
    border-bottom: 1px solid #dee2e6;
    vertical-align: middle;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    background: #4e73df;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
}

.action-buttons {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

.btn-sm {
    padding: 5px 10px;
    font-size: 12px;
}

.badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

.badge-success { background: #d4edda; color: #155724; }
.badge-warning { background: #fff3cd; color: #856404; }
.badge-info { background: #d1ecf1; color: #0c5460; }
.badge-danger { background: #f8d7da; color: #721c24; }

.alert {
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.pagination {
    margin-top: 20px;
    display: flex;
    gap: 5px;
    justify-content: center;
}

.page-link {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    color: #4e73df;
    text-decoration: none;
}

.page-link.active {
    background: #4e73df;
    color: white;
    border-color: #4e73df;
}

.page-link:hover {
    background: #f8f9fa;
}
</style>

<?php
require_once __DIR__ . '/includes/footer.php';
?>