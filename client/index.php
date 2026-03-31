<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Include models
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/PropertyModel.php';
require_once __DIR__ . '/models/NotificationModel.php';
require_once __DIR__ . '/models/SavedPropertyModel.php';

// Initialize models
$propertyModel = new PropertyModel();        
$notificationModel = new NotificationModel(); 
$savedPropertyModel = new SavedPropertyModel();

// Get current user ID
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$isLoggedIn = $user_id > 0;

// Handle search and filters
$filters = [];
$search_location = isset($_GET['location']) ? trim($_GET['location']) : '';
$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 0;
$min_beds = isset($_GET['min_beds']) ? (int)$_GET['min_beds'] : 0;
$property_type = isset($_GET['property_type']) ? trim($_GET['property_type']) : '';

if ($search_location) {
    $filters['location'] = $search_location;
}
if ($min_price > 0) {
    $filters['min_price'] = $min_price;
}
if ($max_price > 0) {
    $filters['max_price'] = $max_price;
}
if ($min_beds > 0) {
    $filters['min_beds'] = $min_beds;
}
if ($property_type) {
    $filters['property_type'] = $property_type;
}

// Get properties from database with images
$properties = $propertyModel->getAllPropertiesWithImages($filters); 
$propertyTypes = $propertyModel->getPropertyTypes();

// Get user's saved properties
$savedPropertyIds = $savedPropertyModel->getSavedPropertyIds($user_id);

// Get user notifications
$notifications = $notificationModel->getUserNotifications($user_id);
$unreadCount = $notificationModel->getUnreadCount($user_id);

// Handle AJAX requests
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'save_property') {
        $property_id = (int)$_POST['property_id'];
        $result = $savedPropertyModel->saveProperty($user_id, $property_id);
        $isSaved = $savedPropertyModel->isSaved($user_id, $property_id);
        
        echo json_encode([
            'success' => $result,
            'saved' => $isSaved
        ]);
        exit;
    }
    
    if ($_POST['action'] === 'newsletter_subscribe') {
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        if ($email) {
            echo json_encode(['success' => true, 'message' => 'Successfully subscribed to newsletter!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        }
        exit;
    }
}

// Helper function for time ago
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) return 'Just now';
    elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 2592000) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y', $time);
    }
}

// Get saved count for AJAX
if (isset($_GET['get_saved_count'])) {
    header('Content-Type: application/json');
    echo json_encode(['count' => count($savedPropertyIds)]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartHunt - Find Your Perfect Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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

        .nav-button {
            background-color: #0077b6;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
        }

        /* Notification Panel */
        .notification-panel {
            position: fixed;
            top: 70px;
            right: 20px;
            width: 400px;
            max-width: calc(100vw - 40px);
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            z-index: 1500;
            display: none;
            max-height: 80vh;
            overflow-y: auto;
        }

        .notification-panel.active {
            display: block;
        }

        .notification-header {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            background-color: white;
        }

        .mark-all-read {
            background: none;
            border: none;
            color: #0077b6;
            font-size: 12px;
            cursor: pointer;
        }

        .notification-list {
            padding: 10px 0;
        }

        .notification-item {
            padding: 15px 20px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background-color 0.2s;
            position: relative;
        }

        .notification-item.unread {
            background-color: #e6f2ff;
        }

        .notification-item.unread::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background-color: #0077b6;
        }

        .notification-type {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .notification-type.rent_request { background-color: #ffeaa7; color: #d63031; }
        .notification-type.payment_received { background-color: #a8e6cf; color: #27ae60; }

        .notification-message {
            font-size: 14px;
            margin-bottom: 8px;
            white-space: pre-line;
        }

        .notification-time {
            font-size: 11px;
            color: #999;
        }

        .reply-btn, .view-property-btn {
            background: none;
            border: 1px solid #0077b6;
            color: #0077b6;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 11px;
            cursor: pointer;
        }

        .reply-form {
            margin-top: 10px;
            display: none;
        }

        .reply-form.active {
            display: block;
        }

        .reply-form textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 12px;
            resize: vertical;
            min-height: 60px;
        }

        .reply-form-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            margin-top: 8px;
        }

        /* Search Container */
        .search-container {
            background-color: white;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .search-box {
            max-width: 1000px;
            margin: 0 auto;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .search-input {
            flex: 1;
            position: relative;
        }

        .search-input i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .search-input input {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .search-filters {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-filters select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            background-color: white;
            cursor: pointer;
            min-width: 120px;
        }

        .search-button {
            background-color: #0077b6;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .clear-button {
            background-color: #666;
        }

        /* Properties Grid */
        .page-header {
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .results-count {
            color: #666;
            font-size: 14px;
        }

        .properties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
        }

        .property-card {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .property-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }

        .property-card.featured {
            border: 2px solid #0077b6;
        }

        .featured-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background-color: #0077b6;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            z-index: 1;
        }

        .property-image {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .property-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .image-gallery-indicator {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 11px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .save-btn {
            position: absolute;
            top: 12px;
            right: 12px;
            background-color: white;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #7f8c8d;
            font-size: 18px;
            transition: all 0.2s;
            z-index: 2;
        }

        .save-btn:hover {
            color: #e74c3c;
            transform: scale(1.1);
        }

        .save-btn.saved i {
            color: #e74c3c;
        }

        .property-details {
            padding: 16px;
            flex: 1;
        }

        .property-price {
            margin-bottom: 8px;
        }

        .price {
            font-size: 24px;
            font-weight: 700;
            color: #0077b6;
        }

        .period {
            font-size: 12px;
            color: #666;
        }

        .property-title {
            margin-bottom: 8px;
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .property-address {
            font-size: 12px;
            color: #666;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .property-features {
            display: flex;
            gap: 16px;
            margin-bottom: 12px;
            font-size: 12px;
            color: #666;
            flex-wrap: wrap;
        }

        .property-type {
            display: inline-block;
            background-color: #e6f2ff;
            color: #0077b6;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: capitalize;
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

        .footer-section ul li a {
            color: #bdc3c7;
            text-decoration: none;
            font-size: 12px;
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

        .newsletter-form {
            display: flex;
            gap: 8px;
            margin-top: 12px;
        }

        .newsletter-form input {
            flex: 1;
            padding: 8px;
            border: none;
            border-radius: 4px;
            font-size: 12px;
        }

        .newsletter-form button {
            background-color: #0077b6;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
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
        }

        .mobile-nav-link.active {
            color: #0077b6;
            font-weight: 600;
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

        .no-results {
            text-align: center;
            padding: 60px 20px;
            background-color: white;
            border-radius: 8px;
            grid-column: 1 / -1;
        }

        .loading-spinner {
            text-align: center;
            padding: 40px;
            grid-column: 1 / -1;
        }

        .loading-spinner i {
            font-size: 40px;
            color: #0077b6;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .nav-menu {
                display: none;
            }
            .mobile-menu-btn {
                display: block;
            }
            .search-box {
                flex-direction: column;
            }
            .search-input, .search-filters {
                width: 100%;
            }
            .properties-grid {
                grid-template-columns: 1fr;
            }
            .notification-panel {
                top: 60px;
                right: 10px;
                left: 10px;
                width: auto;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <a href="index.php">
                    <i class="fas fa-home"></i>
                    <span>SmartHunt</span>
                </a>
            </div>
            <div class="nav-menu">
                <a href="index.php" class="nav-link active">Browse</a>
                <?php if ($isLoggedIn): ?>
                    <a href="profile.php" class="nav-link">My Profile</a>
                    <a href="logout.php" class="nav-link" onclick="return confirm('Logout?')">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="nav-link">Login</a>
                    <a href="register.php" class="nav-button">Sign Up</a>
                <?php endif; ?>
            </div>
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- Notification Panel -->
    <div class="notification-panel" id="notificationPanel">
        <div class="notification-header">
            <h3><i class="fas fa-bell"></i> Notifications</h3>
            <button class="mark-all-read" onclick="markAllRead()">Mark all as read</button>
        </div>
        <div class="notification-list" id="notificationList">
            <?php if (empty($notifications)): ?>
                <div class="no-notifications"><i class="fas fa-bell-slash"></i><p>No notifications yet</p></div>
            <?php else: ?>
                <?php foreach ($notifications as $notification): ?>
                <div class="notification-item <?php echo $notification['is_read'] ? '' : 'unread'; ?>" data-id="<?php echo $notification['id']; ?>">
                    <div class="notification-type <?php echo $notification['type']; ?>"><?php echo ucwords(str_replace('_', ' ', $notification['type'])); ?></div>
                    <div class="notification-message"><?php echo nl2br(htmlspecialchars($notification['message'])); ?></div>
                    <div class="notification-time"><i class="far fa-clock"></i> <?php echo timeAgo($notification['created_at']); ?></div>
                    <div class="notification-actions">
                        <button class="reply-btn" onclick="event.stopPropagation(); showReplyForm(<?php echo $notification['id']; ?>)"><i class="fas fa-reply"></i> Reply</button>
                        <?php if (!empty($notification['property_id'])): ?>
                        <button class="view-property-btn" onclick="event.stopPropagation(); viewProperty(<?php echo $notification['property_id']; ?>)"><i class="fas fa-eye"></i> View</button>
                        <?php endif; ?>
                    </div>
                    <div class="reply-form" id="replyForm-<?php echo $notification['id']; ?>">
                        <textarea placeholder="Type your reply..." id="replyText-<?php echo $notification['id']; ?>"></textarea>
                        <div class="reply-form-actions">
                            <button class="reply-cancel" onclick="hideReplyForm(<?php echo $notification['id']; ?>)">Cancel</button>
                            <button class="reply-submit" onclick="submitReply(<?php echo $notification['id']; ?>)">Send</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="search-container">
        <form method="GET" action="index.php" class="search-box" id="searchForm">
            <div class="search-input">
                <i class="fas fa-search"></i>
                <input type="text" name="location" placeholder="Enter city, neighborhood, or ZIP code" value="<?php echo htmlspecialchars($search_location); ?>">
            </div>
            <div class="search-filters">
                <select name="min_price">
                    <option value="">Min Price</option>
                    <?php $priceSteps = [500, 1000, 1500, 2000, 2500, 3000, 4000, 5000];
                    foreach ($priceSteps as $price) {
                        $selected = ($min_price == $price) ? 'selected' : '';
                        echo "<option value=\"$price\" $selected>Ksh $price+</option>";
                    } ?>
                </select>
                <select name="max_price">
                    <option value="">Max Price</option>
                    <?php $priceSteps = [1000, 2000, 3000, 4000, 5000, 7500, 10000, 15000];
                    foreach ($priceSteps as $price) {
                        $selected = ($max_price == $price) ? 'selected' : '';
                        echo "<option value=\"$price\" $selected>Up to Ksh $price</option>";
                    } ?>
                </select>
                <select name="min_beds">
                    <option value="">Any Beds</option>
                    <option value="1" <?php echo $min_beds == 1 ? 'selected' : ''; ?>>1+ Bed</option>
                    <option value="2" <?php echo $min_beds == 2 ? 'selected' : ''; ?>>2+ Beds</option>
                    <option value="3" <?php echo $min_beds == 3 ? 'selected' : ''; ?>>3+ Beds</option>
                </select>
                <select name="property_type">
                    <option value="">Any Type</option>
                    <?php foreach ($propertyTypes as $type): ?>
                        <?php if (!empty($type)): ?>
                        <option value="<?php echo strtolower($type); ?>" <?php echo $property_type == strtolower($type) ? 'selected' : ''; ?>><?php echo ucfirst($type); ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="search-button">Search</button>
                <?php if ($search_location || $min_price || $max_price || $min_beds || $property_type): ?>
                    <button type="button" class="search-button clear-button" onclick="clearFilters()">Clear</button>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Main Content -->
    <main class="container">
        <div class="page-header">
            <h1><?php echo ($search_location || $min_price || $max_price || $min_beds || $property_type) ? 'Search Results' : 'Available Rentals Near You'; ?></h1>
            <p class="results-count">Showing <?php echo count($properties); ?> properties</p>
        </div>

        <div class="properties-grid" id="propertiesGrid">
            <?php if (count($properties) > 0): ?>
                <?php foreach ($properties as $property): ?>
                <div class="property-card" data-id="<?php echo $property['id']; ?>" onclick="viewProperty(<?php echo $property['id']; ?>)">
                    <?php if (isset($property['featured']) && $property['featured']): ?>
                    <div class="featured-badge">FEATURED</div>
                    <?php endif; ?>
                    
                    <div class="property-image">
                        <?php 
                        $primaryImage = 'assets/icons/bed.jpg';
                        $imageCount = 0;
                        if (!empty($property['images']) && count($property['images']) > 0) {
                            $primaryImage = '/Landlord-MGT/Landlord/Frontend/' . $property['images'][0]['image_path'];
                            $imageCount = count($property['images']);
                        }
                        ?>
                        <img src="<?php echo $primaryImage; ?>" alt="<?php echo htmlspecialchars($property['property_name']); ?>" onerror="this.src='assets/icons/bed.jpg'">
                        <?php if ($imageCount > 1): ?>
                        <div class="image-gallery-indicator"><i class="fas fa-images"></i> <?php echo $imageCount; ?> photos</div>
                        <?php endif; ?>
                        <button class="save-btn <?php echo in_array($property['id'], $savedPropertyIds) ? 'saved' : ''; ?>" onclick="event.stopPropagation(); toggleSave(this, <?php echo $property['id']; ?>)">
                            <i class="<?php echo in_array($property['id'], $savedPropertyIds) ? 'fas' : 'far'; ?> fa-heart"></i>
                        </button>
                    </div>
                    
                    <div class="property-details">
                        <div class="property-price">
                            <span class="price">Ksh <?php echo number_format($property['monthly_rent'], 2); ?></span>
                            <span class="period">/month</span>
                        </div>
                        <h3 class="property-title"><?php echo htmlspecialchars($property['property_name']); ?></h3>
                        <p class="property-address"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($propertyModel->formatAddress($property) ?: 'Location available'); ?></p>
                        <div class="property-features">
                            <?php if (isset($property['bedrooms'])): ?><span><i class="fas fa-bed"></i> <?php echo $property['bedrooms']; ?> bed</span><?php endif; ?>
                            <?php if (isset($property['bathrooms'])): ?><span><i class="fas fa-bath"></i> <?php echo $property['bathrooms']; ?> bath</span><?php endif; ?>
                            <?php if (isset($property['sqft'])): ?><span><i class="fas fa-ruler-combined"></i> <?php echo number_format($property['sqft']); ?> sqft</span><?php endif; ?>
                        </div>
                        <div class="property-type"><?php echo ucfirst($property['property_type'] ?? 'Property'); ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h3>No properties found</h3>
                    <p>Try adjusting your search filters or clear them to see all properties.</p>
                    <button class="nav-button" onclick="clearFilters()" style="margin-top: 20px;">Clear All Filters</button>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h4>SmartHunt</h4>
                <p class="small-text">Find your perfect home quickly and easily.</p>
                <div class="social-links">
                    <a href="#" onclick="showToast('Facebook coming soon!')"><i class="fab fa-facebook"></i></a>
                    <a href="#" onclick="showToast('Twitter coming soon!')"><i class="fab fa-twitter"></i></a>
                    <a href="#" onclick="showToast('Instagram coming soon!')"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a onclick="showToast('Browse rentals coming soon!')">Browse Rentals</a></li>
                    <li><a onclick="showToast('How it works coming soon!')">How it Works</a></li>
                    <li><a onclick="showToast('For landlords coming soon!')">For Landlords</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Support</h4>
                <ul>
                    <li><a onclick="showToast('Help center coming soon!')">Help Center</a></li>
                    <li><a onclick="showToast('Contact us coming soon!')">Contact Us</a></li>
                    <li><a onclick="showToast('Privacy policy coming soon!')">Privacy Policy</a></li>
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
            <p>&copy; <?php echo date('Y'); ?> SmartHunt. All rights reserved.</p>
        </div>
    </footer>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobileMenu">
        <div class="mobile-menu-content">
            <button class="close-menu" onclick="toggleMobileMenu()"><i class="fas fa-times"></i></button>
            <button class="mobile-nav-link active" onclick="window.location.href='index.php'">Browse</button>
            <button class="mobile-nav-link" onclick="showSavedProperties()">Saved Properties <span id="mobile-saved-count">(<?php echo count($savedPropertyIds); ?>)</span></button>
            <button class="mobile-nav-link" onclick="toggleNotifications()">Alerts</button>
            <button class="mobile-nav-link" onclick="showToast('Account coming soon!')">My Account</button>
            <button class="mobile-nav-link" onclick="showToast('Help center coming soon!')">Help Center</button>
        </div>
    </div>

    <div id="toast" class="toast-notification"></div>

    <script>
        // Mobile menu
        function toggleMobileMenu() {
            document.getElementById('mobileMenu').classList.toggle('active');
        }

        document.getElementById('mobileMenu').addEventListener('click', function(e) {
            if (e.target === this) this.classList.remove('active');
        });

        // Toast notification
        function showToast(message, isError = false) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.style.backgroundColor = isError ? '#e74c3c' : '#333';
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }

        // View property - FIXED: Opens property.php with the correct ID
        function viewProperty(propertyId) {
            window.location.href = 'property.php?id=' + propertyId;
        }

        // Save property
        function toggleSave(btn, propertyId) {
            event.stopPropagation();
            
            fetch('index.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=save_property&property_id=' + propertyId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const icon = btn.querySelector('i');
                    if (data.saved) {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        btn.classList.add('saved');
                        showToast('Property saved');
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        btn.classList.remove('saved');
                        showToast('Property removed');
                    }
                    updateSavedCount();
                }
            })
            .catch(error => showToast('Error saving', true));
        }

        function updateSavedCount() {
            fetch('index.php?get_saved_count=1')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('saved-count').textContent = '(' + data.count + ')';
                    document.getElementById('mobile-saved-count').textContent = '(' + data.count + ')';
                });
        }

        function showSavedProperties() {
            const savedCount = <?php echo count($savedPropertyIds); ?>;
            if (savedCount === 0) showToast('No saved properties yet');
            else showToast('Viewing saved properties');
        }

        function clearFilters() {
            window.location.href = 'index.php';
        }

        function subscribeNewsletter() {
            const email = document.getElementById('newsletterEmail').value.trim();
            if (!email) { showToast('Enter your email', true); return; }
            if (!isValidEmail(email)) { showToast('Invalid email', true); return; }
            
            fetch('index.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=newsletter_subscribe&email=' + encodeURIComponent(email)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    document.getElementById('newsletterEmail').value = '';
                } else {
                    showToast(data.message, true);
                }
            })
            .catch(error => showToast('Error', true));
        }

        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        // Notification functions
        function toggleNotifications() {
            const panel = document.getElementById('notificationPanel');
            panel.classList.toggle('active');
            if (panel.classList.contains('active')) refreshNotifications();
        }

        function markAsRead(id) {
            fetch('index.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=mark_notification_read&notification_id=' + id
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector(`.notification-item[data-id="${id}"]`).classList.remove('unread');
                    updateNotificationBadge();
                }
            });
        }

        function markAllRead() {
            fetch('index.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=mark_all_read'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelectorAll('.notification-item').forEach(i => i.classList.remove('unread'));
                    updateNotificationBadge();
                    showToast('All marked as read');
                }
            });
        }

        function showReplyForm(id) {
            event.stopPropagation();
            document.querySelectorAll('.reply-form').forEach(f => f.classList.remove('active'));
            document.getElementById(`replyForm-${id}`).classList.add('active');
        }

        function hideReplyForm(id) {
            document.getElementById(`replyForm-${id}`).classList.remove('active');
        }

        function submitReply(id) {
            const replyText = document.getElementById(`replyText-${id}`).value.trim();
            if (!replyText) { showToast('Enter a reply', true); return; }
            
            fetch('index.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=add_reply&notification_id=${id}&message=${encodeURIComponent(replyText)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Reply sent');
                    hideReplyForm(id);
                    refreshNotifications();
                } else {
                    showToast(data.message, true);
                }
            });
        }

        function refreshNotifications() {
            fetch('index.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=get_notifications'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateNotificationList(data.notifications);
                    updateNotificationBadge(data.unread_count);
                }
            });
        }

        function updateNotificationList(notifications) {
            const list = document.getElementById('notificationList');
            if (!list) return;
            if (notifications.length === 0) {
                list.innerHTML = '<div class="no-notifications"><i class="fas fa-bell-slash"></i><p>No notifications yet</p></div>';
                return;
            }
            let html = '';
            notifications.forEach(n => {
                html += `<div class="notification-item ${n.is_read ? '' : 'unread'}" data-id="${n.id}">
                    <div class="notification-type ${n.type}">${n.type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}</div>
                    <div class="notification-message">${escapeHtml(n.message).replace(/\n/g, '<br>')}</div>
                    <div class="notification-time"><i class="far fa-clock"></i> ${n.time_ago || 'Just now'}</div>
                    <div class="notification-actions">
                        <button class="reply-btn" onclick="showReplyForm(${n.id})"><i class="fas fa-reply"></i> Reply</button>
                        ${n.property_id ? `<button class="view-property-btn" onclick="viewProperty(${n.property_id})"><i class="fas fa-eye"></i> View</button>` : ''}
                    </div>
                    <div class="reply-form" id="replyForm-${n.id}">
                        <textarea placeholder="Type your reply..." id="replyText-${n.id}"></textarea>
                        <div class="reply-form-actions">
                            <button class="reply-cancel" onclick="hideReplyForm(${n.id})">Cancel</button>
                            <button class="reply-submit" onclick="submitReply(${n.id})">Send</button>
                        </div>
                    </div>
                </div>`;
            });
            list.innerHTML = html;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function updateNotificationBadge(count = null) {
            const unread = count !== null ? count : document.querySelectorAll('.notification-item.unread').length;
            const badge = document.getElementById('notificationBadge');
            const mobileBadge = document.getElementById('mobileNotificationBadge');
            if (unread > 0) {
                if (badge) { badge.style.display = 'flex'; badge.textContent = unread; }
                if (mobileBadge) { mobileBadge.style.display = 'inline-flex'; mobileBadge.textContent = unread; }
            } else {
                if (badge) badge.style.display = 'none';
                if (mobileBadge) mobileBadge.style.display = 'none';
            }
        }

        // Search form
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            if (this.location.value.trim()) showToast('Searching...');
        });

        document.querySelectorAll('.search-filters select').forEach(select => {
            select.addEventListener('change', () => document.getElementById('searchForm').submit());
        });

        // Image error handling
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('.property-image img');
            images.forEach(img => {
                if (!img.hasAttribute('data-error-handled')) {
                    img.setAttribute('data-error-handled', 'true');
                    img.addEventListener('error', function() { this.src = 'assets/icons/bed.jpg'; });
                }
            });
            
            document.addEventListener('click', function(e) {
                const panel = document.getElementById('notificationPanel');
                const btn = document.getElementById('notificationBtn');
                if (panel && btn && panel.classList.contains('active') && !panel.contains(e.target) && !btn.contains(e.target)) {
                    panel.classList.remove('active');
                }
            });
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === '/') { e.preventDefault(); document.querySelector('.search-input input').focus(); }
            if (e.key === 'Escape') {
                document.getElementById('mobileMenu').classList.remove('active');
                document.getElementById('notificationPanel').classList.remove('active');
            }
            if (e.key === 'n') { e.preventDefault(); toggleNotifications(); }
        });
    </script>
</body>
</html>