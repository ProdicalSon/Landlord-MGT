<?php
// profile.php
session_start();

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

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'update_profile') {
            $data = [
                'first_name' => trim($_POST['first_name']),
                'last_name' => trim($_POST['last_name']),
                'phone_number' => trim($_POST['phone_number'])
            ];
            
            $result = $userModel->updateProfile($user_id, $data);
            if ($result['success']) {
                $_SESSION['full_name'] = $userModel->getFullName(array_merge($user, $data));
                $message = $result['message'];
                $user = $userModel->getUserById($user_id); // Refresh user data
            } else {
                $error = $result['message'];
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
    <title>My Profile - SmartHunt</title>
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
            background: linear-gradient(135deg, #0077b6, #00a8e8);
            border-radius: 50%;
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
            margin-left: 10px;
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

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #0077b6;
            color: white;
        }

        .btn-primary:hover {
            background: #005a8c;
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
                <i class="fas fa-user-circle"></i>
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
                <div class="profile-section">
                    <h2 class="section-title"><i class="fas fa-user-edit"></i> Edit Profile</h2>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="update_profile">
                        
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

                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>

                <div class="profile-section" style="margin-top: 30px;">
                    <h2 class="section-title"><i class="fas fa-lock"></i> Change Password</h2>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password" required>
                        </div>

                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password" required>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>

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
</body>
</html>