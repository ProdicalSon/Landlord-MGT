<?php
// admin/profile.php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/models/AdminModel.php';

// Helper function to get profile image URL
function getProfileImageUrl($imagePath) {
    if (empty($imagePath)) {
        return null;
    }
    // Images are stored in Admin/uploads/profiles/
    return '/Landlord-MGT/Admin/' . $imagePath;
}

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

<!-- Profile Container -->
<div class="profile-wrapper">
    <!-- Profile Header Card -->
 <!-- Profile Header -->
<div class="profile-header-card">
    <div class="profile-cover-bg"></div>
    <div class="profile-info-wrapper">
        <div class="profile-avatar-container">
            <!-- For the profile avatar in the header -->
            <div class="profile-avatar-large" id="profile-avatar">
                <?php if (!empty($admin['profile_image'])): ?>
                    <!-- Images are stored in Admin/uploads/profiles/ -->
                    <img src="/Landlord-MGT/Admin/<?php echo htmlspecialchars($admin['profile_image']); ?>" alt="Profile Image" class="profile-img">
                <?php else: ?>
                    <div class="avatar-initials">
                        <?php 
                        $initial = substr($admin['first_name'] ?? $admin['username'], 0, 1);
                        echo strtoupper($initial); 
                        ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="profile-status-badge">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="profile-details">
            <h1 class="profile-name"><?php echo htmlspecialchars($admin['first_name'] ?? '') . ' ' . htmlspecialchars($admin['last_name'] ?? ''); ?></h1>
            <p class="profile-badge">
                <span class="role-badge <?php echo $admin['role']; ?>">
                    <i class="fas fa-user-shield"></i>
                    <?php echo ucfirst(str_replace('_', ' ', $admin['role'] ?? 'Admin')); ?>
                </span>
            </p>
            <div class="profile-meta">
                <span><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($admin['email']); ?></span>
                <span><i class="fas fa-calendar-alt"></i> Joined <?php echo date('F Y', strtotime($admin['created_at'])); ?></span>
                <span><i class="fas fa-clock"></i> Last login: <?php echo $admin['last_login'] ? date('M j, Y H:i', strtotime($admin['last_login'])) : 'Never'; ?></span>
            </div>
        </div>
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

    <!-- Profile Content Grid -->
    <div class="profile-grid">
        <!-- Personal Information Card -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-id-card"></i> Personal Information</h3>
            </div>
            <div class="card-body">
                <div class="info-list">
                    <div class="info-row">
                        <span class="info-label">Username</span>
                        <span class="info-value">@<?php echo htmlspecialchars($admin['username']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email Address</span>
                        <span class="info-value"><?php echo htmlspecialchars($admin['email']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">First Name</span>
                        <span class="info-value"><?php echo htmlspecialchars($admin['first_name'] ?? '—'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Last Name</span>
                        <span class="info-value"><?php echo htmlspecialchars($admin['last_name'] ?? '—'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Role</span>
                        <span class="info-value">
                            <span class="badge <?php echo $admin['role'] == 'super_admin' ? 'badge-primary' : 'badge-info'; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $admin['role'] ?? 'Admin')); ?>
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Profile Card -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-user-edit"></i> Edit Profile</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="" enctype="multipart/form-data" class="profile-form">
                    <input type="hidden" name="action" value="update_profile">
                    
                    <!-- Profile Image Upload -->

                        <div class="image-upload-section">
                            <label class="form-label">Profile Image</label>
                            <div class="image-upload-wrapper">
                                <div class="image-preview">
                                    <?php if (!empty($admin['profile_image'])): ?>
                                        <img src="/Landlord-MGT/Admin/<?php echo htmlspecialchars($admin['profile_image']); ?>" alt="Profile" id="image-preview">
                                    <?php else: ?>
                                        <div class="image-placeholder">
                                            <i class="fas fa-user-circle"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="upload-actions">
                                    <div class="upload-buttons">
                                        <label for="profile_image" class="btn btn-outline-primary">
                                            <i class="fas fa-upload"></i> Choose Image
                                        </label>
                                        <input type="file" id="profile_image" name="profile_image" accept="image/jpeg,image/png,image/gif,image/webp" hidden>
                                        <?php if (!empty($admin['profile_image'])): ?>
                                            <button type="button" class="btn btn-outline-danger" onclick="removeImage()">
                                                <i class="fas fa-trash"></i> Remove
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    <small class="form-hint">Max size: 2MB. Allowed: JPG, PNG, GIF, WEBP</small>
                                </div>
                            </div>
                        </div>
                    
                    <!-- Username (readonly) -->
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" value="<?php echo htmlspecialchars($admin['username']); ?>" 
                               class="form-control" readonly disabled>
                        <small class="form-hint">Username cannot be changed</small>
                    </div>
                    
                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Email Address <span class="required-star">*</span></label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($admin['email']); ?>" 
                               class="form-control" required>
                    </div>
                    
                    <!-- Name Fields -->
                    <div class="form-row">
                        <div class="form-group col">
                            <label for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" 
                                   value="<?php echo htmlspecialchars($admin['first_name'] ?? ''); ?>" 
                                   class="form-control" placeholder="Enter first name">
                        </div>
                        
                        <div class="form-group col">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" 
                                   value="<?php echo htmlspecialchars($admin['last_name'] ?? ''); ?>" 
                                   class="form-control" placeholder="Enter last name">
                        </div>
                    </div>
                    
                    <!-- Role (readonly) -->
                    <div class="form-group">
                        <label for="role">Role</label>
                        <input type="text" id="role" value="<?php echo ucfirst(str_replace('_', ' ', $admin['role'] ?? 'Admin')); ?>" 
                               class="form-control" readonly disabled>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </form>
                
                <!-- Hidden form for removing image -->
                <form method="POST" id="remove-image-form" style="display: none;">
                    <input type="hidden" name="action" value="remove_image">
                </form>
            </div>
        </div>

        <!-- Change Password Card -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-lock"></i> Change Password</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="" class="password-form" onsubmit="return validatePassword()">
                    <input type="hidden" name="action" value="change_password">
                    
                    <!-- Current Password -->
                    <div class="form-group">
                        <label for="current_password">Current Password <span class="required-star">*</span></label>
                        <div class="password-input-wrapper">
                            <input type="password" id="current_password" name="current_password" 
                                   class="form-control" required placeholder="Enter current password">
                            <button type="button" class="password-toggle" onclick="togglePassword('current_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- New Password -->
                    <div class="form-group">
                        <label for="new_password">New Password <span class="required-star">*</span></label>
                        <div class="password-input-wrapper">
                            <input type="password" id="new_password" name="new_password" 
                                   class="form-control" required placeholder="Enter new password">
                            <button type="button" class="password-toggle" onclick="togglePassword('new_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-requirements">
                            <p class="requirements-title">Password must:</p>
                            <ul class="requirements-list">
                                <li id="req-length" class="req-item">
                                    <i class="fas fa-circle"></i> Be at least 8 characters
                                </li>
                                <li id="req-uppercase" class="req-item">
                                    <i class="fas fa-circle"></i> Contain at least 1 uppercase letter
                                </li>
                                <li id="req-lowercase" class="req-item">
                                    <i class="fas fa-circle"></i> Contain at least 1 lowercase letter
                                </li>
                                <li id="req-number" class="req-item">
                                    <i class="fas fa-circle"></i> Contain at least 1 number
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password <span class="required-star">*</span></label>
                        <div class="password-input-wrapper">
                            <input type="password" id="confirm_password" name="confirm_password" 
                                   class="form-control" required placeholder="Confirm new password">
                            <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Password Match Indicator -->
                    <div id="password-match" class="password-match-indicator"></div>
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-key"></i> Change Password
                    </button>
                </form>
            </div>
        </div>

        <!-- Account Security Card -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-shield-alt"></i> Account Security</h3>
            </div>
            <div class="card-body">
                <div class="security-list">
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

<!-- Hidden form for removing image -->
<form method="POST" id="remove-image-form" style="display: none;">
    <input type="hidden" name="action" value="remove_image">
</form>

<style>
/* Modern Profile Page Styles */
:root {
    --primary: #4361ee;
    --primary-dark: #3a56d4;
    --primary-light: #4895ef;
    --secondary: #4cc9f0;
    --success: #06d6a0;
    --danger: #ef476f;
    --warning: #ffd166;
    --info: #118ab2;
    --dark: #2b2d42;
    --gray: #8d99ae;
    --light: #edf2f4;
    --white: #ffffff;
    --shadow-sm: 0 2px 4px rgba(0,0,0,0.1);
    --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
    --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
    --border-radius: 12px;
    --transition: all 0.3s ease;
}

/* Profile Wrapper */
.profile-wrapper {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

/* Profile Header Card */
.profile-header-card {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    border-radius: var(--border-radius);
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
    box-shadow: var(--shadow-lg);
}

.profile-cover-bg {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.1"><path d="M0 0 L100 100 M100 0 L0 100" stroke="white" stroke-width="2"/></svg>');
    background-size: 30px 30px;
}

.profile-info-wrapper {
    position: relative;
    padding: 40px;
    display: flex;
    align-items: center;
    gap: 40px;
    z-index: 1;
}

/* Profile Avatar */
.profile-avatar-container {
    position: relative;
}

.profile-avatar-large {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    border: 4px solid rgba(255,255,255,0.3);
    overflow: hidden;
    box-shadow: var(--shadow-lg);
    background: white;
}

.profile-avatar-large img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-initials {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    font-weight: 600;
    color: var(--primary);
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
}

.profile-status-badge {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: var(--success);
    color: white;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid white;
    box-shadow: var(--shadow-sm);
}

/* Profile Details */
.profile-details {
    flex: 1;
    color: white;
}

.profile-name {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 10px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.role-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: rgba(255,255,255,0.2);
    border-radius: 30px;
    font-size: 14px;
    font-weight: 500;
    backdrop-filter: blur(10px);
    margin-bottom: 15px;
}

.role-badge.super_admin {
    background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
    color: var(--dark);
}

.role-badge.admin {
    background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
    color: var(--dark);
}

.profile-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    font-size: 14px;
    opacity: 0.9;
}

.profile-meta span {
    display: flex;
    align-items: center;
    gap: 8px;
}

.profile-meta i {
    width: 16px;
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
    border-left: 4px solid #28a745;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border-left: 4px solid #dc3545;
}

.alert-close {
    margin-left: auto;
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: inherit;
    opacity: 0.5;
    transition: var(--transition);
}

.alert-close:hover {
    opacity: 1;
}

/* Profile Grid */
.profile-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
}

/* Card Styles */
.card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    transition: var(--transition);
    border: 1px solid rgba(0,0,0,0.05);
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.card-header {
    padding: 20px 25px;
    background: linear-gradient(to right, #f8f9fa, #ffffff);
    border-bottom: 1px solid #e9ecef;
}

.card-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: var(--dark);
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-header h3 i {
    color: var(--primary);
    font-size: 20px;
}

.card-body {
    padding: 25px;
}

/* Info List */
.info-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.info-row {
    display: flex;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    width: 120px;
    font-size: 14px;
    color: var(--gray);
    font-weight: 500;
}

.info-value {
    flex: 1;
    font-size: 14px;
    color: var(--dark);
    font-weight: 500;
}

/* Badge */
.badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 30px;
    font-size: 12px;
    font-weight: 600;
}

.badge-primary {
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    color: white;
}

.badge-info {
    background: linear-gradient(135deg, var(--info), var(--secondary));
    color: white;
}

/* Image Upload Section */
.image-upload-section {
    margin-bottom: 25px;
}

.form-label {
    display: block;
    margin-bottom: 10px;
    font-weight: 600;
    font-size: 14px;
    color: var(--dark);
}

.image-upload-wrapper {
    display: flex;
    align-items: center;
    gap: 25px;
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
}

.image-preview {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid white;
    box-shadow: var(--shadow-md);
}

.image-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.image-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #e9ecef, #dee2e6);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 60px;
    color: var(--gray);
}

.upload-actions {
    flex: 1;
}

.upload-buttons {
    display: flex;
    gap: 10px;
    margin-bottom: 8px;
    flex-wrap: wrap;
}

.form-hint {
    font-size: 12px;
    color: var(--gray);
}

/* Form Styles */
.form-group {
    margin-bottom: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    font-size: 14px;
    color: var(--dark);
}

.required-star {
    color: var(--danger);
    margin-left: 3px;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 14px;
    transition: var(--transition);
}

.form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
}

.form-control[readonly],
.form-control[disabled] {
    background: #f8f9fa;
    cursor: not-allowed;
    border-color: #dee2e6;
}

/* Password Input */
.password-input-wrapper {
    position: relative;
}

.password-input-wrapper input {
    padding-right: 45px;
}

.password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--gray);
    cursor: pointer;
    padding: 5px;
    transition: var(--transition);
}

.password-toggle:hover {
    color: var(--primary);
}

/* Password Requirements */
.password-requirements {
    margin-top: 10px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 3px solid var(--primary);
}

.requirements-title {
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 8px;
    color: var(--dark);
}

.requirements-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.req-item {
    font-size: 12px;
    color: var(--gray);
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.req-item i {
    font-size: 8px;
    color: var(--gray);
}

.req-item.valid {
    color: var(--success);
}

.req-item.valid i {
    color: var(--success);
}

/* Password Match Indicator */
.password-match-indicator {
    margin: 15px 0;
    padding: 10px;
    border-radius: 6px;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.password-match-indicator.match {
    background: #d4edda;
    color: #155724;
    border-left: 3px solid #28a745;
}

.password-match-indicator.no-match {
    background: #f8d7da;
    color: #721c24;
    border-left: 3px solid #dc3545;
}

/* Button Styles */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    color: white;
    box-shadow: 0 4px 6px rgba(67, 97, 238, 0.2);
}

.btn-primary:hover {
    background: linear-gradient(135deg, var(--primary-dark), var(--primary));
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(67, 97, 238, 0.3);
}

.btn-outline-primary {
    background: white;
    color: var(--primary);
    border: 2px solid var(--primary);
}

.btn-outline-primary:hover {
    background: var(--primary);
    color: white;
    transform: translateY(-2px);
}

.btn-outline-danger {
    background: white;
    color: var(--danger);
    border: 2px solid var(--danger);
}

.btn-outline-danger:hover {
    background: var(--danger);
    color: white;
    transform: translateY(-2px);
}

.btn-block {
    width: 100%;
}

/* Security List */
.security-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.security-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 8px;
    transition: var(--transition);
}

.security-item:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.security-icon {
    width: 45px;
    height: 45px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    font-size: 18px;
    box-shadow: var(--shadow-sm);
}

.security-details {
    flex: 1;
}

.security-details h4 {
    font-size: 14px;
    margin-bottom: 3px;
    color: var(--dark);
}

.security-details p {
    font-size: 13px;
    color: var(--gray);
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

/* Responsive Design */
@media (max-width: 1200px) {
    .profile-grid {
        gap: 20px;
    }
}

@media (max-width: 992px) {
    .profile-info-wrapper {
        flex-direction: column;
        text-align: center;
        padding: 30px 20px;
    }
    
    .profile-meta {
        justify-content: center;
    }
    
    .profile-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .profile-wrapper {
        padding: 15px;
    }
    
    .profile-name {
        font-size: 24px;
    }
    
    .profile-meta {
        flex-direction: column;
        gap: 10px;
        align-items: center;
    }
    
    .image-upload-wrapper {
        flex-direction: column;
        text-align: center;
    }
    
    .upload-buttons {
        justify-content: center;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .card-header,
    .card-body {
        padding: 20px;
    }
}

/* Loading State */
.btn-loading {
    position: relative;
    pointer-events: none;
    opacity: 0.7;
}

.btn-loading::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    border: 2px solid transparent;
    border-top-color: white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Scrollbar Styling */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: var(--primary);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--primary-dark);
}
</style>

<script>
// Image preview
document.getElementById('profile_image')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validate file size
        if (file.size > 2 * 1024 * 1024) {
            showNotification('File too large. Maximum size is 2MB.', 'error');
            this.value = '';
            return;
        }
        
        // Validate file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            showNotification('Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.', 'error');
            this.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('image-preview');
            if (preview) {
                preview.src = e.target.result;
            } else {
                const previewContainer = document.querySelector('.image-preview');
                previewContainer.innerHTML = `<img src="${e.target.result}" alt="Preview" id="image-preview">`;
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
    
    if (!/[A-Z]/.test(newPass)) {
        showNotification('Password must contain at least one uppercase letter', 'error');
        return false;
    }
    
    if (!/[a-z]/.test(newPass)) {
        showNotification('Password must contain at least one lowercase letter', 'error');
        return false;
    }
    
    if (!/[0-9]/.test(newPass)) {
        showNotification('Password must contain at least one number', 'error');
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
document.getElementById('new_password')?.addEventListener('input', function() {
    const password = this.value;
    
    // Length validation
    document.getElementById('req-length').classList.toggle('valid', password.length >= 8);
    
    // Uppercase validation
    document.getElementById('req-uppercase').classList.toggle('valid', /[A-Z]/.test(password));
    
    // Lowercase validation
    document.getElementById('req-lowercase').classList.toggle('valid', /[a-z]/.test(password));
    
    // Number validation
    document.getElementById('req-number').classList.toggle('valid', /[0-9]/.test(password));
    
    // Update requirement icons
    document.querySelectorAll('.req-item').forEach(item => {
        const icon = item.querySelector('i');
        if (item.classList.contains('valid')) {
            icon.className = 'fas fa-check-circle';
        } else {
            icon.className = 'fas fa-circle';
        }
    });
    
    checkPasswordMatch();
});

document.getElementById('confirm_password')?.addEventListener('input', checkPasswordMatch);

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
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${message}</span>
        <button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 5000);
}

// Add loading state to form submissions
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function() {
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.classList.add('btn-loading');
            submitBtn.disabled = true;
        }
    });
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>