<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/icons/smartlogo.png">
    <title>SmartHunt - Notifications</title>
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

        /* Notification-specific styles */
        .notifications-container {
            background: var(--light);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .notifications-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notifications-header h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .notifications-header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .notification-actions {
            display: flex;
            gap: 10px;
        }

        .notification-actions button {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }

        .notification-actions button:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .notification-tabs {
            display: flex;
            background: var(--light-gray);
            border-bottom: 1px solid var(--gray);
        }

        .notification-tab {
            padding: 15px 25px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
            font-weight: 500;
        }

        .notification-tab.active {
            border-bottom-color: var(--primary);
            color: var(--primary);
            background: white;
        }

        .notification-tab-badge {
            background: var(--primary);
            color: white;
            border-radius: 10px;
            padding: 2px 8px;
            font-size: 12px;
            margin-left: 5px;
        }

        .notifications-list {
            max-height: 600px;
            overflow-y: auto;
        }

        .notification-item {
            display: flex;
            padding: 20px 25px;
            border-bottom: 1px solid var(--light-gray);
            transition: all 0.3s;
            cursor: pointer;
        }

        .notification-item:hover {
            background: var(--light-gray);
        }

        .notification-item.unread {
            background: rgba(66, 133, 244, 0.05);
        }

        .notification-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .notification-icon.inquiry {
            background: rgba(66, 133, 244, 0.1);
            color: var(--secondary);
        }

        .notification-icon.booking {
            background: rgba(0, 166, 153, 0.1);
            color: var(--success);
        }

        .notification-icon.payment {
            background: rgba(255, 180, 0, 0.1);
            color: var(--warning);
        }

        .notification-icon.alert {
            background: rgba(255, 90, 95, 0.1);
            color: var(--danger);
        }

        .notification-icon.system {
            background: rgba(119, 119, 119, 0.1);
            color: #777;
        }

        .notification-content {
            flex: 1;
        }

        .notification-title {
            font-weight: 600;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
        }

        .notification-title .time {
            font-weight: normal;
            color: var(--text);
            margin-left: auto;
            font-size: 13px;
        }

        .notification-message {
            color: var(--text);
            margin-bottom: 8px;
            font-size: 14px;
        }

        .notification-actions-small {
            display: flex;
            gap: 10px;
        }

        .notification-actions-small button {
            background: none;
            border: none;
            color: var(--secondary);
            font-size: 13px;
            cursor: pointer;
            padding: 5px 0;
            transition: color 0.3s;
        }

        .notification-actions-small button:hover {
            color: var(--primary);
        }

        .notification-dot {
            width: 8px;
            height: 8px;
            background: var(--primary);
            border-radius: 50%;
            margin-right: 10px;
            flex-shrink: 0;
        }

        .notification-item.read .notification-dot {
            display: none;
        }

        /* Notification dropdown in navbar */
        .notification-dropdown {
            position: relative;
            margin-right: 20px;
        }

        .notification-dropdown-toggle {
            position: relative;
            background: none;
            border: none;
            font-size: 20px;
            color: var(--text);
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            transition: all 0.3s;
        }

        .notification-dropdown-toggle:hover {
            background: var(--light-gray);
        }

        .notification-dropdown-toggle .notification-indicator {
            position: absolute;
            top: 0;
            right: 0;
            width: 10px;
            height: 10px;
            background: var(--danger);
            border-radius: 50%;
            border: 2px solid white;
        }

        .notification-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            width: 350px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            display: none;
            margin-top: 10px;
        }

        .notification-dropdown-menu.show {
            display: block;
        }

        .notification-dropdown-header {
            padding: 15px 20px;
            border-bottom: 1px solid var(--light-gray);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-dropdown-header h3 {
            font-size: 16px;
            color: var(--dark);
        }

        .notification-dropdown-header a {
            font-size: 13px;
            color: var(--primary);
            text-decoration: none;
        }

        .notification-dropdown-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .notification-dropdown-item {
            padding: 15px 20px;
            border-bottom: 1px solid var(--light-gray);
            cursor: pointer;
            transition: all 0.3s;
        }

        .notification-dropdown-item:hover {
            background: var(--light-gray);
        }

        .notification-dropdown-item.unread {
            background: rgba(66, 133, 244, 0.05);
        }

        .notification-dropdown-footer {
            padding: 15px 20px;
            text-align: center;
            border-top: 1px solid var(--light-gray);
        }

        .notification-dropdown-footer a {
            color: var(--primary);
            text-decoration: none;
            font-size: 14px;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--text);
        }

        .empty-state i {
            font-size: 50px;
            color: var(--gray);
            margin-bottom: 15px;
        }

        .empty-state h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .empty-state p {
            font-size: 14px;
            max-width: 300px;
            margin: 0 auto 20px;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                width: 220px;
            }
            .main-content {
                margin-left: 220px;
            }
            .notification-dropdown-menu {
                width: 300px;
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
            .notification-dropdown-menu {
                width: 280px;
                right: -50px;
            }
            .notification-tabs {
                flex-wrap: wrap;
            }
            .notification-tab {
                flex: 1;
                min-width: 120px;
                text-align: center;
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
                        <a href="listings.php" data-content="edit-listings"><i class="fas fa-edit"></i>Listings</a>
                       
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" data-content="tenants"><i class="fas fa-users"></i> Tenants <span class="notification-badge">3</span></a> 
                    <div class="dropdown-content">
                        <a href="view-tenants.php" data-content="view-tenants"><i class="fas fa-list"></i> View Tenants</a>
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
                <li><a href="payments.php" data-content="payments"><i class="fas fa-credit-card"></i> Payments <span class="notification-badge">2</span></a></li>
                <li><a href="location.php" data-content="location"><i class="fas fa-map-marked-alt"></i> Location</a></li>
                <li><a href="announcements.php" data-content="announcements"><i class="fas fa-bullhorn"></i> Announcements</a></li>
                <li><a href="reports.php" data-content="reports"><i class="fas fa-chart-bar"></i> Reports</a></li>
                <li><a href="profilesettings.php" data-content="profile-settings"><i class="fas fa-user-cog"></i> Profile Setting</a></li>
                <li><a href="notifications.php" class="active" data-content="notifications"><i class="fas fa-bell"></i> Notifications <span class="notification-badge">7</span></a></li>
                <li><a href="support.php" data-content="support"><i class="fas fa-headset"></i> Support</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <nav class="navbar">
                <div class="navbar-brand">Notifications</div>
                
                <div class="login-image">
                    <!-- Notification Dropdown -->
                    <div class="notification-dropdown">
                        <button class="notification-dropdown-toggle">
                            <i class="fas fa-bell"></i>
                            <span class="notification-indicator"></span>
                        </button>
                        <div class="notification-dropdown-menu">
                            <div class="notification-dropdown-header">
                                <h3>Notifications</h3>
                                <a href="#" id="mark-all-read">Mark all as read</a>
                            </div>
                            <div class="notification-dropdown-list">
                                <div class="notification-dropdown-item unread">
                                    <div class="notification-title">
                                        <span class="notification-dot"></span>
                                        New Inquiry Received
                                        <span class="time">2 min ago</span>
                                    </div>
                                    <div class="notification-message">
                                        Sarah M. inquired about your 3-bedroom apartment in Westlands
                                    </div>
                                </div>
                                <div class="notification-dropdown-item unread">
                                    <div class="notification-title">
                                        <span class="notification-dot"></span>
                                        Payment Received
                                        <span class="time">1 hour ago</span>
                                    </div>
                                    <div class="notification-message">
                                        KES 25,000 received from James K. for Riverside Apartment
                                    </div>
                                </div>
                                <div class="notification-dropdown-item">
                                    <div class="notification-title">
                                        Booking Confirmed
                                        <span class="time">5 hours ago</span>
                                    </div>
                                    <div class="notification-message">
                                        David N. confirmed booking for your studio apartment
                                    </div>
                                </div>
                                <div class="notification-dropdown-item">
                                    <div class="notification-title">
                                        Maintenance Request
                                        <span class="time">1 day ago</span>
                                    </div>
                                    <div class="notification-message">
                                        Tenant reported a plumbing issue in Unit 4B
                                    </div>
                                </div>
                            </div>
                            <div class="notification-dropdown-footer">
                                <a href="#" data-content="notifications">View All Notifications</a>
                            </div>
                        </div>
                    </div>
                    
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

            <!-- Notifications Section -->
            <section class="content" id="notifications-content">
                <div class="notifications-container">
                    <div class="notifications-header">
                        <div>
                            <h1><i class="fas fa-bell"></i> Notifications</h1>
                            <p>Stay updated with your property activities</p>
                        </div>
                        <div class="notification-actions">
                            <button id="mark-all-notifications-read">
                                <i class="fas fa-check-double"></i> Mark All as Read
                            </button>
                            <button id="clear-all-notifications">
                                <i class="fas fa-trash"></i> Clear All
                            </button>
                        </div>
                    </div>
                    
                    <div class="notification-tabs">
                        <div class="notification-tab active" data-tab="all">
                            All Notifications <span class="notification-tab-badge">12</span>
                        </div>
                        <div class="notification-tab" data-tab="unread">
                            Unread <span class="notification-tab-badge">7</span>
                        </div>
                        <div class="notification-tab" data-tab="inquiries">
                            Inquiries <span class="notification-tab-badge">5</span>
                        </div>
                        <div class="notification-tab" data-tab="payments">
                            Payments <span class="notification-tab-badge">3</span>
                        </div>
                        <div class="notification-tab" data-tab="system">
                            System <span class="notification-tab-badge">2</span>
                        </div>
                    </div>
                    
                    <div class="notifications-list">
                        <!-- Inquiry Notifications -->
                        <div class="notification-item unread" data-type="inquiry">
                            <div class="notification-icon inquiry">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">
                                    <span class="notification-dot"></span>
                                    New Inquiry Received
                                    <span class="time">Just now</span>
                                </div>
                                <div class="notification-message">
                                    Sarah M. inquired about your 3-bedroom apartment in Westlands. She's interested in viewing the property this weekend.
                                </div>
                                <div class="notification-actions-small">
                                    <button class="mark-as-read">Mark as read</button>
                                    <button class="view-inquiry">View Inquiry</button>
                                    <button class="reply">Reply</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="notification-item unread" data-type="inquiry">
                            <div class="notification-icon inquiry">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">
                                    <span class="notification-dot"></span>
                                    Follow-up Inquiry
                                    <span class="time">30 minutes ago</span>
                                </div>
                                <div class="notification-message">
                                    Michael T. followed up on his inquiry about the studio apartment in Kilimani. He has additional questions about utilities.
                                </div>
                                <div class="notification-actions-small">
                                    <button class="mark-as-read">Mark as read</button>
                                    <button class="view-inquiry">View Inquiry</button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment Notifications -->
                        <div class="notification-item unread" data-type="payment">
                            <div class="notification-icon payment">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">
                                    <span class="notification-dot"></span>
                                    Payment Received
                                    <span class="time">2 hours ago</span>
                                </div>
                                <div class="notification-message">
                                    KES 25,000 received from James K. for Riverside Apartment. Payment for March 2024 rent.
                                </div>
                                <div class="notification-actions-small">
                                    <button class="mark-as-read">Mark as read</button>
                                    <button class="view-payment">View Details</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="notification-item" data-type="payment">
                            <div class="notification-icon payment">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">
                                    Payment Due Reminder
                                    <span class="time">1 day ago</span>
                                </div>
                                <div class="notification-message">
                                    Reminder: Payment for Karen House is due in 3 days. Send a reminder to the tenant.
                                </div>
                                <div class="notification-actions-small">
                                    <button class="send-reminder">Send Reminder</button>
                                    <button class="view-payment">View Details</button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Booking Notifications -->
                        <div class="notification-item unread" data-type="booking">
                            <div class="notification-icon booking">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">
                                    <span class="notification-dot"></span>
                                    Booking Confirmed
                                    <span class="time">5 hours ago</span>
                                </div>
                                <div class="notification-message">
                                    David N. confirmed booking for your studio apartment in Lavington. Move-in date: April 15, 2024.
                                </div>
                                <div class="notification-actions-small">
                                    <button class="mark-as-read">Mark as read</button>
                                    <button class="view-booking">View Booking</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="notification-item" data-type="booking">
                            <div class="notification-icon booking">
                                <i class="fas fa-calendar-times"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">
                                    Booking Cancelled
                                    <span class="time">2 days ago</span>
                                </div>
                                <div class="notification-message">
                                    Grace W. cancelled her booking for the 2-bedroom apartment in Westlands. Reason: Found alternative accommodation.
                                </div>
                                <div class="notification-actions-small">
                                    <button class="view-booking">View Details</button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- System Notifications -->
                        <div class="notification-item unread" data-type="system">
                            <div class="notification-icon system">
                                <i class="fas fa-cog"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">
                                    <span class="notification-dot"></span>
                                    System Maintenance
                                    <span class="time">1 day ago</span>
                                </div>
                                <div class="notification-message">
                                    Scheduled system maintenance on Saturday, 10 PM - 2 AM. The platform may be temporarily unavailable.
                                </div>
                                <div class="notification-actions-small">
                                    <button class="mark-as-read">Mark as read</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="notification-item" data-type="system">
                            <div class="notification-icon system">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">
                                    New Feature Available
                                    <span class="time">3 days ago</span>
                                </div>
                                <div class="notification-message">
                                    The new automated rent reminder feature is now available. Set it up in your payment settings.
                                </div>
                                <div class="notification-actions-small">
                                    <button class="view-feature">Learn More</button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Alert Notifications -->
                        <div class="notification-item unread" data-type="alert">
                            <div class="notification-icon alert">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">
                                    <span class="notification-dot"></span>
                                    Maintenance Request
                                    <span class="time">4 hours ago</span>
                                </div>
                                <div class="notification-message">
                                    Tenant reported a plumbing issue in Unit 4B. Urgent attention required.
                                </div>
                                <div class="notification-actions-small">
                                    <button class="mark-as-read">Mark as read</button>
                                    <button class="view-request">View Request</button>
                                    <button class="assign-contractor">Assign Contractor</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <footer class="footer">
        <img src="assets/icons/smartlogo.png" alt="SmartHunt Logo">
        <p>&copy; 2024 SmartHunt. All rights reserved.</p>
        <p>Making property management smarter and easier.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Notification dropdown toggle
            const notificationToggle = document.querySelector('.notification-dropdown-toggle');
            const notificationMenu = document.querySelector('.notification-dropdown-menu');
            
            if (notificationToggle) {
                notificationToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notificationMenu.classList.toggle('show');
                });
            }
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function() {
                notificationMenu.classList.remove('show');
            });
            
            // Mark all as read in dropdown
            const markAllRead = document.getElementById('mark-all-read');
            if (markAllRead) {
                markAllRead.addEventListener('click', function(e) {
                    e.preventDefault();
                    const unreadItems = document.querySelectorAll('.notification-dropdown-item.unread');
                    unreadItems.forEach(item => {
                        item.classList.remove('unread');
                    });
                    
                    // Update badge counts
                    updateBadgeCounts();
                });
            }
            
            // Notification tabs
            const notificationTabs = document.querySelectorAll('.notification-tab');
            const notificationItems = document.querySelectorAll('.notification-item');
            
            notificationTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabType = this.getAttribute('data-tab');
                    
                    // Update active tab
                    notificationTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Filter notifications
                    notificationItems.forEach(item => {
                        if (tabType === 'all') {
                            item.style.display = 'flex';
                        } else if (tabType === 'unread') {
                            if (item.classList.contains('unread')) {
                                item.style.display = 'flex';
                            } else {
                                item.style.display = 'none';
                            }
                        } else {
                            if (item.getAttribute('data-type') === tabType) {
                                item.style.display = 'flex';
                            } else {
                                item.style.display = 'none';
                            }
                        }
                    });
                });
            });
            
            // Mark as read functionality
            const markAsReadButtons = document.querySelectorAll('.mark-as-read');
            markAsReadButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const notificationItem = this.closest('.notification-item');
                    notificationItem.classList.remove('unread');
                    
                    // Update badge counts
                    updateBadgeCounts();
                });
            });
            
            // Mark all as read in main notifications
            const markAllNotificationsRead = document.getElementById('mark-all-notifications-read');
            if (markAllNotificationsRead) {
                markAllNotificationsRead.addEventListener('click', function() {
                    const unreadNotifications = document.querySelectorAll('.notification-item.unread');
                    unreadNotifications.forEach(item => {
                        item.classList.remove('unread');
                    });
                    
                    // Update badge counts
                    updateBadgeCounts();
                });
            }
            
            // Clear all notifications
            const clearAllNotifications = document.getElementById('clear-all-notifications');
            if (clearAllNotifications) {
                clearAllNotifications.addEventListener('click', function() {
                    if (confirm('Are you sure you want to clear all notifications? This action cannot be undone.')) {
                        const notificationsList = document.querySelector('.notifications-list');
                        notificationsList.innerHTML = `
                            <div class="empty-state">
                                <i class="far fa-bell-slash"></i>
                                <h3>No notifications</h3>
                                <p>You're all caught up! When you have new notifications, they'll appear here.</p>
                            </div>
                        `;
                        
                        // Update badge counts
                        updateBadgeCounts();
                    }
                });
            }
            
            // Function to update badge counts
            function updateBadgeCounts() {
                // Update sidebar badge
                const unreadCount = document.querySelectorAll('.notification-item.unread').length;
                const sidebarBadge = document.querySelector('.sidebar-menu a[data-content="notifications"] .notification-badge');
                
                if (sidebarBadge) {
                    if (unreadCount > 0) {
                        sidebarBadge.textContent = unreadCount;
                    } else {
                        sidebarBadge.textContent = '0';
                        sidebarBadge.style.display = 'none';
                    }
                }
                
                // Update navbar indicator
                const navbarIndicator = document.querySelector('.notification-indicator');
                if (navbarIndicator) {
                    if (unreadCount > 0) {
                        navbarIndicator.style.display = 'block';
                    } else {
                        navbarIndicator.style.display = 'none';
                    }
                }
                
                // Update tab badges
                const inquiryCount = document.querySelectorAll('.notification-item[data-type="inquiry"].unread').length;
                const paymentCount = document.querySelectorAll('.notification-item[data-type="payment"].unread').length;
                const systemCount = document.querySelectorAll('.notification-item[data-type="system"].unread').length;
                
                const inquiryBadge = document.querySelector('.notification-tab[data-tab="inquiries"] .notification-tab-badge');
                const paymentBadge = document.querySelector('.notification-tab[data-tab="payments"] .notification-tab-badge');
                const systemBadge = document.querySelector('.notification-tab[data-tab="system"] .notification-tab-badge');
                const unreadBadge = document.querySelector('.notification-tab[data-tab="unread"] .notification-tab-badge');
                const allBadge = document.querySelector('.notification-tab[data-tab="all"] .notification-tab-badge');
                
                if (inquiryBadge) inquiryBadge.textContent = inquiryCount;
                if (paymentBadge) paymentBadge.textContent = paymentCount;
                if (systemBadge) systemBadge.textContent = systemCount;
                if (unreadBadge) unreadBadge.textContent = unreadCount;
                if (allBadge) allBadge.textContent = document.querySelectorAll('.notification-item').length;
            }
            
            // Simulate real-time notifications (for demo purposes)
            function simulateNewNotification() {
                // Only add if we're on the notifications page
                if (window.location.hash === '#notifications') {
                    const notificationsList = document.querySelector('.notifications-list');
                    const emptyState = document.querySelector('.empty-state');
                    
                    if (emptyState) {
                        notificationsList.removeChild(emptyState);
                    }
                    
                    const newNotification = document.createElement('div');
                    newNotification.className = 'notification-item unread';
                    newNotification.setAttribute('data-type', 'inquiry');
                    newNotification.innerHTML = `
                        <div class="notification-icon inquiry">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">
                                <span class="notification-dot"></span>
                                New Inquiry Received
                                <span class="time">Just now</span>
                            </div>
                            <div class="notification-message">
                                Emily R. is interested in your 2-bedroom apartment in Kilimani. She would like to schedule a viewing.
                            </div>
                            <div class="notification-actions-small">
                                <button class="mark-as-read">Mark as read</button>
                                <button class="view-inquiry">View Inquiry</button>
                                <button class="reply">Reply</button>
                            </div>
                        </div>
                    `;
                    
                    notificationsList.insertBefore(newNotification, notificationsList.firstChild);
                    
                    // Add event listener to the new mark-as-read button
                    newNotification.querySelector('.mark-as-read').addEventListener('click', function() {
                        newNotification.classList.remove('unread');
                        updateBadgeCounts();
                    });
                    
                    // Update badge counts
                    updateBadgeCounts();
                    
                    // Show a subtle notification
                    if ('Notification' in window && Notification.permission === 'granted') {
                        new Notification('SmartHunt - New Inquiry', {
                            body: 'Emily R. is interested in your 2-bedroom apartment',
                            icon: 'assets/icons/smartlogo.png'
                        });
                    }
                }
            }
            
            // Request notification permission
            if ('Notification' in window) {
                Notification.requestPermission();
            }
            
            // Simulate a new notification every 30 seconds for demo
            setInterval(simulateNewNotification, 30000);
        });
    </script>
</body>
</html>