<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/icons/smartlogo.png">
    <title>SmartHunt - Landlord Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* All previous CSS styles remain exactly the same */
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

        .content h1 {
            font-size: 28px;
            margin-bottom: 5px;
            color: var(--dark);
        }

        .content > p {
            color: var(--text);
            margin-bottom: 25px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: var(--light);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .card p {
            margin-bottom: 15px;
            font-size: 15px;
        }

        .card button {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 8px;
            margin-top: 10px;
            transition: background-color 0.3s;
        }

        .card button:hover {
            background-color: #e61e4d;
        }

        .progress-bar {
            width: 100%;
            background-color: var(--gray);
            border-radius: 10px;
            margin: 10px 0;
            height: 10px;
        }

        .progress {
            height: 100%;
            background-color: var(--success);
            border-radius: 10px;
            width: 0%;
            transition: width 0.5s ease;
        }

        .alert {
            background-color: #fff3cd;
            border-left: 4px solid var(--warning);
            padding: 10px 15px;
            margin: 10px 0;
            border-radius: 4px;
            font-size: 14px;
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

        .hidden {
            display: none;
        }

        .notification-badge {
            background-color: var(--danger);
            color: white;
            border-radius: 50%;
            padding: 3px 8px;
            font-size: 12px;
            margin-left: 5px;
        }

        /* Location Styles - Maintaining existing styling patterns */
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
            text-align: center;
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
        }

        .search-box {
            position: relative;
            width: 300px;
        }

        .search-box input {
            padding-left: 40px;
            border-radius: 20px;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text);
        }

        .filter-controls {
            display: flex;
            gap: 15px;
        }

        .filter-controls select {
            padding: 8px 15px;
            border-radius: 20px;
            border: 1px solid var(--gray);
            background-color: var(--light);
        }

        .location-stats {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: var(--light-gray);
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            min-width: 120px;
        }

        .stat-card .value {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
        }

        .stat-card .label {
            font-size: 14px;
            color: var(--text);
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
            height: 500px;
        }

        .map-container h3 {
            font-size: 18px;
            margin-bottom: 15px;
            color: var(--dark);
            text-align: center;
        }

        .map-placeholder {
            background: var(--light-gray);
            border-radius: 8px;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text);
            font-size: 16px;
        }

        .location-list {
            background: var(--light);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            max-height: 500px;
            overflow-y: auto;
        }

        .location-list h3 {
            font-size: 18px;
            margin-bottom: 20px;
            color: var(--dark);
            text-align: center;
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
            width: 40px;
            height: 40px;
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
            font-size: 14px;
            color: var(--text);
            margin-bottom: 5px;
        }

        .location-stats-small {
            display: flex;
            gap: 15px;
            margin-top: 8px;
        }

        .stat-small {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            color: var(--text);
        }

        .stat-small i {
            color: var(--primary);
        }

        .location-actions {
            display: flex;
            gap: 8px;
        }

        .location-btn {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
        }

        .btn-view {
            background-color: var(--secondary);
            color: white;
        }

        .btn-view:hover {
            background-color: #3367d6;
        }

        .btn-edit {
            background-color: var(--warning);
            color: white;
        }

        .btn-edit:hover {
            background-color: #e6a200;
        }

        .location-details-container {
            background: var(--light);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-top: 30px;
        }

        .location-details-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--light-gray);
        }

        .location-details-header h2 {
            font-size: 24px;
            color: var(--dark);
        }

        .location-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-card {
            background: var(--light-gray);
            padding: 20px;
            border-radius: 8px;
        }

        .info-card h4 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .info-card p {
            font-size: 14px;
            color: var(--text);
            line-height: 1.6;
        }

        .amenities-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .amenity-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--text);
        }

        .amenity-item i {
            color: var(--success);
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 50px 30px;
            color: var(--text);
        }

        .empty-state i {
            font-size: 60px;
            color: var(--gray);
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 22px;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .empty-state p {
            margin-bottom: 20px;
        }

        /* Responsive Design */
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
            .cards {
                grid-template-columns: 1fr;
            }
            .location-controls {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }
            .location-stats {
                justify-content: center;
            }
            .filter-controls {
                justify-content: center;
            }
            .location-content {
                grid-template-columns: 1fr;
            }
            .location-info-grid {
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
                    <a href="#" data-content="properties"><i class="fas fa-building"></i> Properties</a>
                    <div class="dropdown-content">
                        <a href="#" data-content="add-property"><i class="fas fa-plus"></i> Add Property</a>
                        <a href="#" data-content="edit-listings"><i class="fas fa-edit"></i>Listings</a>
                        <a href="#" data-content="manage-location"><i class="fas fa-map-marker-alt"></i> Manage Location</a>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" data-content="tenants"><i class="fas fa-users"></i> Tenants <span class="notification-badge">3</span></a> 
                    <div class="dropdown-content">
                        <a href="#" data-content="view-tenants"><i class="fas fa-list"></i> View Tenants</a>
                        <a href="#" data-content="tenant-bookings"><i class="fas fa-calendar-check"></i> Tenant Bookings</a>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" data-content="inquiries"><i class="fas fa-question-circle"></i> Inquiries <span class="notification-badge">5</span></a>
                    <div class="dropdown-content">
                        <a href="#" data-content="inquiries-list"><i class="fas fa-inbox"></i> Inquiries</a>
                        <a href="#" data-content="chat"><i class="fas fa-comments"></i> Chat</a>
                    </div>
                </li>
                <li><a href="#" data-content="payments"><i class="fas fa-credit-card"></i> Payments <span class="notification-badge">2</span></a></li>
                <li><a href="#" class="active" data-content="location"><i class="fas fa-map-marked-alt"></i> Location</a></li>
                <li><a href="#" data-content="announcements"><i class="fas fa-bullhorn"></i> Announcements</a></li>
                <li><a href="#" data-content="reports"><i class="fas fa-chart-bar"></i> Reports</a></li>
                <li><a href="#" data-content="profile-settings"><i class="fas fa-user-cog"></i> Profile Setting</a></li>
                <li><a href="#" data-content="notifications"><i class="fas fa-bell"></i> Notifications <span class="notification-badge">7</span></a></li>
                <li><a href="support.php" data-content="support"><i class="fas fa-headset"></i> Support</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <nav class="navbar">
                <div class="navbar-brand">Landlord Dashboard</div>
                
                <div class="login-image">
                    <div class="dropdown">
                        <a href="#"><img src="https://placehold.co/40x40/4285F4/FFFFFF?text=U" alt="User Icon"></a>
                        <div class="dropdown-content">
                            <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                            <a href="register.php"><i class="fas fa-user-plus"></i> Sign Up</a> 
                            <a href="profilesettings.php"><i class="fas fa-cog"></i> Settings</a>
                            <a href="#"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>    
                </div>
            </nav>

            <!-- Location Section -->
            <section class="content" id="location-content">
                <div class="location-container">
                    <div class="location-header">
                        <h1><i class="fas fa-map-marked-alt"></i> Property Locations</h1>
                        <p>Manage and view all your property locations on an interactive map</p>
                    </div>
                    
                    <div class="location-controls">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="search-locations" placeholder="Search locations...">
                        </div>
                        
                        <div class="location-stats">
                            <div class="stat-card">
                                <div class="value" id="total-locations">6</div>
                                <div class="label">Total Locations</div>
                            </div>
                            <div class="stat-card">
                                <div class="value" id="occupied-locations">4</div>
                                <div class="label">Occupied</div>
                            </div>
                            <div class="stat-card">
                                <div class="value" id="vacant-locations">2</div>
                                <div class="label">Vacant</div>
                            </div>
                        </div>
                        
                        <div class="filter-controls">
                            <select id="location-status-filter">
                                <option value="all">All Properties</option>
                                <option value="occupied">Occupied</option>
                                <option value="vacant">Vacant</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                            <select id="location-type-filter">
                                <option value="all">All Types</option>
                                <option value="apartment">Apartments</option>
                                <option value="house">Houses</option>
                                <option value="commercial">Commercial</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="location-content">
                        <div class="map-container">
                            <h3>Property Locations Map</h3>
                            <div class="map-placeholder">
                                <div>
                                    <i class="fas fa-map-marked-alt" style="font-size: 48px; margin-bottom: 15px; color: var(--primary);"></i>
                                    <p>Interactive Map View</p>
                                    <p style="font-size: 14px; margin-top: 10px;">Properties are displayed on the map</p>
                                    <button class="location-btn btn-view" style="margin-top: 15px;">
                                        <i class="fas fa-sync-alt"></i> Refresh Map
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="location-list">
                            <h3>Property Locations</h3>
                            <div id="locations-list">
                                <!-- Locations will be dynamically populated here -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="location-details-container" id="location-details">
                        <!-- Location details will be dynamically populated here -->
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <img src="https://placehold.co/100x30/FFFFFF/FF385C?text=SmartHunt" alt="SmartHunt Logo"> 
        <h6>&copy; Algorithm-X Softwares. <br>All rights reserved</h6>
    </footer>

    <script>
        // Sample location data
        const locationsData = [
            {
                id: 1,
                name: "Tripple A Apartments",
                address: "123 Main Gate Road, Campus Area",
                city: "Nairobi",
                type: "apartment",
                status: "occupied",
                coordinates: { lat: -1.2921, lng: 36.8219 },
                properties: 12,
                occupied: 8,
                vacant: 4,
                amenities: ["Wi-Fi", "Parking", "Security", "Water", "Electricity"],
                description: "Modern apartments located in the heart of campus area, perfect for students.",
                contact: "+254 712 345 678",
                rating: 4.5
            },
            {
                id: 2,
                name: "Green Valley Homes",
                address: "456 University Road, Westlands",
                city: "Nairobi",
                type: "house",
                status: "occupied",
                coordinates: { lat: -1.2580, lng: 36.7928 },
                properties: 8,
                occupied: 3,
                vacant: 5,
                amenities: ["Garden", "Parking", "Security", "Water", "Electricity", "Laundry"],
                description: "Spacious bedsitters with beautiful garden views in a quiet neighborhood.",
                contact: "+254 723 456 789",
                rating: 4.2
            },
            {
                id: 3,
                name: "Campus View Apartments",
                address: "789 Student Lane, Kilimani",
                city: "Nairobi",
                type: "apartment",
                status: "occupied",
                coordinates: { lat: -1.3000, lng: 36.7800 },
                properties: 6,
                occupied: 6,
                vacant: 0,
                amenities: ["Wi-Fi", "Parking", "Security", "Gym", "Pool", "24/7 Electricity"],
                description: "Luxury 1-bedroom apartments with stunning campus views and premium amenities.",
                contact: "+254 734 567 890",
                rating: 4.8
            },
            {
                id: 4,
                name: "Sunset Heights",
                address: "321 Hilltop Avenue, Kileleshwa",
                city: "Nairobi",
                type: "apartment",
                status: "vacant",
                coordinates: { lat: -1.2700, lng: 36.7900 },
                properties: 4,
                occupied: 2,
                vacant: 2,
                amenities: ["Parking", "Security", "Water", "Electricity", "Balcony"],
                description: "Modern 2-bedroom apartments perfect for students and young professionals.",
                contact: "+254 745 678 901",
                rating: 4.3
            },
            {
                id: 5,
                name: "River Side Apartments",
                address: "654 Riverside Drive, Karen",
                city: "Nairobi",
                type: "house",
                status: "occupied",
                coordinates: { lat: -1.3200, lng: 36.7100 },
                properties: 10,
                occupied: 10,
                vacant: 0,
                amenities: ["Wi-Fi", "Parking", "Security", "Water", "Garden"],
                description: "Affordable single rooms with shared amenities in a serene environment.",
                contact: "+254 756 789 012",
                rating: 4.0
            },
            {
                id: 6,
                name: "Downtown Suites",
                address: "987 Central Business District",
                city: "Nairobi",
                type: "commercial",
                status: "maintenance",
                coordinates: { lat: -1.2860, lng: 36.8230 },
                properties: 5,
                occupied: 0,
                vacant: 5,
                amenities: ["Wi-Fi", "Parking", "Security", "Elevator", "24/7 Access"],
                description: "Currently under renovation. Modern commercial spaces coming soon.",
                contact: "+254 767 890 123",
                rating: 0
            }
        ];

        // Function to render locations
        function renderLocations(locations = locationsData) {
            const locationsList = document.getElementById('locations-list');
            
            if (locations.length === 0) {
                locationsList.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-map-marker-alt"></i>
                        <h3>No Locations Found</h3>
                        <p>You don't have any locations matching your search criteria.</p>
                    </div>
                `;
                return;
            }
            
            // Update location statistics
            updateLocationStats(locations);
            
            locationsList.innerHTML = locations.map(location => `
                <div class="location-item" data-id="${location.id}" onclick="showLocationDetails(${location.id})">
                    <div class="location-icon">
                        <i class="fas fa-${getLocationIcon(location.type)}"></i>
                    </div>
                    <div class="location-details">
                        <h4>${location.name}</h4>
                        <p>${location.address}</p>
                        <p style="color: var(--primary); font-size: 12px;">
                            <i class="fas fa-map-marker-alt"></i> ${location.city}
                        </p>
                        <div class="location-stats-small">
                            <div class="stat-small">
                                <i class="fas fa-home"></i>
                                <span>${location.properties} units</span>
                            </div>
                            <div class="stat-small">
                                <i class="fas fa-user-check"></i>
                                <span>${location.occupied} occupied</span>
                            </div>
                            <div class="stat-small">
                                <i class="fas fa-door-open"></i>
                                <span>${location.vacant} vacant</span>
                            </div>
                        </div>
                    </div>
                    <div class="location-actions">
                        <button class="location-btn btn-view" onclick="event.stopPropagation(); viewOnMap(${location.id})">
                            <i class="fas fa-map"></i> Map
                        </button>
                        <button class="location-btn btn-edit" onclick="event.stopPropagation(); editLocation(${location.id})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                </div>
            `).join('');
        }

        // Function to show location details
        function showLocationDetails(locationId) {
            const location = locationsData.find(l => l.id === locationId);
            if (location) {
                // Update active state
                document.querySelectorAll('.location-item').forEach(item => {
                    item.classList.remove('active');
                });
                document.querySelector(`.location-item[data-id="${locationId}"]`).classList.add('active');
                
                const locationDetails = document.getElementById('location-details');
                
                locationDetails.innerHTML = `
                    <div class="location-details-header">
                        <h2>${location.name}</h2>
                        <span class="payment-status status-${location.status}">
                            ${location.status.charAt(0).toUpperCase() + location.status.slice(1)}
                        </span>
                    </div>
                    
                    <div class="location-info-grid">
                        <div class="info-card">
                            <h4><i class="fas fa-map-marker-alt"></i> Address</h4>
                            <p>${location.address}<br>${location.city}</p>
                        </div>
                        
                        <div class="info-card">
                            <h4><i class="fas fa-info-circle"></i> Property Details</h4>
                            <p><strong>Type:</strong> ${getLocationType(location.type)}</p>
                            <p><strong>Total Units:</strong> ${location.properties}</p>
                            <p><strong>Occupied:</strong> ${location.occupied}</p>
                            <p><strong>Vacant:</strong> ${location.vacant}</p>
                            ${location.rating > 0 ? `<p><strong>Rating:</strong> ${location.rating}/5 <i class="fas fa-star" style="color: var(--warning);"></i></p>` : ''}
                        </div>
                        
                        <div class="info-card">
                            <h4><i class="fas fa-phone"></i> Contact Information</h4>
                            <p><strong>Phone:</strong> ${location.contact}</p>
                            <p><strong>Email:</strong> info@${location.name.toLowerCase().replace(/\s+/g, '')}.com</p>
                        </div>
                        
                        <div class="info-card">
                            <h4><i class="fas fa-concierge-bell"></i> Amenities</h4>
                            <div class="amenities-list">
                                ${location.amenities.map(amenity => `
                                    <div class="amenity-item">
                                        <i class="fas fa-check-circle"></i>
                                        <span>${amenity}</span>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <h4><i class="fas fa-align-left"></i> Description</h4>
                        <p>${location.description}</p>
                    </div>
                    
                    <div style="display: flex; gap: 10px; margin-top: 20px;">
                        <button class="location-btn btn-view" onclick="viewOnMap(${location.id})">
                            <i class="fas fa-map-marked-alt"></i> View on Map
                        </button>
                        <button class="location-btn btn-edit" onclick="editLocation(${location.id})">
                            <i class="fas fa-edit"></i> Edit Location
                        </button>
                        <button class="location-btn" style="background-color: var(--primary); color: white;" onclick="shareLocation(${location.id})">
                            <i class="fas fa-share"></i> Share Location
                        </button>
                    </div>
                `;
            }
        }

        // Function to update location statistics
        function updateLocationStats(locations) {
            const totalLocations = locations.length;
            const occupiedLocations = locations.filter(loc => loc.status === 'occupied').length;
            const vacantLocations = locations.filter(loc => loc.status === 'vacant').length;
            
            document.getElementById('total-locations').textContent = totalLocations;
            document.getElementById('occupied-locations').textContent = occupiedLocations;
            document.getElementById('vacant-locations').textContent = vacantLocations;
        }

        // Helper function to get location icon
        function getLocationIcon(type) {
            const icons = {
                apartment: 'building',
                house: 'home',
                commercial: 'store'
            };
            return icons[type] || 'map-marker-alt';
        }

        // Helper function to get location type display name
        function getLocationType(type) {
            const types = {
                apartment: 'Apartment Building',
                house: 'Residential House',
                commercial: 'Commercial Property'
            };
            return types[type] || type;
        }

        // Function to filter locations
        function filterLocations() {
            const searchTerm = document.getElementById('search-locations').value.toLowerCase();
            const statusFilter = document.getElementById('location-status-filter').value;
            const typeFilter = document.getElementById('location-type-filter').value;
            
            const filteredLocations = locationsData.filter(location => {
                const matchesSearch = location.name.toLowerCase().includes(searchTerm) || 
                                     location.address.toLowerCase().includes(searchTerm) ||
                                     location.city.toLowerCase().includes(searchTerm);
                const matchesStatus = statusFilter === 'all' || location.status === statusFilter;
                const matchesType = typeFilter === 'all' || location.type === typeFilter;
                
                return matchesSearch && matchesStatus && matchesType;
            });
            
            renderLocations(filteredLocations);
            
            // Clear details if no locations or if current detail is not in filtered list
            const currentDetailId = document.querySelector('.location-item.active')?.dataset.id;
            if (filteredLocations.length === 0 || !filteredLocations.find(l => l.id == currentDetailId)) {
                document.getElementById('location-details').innerHTML = '';
            }
        }

        // Location action functions
        function viewOnMap(locationId) {
            const location = locationsData.find(l => l.id === locationId);
            if (location) {
                alert(`Showing ${location.name} on the map at coordinates: ${location.coordinates.lat}, ${location.coordinates.lng}`);
                // In real application, this would center the map on the location
            }
        }

        function editLocation(locationId) {
            const location = locationsData.find(l => l.id === locationId);
            if (location) {
                alert(`Editing location: ${location.name}`);
                // In real application, this would open an edit form
            }
        }

        function shareLocation(locationId) {
            const location = locationsData.find(l => l.id === locationId);
            if (location) {
                alert(`Sharing location: ${location.name}\nAddress: ${location.address}`);
                // In real application, this would open a share dialog
            }
        }

        // Initialize locations when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Set location as active
            document.querySelectorAll('.sidebar-menu a').forEach(link => {
                link.classList.remove('active');
            });
            document.querySelector('[data-content="location"]').classList.add('active');
            
            // Hide all content sections except location
            document.querySelectorAll('.content').forEach(section => {
                if (section.id !== 'location-content') {
                    section.classList.add('hidden');
                }
            });
            
            renderLocations();
            
            // Show details for first location by default
            if (locationsData.length > 0) {
                showLocationDetails(locationsData[0].id);
            }
            
            // Add event listeners for filtering
            document.getElementById('search-locations').addEventListener('input', filterLocations);
            document.getElementById('location-status-filter').addEventListener('change', filterLocations);
            document.getElementById('location-type-filter').addEventListener('change', filterLocations);
        });
    </script>
</body>
</html>