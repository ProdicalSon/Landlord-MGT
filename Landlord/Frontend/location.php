<?php
// Landlord/Frontend/location.php
session_start();

// Check if landlord is logged in
if (!isset($_SESSION['landlord_id'])) {
    $_SESSION['redirect_after_login'] = 'location.php';
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
$properties = $propertyModel->getLandlordProperties($landlord_id);

// Format landlord name
$landlordName = $userModel->getFullName($landlord) ?: $landlord['username'];
$firstName = explode(' ', $landlordName)[0];

// Get unread notifications count
$unreadNotifications = 7; // Placeholder
$unreadInquiries = 5; // Placeholder
$pendingPayments = 2; // Placeholder
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/icons/smartlogo.png">
    <title>Property Locations - SmartHunt Landlord</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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

        .content h1 {
            font-size: 28px;
            margin-bottom: 5px;
            color: var(--dark);
        }

        .content > p {
            color: var(--text);
            margin-bottom: 25px;
        }

        /* Location Styles */
        .location-container {
            background: var(--light);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .location-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 25px 30px;
        }

        .location-header h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .location-header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .location-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 30px;
            border-bottom: 1px solid var(--light-gray);
            flex-wrap: wrap;
            gap: 15px;
        }

        .search-box {
            position: relative;
            width: 300px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid var(--gray);
            border-radius: 8px;
            font-size: 14px;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text);
        }

        .location-stats {
            display: flex;
            gap: 15px;
        }

        .stat-card {
            background: var(--light-gray);
            border-radius: 8px;
            padding: 10px 20px;
            text-align: center;
        }

        .stat-card .value {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary);
        }

        .stat-card .label {
            font-size: 12px;
            color: var(--text);
        }

        .filter-controls {
            display: flex;
            gap: 10px;
        }

        .filter-controls select {
            padding: 8px 15px;
            border-radius: 6px;
            border: 1px solid var(--gray);
            background-color: var(--light);
            font-size: 14px;
        }

        .location-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            padding: 30px;
        }

        .map-container {
            background: var(--light);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .map-container h3 {
            font-size: 18px;
            margin-bottom: 15px;
            color: var(--dark);
        }

        #map {
            height: 450px;
            border-radius: 8px;
            background: var(--light-gray);
        }

        .location-list {
            background: var(--light);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            max-height: 550px;
            overflow-y: auto;
        }

        .location-list h3 {
            font-size: 18px;
            margin-bottom: 20px;
            color: var(--dark);
        }

        .location-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid var(--light-gray);
            transition: all 0.3s;
            cursor: pointer;
        }

        .location-item:hover {
            background-color: var(--light-gray);
        }

        .location-item.active {
            background-color: rgba(255, 56, 92, 0.05);
            border-left: 4px solid var(--primary);
        }

        .location-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--light-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--primary);
            font-size: 18px;
        }

        .location-details {
            flex: 1;
        }

        .location-details h4 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--dark);
        }

        .location-details p {
            font-size: 13px;
            color: var(--text);
            margin-bottom: 5px;
        }

        .location-stats-small {
            display: flex;
            gap: 15px;
            margin-top: 5px;
        }

        .stat-small {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            color: var(--text);
        }

        .stat-small i {
            color: var(--primary);
            font-size: 11px;
        }

        .location-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
        }

        .badge-available {
            background: #d4edda;
            color: #155724;
        }

        .badge-occupied {
            background: #fff3cd;
            color: #856404;
        }

        .badge-maintenance {
            background: #f8d7da;
            color: #721c24;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: var(--text);
        }

        .empty-state i {
            font-size: 48px;
            color: var(--gray);
            margin-bottom: 15px;
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

        @media (max-width: 992px) {
            .sidebar {
                width: 220px;
            }
            .main-content {
                margin-left: 220px;
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
            .location-content {
                grid-template-columns: 1fr;
            }
            .location-controls {
                flex-direction: column;
                align-items: stretch;
            }
            .search-box {
                width: 100%;
            }
            .location-stats {
                justify-content: center;
            }
            .filter-controls {
                justify-content: center;
            }
            #map {
                height: 350px;
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
                        <a href="propertylistings.php"><i class="fas fa-edit"></i> Listings</a>
                        <a href="location.php" class="active"><i class="fas fa-map-marker-alt"></i> Manage Location</a>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#"><i class="fas fa-users"></i> Tenants <span class="notification-badge"><?php echo $stats['occupied'] ?? 0; ?></span></a> 
                    <div class="dropdown-content">
                        <a href="view-tenant.php"><i class="fas fa-list"></i> View Tenants</a>
                        <a href="tenant-bookings.php"><i class="fas fa-calendar-check"></i> Tenant Bookings</a>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#"><i class="fas fa-question-circle"></i> Inquiries <span class="notification-badge"><?php echo $unreadInquiries; ?></span></a>
                    <div class="dropdown-content">
                        <a href="landlordinquiries.php"><i class="fas fa-inbox"></i> Inquiries</a>
                        <a href="#"><i class="fas fa-comments"></i> Chat</a>
                    </div>
                </li>
                <li><a href="payments.php"><i class="fas fa-credit-card"></i> Payments <span class="notification-badge"><?php echo $pendingPayments; ?></span></a></li>
                <li><a href="announcements.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
                <li><a href="landlordreports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                <li><a href="profilesettings.php"><i class="fas fa-user-cog"></i> Profile</a></li>
                <li><a href="notifications.php"><i class="fas fa-bell"></i> Notifications <span class="notification-badge"><?php echo $unreadNotifications; ?></span></a></li>
                <li><a href="support.php"><i class="fas fa-headset"></i> Support</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <nav class="navbar">
                <div class="navbar-brand">Property Locations</div>
                
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
                            <a href="profilesettings.php"><i class="fas fa-user"></i> My Profile</a>
                            <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                            <hr>
                            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Location Section -->
            <div class="location-container">
                <div class="location-header">
                    <h1><i class="fas fa-map-marked-alt"></i> Property Locations</h1>
                    <p>View all your properties on an interactive map</p>
                </div>
                
                <div class="location-controls">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="search-location" placeholder="Search by property name, city, or address...">
                    </div>
                    
                    <div class="location-stats">
                        <div class="stat-card">
                            <div class="value" id="total-properties"><?php echo count($properties); ?></div>
                            <div class="label">Properties</div>
                        </div>
                        <div class="stat-card">
                            <div class="value" id="available-properties"><?php echo $stats['available'] ?? 0; ?></div>
                            <div class="label">Available</div>
                        </div>
                        <div class="stat-card">
                            <div class="value" id="occupied-properties"><?php echo $stats['occupied'] ?? 0; ?></div>
                            <div class="label">Occupied</div>
                        </div>
                    </div>
                    
                    <div class="filter-controls">
                        <select id="status-filter">
                            <option value="all">All Status</option>
                            <option value="available">Available</option>
                            <option value="occupied">Occupied</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                        <select id="type-filter">
                            <option value="all">All Types</option>
                            <option value="apartment">Apartments</option>
                            <option value="house">Houses</option>
                            <option value="studio">Studios</option>
                            <option value="bedsitter">Bedsitters</option>
                        </select>
                    </div>
                </div>
                
                <div class="location-content">
                    <div class="map-container">
                        <h3><i class="fas fa-map"></i> Interactive Map</h3>
                        <div id="map"></div>
                    </div>
                    
                    <div class="location-list">
                        <h3><i class="fas fa-list"></i> Property Locations</h3>
                        <div id="properties-list">
                            <!-- Properties will be loaded here -->
                            <div class="empty-state">
                                <i class="fas fa-spinner fa-spin"></i>
                                <p>Loading properties...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <footer class="footer">
        <img src="assets/icons/smartlogo.png" alt="SmartHunt Logo">
        <p>&copy; <?php echo date('Y'); ?> SmartHunt. All rights reserved.</p>
    </footer>

    <script>
        // Property data from PHP
        const properties = <?php echo json_encode($properties); ?>;
        let map;
        let markers = [];
        let currentPopup = null;

        // Nairobi coordinates (center of Kenya)
        const nairobiCoordinates = [-1.2921, 36.8219];

        // Initialize map
        function initMap() {
            // Create map instance
            map = L.map('map').setView(nairobiCoordinates, 12);
            
            // Add tile layer (OpenStreetMap)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);
            
            // Add properties to map
            addPropertiesToMap(properties);
            
            // Fit bounds to show all markers
            if (markers.length > 0) {
                const group = L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.1));
            }
        }

        // Add properties to map
        function addPropertiesToMap(propertiesList) {
            // Clear existing markers
            markers.forEach(marker => map.removeLayer(marker));
            markers = [];
            
            // Add new markers
            propertiesList.forEach(property => {
                // Use property coordinates if available, otherwise use city center approximation
                let lat, lng;
                
                if (property.latitude && property.longitude) {
                    lat = parseFloat(property.latitude);
                    lng = parseFloat(property.longitude);
                } else {
                    // Approximate coordinates based on city
                    const cityCoords = getCityCoordinates(property.city);
                    lat = cityCoords.lat;
                    lng = cityCoords.lng;
                }
                
                // Get status color
                let markerColor = getMarkerColor(property.status);
                
                // Create custom marker icon
                const markerIcon = L.divIcon({
                    className: 'custom-marker',
                    html: `<div style="background-color: ${markerColor}; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                            <i class="fas fa-home"></i>
                          </div>`,
                    iconSize: [30, 30],
                    popupAnchor: [0, -15]
                });
                
                // Create marker
                const marker = L.marker([lat, lng], { icon: markerIcon }).addTo(map);
                
                // Create popup content
                const popupContent = `
                    <div style="min-width: 200px; max-width: 250px;">
                        <h4 style="color: var(--primary); margin-bottom: 5px;">${escapeHtml(property.property_name)}</h4>
                        <p style="margin-bottom: 5px;"><i class="fas fa-map-marker-alt"></i> ${escapeHtml(property.neighborhood || '')}, ${escapeHtml(property.city || '')}</p>
                        <p style="margin-bottom: 5px;"><strong>KES ${formatNumber(property.monthly_rent)}/month</strong></p>
                        <p style="margin-bottom: 10px;">
                            <span class="badge ${getStatusClass(property.status)}">${property.status}</span>
                        </p>
                        <div style="display: flex; gap: 8px;">
                            <button onclick="viewProperty(${property.id})" style="background: var(--primary); color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">View Details</button>
                            <button onclick="getDirections(${property.id})" style="background: var(--secondary); color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Directions</button>
                        </div>
                    </div>
                `;
                
                marker.bindPopup(popupContent);
                
                // Store property data with marker
                marker.property = property;
                markers.push(marker);
                
                // Add click event
                marker.on('click', () => {
                    if (currentPopup) {
                        currentPopup.close();
                    }
                    marker.openPopup();
                    currentPopup = marker;
                    highlightPropertyInList(property.id);
                });
            });
        }

        // Get city coordinates
        function getCityCoordinates(city) {
            const coordinates = {
                'Nairobi': [-1.2921, 36.8219],
                'Mombasa': [-4.0435, 39.6682],
                'Kisumu': [-0.0917, 34.7680],
                'Nakuru': [-0.3031, 36.0800],
                'Eldoret': [0.5143, 35.2698],
                'Thika': [-1.0388, 37.0833],
                'Machakos': [-1.5177, 37.2634],
                'Meru': [0.0500, 37.6500],
                'Nyeri': [-0.4194, 36.9500],
                'Kitale': [1.0167, 35.0000]
            };
            return coordinates[city] ? { lat: coordinates[city][0], lng: coordinates[city][1] } : nairobiCoordinates;
        }

        // Get marker color based on status
        function getMarkerColor(status) {
            switch(status) {
                case 'available': return '#00A699';
                case 'occupied': return '#FFB400';
                case 'maintenance': return '#FF5A5F';
                default: return '#FF385C';
            }
        }

        // Get status class for badge
        function getStatusClass(status) {
            switch(status) {
                case 'available': return 'badge-available';
                case 'occupied': return 'badge-occupied';
                case 'maintenance': return 'badge-maintenance';
                default: return '';
            }
        }

        // Escape HTML
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Format number
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        // Display properties in list
        function displayPropertiesList(propertiesList) {
            const container = document.getElementById('properties-list');
            
            if (propertiesList.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-map-marker-alt"></i>
                        <h3>No Properties Found</h3>
                        <p>No properties match your search criteria.</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = propertiesList.map(property => `
                <div class="location-item" data-id="${property.id}" onclick="focusOnProperty(${property.id})">
                    <div class="location-icon">
                        <i class="fas fa-${getPropertyIcon(property.property_type)}"></i>
                    </div>
                    <div class="location-details">
                        <h4>${escapeHtml(property.property_name)}</h4>
                        <p><i class="fas fa-map-marker-alt"></i> ${escapeHtml(property.neighborhood || '')}, ${escapeHtml(property.city || '')}</p>
                        <div class="location-stats-small">
                            <span class="stat-small"><i class="fas fa-bed"></i> ${property.bedrooms || 0} beds</span>
                            <span class="stat-small"><i class="fas fa-bath"></i> ${property.bathrooms || 0} baths</span>
                            <span class="stat-small"><i class="fas fa-ruler-combined"></i> ${property.sqft || 0} sqft</span>
                        </div>
                    </div>
                    <div>
                        <span class="location-badge ${getStatusClass(property.status)}">
                            ${property.status}
                        </span>
                    </div>
                </div>
            `).join('');
        }

        // Get property icon
        function getPropertyIcon(type) {
            const icons = {
                apartment: 'building',
                house: 'home',
                studio: 'paintbrush',
                bedsitter: 'bed',
                condo: 'building'
            };
            return icons[type] || 'home';
        }

        // Focus on property on map
        function focusOnProperty(propertyId) {
            const property = properties.find(p => p.id === propertyId);
            if (property) {
                let lat, lng;
                
                if (property.latitude && property.longitude) {
                    lat = parseFloat(property.latitude);
                    lng = parseFloat(property.longitude);
                } else {
                    const coords = getCityCoordinates(property.city);
                    lat = coords.lat;
                    lng = coords.lng;
                }
                
                map.setView([lat, lng], 15);
                
                // Find and open marker popup
                const marker = markers.find(m => m.property.id === propertyId);
                if (marker) {
                    marker.openPopup();
                    highlightPropertyInList(propertyId);
                }
            }
        }

        // Highlight property in list
        function highlightPropertyInList(propertyId) {
            document.querySelectorAll('.location-item').forEach(item => {
                item.classList.remove('active');
                if (item.dataset.id == propertyId) {
                    item.classList.add('active');
                    item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            });
        }

        // Filter properties
        function filterProperties() {
            const searchTerm = document.getElementById('search-location').value.toLowerCase();
            const statusFilter = document.getElementById('status-filter').value;
            const typeFilter = document.getElementById('type-filter').value;
            
            let filtered = properties;
            
            // Apply search
            if (searchTerm) {
                filtered = filtered.filter(p => 
                    p.property_name?.toLowerCase().includes(searchTerm) ||
                    p.city?.toLowerCase().includes(searchTerm) ||
                    p.neighborhood?.toLowerCase().includes(searchTerm) ||
                    p.address?.toLowerCase().includes(searchTerm)
                );
            }
            
            // Apply status filter
            if (statusFilter !== 'all') {
                filtered = filtered.filter(p => p.status === statusFilter);
            }
            
            // Apply type filter
            if (typeFilter !== 'all') {
                filtered = filtered.filter(p => p.property_type === typeFilter);
            }
            
            // Update display
            displayPropertiesList(filtered);
            addPropertiesToMap(filtered);
        }

        // View property details
        function viewProperty(propertyId) {
            window.location.href = `property.php?id=${propertyId}`;
        }

        // Get directions
        function getDirections(propertyId) {
            const property = properties.find(p => p.id === propertyId);
            if (property) {
                let address = `${property.address || ''}, ${property.neighborhood || ''}, ${property.city || ''}`;
                const mapsUrl = `https://www.google.com/maps/search/${encodeURIComponent(address)}`;
                window.open(mapsUrl, '_blank');
            }
        }

        // Update statistics
        function updateStatistics() {
            const total = properties.length;
            const available = properties.filter(p => p.status === 'available').length;
            const occupied = properties.filter(p => p.status === 'occupied').length;
            
            document.getElementById('total-properties').textContent = total;
            document.getElementById('available-properties').textContent = available;
            document.getElementById('occupied-properties').textContent = occupied;
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize map
            initMap();
            
            // Display properties list
            displayPropertiesList(properties);
            
            // Update statistics
            updateStatistics();
            
            // Add event listeners for filters
            document.getElementById('search-location').addEventListener('input', filterProperties);
            document.getElementById('status-filter').addEventListener('change', filterProperties);
            document.getElementById('type-filter').addEventListener('change', filterProperties);
        });
    </script>
</body>
</html>