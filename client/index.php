<?php
session_start();

// Sample properties data - in real app, this would come from database
$properties = [
    [
        'id' => 1,
        'title' => 'Modern Studio Near Campus',
        'price' => 850,
        'address' => '123 University Ave, Boston, MA',
        'beds' => 1,
        'baths' => 1,
        'sqft' => 550,
        'type' => 'Apartment',
        'image' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=400&h=300&fit=crop',
        'featured' => true,
        'city' => 'Boston',
        'state' => 'MA',
        'zip' => '02115'
    ],
    [
        'id' => 2,
        'title' => 'Spacious 2BR House',
        'price' => 1200,
        'address' => '456 Maple St, Chicago, IL',
        'beds' => 2,
        'baths' => 1,
        'sqft' => 950,
        'type' => 'House',
        'image' => 'https://images.unsplash.com/photo-1518780664697-55e3ad937233?w=400&h=300&fit=crop',
        'featured' => false,
        'city' => 'Chicago',
        'state' => 'IL',
        'zip' => '60614'
    ],
    [
        'id' => 3,
        'title' => 'Downtown Luxury Loft',
        'price' => 1600,
        'address' => '789 Downtown Blvd, NYC',
        'beds' => 1,
        'baths' => 1,
        'sqft' => 750,
        'type' => 'Loft',
        'image' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=400&h=300&fit=crop',
        'featured' => true,
        'city' => 'New York',
        'state' => 'NY',
        'zip' => '10001'
    ],
    [
        'id' => 4,
        'title' => 'Family Home with Garden',
        'price' => 2200,
        'address' => '101 Oak Lane, Austin, TX',
        'beds' => 3,
        'baths' => 2,
        'sqft' => 1450,
        'type' => 'House',
        'image' => 'https://images.unsplash.com/photo-1570129477492-45c003edd2be?w=400&h=300&fit=crop',
        'featured' => false,
        'city' => 'Austin',
        'state' => 'TX',
        'zip' => '78701'
    ],
    [
        'id' => 5,
        'title' => 'Student Apartment Complex',
        'price' => 750,
        'address' => '202 College Rd, Berkeley, CA',
        'beds' => 1,
        'baths' => 1,
        'sqft' => 500,
        'type' => 'Apartment',
        'image' => 'https://images.unsplash.com/photo-1558036117-15e82a2c9a9a?w=400&h=300&fit=crop',
        'featured' => false,
        'city' => 'Berkeley',
        'state' => 'CA',
        'zip' => '94704'
    ],
    [
        'id' => 6,
        'title' => 'Modern Townhouse',
        'price' => 1800,
        'address' => '303 Modern Ave, Seattle, WA',
        'beds' => 2,
        'baths' => 2.5,
        'sqft' => 1200,
        'type' => 'Townhouse',
        'image' => 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=400&h=300&fit=crop',
        'featured' => true,
        'city' => 'Seattle',
        'state' => 'WA',
        'zip' => '98101'
    ],
    [
        'id' => 7,
        'title' => 'Cozy Studio Apartment',
        'price' => 650,
        'address' => '404 Pine St, Portland, OR',
        'beds' => 1,
        'baths' => 1,
        'sqft' => 450,
        'type' => 'Apartment',
        'image' => 'https://images.unsplash.com/photo-1499916078039-922301b0eb9b?w=400&h=300&fit=crop',
        'featured' => false,
        'city' => 'Portland',
        'state' => 'OR',
        'zip' => '97205'
    ],
    [
        'id' => 8,
        'title' => 'Luxury City View Condo',
        'price' => 2800,
        'address' => '505 Skyline Blvd, Miami, FL',
        'beds' => 2,
        'baths' => 2,
        'sqft' => 1100,
        'type' => 'Condo',
        'image' => 'https://images.unsplash.com/photo-1448630360428-65456885c650?w=400&h=300&fit=crop',
        'featured' => true,
        'city' => 'Miami',
        'state' => 'FL',
        'zip' => '33101'
    ]
];

// Initialize saved properties in session if not exists
if (!isset($_SESSION['saved_properties'])) {
    $_SESSION['saved_properties'] = [];
}

// Handle search and filters
$filtered_properties = $properties;
$search_location = isset($_GET['location']) ? strtolower(trim($_GET['location'])) : '';
$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$min_beds = isset($_GET['min_beds']) ? (int)$_GET['min_beds'] : 0;
$property_type = isset($_GET['property_type']) ? strtolower(trim($_GET['property_type'])) : '';

if ($search_location || $min_price > 0 || $min_beds > 0 || $property_type) {
    $filtered_properties = array_filter($properties, function($property) use ($search_location, $min_price, $min_beds, $property_type) {
        $matches = true;
        
        if ($search_location) {
            $location_string = strtolower($property['address'] . ' ' . $property['city'] . ' ' . $property['state'] . ' ' . $property['zip']);
            if (strpos($location_string, $search_location) === false) {
                $matches = false;
            }
        }
        
        if ($min_price > 0 && $property['price'] < $min_price) {
            $matches = false;
        }
        
        if ($min_beds > 0 && $property['beds'] < $min_beds) {
            $matches = false;
        }
        
        if ($property_type && strtolower($property['type']) !== $property_type) {
            $matches = false;
        }
        
        return $matches;
    });
}

// Handle AJAX requests
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'save_property') {
        $property_id = (int)$_POST['property_id'];
        if (!in_array($property_id, $_SESSION['saved_properties'])) {
            $_SESSION['saved_properties'][] = $property_id;
            echo json_encode(['success' => true, 'saved' => true]);
        } else {
            $_SESSION['saved_properties'] = array_diff($_SESSION['saved_properties'], [$property_id]);
            echo json_encode(['success' => true, 'saved' => false]);
        }
        exit;
    }
    
    if ($_POST['action'] === 'newsletter_subscribe') {
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        if ($email) {
            // In real app, save to database
            echo json_encode(['success' => true, 'message' => 'Successfully subscribed to newsletter!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        }
        exit;
    }
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
        /* All your existing CSS styles here */
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

        /* Typography */
        h1, h2, h3, h4 {
            font-weight: 600;
            line-height: 1.3;
        }

        h1 { font-size: 24px; }
        h2 { font-size: 20px; }
        h3 { font-size: 18px; }
        h4 { font-size: 16px; }

        .small-text {
            font-size: 12px;
            color: #666;
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
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .nav-logo a:hover {
            color: #023e8a;
        }

        .nav-logo i {
            margin-right: 8px;
            font-size: 22px;
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
        }

        .search-filters select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            background-color: white;
            cursor: pointer;
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
            white-space: nowrap;
        }

        /* Properties Grid */
        .page-header {
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .results-count {
            color: #666;
            font-size: 14px;
        }

        .properties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }

        .property-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
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

        .property-card:hover .property-image img {
            transform: scale(1.05);
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
        }

        .property-price {
            margin-bottom: 8px;
        }

        .price {
            font-size: 20px;
            font-weight: 700;
            color: #0077b6;
        }

        .period {
            font-size: 12px;
            color: #666;
        }

        .property-title {
            margin-bottom: 8px;
            font-size: 16px;
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
        }

        .property-features i {
            margin-right: 4px;
        }

        .property-type {
            display: inline-block;
            background-color: #e6f2ff;
            color: #0077b6;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
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
            border-radius: 4px;
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

        /* No Results */
        .no-results {
            text-align: center;
            padding: 60px 20px;
            background-color: white;
            border-radius: 8px;
            grid-column: 1 / -1;
        }

        .no-results i {
            font-size: 48px;
            color: #ccc;
            margin-bottom: 20px;
        }

        .no-results h3 {
            margin-bottom: 10px;
            color: #333;
        }

        .no-results p {
            color: #666;
        }

        /* Responsive Design */
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
            .search-input,
            .search-filters {
                width: 100%;
            }
            .search-filters {
                flex-wrap: wrap;
            }
            .properties-grid {
                grid-template-columns: 1fr;
            }
            .footer-container {
                grid-template-columns: 1fr;
                gap: 30px;
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
                <button class="nav-link active" onclick="window.location.href='index.php'">
                    <i class="fas fa-search"></i> <span>Browse</span>
                </button>
                <button class="nav-link" onclick="showSavedProperties()">
                    <i class="fas fa-heart"></i> <span>Saved <span id="saved-count" class="saved-count">(<?php echo count($_SESSION['saved_properties']); ?>)</span></span>
                </button>
                <button class="nav-link" onclick="showToast('Alerts feature coming soon!')">
                    <i class="fas fa-bell"></i> <span>Alerts</span>
                </button>
                <button class="nav-link" onclick="showToast('Account feature coming soon!')">
                    <i class="fas fa-user"></i> <span>Account</span>
                </button>
                <button class="nav-button" onclick="showToast('List your property feature coming soon!')">List Your Property</button>
            </div>
            
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- Search Bar -->
    <div class="search-container">
        <form method="GET" action="index.php" class="search-box" id="searchForm">
            <div class="search-input">
                <i class="fas fa-search"></i>
                <input type="text" name="location" placeholder="Enter city, neighborhood, or ZIP code" 
                       value="<?php echo htmlspecialchars($search_location); ?>">
            </div>
            <div class="search-filters">
                <select name="min_price">
                    <option value="">Any Price</option>
                    <option value="500" <?php echo $min_price == 500 ? 'selected' : ''; ?>>$500+</option>
                    <option value="1000" <?php echo $min_price == 1000 ? 'selected' : ''; ?>>$1000+</option>
                    <option value="1500" <?php echo $min_price == 1500 ? 'selected' : ''; ?>>$1500+</option>
                    <option value="2000" <?php echo $min_price == 2000 ? 'selected' : ''; ?>>$2000+</option>
                </select>
                <select name="min_beds">
                    <option value="">Any Beds</option>
                    <option value="1" <?php echo $min_beds == 1 ? 'selected' : ''; ?>>1+ Bed</option>
                    <option value="2" <?php echo $min_beds == 2 ? 'selected' : ''; ?>>2+ Beds</option>
                    <option value="3" <?php echo $min_beds == 3 ? 'selected' : ''; ?>>3+ Beds</option>
                </select>
                <select name="property_type">
                    <option value="">Any Type</option>
                    <option value="apartment" <?php echo $property_type == 'apartment' ? 'selected' : ''; ?>>Apartment</option>
                    <option value="house" <?php echo $property_type == 'house' ? 'selected' : ''; ?>>House</option>
                    <option value="condo" <?php echo $property_type == 'condo' ? 'selected' : ''; ?>>Condo</option>
                    <option value="loft" <?php echo $property_type == 'loft' ? 'selected' : ''; ?>>Loft</option>
                    <option value="townhouse" <?php echo $property_type == 'townhouse' ? 'selected' : ''; ?>>Townhouse</option>
                </select>
                <button type="submit" class="search-button">Search</button>
                <?php if ($search_location || $min_price || $min_beds || $property_type): ?>
                    <button type="button" class="search-button" onclick="clearFilters()" style="background-color: #666;">Clear</button>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Main Content -->
    <main class="container">
        <div class="page-header">
            <h1>
                <?php if ($search_location || $min_price || $min_beds || $property_type): ?>
                    Search Results
                <?php else: ?>
                    Available Rentals Near You
                <?php endif; ?>
            </h1>
            <p class="results-count">Showing <?php echo count($filtered_properties); ?> properties</p>
        </div>

        <div class="properties-grid" id="propertiesGrid">
            <?php if (count($filtered_properties) > 0): ?>
                <?php foreach ($filtered_properties as $property): ?>
                <div class="property-card <?php echo $property['featured'] ? 'featured' : ''; ?>" 
                     onclick="viewProperty(<?php echo $property['id']; ?>)">
                    <?php if ($property['featured']): ?>
                    <div class="featured-badge">FEATURED</div>
                    <?php endif; ?>
                    
                    <div class="property-image">
                        <img src="<?php echo $property['image']; ?>" 
                             alt="<?php echo htmlspecialchars($property['title']); ?>"
                             onerror="this.src='https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=400&h=300&fit=crop'">
                        <button class="save-btn <?php echo in_array($property['id'], $_SESSION['saved_properties']) ? 'saved' : ''; ?>" 
                                onclick="event.stopPropagation(); toggleSave(this, <?php echo $property['id']; ?>)">
                            <i class="<?php echo in_array($property['id'], $_SESSION['saved_properties']) ? 'fas' : 'far'; ?> fa-heart"></i>
                        </button>
                    </div>
                    
                    <div class="property-details">
                        <div class="property-price">
                            <span class="price">$<?php echo number_format($property['price']); ?></span>
                            <span class="period">/month</span>
                        </div>
                        
                        <h3 class="property-title">
                            <?php echo htmlspecialchars($property['title']); ?>
                        </h3>
                        
                        <p class="property-address">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($property['address']); ?>
                        </p>
                        
                        <div class="property-features">
                            <span><i class="fas fa-bed"></i> <?php echo $property['beds']; ?> bed</span>
                            <span><i class="fas fa-bath"></i> <?php echo $property['baths']; ?> bath</span>
                            <span><i class="fas fa-ruler-combined"></i> <?php echo number_format($property['sqft']); ?> sqft</span>
                        </div>
                        
                        <div class="property-type">
                            <?php echo $property['type']; ?>
                        </div>
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
                    <li><a href="#" onclick="showToast('Browse rentals feature coming soon!')">Browse Rentals</a></li>
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
            <button class="mobile-nav-link active" onclick="window.location.href='index.php'">Browse</button>
            <button class="mobile-nav-link" onclick="showSavedProperties()">Saved Properties <span id="mobile-saved-count">(<?php echo count($_SESSION['saved_properties']); ?>)</span></button>
            <button class="mobile-nav-link" onclick="showToast('Alerts feature coming soon!')">Alerts</button>
            <button class="mobile-nav-link" onclick="showToast('Account feature coming soon!')">My Account</button>
            <button class="mobile-nav-link" onclick="showToast('List property feature coming soon!')">List Property</button>
            <button class="mobile-nav-link" onclick="showToast('Help center feature coming soon!')">Help Center</button>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toast" class="toast-notification"></div>

    <script>
        // Mobile menu functionality
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('active');
        }

        // Close menu when clicking outside
        document.getElementById('mobileMenu').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });

        // Toast notification function
        function showToast(message, isError = false) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.style.backgroundColor = isError ? '#e74c3c' : '#333';
            toast.classList.add('show');
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        // Save button functionality with AJAX
        function toggleSave(btn, propertyId) {
            event.stopPropagation();
            
            fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
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
                        showToast('Property saved to favorites');
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        btn.classList.remove('saved');
                        showToast('Property removed from favorites');
                    }
                    
                    // Update saved count
                    updateSavedCount();
                }
            })
            .catch(error => {
                showToast('Error saving property', true);
            });
        }

        // Update saved count in UI
        function updateSavedCount() {
            fetch('index.php?get_saved_count=1')
                .then(response => response.json())
                .then(data => {
                    const countElement = document.getElementById('saved-count');
                    const mobileCountElement = document.getElementById('mobile-saved-count');
                    if (countElement) {
                        countElement.textContent = '(' + data.count + ')';
                    }
                    if (mobileCountElement) {
                        mobileCountElement.textContent = '(' + data.count + ')';
                    }
                });
        }

        // Show saved properties
        function showSavedProperties() {
            const savedIds = <?php echo json_encode($_SESSION['saved_properties']); ?>;
            if (savedIds.length === 0) {
                showToast('No saved properties yet');
                return;
            }
            
            // In a real app, you'd redirect to a saved properties page
            showToast('Viewing saved properties (feature coming soon)');
        }

        // View property details
        function viewProperty(propertyId) {
            window.location.href = 'property.php?id=' + propertyId;
        }

        // Clear all filters
        function clearFilters() {
            window.location.href = 'index.php';
        }

        // Newsletter subscription
        function subscribeNewsletter() {
            const email = document.getElementById('newsletterEmail').value.trim();
            
            if (!email) {
                showToast('Please enter your email', true);
                return;
            }
            
            if (!isValidEmail(email)) {
                showToast('Please enter a valid email address', true);
                return;
            }
            
            fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
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
            .catch(error => {
                showToast('Error subscribing to newsletter', true);
            });
        }

        // Email validation
        function isValidEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        // Handle search form submission
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            const location = this.location.value.trim();
            if (location) {
                showToast('Searching for: ' + location);
            }
        });

        // Handle dropdown changes
        document.querySelectorAll('.search-filters select').forEach(select => {
            select.addEventListener('change', function() {
                document.getElementById('searchForm').submit();
            });
        });

        // Initialize image error handling
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('.property-image img');
            const fallbackImage = 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=400&h=300&fit=crop';
            
            images.forEach(img => {
                if (!img.hasAttribute('data-error-handled')) {
                    img.setAttribute('data-error-handled', 'true');
                    img.addEventListener('error', function() {
                        this.src = fallbackImage;
                    });
                }
            });
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Press '/' to focus search
            if (e.key === '/' && !e.ctrlKey && !e.metaKey) {
                e.preventDefault();
                document.querySelector('.search-input input').focus();
            }
            
            // Press 'Escape' to close mobile menu
            if (e.key === 'Escape') {
                document.getElementById('mobileMenu').classList.remove('active');
            }
        });
    </script>
</body>
</html>