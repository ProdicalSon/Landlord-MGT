<?php
// property.php
session_start();

// Include database and models
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/PropertyModel.php';
require_once __DIR__ . '/models/SavedPropertyModel.php';
require_once __DIR__ . '/models/NotificationModel.php';
require_once __DIR__ . '/models/UserModel.php';

// Initialize models
$propertyModel = new PropertyModel();
$savedPropertyModel = new SavedPropertyModel();
$notificationModel = new NotificationModel();
$userModel = new UserModel();

// Get property ID from URL
$property_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get current user ID from session (if logged in)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$isLoggedIn = $user_id > 0;
$user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : '';

// Fetch property from database - USING THE WORKING METHOD
$property = $propertyModel->getPropertyById($property_id);

// If property not found, redirect to index
if (!$property) {
    header('Location: index.php');
    exit;
}

// Check if property is saved by user
$isSaved = $isLoggedIn ? $savedPropertyModel->isSaved($user_id, $property_id) : false;

// Get user's saved count for navigation
$savedCount = $isLoggedIn ? $savedPropertyModel->countSaved($user_id) : 0;

// Get unread notifications count
$unreadCount = $isLoggedIn ? $notificationModel->getUnreadCount($user_id) : 0;

// Handle rent request submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'send_rent_request') {
        // Check if user is logged in
        if (!$isLoggedIn) {
            echo json_encode(['success' => false, 'message' => 'Please login to send a rent request']);
            exit;
        }
        
        $name = filter_var($_POST['name'] ?? '', FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $phone = filter_var($_POST['phone'] ?? '', FILTER_SANITIZE_STRING);
        $move_date = $_POST['moveDate'] ?? '';
        $message = filter_var($_POST['message'] ?? '', FILTER_SANITIZE_STRING);
        
        if (!$email) {
            echo json_encode(['success' => false, 'message' => 'Invalid email address']);
            exit;
        }
        
        if (empty($name) || empty($phone) || empty($move_date)) {
            echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
            exit;
        }
        
        // Create notification for user
        $notificationMessage = "Rent request sent for {$property['property_name']}";
        $notificationModel->create($user_id, 'rent_request', $notificationMessage, $property_id);
        
        echo json_encode(['success' => true, 'message' => 'Rent request sent successfully!']);
        exit;
    }
    
    if ($_POST['action'] === 'toggle_save') {
        if (!$isLoggedIn) {
            echo json_encode(['success' => false, 'message' => 'Please login to save properties']);
            exit;
        }
        
        $property_id = intval($_POST['property_id']);
        $result = $savedPropertyModel->saveProperty($user_id, $property_id);
        $isSaved = $savedPropertyModel->isSaved($user_id, $property_id);
        $count = $savedPropertyModel->countSaved($user_id);
        
        echo json_encode([
            'success' => $result,
            'saved' => $isSaved,
            'count' => $count,
            'message' => $isSaved ? 'Property saved to favorites' : 'Property removed from favorites'
        ]);
        exit;
    }
}

// Helper function to format property features
function getPropertyFeatures($property) {
    $features = [];
    
    // Add basic features based on property data
    if (!empty($property['property_type'])) {
        $features[] = ucfirst($property['property_type']);
    }
    if (!empty($property['bedrooms'])) {
        $features[] = $property['bedrooms'] . ' Bedroom' . ($property['bedrooms'] > 1 ? 's' : '');
    }
    if (!empty($property['bathrooms'])) {
        $features[] = $property['bathrooms'] . ' Bathroom' . ($property['bathrooms'] > 1 ? 's' : '');
    }
    if (!empty($property['sqft'])) {
        $features[] = number_format($property['sqft']) . ' sq ft';
    }
    
    // Default features
    $defaultFeatures = ['Utilities included', 'High-speed internet', 'Laundry in building', 'Pet-friendly', 'Security system'];
    
    return array_merge($features, $defaultFeatures);
}

// Format address for display
function formatFullAddress($property) {
    $parts = [];
    if (!empty($property['address'])) $parts[] = $property['address'];
    if (!empty($property['neighborhood'])) $parts[] = $property['neighborhood'];
    if (!empty($property['city'])) $parts[] = $property['city'];
    return implode(', ', $parts);
}

// Get property images
function getPropertyImages($property) {
    $images = [
        'apartment' => [
            'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&h=600&fit=crop'
        ],
        'house' => [
            'https://images.unsplash.com/photo-1580587771525-78b9dba3b058?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1570129477492-45c003edd2be?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=800&h=600&fit=crop'
        ],
        'studio' => [
            'https://images.unsplash.com/photo-1536376072261-38c75010e6c9?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=800&h=600&fit=crop'
        ]
    ];
    
    $type = strtolower($property['property_type'] ?? 'apartment');
    return $images[$type] ?? $images['apartment'];
}

// Get landlord info
function getLandlordInfo($property) {
    return [
        'name' => $property['landlord_id'] ?? 'Property Manager',
        'phone' => '(555) 123-4567',
        'email' => 'landlord@example.com',
        'rating' => 4.8,
        'properties' => 12
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($property['property_name'] ?? 'Property Details'); ?> - SmartHunt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* All your existing CSS styles remain exactly as in your working version */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Navigation */
        .navbar {
            background-color: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-logo a {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            font-size: 22px;
            font-weight: 700;
            color: #0077b6;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #333;
            font-size: 14px;
            padding: 8px 12px;
            border-radius: 4px;
            transition: background-color 0.2s;
            background: none;
            border: none;
            cursor: pointer;
            position: relative;
        }

        .nav-link i {
            margin-right: 6px;
            font-size: 16px;
        }

        .nav-link.active {
            color: #0077b6;
            background-color: #e6f2ff;
        }

        .nav-link:hover {
            background-color: #f5f5f5;
        }

        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background-color: #e74c3c;
            color: white;
            font-size: 10px;
            font-weight: bold;
            min-width: 18px;
            height: 18px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 5px;
        }

        .nav-button {
            background-color: #0077b6;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .nav-button:hover {
            background-color: #005a8c;
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 20px;
            color: #333;
            cursor: pointer;
        }

        /* Property Header */
        .property-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .property-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
            color: #333;
        }

        .save-property-btn {
            background-color: white;
            border: 2px solid #0077b6;
            color: #0077b6;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
        }

        .save-property-btn:hover:not(:disabled) {
            background-color: #0077b6;
            color: white;
        }

        .save-property-btn.saved {
            background-color: #e74c3c;
            border-color: #e74c3c;
            color: white;
        }

        .save-property-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .property-location {
            color: #666;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .property-status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 10px;
        }

        .status-occupied {
            background-color: #ffeaa7;
            color: #d63031;
        }

        .status-available {
            background-color: #a8e6cf;
            color: #27ae60;
        }

        /* Property Content */
        .property-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        /* Gallery */
        .property-gallery {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .main-image {
            height: 400px;
            overflow: hidden;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .main-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .main-image img:hover {
            transform: scale(1.05);
        }

        .image-thumbnails {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .image-thumbnails img {
            width: 100%;
            height: 100px;
            object-fit: cover;
            border-radius: 6px;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.2s;
        }

        .image-thumbnails img:hover {
            opacity: 0.8;
            transform: translateY(-2px);
        }

        /* Property Info Sections */
        .property-info {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .price-section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .price-section h2 {
            font-size: 36px;
            color: #0077b6;
            margin-bottom: 15px;
        }

        .price-section .period {
            font-size: 16px;
            color: #666;
            font-weight: normal;
        }

        .property-meta {
            display: flex;
            gap: 25px;
            flex-wrap: wrap;
        }

        .property-meta span {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
            color: #555;
        }

        .property-meta i {
            color: #0077b6;
            font-size: 18px;
        }

        .description-section,
        .features-section,
        .landlord-section,
        .rent-request-section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .description-section h3,
        .features-section h3,
        .landlord-section h3,
        .rent-request-section h3 {
            margin-bottom: 15px;
            color: #333;
            font-size: 20px;
        }

        .description-section p {
            color: #666;
            line-height: 1.8;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #555;
            padding: 8px;
            background: #f8f9fa;
            border-radius: 6px;
        }

        .feature-item i {
            color: #27ae60;
            font-size: 16px;
        }

        /* Landlord Info */
        .landlord-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .landlord-avatar i {
            font-size: 64px;
            color: #0077b6;
        }

        .landlord-details h4 {
            margin-bottom: 5px;
            font-size: 18px;
            color: #333;
        }

        .landlord-rating {
            margin-top: 8px;
        }

        .stars {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 5px;
        }

        .stars i {
            color: #ddd;
            font-size: 14px;
        }

        .stars i.filled {
            color: #f39c12;
        }

        .stars span {
            margin-left: 5px;
            color: #666;
            font-size: 14px;
        }

        /* Rent Request Form */
        .rent-form {
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #333;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.2s;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #0077b6;
        }

        .form-group input:disabled {
            background: #f5f5f5;
            cursor: not-allowed;
        }

        .rent-request-btn {
            width: 100%;
            background-color: #0077b6;
            color: white;
            border: none;
            padding: 16px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: background-color 0.2s;
        }

        .rent-request-btn:hover:not(:disabled) {
            background-color: #005a8c;
        }

        .rent-request-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .form-note {
            margin-top: 12px;
            text-align: center;
            color: #666;
            font-size: 13px;
        }

        .login-prompt {
            text-align: center;
            padding: 30px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-top: 20px;
        }

        .login-prompt p {
            margin-bottom: 15px;
            color: #666;
        }

        .login-prompt .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #0077b6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 3000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background-color: white;
            padding: 40px;
            border-radius: 12px;
            text-align: center;
            max-width: 400px;
            width: 90%;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-icon {
            font-size: 64px;
            color: #27ae60;
            margin-bottom: 20px;
        }

        .modal-icon.error {
            color: #e74c3c;
        }

        .modal h3 {
            margin-bottom: 15px;
            color: #333;
        }

        .modal p {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .modal-close-btn {
            background-color: #0077b6;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .modal-close-btn:hover {
            background-color: #005a8c;
        }

        /* Toast Notification */
        .toast-notification {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background-color: #333;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 4000;
            opacity: 0;
            transition: transform 0.3s, opacity 0.3s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .toast-notification.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }

        .toast-notification.success {
            background-color: #27ae60;
        }

        .toast-notification.error {
            background-color: #e74c3c;
        }

        /* Footer */
        .footer {
            background-color: #2c3e50;
            color: white;
            margin-top: 60px;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
        }

        .footer-section h4 {
            margin-bottom: 16px;
            font-size: 16px;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 8px;
        }

        .footer-section ul li a {
            color: #bdc3c7;
            text-decoration: none;
            font-size: 12px;
            transition: color 0.2s;
            cursor: pointer;
        }

        .footer-section ul li a:hover {
            color: white;
        }

        .social-links {
            display: flex;
            gap: 12px;
            margin-top: 16px;
        }

        .social-links a {
            color: white;
            background-color: #34495e;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: background-color 0.2s;
            cursor: pointer;
        }

        .social-links a:hover {
            background-color: #0077b6;
        }

        .footer-bottom {
            border-top: 1px solid #34495e;
            padding: 20px;
            text-align: center;
        }

        /* Mobile Menu Overlay */
        .mobile-menu-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.8);
            z-index: 2000;
            display: none;
        }

        .mobile-menu-overlay.active {
            display: block;
        }

        .mobile-menu-content {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 280px;
            background-color: white;
            padding: 40px 20px;
            overflow-y: auto;
        }

        .close-menu {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            font-size: 20px;
            color: #333;
            cursor: pointer;
        }

        .mobile-nav-link {
            display: block;
            padding: 15px 0;
            text-decoration: none;
            color: #333;
            font-size: 16px;
            border-bottom: 1px solid #eee;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            position: relative;
        }

        .mobile-nav-link.active {
            color: #0077b6;
            font-weight: 600;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .property-content {
                grid-template-columns: 1fr;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .nav-menu {
                display: none;
            }
            
            .mobile-menu-btn {
                display: block;
            }
            
            .property-header {
                flex-direction: column;
                padding: 20px;
            }
            
            .property-header h1 {
                font-size: 24px;
            }
            
            .save-property-btn {
                width: 100%;
                justify-content: center;
            }
            
            .main-image {
                height: 300px;
            }
            
            .image-thumbnails {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .property-meta {
                gap: 15px;
            }
            
            .property-meta span {
                font-size: 13px;
            }
            
            .landlord-info {
                flex-direction: column;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 15px;
            }
            
            .property-header {
                padding: 15px;
            }
            
            .price-section h2 {
                font-size: 28px;
            }
            
            .property-meta {
                flex-direction: column;
                gap: 10px;
            }
            
            .main-image {
                height: 250px;
            }
            
            .image-thumbnails img {
                height: 80px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <a href="index.php">
                    <i class="fas fa-home"></i>
                    <span>SmartHunt</span>
                </a>
            </div>
            
            <div class="nav-menu">
                <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                    <i class="fas fa-search"></i> <span>Browse</span>
                </a>
                
                <?php if ($isLoggedIn): ?>
                    <a href="profile.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
                        <i class="fas fa-user"></i> 
                        <span><?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?></span>
                    </a>
                    <a href="logout.php" class="nav-link" onclick="return confirm('Are you sure you want to logout?')">
                        <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>">
                        <i class="fas fa-sign-in-alt"></i> <span>Login</span>
                    </a>
                    <a href="register.php" class="nav-button">Sign Up</a>
                <?php endif; ?>
            </div>
            
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- Property Detail -->
    <main class="container">
        <div class="property-header">
            <div>
                <h1><?php echo htmlspecialchars($property['property_name'] ?? 'Property Details'); ?></h1>
                <p class="property-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <?php echo htmlspecialchars(formatFullAddress($property) ?: 'Location available'); ?>
                </p>
                <?php if (!empty($property['status'])): ?>
                <span class="property-status status-<?php echo strtolower($property['status']); ?>">
                    <?php echo ucfirst($property['status']); ?>
                </span>
                <?php endif; ?>
            </div>
            <button class="save-property-btn <?php echo $isSaved ? 'saved' : ''; ?>" 
                    onclick="toggleSaveProperty(<?php echo $property['id']; ?>)"
                    <?php echo !$isLoggedIn ? 'disabled' : ''; ?>>
                <i class="<?php echo $isSaved ? 'fas' : 'far'; ?> fa-heart"></i>
                <span><?php echo $isSaved ? 'Saved' : 'Save Property'; ?></span>
            </button>
        </div>

        <div class="property-content">
            <div class="property-gallery">
                <div class="main-image">
                    <img src="<?php echo getPropertyImages($property)[0]; ?>" alt="Main property image" id="mainPropertyImage">
                </div>
                <div class="image-thumbnails">
                    <?php foreach (getPropertyImages($property) as $index => $image): ?>
                    <img src="<?php echo $image; ?>" alt="Property image <?php echo $index + 1; ?>" onclick="changeMainImage(this.src)">
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="property-info">
                <div class="price-section">
                    <h2>$<?php echo number_format($property['monthly_rent'] ?? 0, 2); ?> <span class="period">/month</span></h2>
                    <div class="property-meta">
                        <?php if (!empty($property['bedrooms'])): ?>
                        <span><i class="fas fa-bed"></i> <?php echo $property['bedrooms']; ?> bed</span>
                        <?php endif; ?>
                        <?php if (!empty($property['bathrooms'])): ?>
                        <span><i class="fas fa-bath"></i> <?php echo $property['bathrooms']; ?> bath</span>
                        <?php endif; ?>
                        <?php if (!empty($property['sqft'])): ?>
                        <span><i class="fas fa-ruler-combined"></i> <?php echo number_format($property['sqft']); ?> sqft</span>
                        <?php endif; ?>
                        <?php if (!empty($property['property_type'])): ?>
                        <span><i class="fas fa-building"></i> <?php echo ucfirst($property['property_type']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="description-section">
                    <h3>Description</h3>
                    <p><?php echo htmlspecialchars($property['description'] ?? 'No description available.'); ?></p>
                </div>

                <div class="features-section">
                    <h3>Features & Amenities</h3>
                    <div class="features-grid">
                        <?php foreach (getPropertyFeatures($property) as $feature): ?>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span><?php echo htmlspecialchars($feature); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="landlord-section">
                    <h3>Property Owner</h3>
                    <div class="landlord-info">
                        <div class="landlord-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="landlord-details">
                            <h4><?php echo htmlspecialchars(getLandlordInfo($property)['name']); ?></h4>
                            <div class="landlord-rating">
                                <div class="stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= floor(getLandlordInfo($property)['rating']) ? 'filled' : ''; ?>"></i>
                                    <?php endfor; ?>
                                    <span>(<?php echo getLandlordInfo($property)['rating']; ?>)</span>
                                </div>
                                <p class="small-text"><?php echo getLandlordInfo($property)['properties']; ?> properties listed</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rent Request Form -->
                <div class="rent-request-section">
                    <h3>Request to Rent</h3>
                    
                    <?php if ($isLoggedIn): ?>
                        <form id="rentRequestForm" class="rent-form">
                            <input type="hidden" name="property_id" value="<?php echo $property['id']; ?>">
                            <div class="form-group">
                                <label for="name">Full Name *</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Phone Number *</label>
                                <input type="tel" id="phone" name="phone" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="moveDate">Desired Move-in Date *</label>
                                <input type="date" id="moveDate" name="moveDate" required 
                                       min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="message">Message to Landlord</label>
                                <textarea id="message" name="message" rows="4" 
                                          placeholder="Tell the landlord about yourself and why you're interested..."></textarea>
                            </div>
                            
                            <button type="submit" class="rent-request-btn">
                                <i class="fas fa-paper-plane"></i> Send Rent Request
                            </button>
                            <p class="small-text form-note">
                                Your request will be sent directly to the property owner.
                                We'll also send you a copy via email.
                            </p>
                        </form>
                    <?php else: ?>
                        <div class="login-prompt">
                            <p><i class="fas fa-lock"></i> Please login to send a rent request</p>
                            <a href="login.php?redirect=property.php?id=<?php echo $property['id']; ?>" class="btn">
                                Login to Continue
                            </a>
                            <p style="margin-top: 10px; font-size: 13px;">
                                Don't have an account? <a href="register.php">Sign up</a>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h4>SmartHunt</h4>
                <p class="small-text">Find your perfect home quickly and easily.</p>
                <div class="social-links">
                    <a href="#" onclick="showToast('Facebook feature coming soon!')"><i class="fab fa-facebook"></i></a>
                    <a href="#" onclick="showToast('Twitter feature coming soon!')"><i class="fab fa-twitter"></i></a>
                    <a href="#" onclick="showToast('Instagram feature coming soon!')"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.php">Browse Rentals</a></li>
                    <li><a href="#" onclick="showToast('How it works feature coming soon!')">How it Works</a></li>
                    <li><a href="#" onclick="showToast('For landlords feature coming soon!')">For Landlords</a></li>
                    <li><a href="#" onclick="showToast('Safety tips feature coming soon!')">Safety Tips</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Support</h4>
                <ul>
                    <li><a href="#" onclick="showToast('Help center feature coming soon!')">Help Center</a></li>
                    <li><a href="#" onclick="showToast('Contact us feature coming soon!')">Contact Us</a></li>
                    <li><a href="#" onclick="showToast('Privacy policy feature coming soon!')">Privacy Policy</a></li>
                    <li><a href="#" onclick="showToast('Terms of service feature coming soon!')">Terms of Service</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Newsletter</h4>
                <p class="small-text">Get the latest rental listings.</p>
                <div class="newsletter-form">
                    <input type="email" id="newsletterEmail" placeholder="Your email">
                    <button onclick="subscribeNewsletter()">Subscribe</button>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p class="small-text">&copy; <?php echo date('Y'); ?> SmartHunt. All rights reserved.</p>
        </div>
    </footer>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobileMenu">
        <div class="mobile-menu-content">
            <button class="close-menu" onclick="toggleMobileMenu()"><i class="fas fa-times"></i></button>
            <a href="index.php" class="mobile-nav-link">Browse</a>
            <?php if ($isLoggedIn): ?>
                <a href="profile.php" class="mobile-nav-link">My Profile</a>
                <a href="saved-properties.php" class="mobile-nav-link">Saved Properties</a>
                <a href="logout.php" class="mobile-nav-link" onclick="return confirm('Are you sure you want to logout?')">Logout</a>
            <?php else: ?>
                <a href="login.php" class="mobile-nav-link">Login</a>
                <a href="register.php" class="mobile-nav-link">Sign Up</a>
            <?php endif; ?>
            <a href="#" class="mobile-nav-link" onclick="showToast('Help center feature coming soon!')">Help Center</a>
        </div>
    </div>

    <!-- Success/Error Modal -->
    <div id="responseModal" class="modal">
        <div class="modal-content">
            <div class="modal-icon" id="modalIcon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3 id="modalTitle">Success!</h3>
            <p id="modalMessage">Your request has been sent successfully.</p>
            <button class="modal-close-btn" onclick="closeModal()">Continue</button>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast-notification"></div>

    <script>
        // Mobile menu functionality
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('active');
        }

        document.querySelector('.mobile-menu-btn').addEventListener('click', toggleMobileMenu);
        
        document.querySelector('.close-menu').addEventListener('click', function() {
            document.getElementById('mobileMenu').classList.remove('active');
        });
        
        document.getElementById('mobileMenu').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });

        // Toast notification function
        function showToast(message, type = 'info') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = 'toast-notification ' + type;
            toast.classList.add('show');
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        // Image gallery
        function changeMainImage(src) {
            document.getElementById('mainPropertyImage').src = src;
        }

        // Toggle save property
        function toggleSaveProperty(propertyId) {
            <?php if (!$isLoggedIn): ?>
                showToast('Please login to save properties', 'error');
                setTimeout(() => {
                    window.location.href = 'login.php?redirect=property.php?id=' + propertyId;
                }, 1500);
                return;
            <?php endif; ?>

            const btn = document.querySelector('.save-property-btn');
            btn.disabled = true;
            
            fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=toggle_save&property_id=' + propertyId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const icon = btn.querySelector('i');
                    const text = btn.querySelector('span');
                    
                    if (data.saved) {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        btn.classList.add('saved');
                        text.textContent = 'Saved';
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        btn.classList.remove('saved');
                        text.textContent = 'Save Property';
                    }
                    
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || 'Error saving property', 'error');
                }
            })
            .catch(error => {
                showToast('Error saving property', 'error');
            })
            .finally(() => {
                btn.disabled = false;
            });
        }

        // Rent request form submission
        document.getElementById('rentRequestForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'send_rent_request');
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            submitBtn.disabled = true;
            
            fetch(window.location.href, {
                method: 'POST',
                body: new URLSearchParams(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success modal
                    document.getElementById('modalIcon').innerHTML = '<i class="fas fa-check-circle"></i>';
                    document.getElementById('modalTitle').textContent = 'Success!';
                    document.getElementById('modalMessage').textContent = data.message || 'Your rent request has been sent successfully!';
                    document.getElementById('responseModal').classList.add('active');
                    
                    // Reset form
                    this.reset();
                } else {
                    showToast(data.message || 'Error sending request', 'error');
                }
            })
            .catch(error => {
                showToast('Error sending request', 'error');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });

        // Modal functions
        function closeModal() {
            document.getElementById('responseModal').classList.remove('active');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('responseModal');
            if (event.target === modal) {
                modal.classList.remove('active');
            }
        };

        // Newsletter subscription
        function subscribeNewsletter() {
            const email = document.getElementById('newsletterEmail').value.trim();
            
            if (!email) {
                showToast('Please enter your email', 'error');
                return;
            }
            
            if (!isValidEmail(email)) {
                showToast('Please enter a valid email address', 'error');
                return;
            }
            
            // Simulate subscription
            showToast('Successfully subscribed to newsletter!', 'success');
            document.getElementById('newsletterEmail').value = '';
        }

        // Email validation
        function isValidEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        // Set minimum date for move-in date
        document.addEventListener('DOMContentLoaded', function() {
            const moveDate = document.getElementById('moveDate');
            if (moveDate) {
                const today = new Date().toISOString().split('T')[0];
                moveDate.setAttribute('min', today);
            }
            
            // Image error handling
            const images = document.querySelectorAll('.property-image img, .main-image img, .image-thumbnails img');
            const fallbackImage = 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=800&h=600&fit=crop';
            
            images.forEach(img => {
                img.addEventListener('error', function() {
                    this.src = fallbackImage;
                });
            });
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Press 'Escape' to close modal and mobile menu
            if (e.key === 'Escape') {
                document.getElementById('responseModal').classList.remove('active');
                document.getElementById('mobileMenu').classList.remove('active');
            }
            
            // Press 's' to save property (if logged in)
            if (e.key === 's' && !e.ctrlKey && !e.metaKey && <?php echo $isLoggedIn ? 'true' : 'false'; ?>) {
                e.preventDefault();
                toggleSaveProperty(<?php echo $property['id']; ?>);
            }
        });
    </script>
</body>
</html>