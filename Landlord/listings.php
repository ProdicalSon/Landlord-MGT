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

        /* Dashboard Layout */
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

        /* Content Section */
        .content h1 {
            font-size: 28px;
            margin-bottom: 5px;
            color: var(--dark);
        }

        .content > p {
            color: var(--text);
            margin-bottom: 25px;
        }

        /* Cards Layout */
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

        /* Progress Bar */
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

        /* Alert Box */
        .alert {
            background-color: #fff3cd;
            border-left: 4px solid var(--warning);
            padding: 10px 15px;
            margin: 10px 0;
            border-radius: 4px;
            font-size: 14px;
        }

        /* Footer */
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

        /* Add property form styles */
        .property-form-container {
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
            text-align: center;
        }

        .form-header h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .form-header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .property-form {
            padding: 30px;
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

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }

        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--gray);
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255, 56, 92, 0.2);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .file-input-container {
            position: relative;
        }

        .file-input-container input[type="file"] {
            padding: 10px;
            background: var(--light-gray);
            border: 1px dashed var(--gray);
        }

        .file-input-container input[type="file"]::file-selector-button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            margin-right: 15px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .file-input-container input[type="file"]::file-selector-button:hover {
            background: var(--primary-light);
        }

        .submit-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 14px 25px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: block;
            width: 100%;
            margin-top: 20px;
            transition: all 0.3s;
        }

        .submit-btn:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 56, 92, 0.3);
        }

        .form-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--light-gray);
        }

        .form-section h2 {
            font-size: 20px;
            color: var(--primary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .form-section h2 i {
            margin-right: 10px;
        }

        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .amenity-checkbox {
            display: flex;
            align-items: center;
        }

        .amenity-checkbox input {
            width: auto;
            margin-right: 10px;
        }

        /* Form validation styles */
        input:invalid, select:invalid, textarea:invalid {
            border-color: var(--danger);
        }
        
        input:valid, select:valid, textarea:valid {
            border-color: var(--success);
        }

        .character-count {
            text-align: right;
            font-size: 12px;
            color: var(--text);
            margin-top: 5px;
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
            .cards, .form-grid {
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
            
            .property-form {
                padding: 20px;
            }
        }

        /* Additional UI Improvements */
        .stats-container {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }

        .stat-item {
            text-align: center;
            padding: 10px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary);
        }

        .stat-label {
            font-size: 14px;
            color: var(--text);
        }

        .quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .action-btn {
            background-color: var(--light-gray);
            border: 1px solid var(--gray);
            padding: 8px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }

        .action-btn:hover {
            background-color: var(--primary);
            color: white;
        }

        .notification-badge {
            background-color: var(--danger);
            color: white;
            border-radius: 50%;
            padding: 3px 8px;
            font-size: 12px;
            margin-left: 5px;
        }

        /* Hidden class for toggling content */
        .hidden {
            display: none;
        }

        /* Chart container styles */
        .chart-container {
            position: relative;
            height: 200px;
            width: 100%;
        }

        .chart-title {
            text-align: center;
            margin-bottom: 10px;
            font-size: 16px;
            font-weight: 600;
            color: var(--dark);
        }

        .chart-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 768px) {
            .chart-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Listings Styles */
        .listings-container {
            background: var(--light);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .listings-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 25px 30px;
            text-align: center;
        }

        .listings-header h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .listings-header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .listings-controls {
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

        .listings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            padding: 30px;
        }

        .listing-card {
            background: var(--light);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }

        .listing-card:hover {
            transform: translateY(-5px);
        }

        .listing-image {
            height: 200px;
            width: 100%;
            background-color: var(--light-gray);
            position: relative;
            overflow: hidden;
        }

        .listing-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .listing-status {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: white;
        }

        .status-occupied {
            background-color: var(--success);
        }

        .status-vacant {
            background-color: var(--warning);
        }

        .status-maintenance {
            background-color: var(--danger);
        }

        .listing-details {
            padding: 20px;
        }

        .listing-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--dark);
        }

        .listing-location {
            display: flex;
            align-items: center;
            color: var(--text);
            font-size: 14px;
            margin-bottom: 10px;
        }

        .listing-location i {
            margin-right: 5px;
            color: var(--primary);
        }

        .listing-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .listing-info-item {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .info-label {
            font-size: 12px;
            color: var(--text);
        }

        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: var(--dark);
        }

        .listing-actions {
            display: flex;
            justify-content: space-between;
        }

        .listing-btn {
            padding: 8px 15px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
        }

        .btn-edit {
            background-color: var(--secondary);
            color: white;
        }

        .btn-edit:hover {
            background-color: #3367d6;
        }

        .btn-delete {
            background-color: var(--danger);
            color: white;
        }

        .btn-delete:hover {
            background-color: #e04a50;
        }

        .btn-view {
            background-color: var(--primary);
            color: white;
        }

        .btn-view:hover {
            background-color: #e61e4d;
        }

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

        .pagination {
            display: flex;
            justify-content: center;
            padding: 20px 0;
            border-top: 1px solid var(--light-gray);
        }

        .pagination button {
            background-color: var(--light);
            border: 1px solid var(--gray);
            padding: 8px 15px;
            margin: 0 5px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .pagination button.active {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .pagination button:hover:not(.active) {
            background-color: var(--light-gray);
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
                    <a href="" data-content="properties"><i class="fas fa-building"></i> Properties</a>
                    <div class="dropdown-content">
                        <a href="addproperty.php" data-content="add-property"><i class="fas fa-plus"></i> Add Property</a>
                        <a href="listings.php" data-content="edit-listings"><i class="fas fa-edit"></i>Listings</a>
                        
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" data-content="tenants"><i class="fas fa-users"></i> Tenants <span class="notification-badge">3</span></a> 
                    <div class="dropdown-content">
                        <a href="view-tenant.php" data-content="view-tenants"><i class="fas fa-list"></i> View Tenants</a>
                        <a href="#" data-content="tenant-bookings"><i class="fas fa-calendar-check"></i> Tenant Bookings</a>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" data-content="inquiries"><i class="fas fa-question-circle"></i> Inquiries <span class="notification-badge">5</span></a>
                    <div class="dropdown-content">
                        <a href="inquiries.php" data-content="inquiries-list"><i class="fas fa-inbox"></i> Inquiries</a>
                        <a href="#" data-content="chat"><i class="fas fa-comments"></i> Chat</a>
                    </div>
                </li>
                <li><a href="#" data-content="payments"><i class="fas fa-credit-card"></i> Payments <span class="notification-badge">2</span></a></li>
                <li><a href="#" data-content="location"><i class="fas fa-map-marked-alt"></i> Location</a></li>
                <li><a href="announcements.php" data-content="announcements"><i class="fas fa-bullhorn"></i> Announcements</a></li>
                <li><a href="reports.php" data-content="reports"><i class="fas fa-chart-bar"></i> Reports</a></li>
                <li><a href="profilesettings.php" data-content="profile-settings"><i class="fas fa-user-cog"></i> Profile Setting</a></li>
                <li><a href="notifications.php" data-content="notifications"><i class="fas fa-bell"></i> Notifications <span class="notification-badge">7</span></a></li>
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

            <section class="content" id="dashboard-content">
                <h1 id="greeting">Welcome Back, Landlord!</h1>
                <p>Manage your properties and track performance</p>

                <div class="cards">
                    <div class="card" id="active-properties-card">
                        <h3><i class="fas fa-building"></i> Active Properties</h3>
                        <p id="active-properties-count">5 Properties</p>
                        <div class="stats-container">
                            <div class="stat-item">
                                <div class="stat-value">3</div>
                                <div class="stat-label">Occupied</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">2</div>
                                <div class="stat-label">Vacant</div>
                            </div>
                        </div>
                        <button>View Details</button>
                    </div>

                    <div class="card">
                        <h3><i class="fas fa-question-circle"></i> Inquiries</h3>
                        <p>12 New Inquiries</p>
                        <div class="quick-actions">
                            <button class="action-btn">Respond to All</button>
                            <button class="action-btn">Sort by Priority</button>
                        </div>
                        <button>Check Now</button> 
                    </div>

                    <div class="card">
                        <h3><i class="fas fa-users"></i> Current Tenants</h3>
                        <p>8 Tenants</p>
                        <div class="stats-container">
                            <div class="stat-item">
                                <div class="stat-value">5</div>
                                <div class="stat-label">Active</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">3</div>
                                <div class="stat-label">Pending</div>
                            </div>
                        </div>
                        <button>Manage Tenants</button> 
                    </div>

                    <div class="card">
                        <h3><i class="fas fa-chart-pie"></i> Occupancy Rate</h3>
                        <p>Occupancy: <span id="occupancyPercentage">0%</span></p>
                        <div class="progress-bar">
                            <div id="progress" class="progress" style="width: 0%;"></div>
                        </div>
                        <div class="quick-actions">
                            <button onclick="occupyRoom()" class="action-btn">Occupy a Room</button>
                            <button onclick="vacateRoom()" class="action-btn">Vacate a Room</button>
                        </div>
                    </div>

                    <div class="card">
                        <h3><i class="fas fa-credit-card"></i> Payments</h3>
                        <p>2 Pending Payments</p>
                        <div class="alert">
                            <p><strong>Alert:</strong> 2 tenants have not paid their rent.</p>
                        </div>
                        <div class="quick-actions">
                            <button class="action-btn">Send Reminders</button>
                            <button class="action-btn">View Payment History</button>
                        </div>
                    </div>

                    <div class="card">
                        <h3><i class="fas fa-bullhorn"></i> Announcements</h3>
                        <p>8 Announcements</p>
                        <div class="quick-actions">
                            <button class="action-btn">Create New</button>
                            <button class="action-btn">Schedule</button>
                        </div>
                        <button class="announcement">View Announcements</button>
                    </div>
                </div>

                <!-- Additional Dashboard Sections -->
                <h2 style="margin: 30px 0 20px;">Recent Activity</h2>
                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h3>Property Performance</h3>
                        <select id="timeRange" style="padding: 5px; border-radius: 4px; border: 1px solid var(--gray);">
                            <option value="7">Last 7 Days</option>
                            <option value="30" selected>Last 30 Days</option>
                            <option value="90">Last 90 Days</option>
                        </select>
                    </div>
                    
                    <div class="chart-grid">
                        <div>
                            <div class="chart-title">Views vs Inquiries</div>
                            <div class="chart-container">
                                <canvas id="viewsInquiriesChart"></canvas>
                            </div>
                        </div>
                        <div>
                            <div class="chart-title">Earnings Overview</div>
                            <div class="chart-container">
                                <canvas id="earningsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Add Property Form (Hidden by default) -->
            <section class="content hidden" id="add-property-content">
                <div class="property-form-container">
                    <div class="form-header">
                        <h1><i class="fas fa-home"></i> Add New Property</h1>
                        <p>List your property to attract potential tenants</p>
                    </div>
                    
                    <div class="property-form">
                        <form action="submit_property.php" method="post" enctype="multipart/form-data">
                            <div class="form-section">
                                <h2><i class="fas fa-info-circle"></i> Basic Information</h2>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="property-name"><i class="fas fa-building"></i> Property Name</label>
                                        <input type="text" id="property-name" name="property_name" placeholder="e.g., Tripple A Apartments" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="property-type"><i class="fas fa-home"></i> Property Type</label>
                                        <select id="property-type" name="property_type" required>
                                            <option value="" disabled selected>Select property type</option>
                                            <option value="Single Rooms">Single Rooms</option>
                                            <option value="Bedsitters">Bedsitters</option>
                                            <option value="Single Rooms & Bedsitters">Single Rooms & Bedsitters</option>
                                            <option value="1B">1 Bedroom</option>
                                            <option value="2B">2 Bedrooms</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="location"><i class="fas fa-map-marker-alt"></i> Location</label>
                                        <input type="text" id="location" name="location" placeholder="e.g., 123 Main Gate, Campus" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="rooms"><i class="fas fa-door-open"></i> Number of Rooms</label>
                                        <input type="number" id="rooms" name="number_of_rooms" min="1" placeholder="How many rooms?" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="price"><i class="fas fa-tag"></i> Price (in Ksh)</label>
                                        <input type="number" id="price" name="price" placeholder="e.g., 5000" min="0" required>
                                    </div>
                                    <div class="form-group full-width">
                                        <label for="description"><i class="fas fa-align-left"></i> Property Description</label>
                                        <textarea id="description" name="property_description" rows="4" placeholder="Describe your property, nearby amenities, and what makes it special"></textarea>
                                        <div class="character-count">0/500 characters</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-section">
                                <h2><i class="fas fa-camera"></i> Media</h2>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="photos"><i class="fas fa-images"></i> Upload Photos</label>
                                        <div class="file-input-container">
                                            <input type="file" id="photos" name="property_photos[]" accept="image/*" multiple required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="rules"><i class="fas fa-file-alt"></i> Upload Property Rules</label>
                                        <div class="file-input-container">
                                            <input type="file" id="rules" name="property_rules[]" accept=".pdf, .doc, .docx, .txt, .ppt" multiple required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-section">
                                <h2><i class="fas fa-concierge-bell"></i> Amenities</h2>
                                <div class="amenities-grid">
                                    <div class="amenity-checkbox">
                                        <input type="checkbox" id="wifi" name="amenities[]" value="wifi">
                                        <label for="wifi">Wi-Fi</label>
                                    </div>
                                    <div class="amenity-checkbox">
                                        <input type="checkbox" id="parking" name="amenities[]" value="parking">
                                        <label for="parking">Parking</label>
                                    </div>
                                    <div class="amenity-checkbox">
                                        <input type="checkbox" id="security" name="amenities[]" value="security">
                                        <label for="security">Security</label>
                                    </div>
                                    <div class="amenity-checkbox">
                                        <input type="checkbox" id="water" name="amenities[]" value="water">
                                        <label for="water">Water Supply</label>
                                    </div>
                                    <div class="amenity-checkbox">
                                        <input type="checkbox" id="electricity" name="amenities[]" value="electricity">
                                        <label for="electricity">24/7 Electricity</label>
                                    </div>
                                    <div class="amenity-checkbox">
                                        <input type="checkbox" id="laundry" name="amenities[]" value="laundry">
                                        <label for="laundry">Laundry</label>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="submit-btn">
                                <i class="fas fa-plus-circle"></i> Submit Property
                            </button>
                        </form>
                    </div>
                </div>
            </section>

            <!-- Listings Section -->
            <section class="content hidden" id="edit-listings-content">
                <div class="listings-container">
                    <div class="listings-header">
                        <h1><i class="fas fa-building"></i> My Property Listings</h1>
                        
                    </div>
                    
                    <div class="listings-controls">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="search-listings" placeholder="Search properties...">
                        </div>
                        <div class="filter-controls">
                            <select id="status-filter">
                                <option value="all">All Status</option>
                                <option value="occupied">Occupied</option>
                                <option value="vacant">Vacant</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                            <select id="type-filter">
                                <option value="all">All Types</option>
                                <option value="single-room">Single Room</option>
                                <option value="bedsitter">Bedsitter</option>
                                <option value="1-bedroom">1 Bedroom</option>
                                <option value="2-bedroom">2 Bedrooms</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="listings-grid" id="listings-grid">
                        <!-- Listings will be dynamically populated here -->
                    </div>
                    
                    <div class="pagination" id="listings-pagination">
                        <!-- Pagination will be dynamically populated here -->
                    </div>
                </div>
            </section>
           
            <section class="content hidden" id="manage-location-content">
                <h1>Manage Location</h1>
                
            </section>
        </main>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <img src="https://placehold.co/100x30/FFFFFF/FF385C?text=SmartHunt" alt="SmartHunt Logo"> 
        <h6>&copy; Algorithm-X Softwares. <br>All rights reserved</h6>
    </footer>

    <script>
        // Sample property listings data
        const propertyListings = [
            {
                id: 1,
                title: "Tripple A Apartments",
                location: "123 Maili Mbili, Behind Campus",
                type: "Bedsitters",
                price: 5000,
                rooms: 15,
                occupied: 8,
                status: "occupied",
                image: src="assets/icons/house-1.png",
                description: "Modern apartments with all amenities included. Close to campus and shopping centers."
            },
            {
                id: 2,
                title: "Green Valley Homes",
                location: "456 University Road",
                type: "Bedsitters",
                price: 4500,
                rooms: 8,
                occupied: 3,
                status: "vacant",
                image: src="assets/icons/IMG_20220328_111353.jpg",
                description: "Spacious bedsitters with beautiful garden views. Secure and quiet neighborhood."
            },
            {
                id: 3,
                title: "Vichmas Flats",
                location: "789 Campus-Kisumu Ndogo Lane",
                type: "Bedsitters",
                price: 6000,
                rooms: 6,
                occupied: 6,
                status: "occupied",
                image: src="assets/icons/h1.png",
                description: "Luxury 1-bedroom apartments with stunning campus views. All utilities included."
            },
            {
                id: 4,
                title: "Sunset Heights",
                location: "321 Hilltop Avenue",
                type: "2 Bedrooms",
                price: 15000,
                rooms: 4,
                occupied: 2,
                status: "vacant",
               image: src="assets/icons/1b.png",
                description: "Modern 2-bedroom apartments perfect for students. Close to public transport."
            },
            {
                id: 5,
                title: "River Side Apartments",
                location: "Way to Roma",
                type: "Single Rooms",
                price: 4500,
                rooms: 10,
                occupied: 10,
                status: "occupied",
                image: src="assets/icons/2b.png",
                description: "Affordable single rooms with shared amenities. Perfect for budget-conscious students."
            },
            {
                id: 6,
                title: "Downtown Suites",
                location: "987 Central Business District",
                type: "Bedsitters",
                price: 5000,
                rooms: 5,
                occupied: 0,
                status: "maintenance",
                image: src="assets/icons/s1.png",
                description: "Currently under renovation. Modern bedsitters coming soon in prime location."
            }
        ];

        // Function to render property listings
        function renderListings(listings = propertyListings) {
            const listingsGrid = document.getElementById('listings-grid');
            
            if (listings.length === 0) {
                listingsGrid.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-home"></i>
                        <h3>No Properties Found</h3>
                        <p>You don't have any properties matching your search criteria.</p>
                        <button class="listing-btn btn-view" onclick="showAddProperty()">Add New Property</button>
                    </div>
                `;
                return;
            }
            
            listingsGrid.innerHTML = listings.map(listing => `
                <div class="listing-card" data-id="${listing.id}">
                    <div class="listing-image">
                        <img src="${listing.image}" alt="${listing.title}">
                        <div class="listing-status status-${listing.status}">
                            ${listing.status.charAt(0).toUpperCase() + listing.status.slice(1)}
                        </div>
                    </div>
                    <div class="listing-details">
                        <h3 class="listing-title">${listing.title}</h3>
                        <div class="listing-location">
                            <i class="fas fa-map-marker-alt"></i>
                            ${listing.location}
                        </div>
                        <p>${listing.description}</p>
                        <div class="listing-info">
                            <div class="listing-info-item">
                                <span class="info-label">Type</span>
                                <span class="info-value">${listing.type}</span>
                            </div>
                            <div class="listing-info-item">
                                <span class="info-label">Price</span>
                                <span class="info-value">Ksh ${listing.price.toLocaleString()}</span>
                            </div>
                            <div class="listing-info-item">
                                <span class="info-label">Occupancy</span>
                                <span class="info-value">${listing.occupied}/${listing.rooms}</span>
                            </div>
                        </div>
                        <div class="listing-actions">
                            <button class="listing-btn btn-edit" onclick="editListing(${listing.id})">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="listing-btn btn-view" onclick="viewListing(${listing.id})">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="listing-btn btn-delete" onclick="deleteListing(${listing.id})">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Function to filter listings
        function filterListings() {
            const searchTerm = document.getElementById('search-listings').value.toLowerCase();
            const statusFilter = document.getElementById('status-filter').value;
            const typeFilter = document.getElementById('type-filter').value;
            
            const filteredListings = propertyListings.filter(listing => {
                const matchesSearch = listing.title.toLowerCase().includes(searchTerm) || 
                                     listing.location.toLowerCase().includes(searchTerm);
                const matchesStatus = statusFilter === 'all' || listing.status === statusFilter;
                const matchesType = typeFilter === 'all' || listing.type.toLowerCase().includes(typeFilter);
                
                return matchesSearch && matchesStatus && matchesType;
            });
            
            renderListings(filteredListings);
        }

        // Function to show add property form
        function showAddProperty() {
            document.querySelectorAll('.content').forEach(section => {
                section.classList.add('hidden');
            });
            
            document.getElementById('add-property-content').classList.remove('hidden');
            
            // Update sidebar active state
            document.querySelectorAll('.sidebar-menu a').forEach(link => {
                link.classList.remove('active');
            });
            document.querySelector('[data-content="add-property"]').classList.add('active');
        }

        // Function to edit a listing
        function editListing(id) {
            alert(`Editing property with ID: ${id}`);
            // In a real application, this would open an edit form with the property data
        }

        // Function to view a listing
        function viewListing(id) {
            alert(`Viewing property with ID: ${id}`);
            // In a real application, this would show a detailed view of the property
        }

        // Function to delete a listing
        function deleteListing(id) {
            if (confirm('Are you sure you want to delete this property listing?')) {
                alert(`Property with ID: ${id} deleted`);
                // In a real application, this would remove the property from the database
                // For now, we'll just remove it from the UI
                const index = propertyListings.findIndex(listing => listing.id === id);
                if (index !== -1) {
                    propertyListings.splice(index, 1);
                    renderListings();
                }
            }
        }

        // Initialize listings when page loads
        document.addEventListener('DOMContentLoaded', function() {
            renderListings();
            
            // Add event listeners for filtering
            document.getElementById('search-listings').addEventListener('input', filterListings);
            document.getElementById('status-filter').addEventListener('change', filterListings);
            document.getElementById('type-filter').addEventListener('change', filterListings);
            
            // Rest of your existing code...
            updateGreeting();
            updateOccupancy();
            
            const sidebarLinks = document.querySelectorAll('.sidebar-menu a');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const contentId = this.getAttribute('data-content') + '-content';
                    
                    document.querySelectorAll('.content').forEach(section => {
                        section.classList.add('hidden');
                    });
                    
                    const targetSection = document.getElementById(contentId);
                    if (targetSection) {
                        targetSection.classList.remove('hidden');
                    }
                    
                    sidebarLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                });
            });
            
            setTimeout(() => {
                document.getElementById('active-properties-count').textContent = '5 Properties';
            }, 500);

            initializeCharts();
            
            document.getElementById('timeRange').addEventListener('change', function() {
                updateCharts(this.value);
            });
        });

        // Rest of your existing functions...
        function updateGreeting() {
            const now = new Date();
            const hour = now.getHours();
            const greetingElement = document.getElementById('greeting');
            
            if (hour >= 5 && hour < 12) {
                greetingElement.textContent = 'Good Morning, Landlord!';
            } else if (hour >= 12 && hour < 18) {
                greetingElement.textContent = 'Good Afternoon, Landlord!';
            } else {
                greetingElement.textContent = 'Good Evening, Landlord!';
            }
        }

        let occupiedRooms = 3;
        let totalRooms = 8;
        
        function updateOccupancy() {
            const percentage = Math.round((occupiedRooms / totalRooms) * 100);
            document.getElementById('occupancyPercentage').textContent = `${percentage}%`;
            document.getElementById('progress').style.width = `${percentage}%`;
        }
        
        function occupyRoom() {
            if (occupiedRooms < totalRooms) {
                occupiedRooms++;
                updateOccupancy();
            } else {
                alert('All rooms are already occupied!');
            }
        }
        
        function vacateRoom() {
            if (occupiedRooms > 0) {
                occupiedRooms--;
                updateOccupancy();
            } else {
                alert('No rooms are currently occupied!');
            }
        }

        const descriptionTextarea = document.getElementById('description');
        const characterCount = document.querySelector('.character-count');
        
        if (descriptionTextarea && characterCount) {
            descriptionTextarea.addEventListener('input', function() {
                const length = this.value.length;
                characterCount.textContent = `${length}/500 characters`;
                
                if (length > 500) {
                    characterCount.style.color = 'var(--danger)';
                } else {
                    characterCount.style.color = 'var(--text)';
                }
            });
        }

        // Chart initialization and data
        let viewsInquiriesChart, earningsChart;

        function initializeCharts() {
            const ctx1 = document.getElementById('viewsInquiriesChart').getContext('2d');
            const ctx2 = document.getElementById('earningsChart').getContext('2d');
            
            // Generate data for the last 30 days by default
            const days = generateDays(30);
            const viewsData = generateRandomData(30, 50, 200);
            const inquiriesData = generateRandomData(30, 5, 40);
            const earningsData = generateRandomData(30, 5000, 25000);
            
            // Create Views vs Inquiries chart
            viewsInquiriesChart = new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: days,
                    datasets: [
                        {
                            label: 'Views',
                            data: viewsData,
                            borderColor: '#4285F4',
                            backgroundColor: 'rgba(66, 133, 244, 0.1)',
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Inquiries',
                            data: inquiriesData,
                            borderColor: '#FF385C',
                            backgroundColor: 'rgba(255, 56, 92, 0.1)',
                            tension: 0.3,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
            
            // Creating Earning chart
            earningsChart = new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: days,
                    datasets: [{
                        label: 'Earnings (Ksh)',
                        data: earningsData,
                        backgroundColor: 'rgba(0, 166, 153, 0.7)',
                        borderColor: 'rgba(0, 166, 153, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        function updateCharts(daysCount) {
            const days = generateDays(daysCount);
            const viewsData = generateRandomData(daysCount, 50, 200);
            const inquiriesData = generateRandomData(daysCount, 5, 40);
            const earningsData = generateRandomData(daysCount, 5000, 25000);
            
            // Update Views vs Inquiries chart
            viewsInquiriesChart.data.labels = days;
            viewsInquiriesChart.data.datasets[0].data = viewsData;
            viewsInquiriesChart.data.datasets[1].data = inquiriesData;
            viewsInquiriesChart.update();
            
            // Update Earnings chart
            earningsChart.data.labels = days;
            earningsChart.data.datasets[0].data = earningsData;
            earningsChart.update();
        }

        function generateDays(count) {
            const days = [];
            for (let i = count - 1; i >= 0; i--) {
                const date = new Date();
                date.setDate(date.getDate() - i);
                days.push(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
            }
            return days;
        }

        function generateRandomData(count, min, max) {
            const data = [];
            for (let i = 0; i < count; i++) {
                data.push(Math.floor(Math.random() * (max - min + 1)) + min);
            }
            return data;
        }
    </script>
</body>
</html>