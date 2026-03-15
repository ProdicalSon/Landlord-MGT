<?php
// admin/profile.php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/models/AdminModel.php';

$adminModel = new AdminModel();
$admin_id = $_SESSION['admin_id'];
$admin = $adminModel->getAdminById($admin_id);

// If admin not found, logout
if (!$admin) {
    header('Location: logout.php');
    exit;
}

// Handle profile update
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        // Update profile with image
        if ($_POST['action'] === 'update_profile') {
            $data = [
                'first_name' => trim($_POST['first_name'] ?? ''),
                'last_name' => trim($_POST['last_name'] ?? ''),
                'email' => trim($_POST['email'] ?? '')
            ];
            
            // Validate email
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $error = 'Please enter a valid email address';
            } else {
                // Check if image was uploaded
                $imageFile = isset($_FILES['profile_image']) ? $_FILES['profile_image'] : null;
                
                $result = $adminModel->updateProfileWithImage($admin_id, $data, $imageFile);
                if ($result['success']) {
                    $message = $result['message'];
                    // Update session
                    $_SESSION['admin_name'] = trim($data['first_name'] . ' ' . $data['last_name']);
                    // Refresh admin data
                    $admin = $adminModel->getAdminById($admin_id);
                } else {
                    $error = $result['message'];
                }
            }
        }
        
        // Remove profile image
        if ($_POST['action'] === 'remove_image') {
            if ($adminModel->removeProfileImage($admin_id)) {
                $message = 'Profile image removed successfully';
                $admin = $adminModel->getAdminById($admin_id);
            } else {
                $error = 'Failed to remove profile image';
            }
        }
        
        // Change password
        if ($_POST['action'] === 'change_password') {
            $current = $_POST['current_password'] ?? '';
            $new = $_POST['new_password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            
            if (empty($current) || empty($new) || empty($confirm)) {
                $error = 'All password fields are required';
            } elseif ($new !== $confirm) {
                $error = 'New passwords do not match';
            } elseif (strlen($new) < 8) {
                $error = 'Password must be at least 8 characters';
            } else {
                $result = $adminModel->changePassword($admin_id, $current, $new);
                if ($result['success']) {
                    $message = $result['message'];
                } else {
                    $error = $result['message'];
                }
            }
        }
    }
}

$page_title = 'My Profile';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="profile-container">
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-cover">
            <div class="profile-avatar-large" id="profile-avatar">
                <?php if (!empty($admin['profile_image'])): ?>
                    <img src="../<?php echo htmlspecialchars($admin['profile_image']); ?>" alt="Profile Image" class="profile-img">
                <?php else: ?>
                    <?php echo strtoupper(substr($admin['first_name'] ?? $admin['username'], 0, 1)); ?>
                <?php endif; ?>
            </div>
            <div class="profile-title">
                <h1><?php echo htmlspecialchars($admin['first_name'] ?? '') . ' ' . htmlspecialchars($admin['last_name'] ?? ''); ?></h1>
                <p class="profile-role">
                    <i class="fas fa-user-shield"></i> 
                    <?php echo ucfirst($admin['role'] ?? 'Admin'); ?>
                </p>
                <p class="profile-since">
                    <i class="fas fa-calendar-alt"></i>
                    Member since <?php echo date('F j, Y', strtotime($admin['created_at'])); ?>
                </p>
            </div>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- Profile Content -->
    <div class="profile-content">
        <div class="profile-grid">
            <!-- Personal Information -->
            <div class="profile-card">
                <div class="card-header">
                    <h3><i class="fas fa-user-circle"></i> Personal Information</h3>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Username</span>
                            <span class="info-value">@<?php echo htmlspecialchars($admin['username']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Email Address</span>
                            <span class="info-value"><?php echo htmlspecialchars($admin['email']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">First Name</span>
                            <span class="info-value"><?php echo htmlspecialchars($admin['first_name'] ?? 'Not set'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Last Name</span>
                            <span class="info-value"><?php echo htmlspecialchars($admin['last_name'] ?? 'Not set'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Role</span>
                            <span class="info-value">
                                <span class="badge <?php echo $admin['role'] == 'super_admin' ? 'badge-primary' : 'badge-info'; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $admin['role'] ?? 'Admin')); ?>
                                </span>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Last Login</span>
                            <span class="info-value">
                                <?php echo $admin['last_login'] ? date('F j, Y H:i', strtotime($admin['last_login'])) : 'Never'; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Profile Form with Image Upload -->
            <div class="profile-card">
                <div class="card-header">
                    <h3><i class="fas fa-edit"></i> Edit Profile</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="" enctype="multipart/form-data" class="profile-form">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <!-- Profile Image Upload -->
                        <div class="form-group">
                            <label>Profile Image</label>
                            <div class="profile-image-upload">
                                <div class="current-image">
                                    <?php if (!empty($admin['profile_image'])): ?>
                                        <img src="../<?php echo htmlspecialchars($admin['profile_image']); ?>" alt="Profile" id="image-preview">
                                    <?php else: ?>
                                        <div class="no-image">
                                            <i class="fas fa-user-circle"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="upload-controls">
                                    <label for="profile_image" class="btn btn-secondary">
                                        <i class="fas fa-upload"></i> Choose Image
                                    </label>
                                    <input type="file" id="profile_image" name="profile_image" accept="image/jpeg,image/png,image/gif,image/webp" style="display: none;">
                                    <?php if (!empty($admin['profile_image'])): ?>
                                        <button type="button" class="btn btn-danger" onclick="removeImage()">
                                            <i class="fas fa-trash"></i> Remove
                                        </button>
                                    <?php endif; ?>
                                    <small class="form-text">Max size: 2MB. Allowed: JPG, PNG, GIF, WEBP</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" value="<?php echo htmlspecialchars($admin['username']); ?>" 
                                   class="form-control" readonly disabled>
                            <small class="form-text text-muted">Username cannot be changed</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address <span class="required">*</span></label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($admin['email']); ?>" 
                                   class="form-control" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col">
                                <label for="first_name">First Name</label>
                                <input type="text" id="first_name" name="first_name" 
                                       value="<?php echo htmlspecialchars($admin['first_name'] ?? ''); ?>" 
                                       class="form-control">
                            </div>
                            
                            <div class="form-group col">
                                <label for="last_name">Last Name</label>
                                <input type="text" id="last_name" name="last_name" 
                                       value="<?php echo htmlspecialchars($admin['last_name'] ?? ''); ?>" 
                                       class="form-control">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="role">Role</label>
                            <input type="text" id="role" value="<?php echo ucfirst(str_replace('_', ' ', $admin['role'] ?? 'Admin')); ?>" 
                                   class="form-control" readonly disabled>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </form>
                    
                    <!-- Hidden form for removing image -->
                    <form method="POST" id="remove-image-form" style="display: none;">
                        <input type="hidden" name="action" value="remove_image">
                    </form>
                </div>
            </div>

            <!-- Change Password Form -->
            <div class="profile-card">
                <div class="card-header">
                    <h3><i class="fas fa-lock"></i> Change Password</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="" class="profile-form" onsubmit="return validatePassword()">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="form-group">
                            <label for="current_password">Current Password <span class="required">*</span></label>
                            <div class="password-input">
                                <input type="password" id="current_password" name="current_password" 
                                       class="form-control" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('current_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">New Password <span class="required">*</span></label>
                            <div class="password-input">
                                <input type="password" id="new_password" name="new_password" 
                                       class="form-control" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('new_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="password-requirements">
                                <p>Password must:</p>
                                <ul>
                                    <li id="req-length" class="req-item">Be at least 8 characters</li>
                                    <li id="req-uppercase" class="req-item">Contain at least 1 uppercase letter</li>
                                    <li id="req-lowercase" class="req-item">Contain at least 1 lowercase letter</li>
                                    <li id="req-number" class="req-item">Contain at least 1 number</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password <span class="required">*</span></label>
                            <div class="password-input">
                                <input type="password" id="confirm_password" name="confirm_password" 
                                       class="form-control" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div id="password-match" class="password-match-indicator"></div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-key"></i> Change Password
                        </button>
                    </form>
                </div>
            </div>

            <!-- Account Security -->
            <div class="profile-card">
                <div class="card-header">
                    <h3><i class="fas fa-shield-alt"></i> Account Security</h3>
                </div>
                <div class="card-body">
                    <div class="security-info">
                        <div class="security-item">
                            <div class="security-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="security-details">
                                <h4>Last Login</h4>
                                <p><?php echo $admin['last_login'] ? date('F j, Y \a\t H:i', strtotime($admin['last_login'])) : 'Never'; ?></p>
                            </div>
                        </div>
                        
                        <div class="security-item">
                            <div class="security-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="security-details">
                                <h4>Account Created</h4>
                                <p><?php echo date('F j, Y', strtotime($admin['created_at'])); ?></p>
                            </div>
                        </div>
                        
                        <div class="security-item">
                            <div class="security-icon">
                                <i class="fas fa-sync-alt"></i>
                            </div>
                            <div class="security-details">
                                <h4>Last Updated</h4>
                                <p><?php echo date('F j, Y \a\t H:i', strtotime($admin['updated_at'])); ?></p>
                            </div>
                        </div>
                        
                        <div class="security-item">
                            <div class="security-icon">
                                <i class="fas fa-user-tag"></i>
                            </div>
                            <div class="security-details">
                                <h4>Account Type</h4>
                                <p><?php echo ucfirst(str_replace('_', ' ', $admin['role'] ?? 'Admin')); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Add these styles to your existing styles */
.profile-image-upload {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.current-image {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.current-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.current-image .no-image {
    width: 100%;
    height: 100%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 60px;
    color: #adb5bd;
}

.upload-controls {
    flex: 1;
}

.upload-controls .btn {
    margin-right: 10px;
    margin-bottom: 10px;
}

.profile-avatar-large {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    overflow: hidden;
    background: rgba(255,255,255,0.2);
    border: 3px solid white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 42px;
    font-weight: 600;
    color: white;
}

.profile-avatar-large img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Rest of your existing styles */
</style>

<script>
// Image preview
document.getElementById('profile_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('image-preview');
            if (preview) {
                preview.src = e.target.result;
            } else {
                // Create preview if it doesn't exist
                const currentImage = document.querySelector('.current-image');
                currentImage.innerHTML = `<img src="${e.target.result}" alt="Preview" id="image-preview">`;
            }
        }
        reader.readAsDataURL(file);
    }
});

// Remove image
function removeImage() {
    if (confirm('Are you sure you want to remove your profile image?')) {
        document.getElementById('remove-image-form').submit();
    }
}

// Password validation
function validatePassword() {
    const newPass = document.getElementById('new_password').value;
    const confirmPass = document.getElementById('confirm_password').value;
    
    if (newPass.length < 8) {
        showNotification('Password must be at least 8 characters', 'error');
        return false;
    }
    
    if (newPass !== confirmPass) {
        showNotification('Passwords do not match', 'error');
        return false;
    }
    
    return true;
}

// Toggle password visibility
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

// Real-time password validation
document.getElementById('new_password').addEventListener('input', function() {
    const password = this.value;
    
    document.getElementById('req-length').classList.toggle('valid', password.length >= 8);
    document.getElementById('req-uppercase').classList.toggle('valid', /[A-Z]/.test(password));
    document.getElementById('req-lowercase').classList.toggle('valid', /[a-z]/.test(password));
    document.getElementById('req-number').classList.toggle('valid', /[0-9]/.test(password));
    
    checkPasswordMatch();
});

document.getElementById('confirm_password').addEventListener('input', checkPasswordMatch);

function checkPasswordMatch() {
    const newPass = document.getElementById('new_password').value;
    const confirmPass = document.getElementById('confirm_password').value;
    const indicator = document.getElementById('password-match');
    
    if (confirmPass.length > 0) {
        if (newPass === confirmPass) {
            indicator.innerHTML = '<i class="fas fa-check-circle"></i> Passwords match';
            indicator.className = 'password-match-indicator match';
        } else {
            indicator.innerHTML = '<i class="fas fa-times-circle"></i> Passwords do not match';
            indicator.className = 'password-match-indicator no-match';
        }
    } else {
        indicator.innerHTML = '';
    }
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    notification.style.animation = 'slideIn 0.3s ease';
    notification.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i> ${message}`;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>