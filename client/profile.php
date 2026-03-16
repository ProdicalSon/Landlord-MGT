<?php
// profile.php
session_start();

// Add error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log that the page loaded
error_log("========== PROFILE.PAGE LOADED ==========");
error_log("Session user_id: " . ($_SESSION['user_id'] ?? 'not set'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST request received");
    error_log("POST data: " . print_r($_POST, true));
    error_log("FILES data: " . print_r($_FILES, true));
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'profile.php';
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/models/UserModel.php';
require_once __DIR__ . '/models/PropertyModel.php';
require_once __DIR__ . '/models/SavedPropertyModel.php';

$userModel = new UserModel();
$propertyModel = new PropertyModel();
$savedPropertyModel = new SavedPropertyModel();

$user_id = $_SESSION['user_id'];
$user = $userModel->getUserById($user_id);
$stats = $userModel->getUserStats($user_id);
$savedProperties = $savedPropertyModel ? $savedPropertyModel->getSavedPropertyIds($user_id) : [];

$full_name = $userModel->getFullName($user);

$message = '';
$error = '';

// Helper function to get profile image URL
function getProfileImageUrl($imagePath) {
    if (empty($imagePath)) {
        return null;
    }
    return '/Landlord-MGT/client/' . $imagePath;
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'update_profile') {
            $data = [
                'first_name' => trim($_POST['first_name']),
                'last_name' => trim($_POST['last_name']),
                'phone_number' => trim($_POST['phone_number'])
            ];
            
            // Check if image was uploaded
            $imageFile = isset($_FILES['profile_image']) ? $_FILES['profile_image'] : null;
            
            $result = $userModel->updateProfileWithImage($user_id, $data, $imageFile);
            if ($result['success']) {
                $_SESSION['full_name'] = $userModel->getFullName(array_merge($user, $data));
                $message = $result['message'];
                $user = $userModel->getUserById($user_id); // Refresh user data
            } else {
                $error = $result['message'];
            }
        } elseif ($_POST['action'] === 'remove_image') {
            if ($userModel->removeProfileImage($user_id)) {
                $message = 'Profile image removed successfully';
                $user = $userModel->getUserById($user_id);
            } else {
                $error = 'Failed to remove profile image';
            }
        } elseif ($_POST['action'] === 'change_password') {
            $current = $_POST['current_password'];
            $new = $_POST['new_password'];
            $confirm = $_POST['confirm_password'];
            
            if ($new !== $confirm) {
                $error = 'New passwords do not match';
            } elseif (strlen($new) < 6) {
                $error = 'Password must be at least 6 characters';
            } else {
                $result = $userModel->changePassword($user_id, $current, $new);
                if ($result['success']) {
                    $message = $result['message'];
                } else {
                    $error = $result['message'];
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: #f8f9fa;
        }

        .navbar {
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo a {
            text-decoration: none;
            font-size: 24px;
            font-weight: 700;
            color: #0077b6;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-menu {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .nav-link {
            text-decoration: none;
            color: #333;
            padding: 8px 12px;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .nav-link:hover {
            background: #f0f0f0;
        }

        .nav-link.active {
            color: #0077b6;
            background: #e6f2ff;
        }

        .logout-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .logout-btn:hover {
            background: #c0392b;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .profile-header {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 40px;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid #0077b6;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            background: linear-gradient(135deg, #0077b6, #00a8e8);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: white;
        }

        .profile-info h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .profile-meta {
            display: flex;
            gap: 20px;
            color: #666;
            font-size: 14px;
            flex-wrap: wrap;
        }

        .profile-meta i {
            margin-right: 5px;
            color: #0077b6;
        }

        .verification-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .verified {
            background-color: #d4edda;
            color: #155724;
        }

        .unverified {
            background-color: #f8d7da;
            color: #721c24;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-card i {
            font-size: 32px;
            color: #0077b6;
            margin-bottom: 10px;
        }

        .stat-card .number {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .stat-card .label {
            color: #666;
            font-size: 14px;
        }

        .profile-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        .profile-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .section-title {
            font-size: 20px;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: #0077b6;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Image Upload Styles */
        .image-upload-section {
            margin-bottom: 25px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .image-upload-wrapper {
            display: flex;
            align-items: center;
            gap: 25px;
            flex-wrap: wrap;
        }

        .current-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid #0077b6;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
            font-size: 48px;
            color: #adb5bd;
        }

        .upload-controls {
            flex: 1;
        }

        .upload-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 8px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #0077b6;
            color: white;
        }

        .btn-primary:hover {
            background: #005a8c;
        }

        .btn-outline-primary {
            background: white;
            color: #0077b6;
            border: 2px solid #0077b6;
        }

        .btn-outline-primary:hover {
            background: #0077b6;
            color: white;
        }

        .btn-outline-danger {
            background: white;
            color: #e74c3c;
            border: 2px solid #e74c3c;
        }

        .btn-outline-danger:hover {
            background: #e74c3c;
            color: white;
        }

        .btn-danger {
            background: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        .form-hint {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        input, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
            font-family: inherit;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: #0077b6;
        }

        input[disabled] {
            background: #f5f5f5;
            cursor: not-allowed;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

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
            color: #666;
            cursor: pointer;
            padding: 5px;
        }

        .password-toggle:hover {
            color: #0077b6;
        }

        .password-requirements {
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
            font-size: 12px;
            border-left: 3px solid #0077b6;
        }

        .requirements-list {
            list-style: none;
            padding-left: 0;
            margin-top: 5px;
        }

        .req-item {
            margin-bottom: 3px;
            color: #666;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .req-item i {
            font-size: 8px;
            color: #999;
        }

        .req-item.valid {
            color: #27ae60;
        }

        .req-item.valid i {
            color: #27ae60;
        }

        .password-match-indicator {
            margin: 15px 0;
            padding: 10px;
            border-radius: 6px;
            font-size: 13px;
        }

        .password-match-indicator.match {
            background: #d4edda;
            color: #155724;
        }

        .password-match-indicator.no-match {
            background: #f8d7da;
            color: #721c24;
        }

        .saved-properties-list {
            list-style: none;
        }

        .saved-property-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .saved-property-item:last-child {
            border-bottom: none;
        }

        .property-info h4 {
            margin-bottom: 5px;
        }

        .property-info p {
            color: #666;
            font-size: 13px;
        }

        .property-price {
            color: #0077b6;
            font-weight: 600;
        }

        .view-link {
            color: #0077b6;
            text-decoration: none;
            font-size: 13px;
        }

        .info-text {
            color: #666;
            font-size: 13px;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }
            
            .profile-content {
                grid-template-columns: 1fr;
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
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <a href="index.php">
                    <i class="fas fa-home"></i>
                    <span>SmartHunt</span>
                </a>
            </div>
            <div class="nav-menu">
                <a href="index.php" class="nav-link"><i class="fas fa-search"></i> Browse</a>
                <a href="profile.php" class="nav-link active"><i class="fas fa-user"></i> Profile</a>
                <a href="logout.php" class="nav-link" onclick="return confirm('Are you sure you want to logout?')">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="profile-header">
            <div class="profile-avatar">
                <?php if (!empty($user['profile_image'])): ?>
                    <img src="<?php echo getProfileImageUrl($user['profile_image']); ?>" alt="Profile Image">
                <?php else: ?>
                    <div class="avatar-placeholder">
                        <i class="fas fa-user-circle"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="profile-info">
                <h1>
                    <?php echo htmlspecialchars($full_name ?: $user['username']); ?>
                    <span class="verification-badge <?php echo $user['is_verified'] ? 'verified' : 'unverified'; ?>">
                        <i class="fas fa-<?php echo $user['is_verified'] ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                        <?php echo $user['is_verified'] ? 'Verified' : 'Unverified'; ?>
                    </span>
                </h1>
                <div class="profile-meta">
                    <span><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></span>
                    <span><i class="fas fa-at"></i> @<?php echo htmlspecialchars($user['username']); ?></span>
                    <?php if ($user['phone_number']): ?>
                        <span><i class="fas fa-phone"></i> <?php echo htmlspecialchars($user['phone_number']); ?></span>
                    <?php endif; ?>
                    <span><i class="fas fa-user-tag"></i> <?php echo ucfirst($user['user_type']); ?></span>
                    <span><i class="fas fa-calendar"></i> Joined <?php echo date('M Y', strtotime($user['created_at'])); ?></span>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-heart"></i>
                <div class="number"><?php echo $stats['saved_properties'] ?? 0; ?></div>
                <div class="label">Saved Properties</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-bell"></i>
                <div class="number"><?php echo $stats['unread_notifications'] ?? 0; ?></div>
                <div class="label">Unread Alerts</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock"></i>
                <div class="number"><?php echo date('M d', strtotime($user['updated_at'])); ?></div>
                <div class="label">Last Activity</div>
            </div>
        </div>

        <div class="profile-content">
            <div>


               <!-- Edit Profile Form with Image Upload -->
<div class="profile-section">
    <h2 class="section-title"><i class="fas fa-user-edit"></i> Edit Profile</h2>
    
    <form method="POST" action="" enctype="multipart/form-data" id="profileForm">
        <input type="hidden" name="action" value="update_profile">
        <input type="hidden" name="form_submitted" value="1">
        
        <!-- Profile Image Upload Section -->
        <div class="image-upload-section">
            <label>Profile Image</label>
            <div class="image-upload-wrapper">
                <div class="current-image">
                    <?php if (!empty($user['profile_image'])): ?>
                        <img src="<?php echo getProfileImageUrl($user['profile_image']); ?>" alt="Profile" id="image-preview">
                    <?php else: ?>
                        <div class="no-image">
                            <i class="fas fa-user-circle"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="upload-controls">
                    <div class="upload-buttons">
                        <label for="profile_image" class="btn btn-outline-primary">
                            <i class="fas fa-upload"></i> Choose Image
                        </label>
                        <input type="file" id="profile_image" name="profile_image" 
                               accept="image/jpeg,image/png,image/gif,image/webp">
                        
                        <?php if (!empty($user['profile_image'])): ?>
                            <button type="button" class="btn btn-outline-danger" onclick="removeImage()">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        <?php endif; ?>
                    </div>
                    <small class="form-hint">
                        <i class="fas fa-info-circle"></i> Max size: 2MB. Allowed: JPG, PNG, GIF, WEBP
                    </small>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
            <div class="info-text">Username cannot be changed</div>
        </div>

        <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" 
                       value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" 
                       value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
        </div>

        <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="tel" id="phone_number" name="phone_number" 
                   value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>">
        </div>

        <button type="submit" name="submit_profile" class="btn btn-primary">Update Profile</button>
    </form>
    
    <!-- Hidden form for removing image -->
    <form method="POST" id="remove-image-form" style="display: none;">
        <input type="hidden" name="action" value="remove_image">
    </form>
</div>

                <div class="profile-section" style="margin-top: 30px;">
                    <h2 class="section-title"><i class="fas fa-lock"></i> Change Password</h2>
                    <form method="POST" action="" onsubmit="return validatePassword()">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <div class="password-input-wrapper">
                                <input type="password" id="current_password" name="current_password" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('current_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <div class="password-input-wrapper">
                                <input type="password" id="new_password" name="new_password" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('new_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="password-requirements">
                                <p>Password must:</p>
                                <ul class="requirements-list">
                                    <li id="req-length" class="req-item">
                                        <i class="fas fa-circle"></i> Be at least 6 characters
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

                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <div class="password-input-wrapper">
                                <input type="password" id="confirm_password" name="confirm_password" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div id="password-match" class="password-match-indicator"></div>

                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </form>
                </div>
            </div>

            <div>
                <div class="profile-section">
                    <h2 class="section-title"><i class="fas fa-heart"></i> Saved Properties</h2>
                    <?php if (empty($savedProperties)): ?>
                        <p style="color: #666; text-align: center; padding: 20px;">
                            <i class="fas fa-info-circle"></i> No saved properties yet.
                            <br>
                            <a href="index.php" style="color: #0077b6;">Browse properties</a> to save them.
                        </p>
                    <?php else: ?>
                        <ul class="saved-properties-list">
                            <?php 
                            $displayLimit = 5;
                            $count = 0;
                            foreach ($savedProperties as $propertyId): 
                                if ($count++ >= $displayLimit) break;
                                $property = $propertyModel->getPropertyById($propertyId);
                                if (!$property) continue;
                            ?>
                                <li class="saved-property-item">
                                    <div class="property-info">
                                        <h4><?php echo htmlspecialchars($property['property_name']); ?></h4>
                                        <p><i class="fas fa-map-marker-alt"></i> <?php echo $propertyModel->formatAddress($property); ?></p>
                                        <span class="property-price">$<?php echo number_format($property['monthly_rent'], 2); ?>/month</span>
                                    </div>
                                    <a href="property.php?id=<?php echo $property['id']; ?>" class="view-link">View <i class="fas fa-arrow-right"></i></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php if (count($savedProperties) > $displayLimit): ?>
                            <p style="text-align: center; margin-top: 15px;">
                                <a href="saved-properties.php" style="color: #0077b6;">View all <?php echo count($savedProperties); ?> saved properties</a>
                            </p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <?php if (!$user['is_verified']): ?>
                <div class="profile-section" style="margin-top: 30px; background: #fff3cd;">
                    <h2 class="section-title"><i class="fas fa-envelope"></i> Verify Your Email</h2>
                    <p style="color: #856404; margin-bottom: 15px;">
                        Your email address is not verified. Please check your inbox for the verification link.
                    </p>
                    <a href="resend-verification.php" class="btn" style="background: #856404; color: white;">Resend Verification Email</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Image preview
        document.getElementById('profile_image')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size
                if (file.size > 2 * 1024 * 1024) {
                    alert('File too large. Maximum size is 2MB.');
                    this.value = '';
                    return;
                }
                
                // Validate file type
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    alert('Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.');
                    this.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('image-preview');
                    if (preview) {
                        preview.src = e.target.result;
                    } else {
                        const currentImage = document.querySelector('.current-image');
                        currentImage.innerHTML = `<img src="${e.target.result}" alt="Preview" id="image-preview">`;
                    }
                }
                reader.readAsDataURL(file);
            }
        });

        // Remove image function
        function removeImage() {
            if (confirm('Are you sure you want to remove your profile image?')) {
                document.getElementById('remove-image-form').submit();
            }
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

        // Password validation
        function validatePassword() {
            const newPass = document.getElementById('new_password').value;
            const confirmPass = document.getElementById('confirm_password').value;
            
            if (newPass.length < 6) {
                alert('Password must be at least 6 characters long');
                return false;
            }
            
            if (!/[A-Z]/.test(newPass)) {
                alert('Password must contain at least one uppercase letter');
                return false;
            }
            
            if (!/[a-z]/.test(newPass)) {
                alert('Password must contain at least one lowercase letter');
                return false;
            }
            
            if (!/[0-9]/.test(newPass)) {
                alert('Password must contain at least one number');
                return false;
            }
            
            if (newPass !== confirmPass) {
                alert('Passwords do not match');
                return false;
            }
            
            return true;
        }

        // Real-time password validation
        document.getElementById('new_password')?.addEventListener('input', function() {
            const password = this.value;
            
            document.getElementById('req-length')?.classList.toggle('valid', password.length >= 6);
            document.getElementById('req-uppercase')?.classList.toggle('valid', /[A-Z]/.test(password));
            document.getElementById('req-lowercase')?.classList.toggle('valid', /[a-z]/.test(password));
            document.getElementById('req-number')?.classList.toggle('valid', /[0-9]/.test(password));
            
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
            
            if (confirmPass.length > 0 && indicator) {
                if (newPass === confirmPass) {
                    indicator.innerHTML = '<i class="fas fa-check-circle"></i> Passwords match';
                    indicator.className = 'password-match-indicator match';
                } else {
                    indicator.innerHTML = '<i class="fas fa-times-circle"></i> Passwords do not match';
                    indicator.className = 'password-match-indicator no-match';
                }
            } else if (indicator) {
                indicator.innerHTML = '';
            }
        }

        // Phone number formatting
        document.getElementById('phone_number')?.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9+\-\s]/g, '');
        });
    </script>
</body>
</html>