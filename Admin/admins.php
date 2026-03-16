<?php
// admin/admins.php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/models/AdminModel.php';

// Check if user is super admin
if (!isSuperAdmin()) {
    header('HTTP/1.0 403 Forbidden');
    die('Access denied. Super Admin privileges required.');
}

$adminModel = new AdminModel();

// Handle actions
$message = '';
$error = '';

// Delete admin
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($adminModel->deleteAdmin($id)) {
        $message = "Admin deleted successfully";
    } else {
        $error = "Failed to delete admin or you cannot delete yourself";
    }
}

// Toggle admin status
if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $status = intval($_GET['status'] ?? 1);
    if ($adminModel->updateAdminStatus($id, $status)) {
        $message = "Admin status updated successfully";
    } else {
        $error = "Failed to update admin status";
    }
}

// Handle adding new admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_admin') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $role = $_POST['role'] ?? 'admin';
    
    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Username, email and password are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters';
    } else {
        $result = $adminModel->createAdmin([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'role' => $role
        ]);
        
        if ($result['success']) {
            $message = $result['message'];
            // Clear form
            $_POST = [];
        } else {
            $error = $result['message'];
        }
    }
}

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Get all admins
$admins = $adminModel->getAllAdmins($limit, $offset);
$totalAdmins = $adminModel->countAdmins();
$totalPages = ceil($totalAdmins / $limit);

$page_title = 'Manage Admins';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="content-header">
   
    <button class="btn btn-primary" onclick="showAddAdminModal()">
        <i class="fas fa-plus"></i> Add New Admin
    </button>
</div>

<?php if ($message): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo $message; ?>
        <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">&times;</button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">&times;</button>
    </div>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="stats-grid" style="display: flex; gap: 150px; margin-top: 20px; margin-bottom: 20px;">
    <div class="stat-card" style="display: flex;">
        <div class="stat-icon" style="background: #4e73df20; color: #4e73df;">
            <i class="fas fa-user-shield"></i>
        </div>
        <div class="stat-details">
            <h3>Total Admins</h3>
            <p class="stat-value"><?php echo $totalAdmins; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #1cc88a20; color: #1cc88a;">
            <i class="fas fa-crown"></i>
        </div>
        <div class="stat-details">
            <h3>Super Admins</h3>
            <p class="stat-value"><?php echo $adminModel->countSuperAdmins(); ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #f6c23e20; color: #f6c23e;">
            <i class="fas fa-user-tie"></i>
        </div>
        <div class="stat-details">
            <h3>Regular Admins</h3>
            <p class="stat-value"><?php echo $totalAdmins - $adminModel->countSuperAdmins(); ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #e74a3b20; color: #e74a3b;">
            <i class="fas fa-ban"></i>
        </div>
        <div class="stat-details">
            <h3>Inactive</h3>
            <p class="stat-value"><?php echo $adminModel->countInactiveAdmins(); ?></p>
        </div>
    </div>
</div>

<!-- Admins Table -->
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Admin</th>
                <th>Contact</th>
                <th>Role</th>
                <th>Last Login</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($admins)): ?>
                <tr>
                    <td colspan="7" class="text-center">No admins found</td>
                </tr>
            <?php else: ?>
                <?php foreach ($admins as $admin): ?>
                <tr>
                    <td>#<?php echo $admin['id']; ?></td>
                    <td>
                        <div class="user-info">
                            <div class="user-avatar" style="background: <?php echo $admin['role'] == 'super_admin' ? '#e74a3b' : '#4e73df'; ?>">
                                <?php echo strtoupper(substr($admin['username'] ?? 'A', 0, 1)); ?>
                            </div>
                            <div>
                                <strong><?php echo htmlspecialchars($admin['first_name'] ?? '') . ' ' . htmlspecialchars($admin['last_name'] ?? ''); ?></strong>
                                <br>
                                <small>@<?php echo htmlspecialchars($admin['username']); ?></small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($admin['email']); ?></div>
                    </td>
                    <td>
                        <span class="badge <?php echo $admin['role'] == 'super_admin' ? 'badge-danger' : 'badge-primary'; ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $admin['role'])); ?>
                        </span>
                    </td>
                    <td>
                        <?php echo $admin['last_login'] ? date('M d, Y H:i', strtotime($admin['last_login'])) : 'Never'; ?>
                    </td>
                    <td>
                        <?php if ($admin['is_active']): ?>
                            <span class="badge badge-success">Active</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <?php if ($admin['id'] != $_SESSION['admin_id']): ?>
                                <?php if ($admin['is_active']): ?>
                                    <a href="?toggle=<?php echo $admin['id']; ?>&status=0" class="btn btn-sm btn-warning" title="Deactivate" onclick="return confirm('Deactivate this admin?')">
                                        <i class="fas fa-ban"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="?toggle=<?php echo $admin['id']; ?>&status=1" class="btn btn-sm btn-success" title="Activate" onclick="return confirm('Activate this admin?')">
                                        <i class="fas fa-check-circle"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="?delete=<?php echo $admin['id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this admin? This action cannot be undone.')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-muted"><i class="fas fa-user"></i> Current User</span>
                            <?php endif; ?>
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

<!-- Add Admin Modal -->
<div id="addAdminModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus"></i> Add New Administrator</h3>
            <button class="close-modal" onclick="hideAddAdminModal()">&times;</button>
        </div>
        
        <form method="POST" action="" id="addAdminForm">
            <input type="hidden" name="action" value="add_admin">
            
            <div class="form-group">
                <label for="username">Username <span class="required">*</span></label>
                <input type="text" id="username" name="username" class="form-control" 
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address <span class="required">*</span></label>
                <input type="email" id="email" name="email" class="form-control" 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>
            
            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" 
                           value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" class="form-control" 
                           value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password <span class="required">*</span></label>
                <div class="password-input-wrapper">
                    <input type="password" id="password" name="password" class="form-control" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <small class="form-hint">Must be at least 8 characters</small>
            </div>
            
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" class="form-control">
                    <option value="admin">Admin</option>
                    <option value="super_admin">Super Admin</option>
                </select>
            </div>
            
            <div class="form-actions" style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="hideAddAdminModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Admin</button>
            </div>
        </form>
    </div>
</div>

<style>
/* Modal Styles */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.modal-content {
    background: white;
    border-radius: 10px;
    padding: 30px;
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    animation: slideIn 0.3s ease;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #dee2e6;
}

.modal-header h3 {
    margin: 0;
    color: #333;
}

.close-modal {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #666;
}

.close-modal:hover {
    color: #333;
}

.required {
    color: #e74a3b;
    margin-left: 3px;
}

.password-input-wrapper {
    position: relative;
}

.password-input-wrapper input {
    padding-right: 40px;
}

.password-toggle {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #666;
    cursor: pointer;
}

@keyframes slideIn {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}
</style>

<script>
function showAddAdminModal() {
    document.getElementById('addAdminModal').style.display = 'flex';
}

function hideAddAdminModal() {
    document.getElementById('addAdminModal').style.display = 'none';
}

function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const button = input.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('addAdminModal');
    if (event.target === modal) {
        hideAddAdminModal();
    }
}

// Prevent modal from closing when clicking inside
document.querySelector('.modal-content')?.addEventListener('click', function(e) {
    e.stopPropagation();
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>