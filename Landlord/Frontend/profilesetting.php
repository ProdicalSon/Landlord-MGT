<?php
// Add error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log that the page loaded
error_log("=== Profile Settings Page Loaded ===");
error_log("Session landlord_id: " . ($_SESSION['landlord_id'] ?? 'not set'));
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST data: " . print_r($_POST, true));
    error_log("FILES data: " . print_r($_FILES, true));
}
// Landlord/Frontend/profilesettings.php
session_start();

// Check if landlord is logged in
if (!isset($_SESSION['landlord_id'])) {
    $_SESSION['redirect_after_login'] = 'profilesettings.php';
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

// Helper function to get profile image URL
function getProfileImageUrl($imagePath) {
    if (empty($imagePath)) {
        return null;
    }
    return '/Landlord-MGT/Landlord/Frontend/' . $imagePath;
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
                'phone_number' => trim($_POST['phone_number'] ?? '')
            ];
            
            // Check if image was uploaded
            $imageFile = isset($_FILES['profile_image']) ? $_FILES['profile_image'] : null;
            
            $result = $userModel->updateProfileWithImage($landlord_id, $data, $imageFile);
            if ($result['success']) {
                $message = $result['message'];
                // Refresh landlord data
                $landlord = $userModel->getLandlordById($landlord_id);
                $fullName = $userModel->getFullName($landlord);
                $firstName = explode(' ', $fullName)[0];
            } else {
                $error = $result['message'];
            }
        }
        
        // Remove profile image
        if ($_POST['action'] === 'remove_image') {
            if ($userModel->removeProfileImage($landlord_id)) {
                $message = 'Profile image removed successfully';
                $landlord = $userModel->getLandlordById($landlord_id);
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
            overflow: hidden;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
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
            background: white;
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

        .required-star {
            color: var(--danger);
            margin-left: 3px;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--gray);
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        input:focus, select:focus, textarea:focus {
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
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
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

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: #e04a50;
        }

        .btn-outline-primary {
            background: white;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
        }

        .btn-outline-danger {
            background: white;
            color: var(--danger);
            border: 2px solid var(--danger);
        }

        .btn-outline-danger:hover {
            background: var(--danger);
            color: white;
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

        .alert-close {
            margin-left: auto;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: inherit;
            opacity: 0.5;
        }

        .alert-close:hover {
            opacity: 1;
        }

        /* Profile Image Upload Styles */
        .profile-image-upload {
            display: flex;
            align-items: center;
            gap: 25px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .current-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid var(--primary);
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
            background: var(--light-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            color: var(--gray);
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

        .form-hint {
            font-size: 12px;
            color: var(--text);
            display: block;
            margin-top: 5px;
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
        }

        .password-toggle:hover {
            color: var(--primary);
        }

        /* Password Requirements */
        .password-requirements {
            margin-top: 10px;
            padding: 10px;
            background: var(--light-gray);
            border-radius: 6px;
            border-left: 3px solid var(--primary);
        }

        .requirements-title {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .requirements-list {
            list-style: none;
            padding: 0;
        }

        .req-item {
            font-size: 11px;
            color: var(--text);
            margin-bottom: 3px;
            display: flex;
            align-items: center;
            gap: 5px;
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
            .profile-image-upload {
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
    <div class="dashboard-container">
        <!-- Sidebar -->
     <aside class="sidebar">
            <div class="logo-container">
                <img src="assets/icons/smartlogo.png" alt="SmartHunt Logo">
                <h2>SmartHunt</h2>
            </div>

            <ul class="sidebar-menu">
                <li><a href="index.php" class="active" data-content="dashboard"><i class="fas fa-home"></i> Dashboard</a></li> 
                <li class="dropdown">
                    <a href="#" data-content="properties"><i class="fas fa-building"></i> Properties</a>
                    <div class="dropdown-content">
                        <a href="propertylistings.php" data-content="edit-listings"><i class="fas fa-edit"></i> Listings</a>
                        <a href="location.php" data-content="manage-location"><i class="fas fa-map-marker-alt"></i> Manage Location</a>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" data-content="tenants"><i class="fas fa-users"></i> Tenants <span class="notification-badge"><?php echo $stats['occupied']; ?></span></a> 
                    <div class="dropdown-content">
                        <a href="view-tenant.php" data-content="view-tenants"><i class="fas fa-list"></i> View Tenants</a>
                        <a href="view-tenant.php" data-content="tenant-bookings"><i class="fas fa-calendar-check"></i> Tenant Bookings</a>
                    </div>
                </li>
                <li class="dropdown">
                        <a href="#" data-content="inquiries"><i class="fas fa-question-circle"></i> Inquiries</a>                    <div class="dropdown-content">
                        <a href="landlordinquiries.php" data-content="inquiries-list"><i class="fas fa-inbox"></i> Inquiries</a>
                        <a href="#" data-content="chat"><i class="fas fa-comments"></i> Chat</a>
                    </div>
                </li>
                <li><a href="payments.php" data-content="payments"><i class="fas fa-credit-card"></i> Payments </span></a></li>
                <li><a href="location.php" data-content="location"><i class="fas fa-map-marked-alt"></i> Location</a></li>
                <li><a href="announcements.php" data-content="announcements"><i class="fas fa-bullhorn"></i> Announcements</a></li>
                <li><a href="landlordreports.php" data-content="reports"><i class="fas fa-chart-bar"></i> Reports</a></li>
                <li><a href="profilesetting.php" data-content="profile-settings"><i class="fas fa-user-cog"></i> Profile Settings</a></li>
                <li><a href="notifications.php" data-content="notifications"><i class="fas fa-bell"></i> Notifications </a></li>
                <li><a href="support.php" data-content="support"><i class="fas fa-headset"></i> Support</a></li>
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
                            <?php if (!empty($landlord['profile_image'])): ?>
                                <img src="<?php echo getProfileImageUrl($landlord['profile_image']); ?>" alt="Profile">
                            <?php else: ?>
                                <?php echo strtoupper(substr($firstName, 0, 1)); ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="dropdown-menu">
                            <a href="profilesetting.php"><i class="fas fa-user"></i> My Profile</a>
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
                            <?php if (!empty($landlord['profile_image'])): ?>
                                <img src="<?php echo getProfileImageUrl($landlord['profile_image']); ?>" alt="Profile Image">
                            <?php else: ?>
                                <div class="avatar-initials">
                                    <?php echo strtoupper(substr($firstName, 0, 1)); ?>
                                </div>
                            <?php endif; ?>
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
                        <span><?php echo htmlspecialchars($message); ?></span>
                        <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">&times;</button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-error" style="margin: 20px;">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo htmlspecialchars($error); ?></span>
                        <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">&times;</button>
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
                                            echo date('F j, Y', strtotime($landlord['created_at']));
                                        }
                                        ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Profile Form with Image Upload -->
                        <!-- Edit Profile Form with Image Upload -->
<div class="profile-section">
    <h2 class="section-title">
        <i class="fas fa-edit"></i>
        Edit Profile
    </h2>
    
    <!-- IMPORTANT: Make sure enctype="multipart/form-data" is set for file uploads -->
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
        <input type="hidden" name="action" value="update_profile">
        
        <!-- Profile Image Upload Section -->
        <div class="profile-image-upload">
            <div class="current-image">
                <?php if (!empty($landlord['profile_image'])): ?>
                    <img src="<?php echo getProfileImageUrl($landlord['profile_image']); ?>" alt="Profile" id="image-preview">
                <?php else: ?>
                    <div class="no-image">
                        <i class="fas fa-user-circle"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="upload-controls">
                <div class="upload-buttons">
                    <label for="profile_image" class="btn btn-primary" style="cursor: pointer;">
                        <i class="fas fa-upload"></i> Choose Image
                    </label>
                    <input type="file" id="profile_image" name="profile_image" 
                           accept="image/jpeg,image/png,image/gif,image/webp">
                    
                    <?php if (!empty($landlord['profile_image'])): ?>
                        <button type="button" class="btn btn-danger" onclick="removeImage()">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    <?php endif; ?>
                </div>
                <small class="form-hint">
                    <i class="fas fa-info-circle"></i> Max size: 2MB. Allowed: JPG, PNG, GIF, WEBP
                </small>
            </div>
        </div>
        
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

        <button type="submit" name="submit_profile" class="btn btn-primary">
            <i class="fas fa-save"></i> Update Profile
        </button>
    </form>
    
    <!-- Hidden form for removing image -->
    <form method="POST" id="remove-image-form" style="display: none;">
        <input type="hidden" name="action" value="remove_image">
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
                                    <label for="current_password">Current Password <span class="required-star">*</span></label>
                                    <div class="password-input-wrapper">
                                        <input type="password" id="current_password" name="current_password" required>
                                        <button type="button" class="password-toggle" onclick="togglePassword('current_password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="new_password">New Password <span class="required-star">*</span></label>
                                    <div class="password-input-wrapper">
                                        <input type="password" id="new_password" name="new_password" required>
                                        <button type="button" class="password-toggle" onclick="togglePassword('new_password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="password-requirements">
                                        <p class="requirements-title">Password must:</p>
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
                                    <label for="confirm_password">Confirm New Password <span class="required-star">*</span></label>
                                    <div class="password-input-wrapper">
                                        <input type="password" id="confirm_password" name="confirm_password" required>
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
                                        <a href="propertylistings.php" class="btn btn-secondary">View All Properties</a>
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

        // Phone number formatting
        document.getElementById('phone_number')?.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9+\-\s]/g, '');
        });

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

        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>