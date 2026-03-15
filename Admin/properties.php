<?php
// admin/properties.php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/models/PropertyModel.php';
require_once __DIR__ . '/models/UserModel.php';
require_once __DIR__ . '/models/TenancyModel.php';

$propertyModel = new PropertyModel();
$userModel = new UserModel();
$tenancyModel = new TenancyModel();

// Handle actions
$message = '';
$error = '';

// Delete property
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($propertyModel->deleteProperty($id)) {
        $message = "Property deleted successfully";
    } else {
        $error = "Failed to delete property";
    }
}

// Update property status
if (isset($_POST['update_status'])) {
    $id = intval($_POST['property_id']);
    $status = $_POST['status'];
    if ($propertyModel->updatePropertyStatus($id, $status)) {
        $message = "Property status updated successfully";
    } else {
        $error = "Failed to update property status";
    }
}

// Toggle featured status
if (isset($_GET['featured'])) {
    $id = intval($_GET['featured']);
    $featured = intval($_GET['value'] ?? 1);
    if ($propertyModel->toggleFeatured($id, $featured)) {
        $message = "Property featured status updated";
    } else {
        $error = "Failed to update featured status";
    }
}

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$type_filter = $_GET['type'] ?? '';
$landlord_filter = $_GET['landlord'] ?? '';
$search = $_GET['search'] ?? '';

// Build filters array
$filters = [];
if ($status_filter) $filters['status'] = $status_filter;
if ($type_filter) $filters['property_type'] = $type_filter;
if ($landlord_filter) $filters['landlord_id'] = $landlord_filter;
if ($search) $filters['search'] = $search;

// Get properties
$properties = $propertyModel->getAllProperties($filters, $limit, $offset);
$totalProperties = $propertyModel->countProperties($filters);
$totalPages = ceil($totalProperties / $limit);

// Get all landlords for filter dropdown
$landlords = $userModel->getUsersByType('landlord', 100, 0);

// Get property types for filter dropdown
$propertyTypes = $propertyModel->getPropertyTypes();

// Get statistics
$stats = [
    'total' => $propertyModel->countProperties(),
    'available' => $propertyModel->countPropertiesByStatus('available'),
    'occupied' => $propertyModel->countPropertiesByStatus('occupied'),
    'maintenance' => $propertyModel->countPropertiesByStatus('maintenance'),
    'featured' => $propertyModel->countFeaturedProperties(),
    'total_rent' => $propertyModel->getTotalMonthlyRent()
];

$page_title = 'Manage Properties';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="content-header">
    
    <div class="header-actions">
        <a href="add_property.php" class="btn btn-primary" style="text-decoration: none;">
            <i class="fas fa-plus"></i> Add New Property
        </a>
        <a href="export_properties.php" class="btn btn-secondary">
            <i class="fas fa-download"></i> Export
        </a>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible">
        <i class="fas fa-check-circle"></i> <?php echo $message; ?>
        <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible">
        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
    </div>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: #4e73df20; color: #4e73df;">
            <i class="fas fa-home"></i>
        </div>
        <div class="stat-details">
            <h3>Total Properties</h3>
            <p class="stat-value"><?php echo $stats['total']; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #1cc88a20; color: #1cc88a;">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-details">
            <h3>Available</h3>
            <p class="stat-value"><?php echo $stats['available']; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #f6c23e20; color: #f6c23e;">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-details">
            <h3>Occupied</h3>
            <p class="stat-value"><?php echo $stats['occupied']; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #e74a3b20; color: #e74a3b;">
            <i class="fas fa-tools"></i>
        </div>
        <div class="stat-details">
            <h3>Maintenance</h3>
            <p class="stat-value"><?php echo $stats['maintenance']; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #f6c23e20; color: #f6c23e;">
            <i class="fas fa-star"></i>
        </div>
        <div class="stat-details">
            <h3>Featured</h3>
            <p class="stat-value"><?php echo $stats['featured']; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #1cc88a20; color: #1cc88a;">
            <i class="fas fa-credit-card"></i>
        </div>
        <div class="stat-details">
            <h3>Monthly Rent</h3>
            <p class="stat-value">KES <?php echo number_format($stats['total_rent']); ?></p>
        </div>
    </div>
</div>

<!-- Search and Filters -->
<div class="filters-section">
    <form method="GET" action="" class="filters-form">
        <div class="filters-grid">
            <div class="filter-group">
                <label for="search">Search</label>
                <input type="text" id="search" name="search" placeholder="Property name, address..." 
                       value="<?php echo htmlspecialchars($search); ?>" class="form-control">
            </div>
            
            <div class="filter-group">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="available" <?php echo $status_filter == 'available' ? 'selected' : ''; ?>>Available</option>
                    <option value="occupied" <?php echo $status_filter == 'occupied' ? 'selected' : ''; ?>>Occupied</option>
                    <option value="maintenance" <?php echo $status_filter == 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="type">Property Type</label>
                <select id="type" name="type" class="form-control">
                    <option value="">All Types</option>
                    <?php foreach ($propertyTypes as $type): ?>
                        <option value="<?php echo $type; ?>" <?php echo $type_filter == $type ? 'selected' : ''; ?>>
                            <?php echo ucfirst($type); ?>
                        </option>
                    <?php endforeach; ?>
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
            
            <div class="filter-group filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Apply Filters
                </button>
                <a href="properties.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Clear
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Properties Table -->
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Property</th>
                <th>Landlord</th>
                <th>Location</th>
                <th>Rent</th>
                <th>Status</th>
                <th>Features</th>
                <th>Listed</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($properties)): ?>
                <tr>
                    <td colspan="9" class="text-center">No properties found</td>
                </tr>
            <?php else: ?>
                <?php foreach ($properties as $property): 
                    // Get current tenant if occupied
                    $currentTenant = null;
                    if ($property['status'] == 'occupied') {
                        $currentTenant = $tenancyModel->getActiveTenancyByProperty($property['id']);
                    }
                    
                    // Get landlord info
                    $landlord = $userModel->getUserById($property['landlord_id']);
                ?>
                <tr>
                    <td>#<?php echo $property['id']; ?></td>
                    <td>
                        <div class="property-info">
                            <?php if ($property['featured']): ?>
                                <span class="featured-badge" title="Featured Property">
                                    <i class="fas fa-star" style="color: #f6c23e;"></i>
                                </span>
                            <?php endif; ?>
                            <strong><?php echo htmlspecialchars($property['property_name']); ?></strong>
                            <br>
                            <small><?php echo ucfirst($property['property_type'] ?? 'Property'); ?></small>
                            <?php if ($property['bedrooms'] || $property['bathrooms']): ?>
                                <br>
                                <small class="text-muted">
                                    <?php echo $property['bedrooms'] ? $property['bedrooms'] . ' bed' : ''; ?>
                                    <?php echo ($property['bedrooms'] && $property['bathrooms']) ? ' · ' : ''; ?>
                                    <?php echo $property['bathrooms'] ? $property['bathrooms'] . ' bath' : ''; ?>
                                    <?php echo $property['sqft'] ? ' · ' . number_format($property['sqft']) . ' sqft' : ''; ?>
                                </small>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <?php if ($landlord): ?>
                            <div class="landlord-info">
                                <div class="user-avatar small">
                                    <?php echo strtoupper(substr($landlord['username'] ?? 'L', 0, 1)); ?>
                                </div>
                                <div>
                                    <strong><?php echo htmlspecialchars($landlord['first_name'] ?? '') . ' ' . htmlspecialchars($landlord['last_name'] ?? ''); ?></strong>
                                    <br>
                                    <small>@<?php echo htmlspecialchars($landlord['username']); ?></small>
                                </div>
                            </div>
                        <?php else: ?>
                            <span class="text-muted">Unknown</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($property['neighborhood'] ?? ''); ?></div>
                        <small><?php echo htmlspecialchars($property['city'] ?? ''); ?></small>
                        <?php if (!empty($property['address'])): ?>
                            <br><small class="text-muted"><?php echo htmlspecialchars($property['address']); ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong>KES <?php echo number_format($property['monthly_rent']); ?></strong>
                        <br><small class="text-muted">per month</small>
                    </td>
                    <td>
                        <?php
                        $status = $property['status'] ?? 'unknown';
                        $statusClass = '';
                        $statusText = ucfirst($status);
                        
                        switch($status) {
                            case 'available':
                                $statusClass = 'badge-success';
                                break;
                            case 'occupied':
                                $statusClass = 'badge-warning';
                                break;
                            case 'maintenance':
                                $statusClass = 'badge-danger';
                                break;
                            default:
                                $statusClass = 'badge-secondary';
                        }
                        ?>
                        <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                        
                        <?php if ($currentTenant): ?>
                            <br>
                            <small class="text-info">
                                <i class="fas fa-user"></i> 
                                <?php echo htmlspecialchars($currentTenant['student_first_name'] ?? '') . ' ' . htmlspecialchars($currentTenant['student_last_name'] ?? ''); ?>
                            </small>
                        <?php endif; ?>
                        
                        <!-- Quick status update -->
                        <div class="status-update">
                            <select class="status-select" data-id="<?php echo $property['id']; ?>" onchange="updateStatus(this)">
                                <option value="">Change</option>
                                <option value="available">Available</option>
                                <option value="occupied">Occupied</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        <?php 
                        // Parse amenities if available
                        $amenities = [];
                        if (!empty($property['amenities'])) {
                            $amenities = json_decode($property['amenities'], true) ?? [];
                        }
                        $amenityCount = count($amenities);
                        ?>
                        <span class="badge badge-info"><?php echo $amenityCount; ?> amenities</span>
                        <?php if ($property['featured']): ?>
                            <span class="badge badge-warning">Featured</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div><?php echo date('M d, Y', strtotime($property['created_at'])); ?></div>
                        <small class="text-muted"><?php echo timeAgo($property['created_at']); ?></small>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="../client/property.php?id=<?php echo $property['id']; ?>" target="_blank" class="btn btn-sm btn-info" title="View on site">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="edit_property.php?id=<?php echo $property['id']; ?>" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?featured=<?php echo $property['id']; ?>&value=<?php echo $property['featured'] ? 0 : 1; ?>" 
                               class="btn btn-sm btn-warning" 
                               title="<?php echo $property['featured'] ? 'Remove featured' : 'Mark as featured'; ?>"
                               onclick="return confirm('<?php echo $property['featured'] ? 'Remove from featured?' : 'Mark as featured?'; ?>')">
                                <i class="fas fa-star"></i>
                            </a>
                            <a href="?delete=<?php echo $property['id']; ?>" class="btn btn-sm btn-danger" title="Delete" 
                               onclick="return confirmDelete('Delete this property? This action cannot be undone.')">
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
            <a href="?page=<?php echo ($page - 1); ?><?php echo $status_filter ? '&status='.$status_filter : ''; ?><?php echo $type_filter ? '&type='.$type_filter : ''; ?><?php echo $landlord_filter ? '&landlord='.$landlord_filter : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" class="page-link">
                <i class="fas fa-chevron-left"></i> Previous
            </a>
        <?php endif; ?>
        
        <?php
        $start = max(1, $page - 2);
        $end = min($totalPages, $page + 2);
        for ($i = $start; $i <= $end; $i++):
        ?>
            <a href="?page=<?php echo $i; ?><?php echo $status_filter ? '&status='.$status_filter : ''; ?><?php echo $type_filter ? '&type='.$type_filter : ''; ?><?php echo $landlord_filter ? '&landlord='.$landlord_filter : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" 
               class="page-link <?php echo $i == $page ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
        
        <?php if ($page < $totalPages): ?>
            <a href="?page=<?php echo ($page + 1); ?><?php echo $status_filter ? '&status='.$status_filter : ''; ?><?php echo $type_filter ? '&type='.$type_filter : ''; ?><?php echo $landlord_filter ? '&landlord='.$landlord_filter : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" class="page-link">
                Next <i class="fas fa-chevron-right"></i>
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Time ago helper function -->
<?php
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return 'just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 2592000) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return 'on ' . date('M j, Y', $time);
    }
}
?>

<style>
/* Additional styles for properties page */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 15px;
    margin-bottom: 25px;
}

.filters-section {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
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
    font-weight: 500;
    font-size: 13px;
    color: #666;
}

.filter-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

.filter-actions .btn {
    flex: 1;
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
    white-space: nowrap;
}

.table td {
    padding: 15px 12px;
    border-bottom: 1px solid #dee2e6;
    vertical-align: middle;
}

.property-info {
    position: relative;
}

.featured-badge {
    position: absolute;
    top: -5px;
    left: -5px;
}

.landlord-info {
    display: flex;
    align-items: center;
    gap: 8px;
}

.user-avatar.small {
    width: 30px;
    height: 30px;
    font-size: 12px;
}

.status-update {
    margin-top: 5px;
}

.status-select {
    font-size: 11px;
    padding: 2px 4px;
    border: 1px solid #ddd;
    border-radius: 3px;
    width: 80px;
}

.action-buttons {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

.btn-sm {
    padding: 5px 8px;
    font-size: 12px;
}

.badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}

.badge-success { background: #d4edda; color: #155724; }
.badge-warning { background: #fff3cd; color: #856404; }
.badge-danger { background: #f8d7da; color: #721c24; }
.badge-info { background: #d1ecf1; color: #0c5460; }
.badge-secondary { background: #e2e3e5; color: #383d41; }

.alert-dismissible {
    position: relative;
    padding-right: 40px;
}

.alert-dismissible .close {
    position: absolute;
    top: 10px;
    right: 10px;
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: inherit;
    opacity: 0.7;
}

.alert-dismissible .close:hover {
    opacity: 1;
}

.pagination {
    margin-top: 20px;
    display: flex;
    gap: 5px;
    justify-content: center;
    flex-wrap: wrap;
}

.page-link {
    display: inline-block;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    color: #4e73df;
    text-decoration: none;
    transition: all 0.3s;
}

.page-link:hover {
    background: #f8f9fa;
    border-color: #4e73df;
}

.page-link.active {
    background: #4e73df;
    color: white;
    border-color: #4e73df;
}

.text-muted {
    color: #6c757d;
}

.text-info {
    color: #17a2b8;
}
</style>

<script>
function updateStatus(select) {
    const propertyId = select.dataset.id;
    const newStatus = select.value;
    
    if (!newStatus) return;
    
    if (confirm(`Change property status to ${newStatus}?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="update_status" value="1">
            <input type="hidden" name="property_id" value="${propertyId}">
            <input type="hidden" name="status" value="${newStatus}">
        `;
        document.body.appendChild(form);
        form.submit();
    } else {
        select.value = '';
    }
}

function confirmDelete(message) {
    return confirm(message);
}
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>