<?php
// edit-property.php
session_start();

// Check if landlord is logged in
if (!isset($_SESSION['landlord_id'])) {
    $_SESSION['redirect_after_login'] = 'edit-property.php';
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/LandlordUserModel.php';
require_once __DIR__ . '/models/LandlordPropertyModel.php';
require_once __DIR__ . '/models/PropertyImageModel.php';

$userModel = new LandlordUserModel();
$propertyModel = new LandlordPropertyModel();
$propertyImageModel = new PropertyImageModel();

$landlord_id = $_SESSION['landlord_id'];
$landlord = $userModel->getLandlordById($landlord_id);

// If landlord not found in database, logout
if (!$landlord) {
    header('Location: logout.php');
    exit;
}

// Get property ID from URL
$property_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($property_id <= 0) {
    header('Location: propertylistings.php');
    exit;
}

// Verify property ownership
$property = $propertyModel->getPropertyById($property_id);
if (!$property || $property['landlord_id'] != $landlord_id) {
    header('Location: propertylistings.php');
    exit;
}

// Get property images
$propertyImages = $propertyImageModel->getPropertyImages($property_id);

// Decode amenities if stored as JSON
$amenities = [];
if (isset($property['amenities'])) {
    if (is_string($property['amenities'])) {
        $amenities = json_decode($property['amenities'], true);
        if (!is_array($amenities)) $amenities = [];
    } elseif (is_array($property['amenities'])) {
        $amenities = $property['amenities'];
    }
}

// Format landlord name
$landlordName = $userModel->getFullName($landlord) ?: $landlord['username'];
$firstName = explode(' ', $landlordName)[0];

// Handle AJAX requests for this page
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'update_property') {
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
        $amenitiesList = [];
        if (isset($_POST['amenities']) && is_array($_POST['amenities'])) {
            $amenitiesList = $_POST['amenities'];
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
        
        // Prepare data for update
        $propertyData = [
            'property_name' => $title,
            'property_type' => $type,
            'monthly_rent' => $price,
            'status' => $status,
            'description' => $description,
            'address' => $address,
            'city' => $city,
            'neighborhood' => $neighborhood,
            'zip_code' => $zip,
            'bedrooms' => $bedrooms == '5+' ? 5 : intval($bedrooms),
            'bathrooms' => $bathrooms == '5+' ? 5 : floatval($bathrooms),
            'sqft' => $area,
            'year_built' => $year_built,
            'amenities' => json_encode($amenitiesList)
        ];
        
        // Update property
        $result = $propertyModel->updateProperty($property_id, $propertyData);
        
        if ($result['success']) {
            // Handle new image uploads
            if (isset($_FILES['property_images']) && !empty($_FILES['property_images']['name'][0])) {
                $uploadResult = $propertyImageModel->uploadMultipleImages($_FILES['property_images'], $property_id);
                if (!$uploadResult['success']) {
                    $result['image_message'] = $uploadResult['message'];
                }
            }
            
            echo json_encode($result);
        } else {
            echo json_encode($result);
        }
        exit;
    }
    
    if ($_POST['action'] === 'delete_image') {
        $image_id = intval($_POST['image_id']);
        $result = $propertyImageModel->deleteImage($image_id, $property_id);
        echo json_encode($result);
        exit;
    }
    
    if ($_POST['action'] === 'set_main_image') {
        $image_id = intval($_POST['image_id']);
        $result = $propertyImageModel->setMainImage($image_id, $property_id);
        echo json_encode($result);
        exit;
    }
    
    if ($_POST['action'] === 'reorder_images') {
        $image_ids = json_decode($_POST['image_ids'], true);
        $result = $propertyImageModel->reorderImages($image_ids, $property_id);
        echo json_encode($result);
        exit;
    }
}

$page_title = 'Edit Property';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="/Landlord-MGT/Landlord/Frontend/">
    <link rel="icon" href="assets/icons/smartlogo.png">
    <title>Edit Property - SmartHunt</title>
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

        /* Edit Property Form Styles */
        .edit-property-container {
            background: var(--light);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
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

        .back-link {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            transition: background 0.3s;
        }

        .back-link:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .form-content {
            padding: 30px;
            max-height: calc(100vh - 200px);
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

        /* Image Gallery Styles */
        .image-gallery-section {
            margin-top: 20px;
        }

        .existing-images {
            margin-bottom: 30px;
        }

        .existing-images h3 {
            font-size: 16px;
            margin-bottom: 15px;
            color: var(--dark);
        }

        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
        }

        .image-card {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid var(--light-gray);
            background: var(--light-gray);
            transition: all 0.3s;
        }

        .image-card.main {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px var(--primary);
        }

        .image-card img {
            width: 100%;
            height: 120px;
            object-fit: cover;
        }

        .image-card-actions {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            gap: 5px;
            padding: 5px;
            transform: translateY(100%);
            transition: transform 0.3s;
        }

        .image-card:hover .image-card-actions {
            transform: translateY(0);
        }

        .image-card-actions button {
            background: none;
            border: none;
            color: white;
            padding: 5px;
            cursor: pointer;
            font-size: 12px;
            transition: opacity 0.3s;
        }

        .image-card-actions button:hover {
            opacity: 0.7;
        }

        .image-card-actions .delete-image {
            color: var(--danger);
        }

        .main-badge {
            position: absolute;
            top: 5px;
            left: 5px;
            background: var(--primary);
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
        }

        /* Image Upload Area */
        .image-upload-area {
            border: 2px dashed var(--gray);
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: var(--light-gray);
            margin-bottom: 15px;
        }

        .image-upload-area:hover {
            border-color: var(--primary);
            background: rgba(255, 56, 92, 0.05);
        }

        .image-upload-area i {
            font-size: 48px;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .image-upload-area h3 {
            font-size: 18px;
            margin-bottom: 5px;
            color: var(--dark);
        }

        .image-upload-area p {
            color: var(--text);
            font-size: 14px;
        }

        .image-preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .preview-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            height: 120px;
            border: 2px solid var(--light-gray);
        }

        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .preview-remove {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255, 255, 255, 0.9);
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
            color: var(--danger);
        }

        .preview-remove:hover {
            background: var(--danger);
            color: white;
        }

        /* Amenities Grid */
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

        /* Form Actions */
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
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
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
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background: var(--primary-light);
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

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .main-content {
                margin-left: 0;
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
            .image-grid {
                grid-template-columns: repeat(2, 1fr);
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
                    <a href="#" class="active"><i class="fas fa-building"></i> Properties</a>
                    <div class="dropdown-content">
                        <a href="addproperty.php"><i class="fas fa-plus"></i> Add Property</a>
                        <a href="propertylistings.php"><i class="fas fa-edit"></i> My Listings</a>
                        <a href="location.php"><i class="fas fa-map-marker-alt"></i> Manage Location</a>
                    </div>
                </li>
                <li><a href="tenants.php"><i class="fas fa-users"></i> Tenants</a></li>
                <li><a href="inquiries.php"><i class="fas fa-question-circle"></i> Inquiries</a></li>
                <li><a href="payments.php"><i class="fas fa-credit-card"></i> Payments</a></li>
                <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                <li><a href="profilesetting.php"><i class="fas fa-user-cog"></i> Profile</a></li>
                <li><a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
                <li><a href="support.php"><i class="fas fa-headset"></i> Support</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <nav class="navbar">
                <div class="navbar-brand">Edit Property</div>
                
                <div class="user-menu">
                    <div class="user-info">
                        <div class="user-details">
                            <div class="user-name"><?php echo htmlspecialchars($landlordName); ?></div>
                            <div class="user-role">Landlord</div>
                        </div>
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($firstName, 0, 1)); ?>
                        </div>
                        <div class="dropdown-menu">
                            <a href="profilesetting.php"><i class="fas fa-user"></i> My Profile</a>
                            <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                            <hr>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Edit Property Form -->
            <div class="edit-property-container">
                <div class="form-header">
                    <h1><i class="fas fa-edit"></i> Edit Property</h1>
                    <a href="propertylistings.php" class="back-link">
                        <i class="fas fa-arrow-left"></i> Back to Listings
                    </a>
                </div>
                
                <div class="form-content">
                    <form id="edit-property-form" enctype="multipart/form-data">
                        <input type="hidden" name="property_id" value="<?php echo $property_id; ?>">
                        
                        <!-- Basic Information -->
                        <div class="form-section">
                            <h2><i class="fas fa-info-circle"></i> Basic Information</h2>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="title">Property Title *</label>
                                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($property['property_name'] ?? ''); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="type">Property Type *</label>
                                    <select id="type" name="type" required>
                                        <option value="">Select Type</option>
                                        <option value="apartment" <?php echo ($property['property_type'] ?? '') == 'apartment' ? 'selected' : ''; ?>>Apartment</option>
                                        <option value="house" <?php echo ($property['property_type'] ?? '') == 'house' ? 'selected' : ''; ?>>House</option>
                                        <option value="studio" <?php echo ($property['property_type'] ?? '') == 'studio' ? 'selected' : ''; ?>>Studio</option>
                                        <option value="condo" <?php echo ($property['property_type'] ?? '') == 'condo' ? 'selected' : ''; ?>>Condo</option>
                                        <option value="townhouse" <?php echo ($property['property_type'] ?? '') == 'townhouse' ? 'selected' : ''; ?>>Townhouse</option>
                                        <option value="bedsitter" <?php echo ($property['property_type'] ?? '') == 'bedsitter' ? 'selected' : ''; ?>>Bedsitter</option>
                                        <option value="single room" <?php echo ($property['property_type'] ?? '') == 'single room' ? 'selected' : ''; ?>>Single Room</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="price">Monthly Rent (KES) *</label>
                                    <input type="number" id="price" name="price" value="<?php echo $property['monthly_rent'] ?? 0; ?>" min="0" required>
                                </div>
                                <div class="form-group">
                                    <label for="status">Status *</label>
                                    <select id="status" name="status" required>
                                        <option value="available" <?php echo ($property['status'] ?? '') == 'available' ? 'selected' : ''; ?>>Available</option>
                                        <option value="occupied" <?php echo ($property['status'] ?? '') == 'occupied' ? 'selected' : ''; ?>>Occupied</option>
                                        <option value="maintenance" <?php echo ($property['status'] ?? '') == 'maintenance' ? 'selected' : ''; ?>>Under Maintenance</option>
                                    </select>
                                </div>
                                <div class="form-group full-width">
                                    <label for="description">Property Description *</label>
                                    <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($property['description'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Location Details -->
                        <div class="form-section">
                            <h2><i class="fas fa-map-marker-alt"></i> Location Details</h2>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="address">Street Address *</label>
                                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($property['address'] ?? ''); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="city">City *</label>
                                    <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($property['city'] ?? ''); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="neighborhood">Neighborhood/Area *</label>
                                    <input type="text" id="neighborhood" name="neighborhood" value="<?php echo htmlspecialchars($property['neighborhood'] ?? ''); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="zip">ZIP Code</label>
                                    <input type="text" id="zip" name="zip" value="<?php echo htmlspecialchars($property['zip_code'] ?? ''); ?>">
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
                                        <option value="1" <?php echo ($property['bedrooms'] ?? 0) == 1 ? 'selected' : ''; ?>>1</option>
                                        <option value="2" <?php echo ($property['bedrooms'] ?? 0) == 2 ? 'selected' : ''; ?>>2</option>
                                        <option value="3" <?php echo ($property['bedrooms'] ?? 0) == 3 ? 'selected' : ''; ?>>3</option>
                                        <option value="4" <?php echo ($property['bedrooms'] ?? 0) == 4 ? 'selected' : ''; ?>>4</option>
                                        <option value="5" <?php echo ($property['bedrooms'] ?? 0) >= 5 ? 'selected' : ''; ?>>5+</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="bathrooms">Bathrooms *</label>
                                    <select id="bathrooms" name="bathrooms" required>
                                        <option value="1" <?php echo ($property['bathrooms'] ?? 0) == 1 ? 'selected' : ''; ?>>1</option>
                                        <option value="1.5" <?php echo ($property['bathrooms'] ?? 0) == 1.5 ? 'selected' : ''; ?>>1.5</option>
                                        <option value="2" <?php echo ($property['bathrooms'] ?? 0) == 2 ? 'selected' : ''; ?>>2</option>
                                        <option value="2.5" <?php echo ($property['bathrooms'] ?? 0) == 2.5 ? 'selected' : ''; ?>>2.5</option>
                                        <option value="3" <?php echo ($property['bathrooms'] ?? 0) == 3 ? 'selected' : ''; ?>>3</option>
                                        <option value="3.5" <?php echo ($property['bathrooms'] ?? 0) == 3.5 ? 'selected' : ''; ?>>3.5</option>
                                        <option value="4" <?php echo ($property['bathrooms'] ?? 0) == 4 ? 'selected' : ''; ?>>4</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="area">Area (sq ft) *</label>
                                    <input type="number" id="area" name="area" value="<?php echo $property['sqft'] ?? 0; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="year_built">Year Built</label>
                                    <input type="number" id="year_built" name="year_built" value="<?php echo $property['year_built'] ?? ''; ?>" min="1900" max="<?php echo date('Y'); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Amenities -->
                        <div class="form-section">
                            <h2><i class="fas fa-concierge-bell"></i> Amenities</h2>
                            <div class="amenities-grid">
                                <?php
                                $amenityList = [
                                    'WiFi', 'Parking', 'Swimming Pool', 'Gym', 'Security', 
                                    'Water Supply', '24/7 Electricity', 'Laundry', 'Air Conditioning', 
                                    'Heating', 'Balcony', 'Furnished'
                                ];
                                
                                foreach ($amenityList as $amenity):
                                    $checked = in_array($amenity, $amenities) ? 'checked' : '';
                                ?>
                                <div class="amenity-item">
                                    <input type="checkbox" id="amenity_<?php echo strtolower(str_replace(' ', '_', $amenity)); ?>" 
                                           name="amenities[]" value="<?php echo htmlspecialchars($amenity); ?>" <?php echo $checked; ?>>
                                    <label for="amenity_<?php echo strtolower(str_replace(' ', '_', $amenity)); ?>"><?php echo htmlspecialchars($amenity); ?></label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Property Images -->
                        <div class="form-section">
                            <h2><i class="fas fa-images"></i> Property Images</h2>
                            
                            <!-- Existing Images -->
                            <?php if (!empty($propertyImages)): ?>
                            <div class="existing-images">
                                <h3>Current Images (<?php echo count($propertyImages); ?>)</h3>
                                <div class="image-grid" id="image-grid">
                                    <?php foreach ($propertyImages as $index => $image): ?>
                                    <div class="image-card <?php echo $image['is_main'] ? 'main' : ''; ?>" data-id="<?php echo $image['id']; ?>">
                                        <img src="/Landlord-MGT/Landlord/Frontend/<?php echo $image['image_path']; ?>" alt="Property Image">
                                        <?php if ($image['is_main']): ?>
                                        <div class="main-badge">Main</div>
                                        <?php endif; ?>
                                        <div class="image-card-actions">
                                            <?php if (!$image['is_main']): ?>
                                            <button type="button" class="set-main" onclick="setMainImage(<?php echo $image['id']; ?>)">
                                                <i class="fas fa-star"></i> Set Main
                                            </button>
                                            <?php endif; ?>
                                            <button type="button" class="delete-image" onclick="deleteImage(<?php echo $image['id']; ?>)">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Add New Images -->
                            <div>
                                <h3>Add New Images</h3>
                                <div class="image-upload-area" id="image-upload-area">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <h3>Upload Property Images</h3>
                                    <p>Drag & drop images here or click to browse</p>
                                    <p><small>You can select multiple images. First image will be the main photo.</small></p>
                                    <input type="file" id="property-images" name="property_images[]" multiple accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" style="display: none;">
                                </div>
                                <div class="image-counter" id="image-counter">0 images selected</div>
                                <div class="image-preview-grid" id="image-preview-grid"></div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <a href="propertylistings.php" class="btn-cancel">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn-submit">
                                <i class="fas fa-save"></i> Update Property
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <footer class="footer">
        <img src="assets/icons/smartlogo.png" alt="SmartHunt Logo">
        <p>&copy; <?php echo date('Y'); ?> SmartHunt. All rights reserved.</p>
    </footer>

    <script>
        let selectedFiles = [];

        document.addEventListener('DOMContentLoaded', function() {
            // Form submission
            const editForm = document.getElementById('edit-property-form');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    submitEditForm();
                });
            }

            // Image upload functionality
            const uploadArea = document.getElementById('image-upload-area');
            const fileInput = document.getElementById('property-images');
            
            if (uploadArea && fileInput) {
                uploadArea.addEventListener('click', function() {
                    fileInput.click();
                });
                
                uploadArea.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    uploadArea.style.borderColor = 'var(--primary)';
                    uploadArea.style.background = 'rgba(255, 56, 92, 0.05)';
                });
                
                uploadArea.addEventListener('dragleave', function() {
                    uploadArea.style.borderColor = 'var(--gray)';
                    uploadArea.style.background = 'var(--light-gray)';
                });
                
                uploadArea.addEventListener('drop', function(e) {
                    e.preventDefault();
                    uploadArea.style.borderColor = 'var(--gray)';
                    uploadArea.style.background = 'var(--light-gray)';
                    
                    const files = e.dataTransfer.files;
                    handleFiles(files);
                });
                
                fileInput.addEventListener('change', function() {
                    handleFiles(this.files);
                });
            }
        });

        function handleFiles(files) {
            selectedFiles = Array.from(files);
            updateImagePreview();
        }

        function updateImagePreview() {
            const previewGrid = document.getElementById('image-preview-grid');
            const counter = document.getElementById('image-counter');
            
            previewGrid.innerHTML = '';
            
            if (selectedFiles.length === 0) {
                counter.textContent = '0 images selected';
                return;
            }
            
            counter.textContent = selectedFiles.length + ' image(s) selected';
            
            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                const previewItem = document.createElement('div');
                previewItem.className = 'preview-item';
                
                reader.onload = function(e) {
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" alt="Preview ${index + 1}">
                        <div class="preview-remove" onclick="removeImage(${index})">&times;</div>
                        ${index === 0 ? '<small style="position:absolute; bottom:0; left:0; background:var(--primary); color:white; padding:2px 5px; font-size:10px;">New Main</small>' : ''}
                    `;
                };
                
                reader.readAsDataURL(file);
                previewGrid.appendChild(previewItem);
            });
        }

        function removeImage(index) {
            selectedFiles.splice(index, 1);
            
            const fileInput = document.getElementById('property-images');
            const dt = new DataTransfer();
            
            selectedFiles.forEach(file => {
                dt.items.add(file);
            });
            
            fileInput.files = dt.files;
            updateImagePreview();
        }

        function submitEditForm() {
            const form = document.getElementById('edit-property-form');
            const formData = new FormData(form);
            
            formData.append('action', 'update_property');
            
            // Add selected files
            if (selectedFiles.length > 0) {
                selectedFiles.forEach(file => {
                    formData.append('property_images[]', file);
                });
            }
            
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
            submitBtn.disabled = true;
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Property updated successfully!', 'success');
                    setTimeout(() => {
                        window.location.href = 'propertylistings.php';
                    }, 1500);
                } else {
                    showNotification(data.message || 'Error updating property', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while updating', 'error');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }

        function deleteImage(imageId) {
            if (!confirm('Are you sure you want to delete this image?')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'delete_image');
            formData.append('image_id', imageId);
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove image from DOM
                    const imageCard = document.querySelector(`.image-card[data-id="${imageId}"]`);
                    if (imageCard) {
                        imageCard.remove();
                    }
                    showNotification('Image deleted successfully', 'success');
                } else {
                    showNotification(data.message || 'Error deleting image', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            });
        }

        function setMainImage(imageId) {
            const formData = new FormData();
            formData.append('action', 'set_main_image');
            formData.append('image_id', imageId);
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI - remove main from all, add to selected
                    document.querySelectorAll('.image-card').forEach(card => {
                        card.classList.remove('main');
                        const badge = card.querySelector('.main-badge');
                        if (badge) badge.remove();
                    });
                    
                    const selectedCard = document.querySelector(`.image-card[data-id="${imageId}"]`);
                    selectedCard.classList.add('main');
                    selectedCard.insertAdjacentHTML('afterbegin', '<div class="main-badge">Main</div>');
                    
                    // Update button visibility
                    document.querySelectorAll('.set-main').forEach(btn => {
                        btn.style.display = 'inline-block';
                    });
                    const setMainBtn = selectedCard.querySelector('.set-main');
                    if (setMainBtn) setMainBtn.style.display = 'none';
                    
                    showNotification('Main image updated', 'success');
                } else {
                    showNotification(data.message || 'Error setting main image', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
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