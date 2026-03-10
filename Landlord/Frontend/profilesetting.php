<?php
// Landlord/Frontend/profile.php
session_start();

// Check if landlord is logged in
if (!isset($_SESSION['landlord_id'])) {
    $_SESSION['redirect_after_login'] = 'profile.php';
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/models/LandlordUserModel.php';
require_once __DIR__ . '/models/LandlordPropertyModel.php';

$userModel = new LandlordUserModel();
$propertyModel = new LandlordPropertyModel();

$landlord_id = $_SESSION['landlord_id'];
$landlord = $userModel->getLandlordById($landlord_id);

// If landlord not found in database, logout
if (!$landlord) {
    header('Location: logout.php');
    exit;
}

// Get property statistics
$stats = $propertyModel->getPropertyStats($landlord_id);
$recentProperties = $propertyModel->getLandlordProperties($landlord_id);
$monthlyRevenue = $propertyModel->getMonthlyRevenue($landlord_id);

// Format landlord name
$fullName = $userModel->getFullName($landlord);
$firstName = explode(' ', $fullName)[0];

// Handle profile update
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'update_profile') {
            $data = [
                'first_name' => trim($_POST['first_name'] ?? ''),
                'last_name' => trim($_POST['last_name'] ?? ''),
                'phone_number' => trim($_POST['phone_number'] ?? '')
            ];
            
            $result = $userModel->updateProfile($landlord_id, $data);
            if ($result['success']) {
                $message = $result['message'];
                // Refresh landlord data
                $landlord = $userModel->getLandlordById($landlord_id);
                $fullName = $userModel->getFullName($landlord);
            } else {
                $error = $result['message'];
            }
        } elseif ($_POST['action'] === 'change_password') {
            $current = $_POST['current_password'] ?? '';
            $new = $_POST['new_password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            
            if (empty($current) || empty($new) || empty($confirm)) {
                $error = 'All password fields are required';
            } elseif ($new !== $confirm) {
                $error = 'New passwords do not match';
            } elseif (strlen($new) < 6) {
                $error = 'Password must be at least 6 characters';
            } else {
                $result = $userModel->changePassword($landlord_id, $current, $new);
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
    <link rel="icon" href="assets/icons/smartlogo.png">
    <title>Landlord Profile - SmartHunt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        :root {
            --primary: #FF385C;
            --primary-light: #FF667D;
            --secondary: #4285F4;
            --dark: #222222;
            --light: #FFFFFF;
            --gray: #DDDDDD;
            --light-gray: #F7F7F7;
            --text: #484848;
            --success: #00A699;
            --warning: #FFB400;
            --danger: #FF5A5F;
        }

        body {
            color: var(--text);
            background-color: #f5f7f9;
            line-height: 1.5;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .dashboard-container {
            display: flex;
            flex: 1;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 260px;
            background: var(--light);
            height: 100vh;
            position: fixed;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px 0;
            overflow-y: auto;
        }

        .logo-container {
            padding: 0 25px 20px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid var(--gray);
            margin-bottom: 20px;
        }

        .logo-container img {
            height: 80px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .logo-container h2 {
            font-size: 20px;
            color: var(--primary);
        }

        .sidebar-menu {
            list-style: none;
            padding: 0 15px;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            text-decoration: none;
            color: var(--text);
            border-radius: 8px;
            transition: all 0.3s;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: var(--light-gray);
            color: var(--primary);
        }

        .sidebar-menu i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }

        .dropdown {
            position: relative;
        }

        .dropdown-content {
            display: none;
            position: static;
            background-color: var(--light-gray);
            border-radius: 8px;
            padding: 5px 0;
            margin: 5px 0 5px 30px;
            min-width: 200px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown-content a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 14px;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 20px;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 25px;
            background-color: var(--light);
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }

        .navbar-brand {
            font-size: 22px;
            font-weight: 700;
            color: var(--primary);
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            position: relative;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 18px;
        }

        .user-details {
            text-align: right;
        }

        .user-name {
            font-weight: 600;
            color: var(--dark);
        }

        .user-role {
            font-size: 12px;
            color: var(--text);
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            min-width: 200px;
            display: none;
            z-index: 1000;
        }

        .user-info:hover .dropdown-menu {
            display: block;
        }

        .dropdown-menu a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 15px;
            text-decoration: none;
            color: var(--text);
            transition: background 0.3s;
        }

        .dropdown-menu a:hover {
            background: var(--light-gray);
        }

        .dropdown-menu hr {
            margin: 5px 0;
            border: none;
            border-top: 1px solid var(--gray);
        }

        .logout-btn {
            color: var(--danger) !important;
        }

        /* Profile Content */
        .profile-container {
            background: var(--light);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .profile-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 40px 30px;
            position: relative;
        }

        .profile-cover {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .profile-avatar-large {
            width: 120px;
            height: 120px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: 600;
            color: var(--primary);
            border: 4px solid white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .profile-title h1 {
            font-size: 32px;
            margin-bottom: 5px;
        }

        .profile-title p {
            opacity: 0.9;
            font-size: 16px;
        }

        .profile-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            margin-top: 10px;
        }

        .profile-content {
            padding: 30px;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .profile-section {
            background: var(--light-gray);
            border-radius: 12px;
            padding: 25px;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            color: var(--dark);
            font-size: 18px;
        }

        .section-title i {
            color: var(--primary);
            font-size: 20px;
        }

        .info-grid {
            display: grid;
            gap: 15px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 10px;
            background: white;
            border-radius: 8px;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            background: var(--light-gray);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-size: 12px;
            color: var(--text);
            margin-bottom: 2px;
        }

        .info-value {
            font-weight: 600;
            color: var(--dark);
        }

        .verification-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .verified {
            background: #d4edda;
            color: #155724;
        }

        .unverified {
            background: #f8d7da;
            color: #721c24;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 30px 0;
        }

        .stat-card {
            background: var(--light);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .stat-card i {
            font-size: 24px;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .stat-card .number {
            font-size: 24px;
            font-weight: 600;
            color: var(--dark);
        }

        .stat-card .label {
            font-size: 13px;
            color: var(--text);
        }

        /* Forms */
        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--gray);
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255, 56, 92, 0.1);
        }

        input[readonly] {
            background: var(--light-gray);
            cursor: not-allowed;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-light);
        }

        .btn-secondary {
            background: var(--light-gray);
            color: var(--text);
        }

        .btn-secondary:hover {
            background: var(--gray);
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
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

        .password-requirements {
            font-size: 12px;
            color: var(--text);
            margin-top: 5px;
        }

        .recent-properties {
            margin-top: 30px;
        }

        .property-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid var(--light-gray);
        }

        .property-item:last-child {
            border-bottom: none;
        }

        .property-info h4 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .property-info p {
            font-size: 13px;
            color: var(--text);
        }

        .property-status {
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-available {
            background: #d4edda;
            color: #155724;
        }

        .status-occupied {
            background: #fff3cd;
            color: #856404;
        }

        .status-maintenance {
            background: #f8d7da;
            color: #721c24;
        }

        .footer {
            background-color: var(--dark);
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: auto;
        }

        .footer img {
            height: 40px;
            margin-bottom: 10px;
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 220px;
            }
            .main-content {
                margin-left: 220px;
            }
            .profile-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .dashboard-container {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .main-content {
                margin-left: 0;
            }
            .profile-cover {
                flex-direction: column;
                text-align: center;
            }
            .stats-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo-container">
                <img src="assets/icons/smartlogo.png" alt="SmartHunt Logo">
                <h2>SmartHunt</h2>
            </div>

            <ul class="sidebar-menu">
                <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li> 
                <li class="dropdown">
                    <a href="#"><i class="fas fa-building"></i> Properties</a>
                    <div class="dropdown-content">
                        <a href="addproperty.php"><i class="fas fa-plus"></i> Add Property</a>
                        <a href="propertypropertylistings.php"><i class="fas fa-edit"></i> Listings</a>
                        <a href="location.php"><i class="fas fa-map-marker-alt"></i> Manage Location</a>
                    </div>
                </li>
                <li><a href="view-tenant.php"><i class="fas fa-users"></i> Tenants</a></li>
                <li><a href="inquiries.php"><i class="fas fa-question-circle"></i> Inquiries</a></li>
                <li><a href="payments.php"><i class="fas fa-credit-card"></i> Payments</a></li>
                <li><a href="profile.php" class="active"><i class="fas fa-user-cog"></i> Profile</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <nav class="navbar">
                <div class="navbar-brand">My Profile</div>
                
                <div class="user-menu">
                    <div class="user-info">
                        <div class="user-details">
                            <div class="user-name"><?php echo htmlspecialchars($fullName); ?></div>
                            <div class="user-role">Landlord</div>
                        </div>
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($firstName, 0, 1)); ?>
                        </div>
                        
                        <div class="dropdown-menu">
                            <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
                            <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                            <hr>
                            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Profile Content -->
            <div class="profile-container">
                <div class="profile-header">
                    <div class="profile-cover">
                        <div class="profile-avatar-large">
                            <?php echo strtoupper(substr($firstName, 0, 1)); ?>
                        </div>
                        <div class="profile-title">
                            <h1><?php echo htmlspecialchars($fullName); ?></h1>
                            <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($landlord['email']); ?></p>
                            <span class="profile-badge">
                                <i class="fas fa-building"></i> Landlord since <?php echo date('F Y', strtotime($landlord['created_at'])); ?>
                            </span>
                        </div>
                    </div>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-success" style="margin: 20px;">
                        <i class="fas fa-check-circle"></i>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-error" style="margin: 20px;">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <div class="stats-cards">
                    <div class="stat-card">
                        <i class="fas fa-building"></i>
                        <div class="number"><?php echo $stats['total']; ?></div>
                        <div class="label">Total Properties</div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-users"></i>
                        <div class="number"><?php echo $stats['occupied']; ?></div>
                        <div class="label">Occupied</div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-credit-card"></i>
                        <div class="number">KES <?php echo number_format($monthlyRevenue); ?></div>
                        <div class="label">Monthly Revenue</div>
                    </div>
                </div>

                <div class="profile-content">
                    <div class="profile-grid">
                        <!-- Personal Information -->
                        <div class="profile-section">
                            <h2 class="section-title">
                                <i class="fas fa-user-circle"></i>
                                Personal Information
                            </h2>
                            
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Username</div>
                                        <div class="info-value"><?php echo htmlspecialchars($landlord['username']); ?></div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Email Address</div>
                                        <div class="info-value"><?php echo htmlspecialchars($landlord['email']); ?></div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Phone Number</div>
                                        <div class="info-value"><?php echo htmlspecialchars($landlord['phone_number'] ?? 'Not provided'); ?></div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Account Status</div>
                                        <div class="info-value">
                                            <span class="verification-badge <?php echo $landlord['is_verified'] ? 'verified' : 'unverified'; ?>">
                                                <i class="fas fa-<?php echo $landlord['is_verified'] ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                                                <?php echo $landlord['is_verified'] ? 'Verified' : 'Unverified'; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-calendar"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Member Since</div>
                                        <div class="info-value"><?php echo date('F j, Y', strtotime($landlord['created_at'])); ?></div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Last Updated</div>
                                        <div class="info-value">
                                        <?php 
                                        if (isset($landlord['updated_at']) && !empty($landlord['updated_at'])) {
                                            echo date('F j, Y', strtotime($landlord['updated_at']));
                                        } else {
                                            echo date('F j, Y', strtotime($landlord['created_at'])); // Fallback to created_at
                                        }
                                        ?>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Profile Form -->
                        <div class="profile-section">
                            <h2 class="section-title">
                                <i class="fas fa-edit"></i>
                                Edit Profile
                            </h2>
                            
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="update_profile">
                                
                                <div class="form-group">
                                    <label for="first_name">First Name</label>
                                    <input type="text" id="first_name" name="first_name" 
                                           value="<?php echo htmlspecialchars($landlord['first_name'] ?? ''); ?>"
                                           placeholder="Enter your first name">
                                </div>

                                <div class="form-group">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" id="last_name" name="last_name" 
                                           value="<?php echo htmlspecialchars($landlord['last_name'] ?? ''); ?>"
                                           placeholder="Enter your last name">
                                </div>

                                <div class="form-group">
                                    <label for="phone_number">Phone Number</label>
                                    <input type="tel" id="phone_number" name="phone_number" 
                                           value="<?php echo htmlspecialchars($landlord['phone_number'] ?? ''); ?>"
                                           placeholder="Enter your phone number">
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Profile
                                </button>
                            </form>
                        </div>

                        <!-- Change Password Form -->
                        <div class="profile-section">
                            <h2 class="section-title">
                                <i class="fas fa-lock"></i>
                                Change Password
                            </h2>
                            
                            <form method="POST" action="" onsubmit="return validatePassword()">
                                <input type="hidden" name="action" value="change_password">
                                
                                <div class="form-group">
                                    <label for="current_password">Current Password</label>
                                    <input type="password" id="current_password" name="current_password" required>
                                </div>

                                <div class="form-group">
                                    <label for="new_password">New Password</label>
                                    <input type="password" id="new_password" name="new_password" required>
                                    <div class="password-requirements">
                                        <i class="fas fa-info-circle"></i> Must be at least 6 characters
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="confirm_password">Confirm New Password</label>
                                    <input type="password" id="confirm_password" name="confirm_password" required>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-key"></i> Change Password
                                </button>
                            </form>
                        </div>

                        <!-- Recent Properties -->
                        <div class="profile-section">
                            <h2 class="section-title">
                                <i class="fas fa-building"></i>
                                Recent Properties
                            </h2>
                            
                            <?php if (empty($recentProperties)): ?>
                                <p style="text-align: center; color: var(--text); padding: 20px;">
                                    <i class="fas fa-home" style="font-size: 48px; color: var(--gray); margin-bottom: 10px; display: block;"></i>
                                    No properties yet.
                                    <br>
                                    <a href="addproperty.php" style="color: var(--primary); text-decoration: none;">Add your first property</a>
                                </p>
                            <?php else: ?>
                                <div class="recent-properties">
                                    <?php foreach (array_slice($recentProperties, 0, 5) as $property): ?>
                                        <div class="property-item">
                                            <div class="property-info">
                                                <h4><?php echo htmlspecialchars($property['property_name']); ?></h4>
                                                <p>
                                                    <i class="fas fa-map-marker-alt"></i> 
                                                    <?php echo htmlspecialchars($property['neighborhood'] . ', ' . $property['city']); ?>
                                                </p>
                                            </div>
                                            <div>
                                                <span class="property-status status-<?php echo $property['status']; ?>">
                                                    <?php echo ucfirst($property['status']); ?>
                                                </span>
                                                <strong style="margin-left: 15px; color: var(--primary);">
                                                    KES <?php echo number_format($property['monthly_rent']); ?>
                                                </strong>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <?php if (count($recentProperties) > 5): ?>
                                    <div style="text-align: center; margin-top: 20px;">
                                        <a href="propertypropertylistings.php" class="btn btn-secondary">View All Properties</a>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <footer class="footer">
        <img src="assets/icons/smartlogo.png" alt="SmartHunt Logo">
        <h6>&copy; <?php echo date('Y'); ?> Algorithm-X Softwares. <br>All rights reserved</h6>
    </footer>

    <script>
        // Password validation
        function validatePassword() {
            const newPass = document.getElementById('new_password').value;
            const confirmPass = document.getElementById('confirm_password').value;
            
            if (newPass.length < 6) {
                alert('Password must be at least 6 characters long');
                return false;
            }
            
            if (newPass !== confirmPass) {
                alert('Passwords do not match');
                return false;
            }
            
            return true;
        }

        // Phone number formatting
        document.getElementById('phone_number')?.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9+\-\s]/g, '');
        });

        // Show/hide password toggle (optional)
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
        }
    </script>
</body>
</html>