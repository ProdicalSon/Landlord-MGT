<?php
// Landlord/Frontend/index.php
session_start();

// Check if landlord is logged in
if (!isset($_SESSION['landlord_id'])) {
    $_SESSION['redirect_after_login'] = 'index.php';
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

// Get unread notifications count (you'll need to implement this)
$unreadNotifications = 7; // Placeholder
$unreadInquiries = 5; // Placeholder
$pendingPayments = 2; // Placeholder

// Format landlord name for display
$landlordName = $userModel->getFullName($landlord) ?: $landlord['username'];
$firstName = explode(' ', $landlordName)[0];
?>
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
        /* All your existing CSS styles remain exactly the same */
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

        .card button, .card .action-btn {
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

        .card button:hover, .card .action-btn:hover {
            background-color: #e61e4d;
        }

        .card .action-btn a {
            color: white;
            text-decoration: none;
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

        /* Stats and quick actions */
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

        /* Welcome section */
        .welcome-section {
            margin-bottom: 20px;
        }

        .welcome-section h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }

        .welcome-section .date {
            color: var(--text);
            font-size: 14px;
        }

        /* Recent properties list */
        .recent-properties {
            margin-top: 15px;
        }

        .property-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid var(--light-gray);
        }

        .property-item:last-child {
            border-bottom: none;
        }

        .property-info h4 {
            font-size: 16px;
            margin-bottom: 3px;
        }

        .property-info p {
            font-size: 13px;
            color: var(--text);
            margin: 0;
        }

        .property-status {
            padding: 3px 8px;
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
            .chart-grid {
                grid-template-columns: 1fr;
            }
            .user-details {
                display: none;
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
                    <a href="#" data-content="inquiries"><i class="fas fa-question-circle"></i> Inquiries <span class="notification-badge"><?php echo $unreadInquiries; ?></span></a>
                    <div class="dropdown-content">
                        <a href="inquiries.php" data-content="inquiries-list"><i class="fas fa-inbox"></i> Inquiries</a>
                        <a href="#" data-content="chat"><i class="fas fa-comments"></i> Chat</a>
                    </div>
                </li>
                <li><a href="payments.php" data-content="payments"><i class="fas fa-credit-card"></i> Payments <span class="notification-badge"><?php echo $pendingPayments; ?></span></a></li>
                <li><a href="location.php" data-content="location"><i class="fas fa-map-marked-alt"></i> Location</a></li>
                <li><a href="announcements.php" data-content="announcements"><i class="fas fa-bullhorn"></i> Announcements</a></li>
                <li><a href="landlordreports.php" data-content="reports"><i class="fas fa-chart-bar"></i> Reports</a></li>
                <li><a href="profile.php" data-content="profile-settings"><i class="fas fa-user-cog"></i> Profile Settings</a></li>
                <li><a href="notifications.php" data-content="notifications"><i class="fas fa-bell"></i> Notifications <span class="notification-badge"><?php echo $unreadNotifications; ?></span></a></li>
                <li><a href="support.php" data-content="support"><i class="fas fa-headset"></i> Support</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <nav class="navbar">
                <div class="navbar-brand">Landlord Dashboard</div>
                
                <div class="user-menu">
                    <div class="user-info">
                        <div class="user-details">
                            <div class="user-name"><?php echo htmlspecialchars($landlordName); ?></div>
                            <div class="user-role">Landlord</div>
                        </div>
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($firstName, 0, 1)); ?>
                        </div>
                        
                        <!-- Dropdown Menu -->
                        <div class="dropdown-menu">
                            <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
                            <a href="profilesettings.php"><i class="fas fa-cog"></i> Settings</a>
                            <hr>
                            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Dashboard Content -->
            <section class="content" id="dashboard-content">
                <div class="welcome-section">
                    <h1 id="greeting">Welcome Back, <?php echo htmlspecialchars($firstName); ?>!</h1>
                    <p class="date"><?php echo date('l, F j, Y'); ?></p>
                </div>
                <p>Manage your properties and track performance</p>

                <div class="cards">
                    <div class="card" id="active-properties-card">
                        <h3><i class="fas fa-building"></i> Active Properties</h3>
                        <p id="active-properties-count"><?php echo $stats['total']; ?> Properties</p>
                        <div class="stats-container">
                            <div class="stat-item">
                                <div class="stat-value"><?php echo $stats['occupied']; ?></div>
                                <div class="stat-label">Occupied</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value"><?php echo $stats['available']; ?></div>
                                <div class="stat-label">Vacant</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value"><?php echo $stats['maintenance']; ?></div>
                                <div class="stat-label">Maintenance</div>
                            </div>
                        </div>
                        <button class="action-btn"><a href="propertylistings.php">View Details</a></button>
                    </div>

                    <div class="card">
                        <h3><i class="fas fa-question-circle"></i> Inquiries</h3>
                        <p><?php echo $unreadInquiries; ?> New Inquiries</p>
                        <div class="quick-actions">
                            <button class="action-btn">Respond to All</button>
                            <button class="action-btn">Sort by Priority</button>
                        </div>
                        <button class="action-btn"><a href="inquiries.php">Check Now</a></button> 
                    </div>

                    <div class="card">
                        <h3><i class="fas fa-users"></i> Current Tenants</h3>
                        <p><?php echo $stats['occupied']; ?> Tenants</p>
                        <div class="stats-container">
                            <div class="stat-item">
                                <div class="stat-value"><?php echo min(5, $stats['occupied']); ?></div>
                                <div class="stat-label">Active</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value"><?php echo max(0, $stats['occupied'] - 5); ?></div>
                                <div class="stat-label">Pending</div>
                            </div>
                        </div>
                        <button class="action-btn"><a href="view-tenant.php">Manage Tenants</a></button> 
                    </div>

                    <div class="card">
                        <h3><i class="fas fa-chart-pie"></i> Occupancy Rate</h3>
                        <p>Occupancy: <span id="occupancyPercentage">
                            <?php 
                            $occupancyRate = $stats['total'] > 0 ? round(($stats['occupied'] / $stats['total']) * 100) : 0;
                            echo $occupancyRate; ?>%
                        </span></p>
                        <div class="progress-bar">
                            <div id="progress" class="progress" style="width: <?php echo $occupancyRate; ?>%;"></div>
                        </div>
                        <div class="quick-actions">
                            <button onclick="occupyRoom()" class="action-btn">Occupy a Room</button>
                            <button onclick="vacateRoom()" class="action-btn"><a href="editrooms.php">Vacate Rooms</a></button>
                        </div>
                    </div>

                    <div class="card">
                        <h3><i class="fas fa-credit-card"></i> Payments</h3>
                        <p><?php echo $pendingPayments; ?> Pending Payments</p>
                        <div class="alert">
                            <p><strong>Alert:</strong> <?php echo $pendingPayments; ?> tenant<?php echo $pendingPayments != 1 ? 's' : ''; ?> ha<?php echo $pendingPayments != 1 ? 've' : 's'; ?> not paid rent.</p>
                        </div>
                        <div class="quick-actions">
                            <button class="action-btn">Send Reminders</button>
                            <button class="action-btn"><a href="payments.php">View Payment History</a></button>
                        </div>
                    </div>

                    <div class="card">
                        <h3><i class="fas fa-bullhorn"></i> Announcements</h3>
                        <p>8 Announcements</p>
                        <div class="quick-actions">
                            <button class="action-btn">Create New</button>
                            <button class="action-btn">Schedule</button>
                        </div>
                        <button class="action-btn"><a href="announcements.php">View Announcements</a></button>
                    </div>
                </div>

                <!-- Recent Properties -->
                <?php if (!empty($recentProperties)): ?>
                <div class="card" style="margin-top: 20px;">
                    <h3><i class="fas fa-clock"></i> Recent Properties</h3>
                    <div class="recent-properties">
                        <?php foreach (array_slice($recentProperties, 0, 5) as $property): ?>
                        <div class="property-item">
                            <div class="property-info">
                                <h4><?php echo htmlspecialchars($property['property_name']); ?></h4>
                                <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($property['neighborhood'] . ', ' . $property['city']); ?></p>
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
                </div>
                <?php endif; ?>

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
                        <form id="propertyForm" method="POST" action="add_property_handler.php" enctype="multipart/form-data">
                            <div class="form-section">
                                <h2><i class="fas fa-info-circle"></i> Basic Information</h2>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="property-name"><i class="fas fa-building"></i> Property Name *</label>
                                        <input type="text" id="property-name" name="property_name" placeholder="e.g., Tripple A Apartments" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="property-type"><i class="fas fa-home"></i> Property Type *</label>
                                        <select id="property-type" name="property_type" required>
                                            <option value="" disabled selected>Select property type</option>
                                            <option value="apartment">Apartment</option>
                                            <option value="house">House</option>
                                            <option value="studio">Studio</option>
                                            <option value="condo">Condo</option>
                                            <option value="townhouse">Townhouse</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="price"><i class="fas fa-tag"></i> Monthly Rent (KES) *</label>
                                        <input type="number" id="price" name="price" placeholder="e.g., 5000" min="0" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="property-status"><i class="fas fa-info-circle"></i> Status *</label>
                                        <select id="property-status" name="status" required>
                                            <option value="available">Available</option>
                                            <option value="occupied">Occupied</option>
                                            <option value="maintenance">Under Maintenance</option>
                                        </select>
                                    </div>
                                    <div class="form-group full-width">
                                        <label for="description"><i class="fas fa-align-left"></i> Property Description *</label>
                                        <textarea id="description" name="description" rows="4" placeholder="Describe your property, nearby amenities, and what makes it special" required></textarea>
                                        <div class="character-count">0/500 characters</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-section">
                                <h2><i class="fas fa-map-marker-alt"></i> Location Details</h2>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="address">Street Address *</label>
                                        <input type="text" id="address" name="address" placeholder="123 Main Street" required>
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
                                        <input type="number" id="area" name="area" placeholder="0000" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="year-built">Year Built</label>
                                        <input type="number" id="year-built" name="year_built" placeholder="2020" min="1900" max="<?php echo date('Y'); ?>">
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
                                        <input type="checkbox" id="pool" name="amenities[]" value="pool">
                                        <label for="pool">Swimming Pool</label>
                                    </div>
                                    <div class="amenity-checkbox">
                                        <input type="checkbox" id="gym" name="amenities[]" value="gym">
                                        <label for="gym">Gym</label>
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
                                    <div class="amenity-checkbox">
                                        <input type="checkbox" id="ac" name="amenities[]" value="ac">
                                        <label for="ac">Air Conditioning</label>
                                    </div>
                                    <div class="amenity-checkbox">
                                        <input type="checkbox" id="heating" name="amenities[]" value="heating">
                                        <label for="heating">Heating</label>
                                    </div>
                                    <div class="amenity-checkbox">
                                        <input type="checkbox" id="balcony" name="amenities[]" value="balcony">
                                        <label for="balcony">Balcony</label>
                                    </div>
                                    <div class="amenity-checkbox">
                                        <input type="checkbox" id="furnished" name="amenities[]" value="furnished">
                                        <label for="furnished">Furnished</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-section">
                                <h2><i class="fas fa-camera"></i> Media</h2>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="photos">Property Photos (max 5MB each)</label>
                                        <input type="file" id="photos" name="property_photos[]" multiple accept="image/*">
                                        <small class="text-muted">You can select multiple images. The first one will be the primary image.</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="rules">Property Rules/Documents</label>
                                        <input type="file" id="rules" name="property_rules[]" multiple accept=".pdf,.doc,.docx,.txt">
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

            <!-- Other content sections -->
            <section class="content hidden" id="edit-listings-content">
                <h1>Edit Listings</h1>
                <p>This is where you would edit your property listings</p>
            </section>

            <section class="content hidden" id="manage-location-content">
                <h1>Manage Location</h1>
                <p>This is where you would manage property locations</p>
            </section>
        </main>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <img src="assets/icons/smartlogo.png" alt="SmartHunt Logo" style="height: 40px;"> 
        <h6>&copy; <?php echo date('Y'); ?> Algorithm-X Softwares. <br>All rights reserved</h6>
    </footer>

    <script>
    // Check for any global AJAX setup
    console.log('Checking for jQuery AJAX setup:', typeof $ !== 'undefined' ? $.ajaxSettings : 'No jQuery');

    // Monitor all fetch requests
    const originalFetch = window.fetch;
    window.fetch = function() {
        console.log('Fetch called with:', arguments);
        return originalFetch.apply(this, arguments);
    };

    // Monitor all XHR requests
    const originalXHROpen = XMLHttpRequest.prototype.open;
    XMLHttpRequest.prototype.open = function() {
        console.log('XHR open called with:', arguments);
        return originalXHROpen.apply(this, arguments);
    };

    // Update greeting based on time of day
    function updateGreeting() {
        const now = new Date();
        const hour = now.getHours();
        const greetingElement = document.getElementById('greeting');
        const firstName = "<?php echo htmlspecialchars($firstName); ?>";
        
        if (hour >= 5 && hour < 12) {
            greetingElement.textContent = 'Good Morning, ' + firstName + '!';
        } else if (hour >= 12 && hour < 18) {
            greetingElement.textContent = 'Good Afternoon, ' + firstName + '!';
        } else {
            greetingElement.textContent = 'Good Evening, ' + firstName + '!';
        }
    }

    // Occupancy management
    let occupiedRooms = <?php echo $stats['occupied']; ?>;
    let totalRooms = <?php echo $stats['total'] ?: 1; ?>;
    
    function updateOccupancy() {
        const percentage = Math.round((occupiedRooms / totalRooms) * 100);
        const occupancyElement = document.getElementById('occupancyPercentage');
        const progressElement = document.getElementById('progress');
        if (occupancyElement) occupancyElement.textContent = `${percentage}%`;
        if (progressElement) progressElement.style.width = `${percentage}%`;
    }
    
    function occupyRoom() {
        if (occupiedRooms < totalRooms) {
            occupiedRooms++;
            updateOccupancy();
            showNotification('Room marked as occupied', 'success');
        } else {
            showNotification('All rooms are already occupied!', 'warning');
        }
    }
    
    function vacateRoom() {
        if (occupiedRooms > 0) {
            occupiedRooms--;
            updateOccupancy();
            showNotification('Room marked as vacant', 'info');
        } else {
            showNotification('No rooms are currently occupied!', 'warning');
        }
    }

    // =============================================
    // FIXED SIDEBAR NAVIGATION - This is the key part
    // =============================================
    document.addEventListener('DOMContentLoaded', function() {
        updateGreeting();
        updateOccupancy();
        
        // Handle sidebar navigation properly
        const sidebarLinks = document.querySelectorAll('.sidebar-menu a');
        
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                
                // Case 1: Dropdown toggles (links with href="#")
                if (href === '#') {
                    e.preventDefault();
                    // Toggle dropdown
                    const dropdown = this.closest('.dropdown');
                    if (dropdown) {
                        dropdown.classList.toggle('active');
                    }
                    return;
                }
                
                // Case 2: Links to external pages (like propertylistings.php, profile.php, etc.)
                // Don't prevent default - let browser navigate
                
                // Update active class
                sidebarLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                
                // For index.php internal content (dashboard), handle specially
                if (href === 'index.php') {
                    e.preventDefault();
                    // Show dashboard content
                    document.querySelectorAll('.content').forEach(section => {
                        section.classList.add('hidden');
                    });
                    const dashboard = document.getElementById('dashboard-content');
                    if (dashboard) {
                        dashboard.classList.remove('hidden');
                    }
                }
                
                console.log('Navigating to:', href);
            });
        });
        
        // Set active class based on current page
        const currentPage = window.location.pathname.split('/').pop();
        sidebarLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href === currentPage || (currentPage === 'index.php' && href === 'index.php')) {
                link.classList.add('active');
            }
        });
        
        // Initialize charts if they exist
        if (document.getElementById('viewsInquiriesChart') && typeof initializeCharts === 'function') {
            initializeCharts();
        }
        
        // Time range change handler
        const timeRange = document.getElementById('timeRange');
        if (timeRange) {
            timeRange.addEventListener('change', function() {
                if (typeof updateCharts === 'function') {
                    updateCharts(this.value);
                }
            });
        }
    });

    // =============================================
    // PROPERTY FORM SUBMISSION
    // =============================================
    const propertyForm = document.getElementById('propertyForm');
    if (propertyForm) {
        // Remove any existing event listeners (by cloning and replacing)
        const newForm = propertyForm.cloneNode(true);
        propertyForm.parentNode.replaceChild(newForm, propertyForm);
        
        // Add new event listener to the cloned form
        newForm.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('=== FORM SUBMISSION STARTED ===');
            console.log('Form method attribute:', this.method);
            console.log('Form action attribute:', this.action);
            
            // Collect form data
            const formData = new FormData(this);
            
            // Log all form data
            console.log('Form data being sent:');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            
            // Check for file uploads
            const files = document.getElementById('photos')?.files;
            if (files && files.length > 0) {
                console.log(`Uploading ${files.length} images...`);
            }
            
            // Disable submit button
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
            submitBtn.disabled = true;
            
            // Create and send request with explicit POST
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'add_property_handler.php', true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            
            xhr.onload = function() {
                console.log('Response status:', xhr.status);
                console.log('Response text:', xhr.responseText);
                
                try {
                    const data = JSON.parse(xhr.responseText);
                    console.log('Parsed data:', data);
                    
                    if (data.success) {
                        showNotification('Property added successfully!', 'success');
                        newForm.reset();
                        
                        // Clear image previews if they exist
                        const imagePreview = document.getElementById('image-preview');
                        if (imagePreview) imagePreview.innerHTML = '';
                        
                        // Switch back to dashboard
                        document.querySelectorAll('.content').forEach(s => s.classList.add('hidden'));
                        const dashboard = document.getElementById('dashboard-content');
                        if (dashboard) dashboard.classList.remove('hidden');
                    } else {
                        showNotification(data.message || 'Error adding property', 'error');
                    }
                } catch (e) {
                    console.error('Parse error:', e);
                    showNotification('Server error: Invalid response', 'error');
                }
                
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            };
            
            xhr.onerror = function() {
                console.error('Network error occurred');
                showNotification('Network error occurred', 'error');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            };
            
            xhr.send(formData);
            console.log('Request sent via XMLHttpRequest with POST method');
        });
        
        console.log('Form event listener reattached successfully');
    }

    // =============================================
    // NOTIFICATION FUNCTION
    // =============================================
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        
        let icon = 'info-circle';
        if (type === 'success') icon = 'check-circle';
        if (type === 'error') icon = 'exclamation-circle';
        if (type === 'warning') icon = 'exclamation-triangle';
        
        notification.innerHTML = `
            <i class="fas fa-${icon}"></i>
            <span>${message}</span>
        `;
        
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.padding = '15px 20px';
        notification.style.borderRadius = '8px';
        notification.style.backgroundColor = type === 'success' ? '#00A699' : 
                                          type === 'error' ? '#FF5A5F' : 
                                          type === 'warning' ? '#FFB400' : '#4285F4';
        notification.style.color = 'white';
        notification.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
        notification.style.zIndex = '9999';
        notification.style.display = 'flex';
        notification.style.alignItems = 'center';
        notification.style.gap = '10px';
        notification.style.animation = 'slideIn 0.3s ease';
        
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

    // =============================================
    // CHART FUNCTIONS (if needed)
    // =============================================
    let viewsInquiriesChart, earningsChart;

    function initializeCharts() {
        const ctx1 = document.getElementById('viewsInquiriesChart')?.getContext('2d');
        const ctx2 = document.getElementById('earningsChart')?.getContext('2d');
        
        if (!ctx1 || !ctx2) return;
        
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
        
        // Create Earnings chart
        earningsChart = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: days,
                datasets: [{
                    label: 'Earnings (KES)',
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
        if (!viewsInquiriesChart || !earningsChart) return;
        
        const days = generateDays(daysCount);
        const viewsData = generateRandomData(daysCount, 50, 200);
        const inquiriesData = generateRandomData(daysCount, 5, 40);
        const earningsData = generateRandomData(daysCount, 5000, 25000);
        
        viewsInquiriesChart.data.labels = days;
        viewsInquiriesChart.data.datasets[0].data = viewsData;
        viewsInquiriesChart.data.datasets[1].data = inquiriesData;
        viewsInquiriesChart.update();
        
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

    // =============================================
    // ANIMATION STYLES
    // =============================================
    const style = document.createElement('style');
    style.textContent = `
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
    `;
    document.head.appendChild(style);
</script>
</body>
</html>