<?php
// propertylistings.php
session_start();

// Check if landlord is logged in
if (!isset($_SESSION['landlord_id'])) {
    $_SESSION['redirect_after_login'] = 'propertylistings.php';
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

// Get only this landlord's properties
$properties = $propertyModel->getLandlordProperties($landlord_id);
$stats = $propertyModel->getPropertyStats($landlord_id);

// Format landlord name
$landlordName = $userModel->getFullName($landlord) ?: $landlord['username'];
$firstName = explode(' ', $landlordName)[0];

// Get unread notifications count
$unreadNotifications = 7; // Placeholder
$unreadInquiries = 5; // Placeholder
$pendingPayments = 2; // Placeholder

// Handle AJAX requests for this page
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'delete_property') {
        $property_id = intval($_POST['property_id']);
        
        // Verify ownership
        if (!$propertyModel->verifyOwnership($property_id, $landlord_id)) {
            echo json_encode(['success' => false, 'message' => 'You do not own this property']);
            exit;
        }
        
        $result = $propertyModel->deleteProperty($property_id);
        echo json_encode($result);
        exit;
    }
    
    if ($_POST['action'] === 'add_property') {
        // Get form data
        $title = trim($_POST['title'] ?? '');
        $type = $_POST['type'] ?? '';
        $price = floatval($_POST['price'] ?? 0);
        $status = $_POST['status'] ?? 'available';
        $description = trim($_POST['description'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $neighborhood = trim($_POST['neighborhood'] ?? '');
        $zip = trim($_POST['zip'] ?? '');
        $bedrooms = $_POST['bedrooms'] ?? '1';
        $bathrooms = $_POST['bathrooms'] ?? '1';
        $area = floatval($_POST['area'] ?? 0);
        $year_built = isset($_POST['year_built']) && !empty($_POST['year_built']) ? intval($_POST['year_built']) : null;
        
        // Get amenities
        $amenities = [];
        if (isset($_POST['amenities']) && is_array($_POST['amenities'])) {
            $amenities = $_POST['amenities'];
        }
        
        // Validate required fields
        $errors = [];
        if (empty($title)) $errors[] = 'Property title is required';
        if (empty($type)) $errors[] = 'Property type is required';
        if ($price <= 0) $errors[] = 'Valid monthly rent is required';
        if (empty($description)) $errors[] = 'Property description is required';
        if (empty($address)) $errors[] = 'Street address is required';
        if (empty($city)) $errors[] = 'City is required';
        if (empty($neighborhood)) $errors[] = 'Neighborhood is required';
        if ($area <= 0) $errors[] = 'Valid area is required';
        
        if (!empty($errors)) {
            echo json_encode(['success' => false, 'message' => implode('<br>', $errors)]);
            exit;
        }
        
        // Prepare data for database
        $propertyData = [
            'title' => $title,
            'type' => $type,
            'price' => $price,
            'status' => $status,
            'description' => $description,
            'address' => $address,
            'city' => $city,
            'neighborhood' => $neighborhood,
            'zip' => $zip,
            'bedrooms' => $bedrooms == '5+' ? 5 : intval($bedrooms),
            'bathrooms' => $bathrooms == '5+' ? 5 : floatval($bathrooms),
            'area' => $area,
            'year_built' => $year_built,
            'amenities' => $amenities,
            'featured' => 0
        ];
        
        // Add property to database
        $result = $propertyModel->addProperty($propertyData, $landlord_id);
        echo json_encode($result);
        exit;
    }
}

// Handle GET request to refresh properties
if (isset($_GET['action']) && $_GET['action'] === 'get_properties') {
    header('Content-Type: application/json');
    $properties = $propertyModel->getLandlordProperties($landlord_id);
    $stats = $propertyModel->getPropertyStats($landlord_id);
    
    // Format properties for display
    foreach ($properties as &$property) {
        // Decode amenities if they're stored as JSON
        if (isset($property['amenities'])) {
            $property['amenities'] = json_decode($property['amenities'], true);
        }
    }
    
    echo json_encode([
        'success' => true,
        'properties' => $properties,
        'stats' => $stats
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="/Landlord-MGT/Landlord/Frontend/">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="icon" href="assets/icons/smartlogo.png">
    <title>My Property Listings - SmartHunt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
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

        .login-image {
            display: flex;
            align-items: center;
        }

        .login-image img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
        }

        .page-header {
            margin-bottom: 25px;
        }

        .page-header h1 {
            font-size: 28px;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .page-header p {
            color: var(--text);
        }

        /* Toggle between list and form */
        .section-toggle {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .toggle-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .toggle-btn.active {
            background: var(--primary);
            color: white;
        }

        .toggle-btn:not(.active) {
            background: var(--light-gray);
            color: var(--text);
        }

        .toggle-btn:not(.active):hover {
            background: var(--gray);
        }

        .section {
            display: none;
        }

        .section.active {
            display: block;
        }

        /* Stats Cards */
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--light);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: var(--primary-light);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .stat-info h3 {
            font-size: 14px;
            color: var(--text);
            margin-bottom: 5px;
        }

        .stat-info .number {
            font-size: 24px;
            font-weight: 600;
            color: var(--dark);
        }

        /* Actions Bar */
        .actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .btn-primary:hover {
            background: var(--primary-light);
        }

        .btn-secondary {
            background: var(--light-gray);
            color: var(--text);
            border: 1px solid var(--gray);
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-secondary:hover {
            background: var(--gray);
        }

        .search-box {
            display: flex;
            gap: 10px;
            flex: 1;
            max-width: 400px;
        }

        .search-box input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid var(--gray);
            border-radius: 8px;
            font-size: 14px;
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary);
        }

        /* Properties Grid */
        .properties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }

        .property-card {
            background: var(--light);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            border: 1px solid var(--light-gray);
        }

        .property-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        }

        .property-image {
            height: 200px;
            background: var(--light-gray);
            position: relative;
            overflow: hidden;
        }

        .property-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .property-status-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-available {
            background: rgba(0, 166, 153, 0.1);
            color: var(--success);
        }

        .status-occupied {
            background: rgba(255, 180, 0, 0.1);
            color: var(--warning);
        }

        .status-maintenance {
            background: rgba(255, 90, 95, 0.1);
            color: var(--danger);
        }

        .property-details {
            padding: 20px;
        }

        .property-price {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .property-price span {
            font-size: 14px;
            font-weight: normal;
            color: var(--text);
        }

        .property-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .property-location {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text);
            margin-bottom: 15px;
            font-size: 14px;
        }

        .property-features {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--light-gray);
        }

        .property-feature {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .property-feature i {
            font-size: 16px;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .property-feature span {
            font-size: 12px;
            color: var(--text);
        }

        .property-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-size: 13px;
        }

        .property-date {
            color: var(--text);
        }

        .property-actions {
            display: flex;
            gap: 10px;
        }

        .property-actions button {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .btn-edit {
            background: var(--light-gray);
            color: var(--text);
        }

        .btn-edit:hover {
            background: var(--gray);
        }

        .btn-view {
            background: var(--secondary);
            color: white;
        }

        .btn-view:hover {
            background: #3367d6;
        }

        .btn-delete {
            background: var(--danger);
            color: white;
        }

        .btn-delete:hover {
            background: #e04a50;
        }

        /* Add Property Form Styles */
        .add-property-container {
            background: var(--light);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .form-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .form-header h1 {
            font-size: 24px;
            font-weight: 600;
        }

        .close-form {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-size: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s;
        }

        .close-form:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .form-content {
            padding: 30px;
            max-height: 600px;
            overflow-y: auto;
        }

        .form-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--light-gray);
        }

        .form-section h2 {
            font-size: 18px;
            color: var(--primary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
            font-size: 14px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--gray);
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255, 56, 92, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .amenity-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .amenity-item input[type="checkbox"] {
            width: auto;
            margin-right: 5px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--light-gray);
        }

        .btn-cancel {
            background: var(--light-gray);
            color: var(--text);
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-cancel:hover {
            background: var(--gray);
        }

        .btn-submit {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-submit:hover {
            background: var(--primary-light);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: var(--light);
            border-radius: 12px;
            grid-column: 1 / -1;
        }

        .empty-state i {
            font-size: 60px;
            color: var(--gray);
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .empty-state p {
            color: var(--text);
            margin-bottom: 25px;
        }

        .loading-spinner {
            text-align: center;
            padding: 40px;
            grid-column: 1 / -1;
        }

        .loading-spinner i {
            font-size: 40px;
            color: var(--primary);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 9999;
            animation: slideIn 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            font-weight: 500;
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

        .notification-badge {
            background-color: var(--danger);
            color: white;
            border-radius: 50%;
            padding: 3px 8px;
            font-size: 12px;
            margin-left: 5px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .main-content {
                margin-left: 0;
            }
            .stats-cards {
                grid-template-columns: 1fr;
            }
            .properties-grid {
                grid-template-columns: 1fr;
            }
            .actions-bar {
                flex-direction: column;
                align-items: stretch;
            }
            .search-box {
                max-width: 100%;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
            .form-group.full-width {
                grid-column: span 1;
            }
            .amenities-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .amenities-grid {
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
                <li><a href="index.php" data-content="dashboard"><i class="fas fa-home"></i> Dashboard</a></li> 
                <li class="dropdown">
                    <a href="#" class="active" data-content="properties"><i class="fas fa-building"></i> Properties</a>
                    <div class="dropdown-content">
                        <a href="propertylistings.php" data-content="edit-listings" class="active"><i class="fas fa-edit"></i> My Listings</a>
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
                    <a href="#" data-content="inquiries"><i class="fas fa-question-circle"></i> Inquiries <span class="notification-badge"><?php echo $unreadInquiries; ?></span></a>
                    <div class="dropdown-content">
                        <a href="inquiries.php" data-content="inquiries-list"><i class="fas fa-inbox"></i> Inquiries</a>
                        <a href="#" data-content="chat"><i class="fas fa-comments"></i> Chat</a>
                    </div>
                </li>
                <li><a href="payments.php" data-content="payments"><i class="fas fa-credit-card"></i> Payments <span class="notification-badge"><?php echo $pendingPayments; ?></span></a></li>
                <li><a href="location.php" data-content="location"><i class="fas fa-map-marked-alt"></i> Location</a></li>
                <li><a href="announcements.php" data-content="announcements"><i class="fas fa-bullhorn"></i> Announcements</a></li>
                <li><a href="reports.php" data-content="reports"><i class="fas fa-chart-bar"></i> Reports</a></li>
                <li><a href="profilesetting.php" data-content="profile-settings"><i class="fas fa-user-cog"></i> Profile Setting</a></li>
                <li><a href="notifications.php" data-content="notifications"><i class="fas fa-bell"></i> Notifications <span class="notification-badge"><?php echo $unreadNotifications; ?></span></a></li>
                <li><a href="support.php" data-content="support"><i class="fas fa-headset"></i> Support</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <nav class="navbar">
                <div class="navbar-brand">My Property Listings</div>
                
                <div class="login-image">
                    <div class="dropdown">
                        <a href="#"><img src="https://placehold.co/40x40/4285F4/FFFFFF?text=<?php echo strtoupper(substr($firstName, 0, 1)); ?>" alt="User Icon"></a>
                        <div class="dropdown-content">
                            <a href="profilesetting.php"><i class="fas fa-user"></i> My Profile</a>
                            <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>    
                </div>
            </nav>

            <!-- Statistics Cards (Always Visible) -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-home"></i></div>
                    <div class="stat-info">
                        <h3>Total Properties</h3>
                        <div class="number" id="total-properties">0</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--success);"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-info">
                        <h3>Available</h3>
                        <div class="number" id="available-count">0</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--warning);"><i class="fas fa-users"></i></div>
                    <div class="stat-info">
                        <h3>Occupied</h3>
                        <div class="number" id="occupied-count">0</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--danger);"><i class="fas fa-tools"></i></div>
                    <div class="stat-info">
                        <h3>Maintenance</h3>
                        <div class="number" id="maintenance-count">0</div>
                    </div>
                </div>
            </div>

            <!-- Toggle Buttons -->
            <div class="section-toggle">
                <button class="toggle-btn active" id="show-listings-btn" onclick="showSection('listings')">
                    <i class="fas fa-list"></i> View My Listings
                </button>
                <button class="toggle-btn" id="show-add-form-btn" onclick="showSection('add-form')">
                    <i class="fas fa-plus"></i> Add New Property
                </button>
            </div>

            <!-- Listings Section -->
            <div class="section active" id="listings-section">
                <!-- Actions Bar -->
                <div class="actions-bar">
                    <div class="search-box">
                        <input type="text" id="search-input" placeholder="Search properties by name or location...">
                        <button class="btn-secondary" onclick="searchProperties()">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                    <button class="btn-secondary" onclick="loadProperties()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>

                <!-- Properties Grid -->
                <div class="properties-grid" id="properties-grid">
                    <div class="loading-spinner">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Loading your properties...</p>
                    </div>
                </div>
            </div>

            <!-- Add Property Form Section -->
            <div class="section" id="add-form-section">
                <div class="add-property-container">
                    <div class="form-header">
                        <h1><i class="fas fa-plus-circle"></i> Add New Property</h1>
                        <button class="close-form" onclick="showSection('listings')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="form-content">
                        <form id="add-property-form">
                            <!-- Basic Information -->
                            <div class="form-section">
                                <h2><i class="fas fa-info-circle"></i> Basic Information</h2>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="property-title">Property Title *</label>
                                        <input type="text" id="property-title" name="title" placeholder="e.g., Luxury 2-Bedroom Apartment" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="property-type">Property Type *</label>
                                        <select id="property-type" name="type" required>
                                            <option value="">Select Type</option>
                                            <option value="apartment">Apartment</option>
                                            <option value="house">House</option>
                                            <option value="studio">Studio</option>
                                            <option value="condo">Condo</option>
                                            <option value="townhouse">Townhouse</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="price">Monthly Rent (KES) *</label>
                                        <input type="number" id="price" name="price" placeholder="35000" min="0" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="property-status">Status *</label>
                                        <select id="property-status" name="status" required>
                                            <option value="available">Available</option>
                                            <option value="occupied">Occupied</option>
                                            <option value="maintenance">Under Maintenance</option>
                                        </select>
                                    </div>
                                    <div class="form-group full-width">
                                        <label for="description">Property Description *</label>
                                        <textarea id="description" name="description" rows="4" placeholder="Describe your property, features, and amenities..." required></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Location Details -->
                            <div class="form-section">
                                <h2><i class="fas fa-map-marker-alt"></i> Location Details</h2>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="address">Street Address *</label>
                                        <input type="text" id="address" name="address" placeholder="123 Management Plaza" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="city">City *</label>
                                        <input type="text" id="city" name="city" placeholder="Nairobi" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="neighborhood">Neighborhood/Area *</label>
                                        <input type="text" id="neighborhood" name="neighborhood" placeholder="Westlands" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="zip-code">ZIP Code</label>
                                        <input type="text" id="zip-code" name="zip" placeholder="00100">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Property Details -->
                            <div class="form-section">
                                <h2><i class="fas fa-home"></i> Property Details</h2>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="bedrooms">Bedrooms *</label>
                                        <select id="bedrooms" name="bedrooms" required>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5+</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="bathrooms">Bathrooms *</label>
                                        <select id="bathrooms" name="bathrooms" required>
                                            <option value="1">1</option>
                                            <option value="1.5">1.5</option>
                                            <option value="2">2</option>
                                            <option value="2.5">2.5</option>
                                            <option value="3">3</option>
                                            <option value="3.5">3.5</option>
                                            <option value="4">4</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="area">Area (sq ft) *</label>
                                        <input type="number" id="area" name="area" placeholder="1200" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="year-built">Year Built</label>
                                        <input type="number" id="year-built" name="year_built" placeholder="2020" min="1900" max="<?php echo date('Y'); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Amenities -->
                            <div class="form-section">
                                <h2><i class="fas fa-concierge-bell"></i> Amenities</h2>
                                <div class="amenities-grid">
                                    <div class="amenity-item">
                                        <input type="checkbox" id="wifi" name="amenities[]" value="wifi">
                                        <label for="wifi">Wi-Fi</label>
                                    </div>
                                    <div class="amenity-item">
                                        <input type="checkbox" id="parking" name="amenities[]" value="parking">
                                        <label for="parking">Parking</label>
                                    </div>
                                    <div class="amenity-item">
                                        <input type="checkbox" id="pool" name="amenities[]" value="pool">
                                        <label for="pool">Swimming Pool</label>
                                    </div>
                                    <div class="amenity-item">
                                        <input type="checkbox" id="gym" name="amenities[]" value="gym">
                                        <label for="gym">Gym</label>
                                    </div>
                                    <div class="amenity-item">
                                        <input type="checkbox" id="security" name="amenities[]" value="security">
                                        <label for="security">Security</label>
                                    </div>
                                    <div class="amenity-item">
                                        <input type="checkbox" id="water" name="amenities[]" value="water">
                                        <label for="water">Water Supply</label>
                                    </div>
                                    <div class="amenity-item">
                                        <input type="checkbox" id="electricity" name="amenities[]" value="electricity">
                                        <label for="electricity">24/7 Electricity</label>
                                    </div>
                                    <div class="amenity-item">
                                        <input type="checkbox" id="laundry" name="amenities[]" value="laundry">
                                        <label for="laundry">Laundry</label>
                                    </div>
                                    <div class="amenity-item">
                                        <input type="checkbox" id="ac" name="amenities[]" value="ac">
                                        <label for="ac">Air Conditioning</label>
                                    </div>
                                    <div class="amenity-item">
                                        <input type="checkbox" id="heating" name="amenities[]" value="heating">
                                        <label for="heating">Heating</label>
                                    </div>
                                    <div class="amenity-item">
                                        <input type="checkbox" id="balcony" name="amenities[]" value="balcony">
                                        <label for="balcony">Balcony</label>
                                    </div>
                                    <div class="amenity-item">
                                        <input type="checkbox" id="furnished" name="amenities[]" value="furnished">
                                        <label for="furnished">Furnished</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="button" class="btn-cancel" onclick="showSection('listings')">Cancel</button>
                                <button type="submit" class="btn-submit">
                                    <i class="fas fa-plus-circle"></i> Add Property
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <footer class="footer">
        <img src="assets/icons/smartlogo.png" alt="SmartHunt Logo">
        <p>&copy; 2024 SmartHunt. All rights reserved.</p>
        <p>Making property management smarter and easier.</p>
    </footer>

    <script>
        let allProperties = [];

        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, loading properties...');
            loadProperties();
            
            // Add event listener for search
            const searchInput = document.getElementById('search-input');
            if (searchInput) {
                searchInput.addEventListener('keyup', function(e) {
                    if (e.key === 'Enter') {
                        searchProperties();
                    }
                });
            }

            // Add form submission handler
            const addPropertyForm = document.getElementById('add-property-form');
            if (addPropertyForm) {
                addPropertyForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    submitAddPropertyForm();
                });
            }
        });

        function showSection(section) {
            // Update toggle buttons
            document.getElementById('show-listings-btn').classList.toggle('active', section === 'listings');
            document.getElementById('show-add-form-btn').classList.toggle('active', section === 'add-form');
            
            // Show/hide sections
            document.getElementById('listings-section').classList.toggle('active', section === 'listings');
            document.getElementById('add-form-section').classList.toggle('active', section === 'add-form');
        }

        function loadProperties() {
            console.log('Loading properties...');
            
            const grid = document.getElementById('properties-grid');
            grid.innerHTML = `
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading your properties...</p>
                </div>
            `;
            
            const url = window.location.pathname + '?action=get_properties&t=' + new Date().getTime();
            
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        allProperties = data.properties || [];
                        displayProperties(allProperties);
                        updateStats(data.stats || { total: 0, available: 0, occupied: 0, maintenance: 0 });
                    } else {
                        showNotification('Failed to load properties: ' + (data.message || 'Unknown error'), 'error');
                        displayEmptyState();
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showNotification('Connection error: ' + error.message, 'error');
                    displayEmptyState();
                });
        }

        function displayProperties(properties) {
            const grid = document.getElementById('properties-grid');
            
            if (!properties || properties.length === 0) {
                displayEmptyState();
                return;
            }

            let html = '';
            properties.forEach(property => {
                const statusClass = `status-${property.status || 'available'}`;
                const formattedDate = property.created_at ? new Date(property.created_at).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                }) : 'N/A';

                html += `
                    <div class="property-card" data-id="${property.id}">
                        <div class="property-image">
                            <img src="assets/icons/bed.jpg" alt="${property.property_name || 'Property'}">
                            <div class="property-status-badge ${statusClass}">
                                ${property.status || 'available'}
                            </div>
                        </div>
                        <div class="property-details">
                            <div class="property-price">
                                KES ${Number(property.monthly_rent || 0).toLocaleString()} <span>/month</span>
                            </div>
                            <h3 class="property-title">${property.property_name || 'Untitled Property'}</h3>
                            <div class="property-location">
                                <i class="fas fa-map-marker-alt"></i>
                                ${property.neighborhood || ''} ${property.city ? ', ' + property.city : ''}
                            </div>
                            <div class="property-features">
                                <div class="property-feature">
                                    <i class="fas fa-bed"></i>
                                    <span>${property.bedrooms || 0} Beds</span>
                                </div>
                                <div class="property-feature">
                                    <i class="fas fa-bath"></i>
                                    <span>${property.bathrooms || 0} Baths</span>
                                </div>
                                <div class="property-feature">
                                    <i class="fas fa-vector-square"></i>
                                    <span>${property.sqft || 0} sqft</span>
                                </div>
                            </div>
                            <div class="property-meta">
                                <span class="property-date">
                                    <i class="far fa-calendar"></i> Added: ${formattedDate}
                                </span>
                            </div>
                            <div class="property-actions">
                                <button class="btn-edit" onclick="editProperty(${property.id})">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn-view" onclick="viewProperty(${property.id})">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <button class="btn-delete" onclick="deleteProperty(${property.id})">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });

            grid.innerHTML = html;
        }

        function displayEmptyState() {
            const grid = document.getElementById('properties-grid');
            grid.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-home"></i>
                    <h3>No Properties Yet</h3>
                    <p>You haven't added any properties. Click "Add New Property" to get started.</p>
                    <button class="btn-primary" onclick="showSection('add-form')">
                        <i class="fas fa-plus"></i> Add Your First Property
                    </button>
                </div>
            `;
        }

        function updateStats(stats) {
            document.getElementById('total-properties').textContent = stats.total || 0;
            document.getElementById('available-count').textContent = stats.available || 0;
            document.getElementById('occupied-count').textContent = stats.occupied || 0;
            document.getElementById('maintenance-count').textContent = stats.maintenance || 0;
        }

        function searchProperties() {
            const searchTerm = document.getElementById('search-input').value.toLowerCase().trim();
            
            if (!searchTerm) {
                displayProperties(allProperties);
                return;
            }

            const filtered = allProperties.filter(property => 
                (property.property_name && property.property_name.toLowerCase().includes(searchTerm)) ||
                (property.city && property.city.toLowerCase().includes(searchTerm)) ||
                (property.neighborhood && property.neighborhood.toLowerCase().includes(searchTerm)) ||
                (property.status && property.status.toLowerCase().includes(searchTerm))
            );

            if (filtered.length === 0) {
                document.getElementById('properties-grid').innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        <h3>No Results Found</h3>
                        <p>No properties match your search term "${searchTerm}"</p>
                        <button class="btn-secondary" onclick="document.getElementById('search-input').value = ''; loadProperties();">
                            Clear Search
                        </button>
                    </div>
                `;
            } else {
                displayProperties(filtered);
            }
        }

        function submitAddPropertyForm() {
            const form = document.getElementById('add-property-form');
            const formData = new FormData(form);
            formData.append('action', 'add_property');
            
            // Disable submit button
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
            submitBtn.disabled = true;
            
            fetch(window.location.pathname, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Property added successfully!', 'success');
                    form.reset();
                    showSection('listings');
                    loadProperties(); // Refresh the listings
                } else {
                    showNotification(data.message || 'Error adding property', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred. Please try again.', 'error');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }

        function editProperty(propertyId) {
            window.location.href = `edit-property.php?id=${propertyId}`;
        }

        function viewProperty(propertyId) {
            window.location.href = `property.php?id=${propertyId}`;
        }

        function deleteProperty(propertyId) {
            if (!confirm('Are you sure you want to delete this property? This action cannot be undone.')) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'delete_property');
            formData.append('property_id', propertyId);

            fetch(window.location.pathname, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Property deleted successfully!', 'success');
                    loadProperties();
                } else {
                    showNotification(data.message || 'Error deleting property', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while deleting', 'error');
            });
        }

        function showNotification(message, type = 'info') {
            const existing = document.querySelector('.notification');
            if (existing) existing.remove();

            const notification = document.createElement('div');
            notification.className = 'notification';
            
            let icon = 'info-circle';
            let bgColor = '#4285F4';
            
            if (type === 'success') {
                icon = 'check-circle';
                bgColor = '#00A699';
            } else if (type === 'error') {
                icon = 'exclamation-circle';
                bgColor = '#FF5A5F';
            } else if (type === 'warning') {
                icon = 'exclamation-triangle';
                bgColor = '#FFB400';
            }

            notification.style.backgroundColor = bgColor;
            notification.innerHTML = `
                <i class="fas fa-${icon}"></i>
                <span>${message}</span>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }
    </script>
</body>
</html>