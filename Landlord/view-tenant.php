<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/icons/smartlogo.png">
    <title>SmartHunt - Tenant Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* All your CSS styles remain exactly the same */
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
            display: none !important;
        }

        .notification-badge {
            background-color: var(--danger);
            color: white;
            border-radius: 50%;
            padding: 3px 8px;
            font-size: 12px;
            margin-left: 5px;
        }

        /* Tenant Management Styles */
        .tenants-container {
            background: var(--light);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .tenants-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .tenants-header h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .tenants-header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .tenants-actions {
            display: flex;
            gap: 15px;
        }

        .tenants-actions button {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tenants-actions button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .tenants-content {
            padding: 30px;
        }

        .tenants-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--light);
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border-left: 4px solid var(--primary);
        }

        .stat-card i {
            font-size: 32px;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .stat-card h3 {
            font-size: 32px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .stat-card p {
            color: var(--text);
            font-size: 14px;
        }

        .tenants-tabs {
            display: flex;
            background: var(--light-gray);
            border-bottom: 1px solid var(--gray);
            margin-bottom: 25px;
        }

        .tenants-tab {
            padding: 15px 25px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
            font-weight: 500;
        }

        .tenants-tab.active {
            border-bottom-color: var(--primary);
            color: var(--primary);
            background: white;
        }

        .tenants-tab-badge {
            background: var(--primary);
            color: white;
            border-radius: 10px;
            padding: 2px 8px;
            font-size: 12px;
            margin-left: 5px;
        }

        .tenants-table-container {
            background: var(--light);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .table-responsive {
            overflow-x: auto;
        }

        .tenants-table {
            width: 100%;
            border-collapse: collapse;
        }

        .tenants-table th {
            background: var(--light-gray);
            padding: 15px 20px;
            text-align: left;
            font-weight: 600;
            color: var(--dark);
            border-bottom: 1px solid var(--gray);
        }

        .tenants-table td {
            padding: 15px 20px;
            border-bottom: 1px solid var(--light-gray);
            vertical-align: middle;
        }

        .tenants-table tr:hover {
            background: var(--light-gray);
        }

        .tenant-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        .tenant-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .tenant-details h4 {
            font-size: 16px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 2px;
        }

        .tenant-details p {
            font-size: 13px;
            color: var(--text);
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-active {
            background: rgba(0, 166, 153, 0.1);
            color: var(--success);
        }

        .status-pending {
            background: rgba(255, 180, 0, 0.1);
            color: var(--warning);
        }

        .status-inactive {
            background: rgba(255, 90, 95, 0.1);
            color: var(--danger);
        }

        .rent-status {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .rent-paid {
            color: var(--success);
        }

        .rent-overdue {
            color: var(--danger);
        }

        .rent-pending {
            color: var(--warning);
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-action {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-view {
            background: var(--secondary);
            color: white;
        }

        .btn-view:hover {
            background: #3367d6;
        }

        .btn-edit {
            background: var(--light-gray);
            color: var(--text);
        }

        .btn-edit:hover {
            background: var(--gray);
        }

        .btn-message {
            background: var(--success);
            color: white;
        }

        .btn-message:hover {
            background: #009688;
        }

        .search-filter {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            align-items: center;
        }

        .search-box {
            flex: 1;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 12px 15px 12px 40px;
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

        .filter-select {
            padding: 12px 15px;
            border: 1px solid var(--gray);
            border-radius: 8px;
            font-size: 14px;
            background: white;
            min-width: 150px;
        }

        /* Tenant Details Modal */
        .tenant-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 20px;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            width: 100%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            font-size: 24px;
            font-weight: 600;
        }

        .close-modal {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .close-modal:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .modal-body {
            padding: 30px;
        }

        .tenant-profile {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .profile-avatar {
            text-align: center;
        }

        .profile-avatar img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--primary);
            margin-bottom: 15px;
        }

        .profile-details h3 {
            font-size: 24px;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .profile-details p {
            color: var(--text);
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .detail-card {
            background: var(--light-gray);
            padding: 20px;
            border-radius: 8px;
        }

        .detail-card h4 {
            font-size: 16px;
            margin-bottom: 15px;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--gray);
        }

        .detail-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .detail-label {
            color: var(--text);
            font-weight: 500;
        }

        .detail-value {
            color: var(--dark);
            font-weight: 600;
        }

        .modal-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--light-gray);
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: var(--primary-light);
        }

        .btn-secondary {
            background: var(--light-gray);
            color: var(--text);
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-secondary:hover {
            background: var(--gray);
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                width: 220px;
            }
            .main-content {
                margin-left: 220px;
            }
            .tenant-profile {
                grid-template-columns: 1fr;
                text-align: center;
            }
            .details-grid {
                grid-template-columns: 1fr;
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
            .tenants-stats {
                grid-template-columns: repeat(2, 1fr);
            }
            .tenants-tabs {
                flex-wrap: wrap;
            }
            .tenants-tab {
                flex: 1;
                min-width: 120px;
                text-align: center;
            }
            .search-filter {
                flex-direction: column;
                align-items: stretch;
            }
            .action-buttons {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .tenants-stats {
                grid-template-columns: 1fr;
            }
            .tenants-actions {
                flex-direction: column;
                width: 100%;
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
                    <a href="#" class="active" data-content="tenants"><i class="fas fa-users"></i> Tenants <span class="notification-badge">3</span></a> 
                    <div class="dropdown-content">
                        <a href="#" data-content="view-tenants" class="active"><i class="fas fa-list"></i> View Tenants</a>
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
                <li><a href="#" data-content="location"><i class="fas fa-map-marked-alt"></i> Location</a></li>
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
                <div class="navbar-brand">Tenants</div>
                
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

            <!-- Tenants Section -->
            <section class="content" id="tenants-content">
                <div class="tenants-container">
                    <div class="tenants-header">
                        <div>
                            <h1><i class="fas fa-users"></i> Tenants</h1>
                            <p>Manage your tenants, track payments, and handle communications</p>
                        </div>
                        <div class="tenants-actions">
                            <button id="add-tenant-btn">
                                <i class="fas fa-user-plus"></i> Add Tenant
                            </button>
                            <button id="export-tenants">
                                <i class="fas fa-download"></i> Export Data
                            </button>
                        </div>
                    </div>
                    
                    <div class="tenants-content">
                        <!-- Statistics Cards -->
                        <div class="tenants-stats">
                            <div class="stat-card">
                                <i class="fas fa-users"></i>
                                <h3>12</h3>
                                <p>Total Tenants</p>
                            </div>
                            <div class="stat-card">
                                <i class="fas fa-home"></i>
                                <h3>8</h3>
                                <p>Occupied Units</p>
                            </div>
                            <div class="stat-card">
                                <i class="fas fa-money-bill-wave"></i>
                                <h3>KES 245,000</h3>
                                <p>Monthly Revenue</p>
                            </div>
                            <div class="stat-card">
                                <i class="fas fa-exclamation-triangle"></i>
                                <h3>2</h3>
                                <p>Rent Overdue</p>
                            </div>
                        </div>
                        
                        <!-- Search and Filter -->
                        <div class="search-filter">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="tenant-search" placeholder="Search tenants by name, email, or property...">
                            </div>
                            <select class="filter-select" id="status-filter">
                                <option value="all">All Status</option>
                                <option value="active">Active</option>
                                <option value="pending">Pending</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <select class="filter-select" id="property-filter">
                                <option value="all">All Properties</option>
                                <option value="westlands">Westlands Apartment</option>
                                <option value="kilimani">Kilimani Studio</option>
                                <option value="karen">Karen House</option>
                            </select>
                        </div>
                        
                        <!-- Tabs -->
                        <div class="tenants-tabs">
                            <div class="tenants-tab active" data-tab="all">
                                All Tenants <span class="tenants-tab-badge">12</span>
                            </div>
                            <div class="tenants-tab" data-tab="active">
                                Active <span class="tenants-tab-badge">8</span>
                            </div>
                            <div class="tenants-tab" data-tab="pending">
                                Pending <span class="tenants-tab-badge">2</span>
                            </div>
                            <div class="tenants-tab" data-tab="overdue">
                                Rent Overdue <span class="tenants-tab-badge">2</span>
                            </div>
                        </div>
                        
                        <!-- Tenants Table -->
                        <div class="tenants-table-container">
                            <div class="table-responsive">
                                <table class="tenants-table">
                                    <thead>
                                        <tr>
                                            <th>Tenant</th>
                                            <th>Property</th>
                                            <th>Contact</th>
                                            <th>Lease Period</th>
                                            <th>Rent Status</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Tenant 1 -->
                                        <tr data-status="active" data-property="westlands">
                                            <td>
                                                <div class="tenant-info">
                                                    <img src="https://placehold.co/40x40/4285F4/FFFFFF?text=JM" alt="John Mwangi" class="tenant-avatar">
                                                    <div class="tenant-details">
                                                        <h4>John Mwangi</h4>
                                                        <p>ID: TN-001</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>Westlands Apartment</td>
                                            <td>
                                                <div>john.mwangi@email.com</div>
                                                <div>+254 712 345 678</div>
                                            </td>
                                            <td>
                                                <div>Jan 15, 2024 - Jan 14, 2025</div>
                                                <div style="font-size: 12px; color: var(--text);">12 months</div>
                                            </td>
                                            <td>
                                                <div class="rent-status">
                                                    <i class="fas fa-check-circle rent-paid"></i>
                                                    <span>Paid - Mar 2024</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="status-badge status-active">Active</span>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button class="btn-action btn-view" data-tenant-id="1">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                    <button class="btn-action btn-message">
                                                        <i class="fas fa-envelope"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        <!-- Tenant 2 -->
                                        <tr data-status="active" data-property="kilimani">
                                            <td>
                                                <div class="tenant-info">
                                                    <img src="https://placehold.co/40x40/00A699/FFFFFF?text=SK" alt="Sarah Kamau" class="tenant-avatar">
                                                    <div class="tenant-details">
                                                        <h4>Sarah Kamau</h4>
                                                        <p>ID: TN-002</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>Kilimani Studio</td>
                                            <td>
                                                <div>sarah.kamau@email.com</div>
                                                <div>+254 723 456 789</div>
                                            </td>
                                            <td>
                                                <div>Mar 1, 2024 - Feb 28, 2025</div>
                                                <div style="font-size: 12px; color: var(--text);">12 months</div>
                                            </td>
                                            <td>
                                                <div class="rent-status">
                                                    <i class="fas fa-exclamation-triangle rent-overdue"></i>
                                                    <span>Overdue - Mar 2024</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="status-badge status-active">Active</span>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button class="btn-action btn-view" data-tenant-id="2">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                    <button class="btn-action btn-message">
                                                        <i class="fas fa-envelope"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        <!-- Tenant 3 -->
                                        <tr data-status="pending" data-property="karen">
                                            <td>
                                                <div class="tenant-info">
                                                    <img src="https://placehold.co/40x40/FF385C/FFFFFF?text=DN" alt="David Njoroge" class="tenant-avatar">
                                                    <div class="tenant-details">
                                                        <h4>David Njoroge</h4>
                                                        <p>ID: TN-003</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>Karen House</td>
                                            <td>
                                                <div>david.njoroge@email.com</div>
                                                <div>+254 734 567 890</div>
                                            </td>
                                            <td>
                                                <div>Apr 1, 2024 - Mar 31, 2025</div>
                                                <div style="font-size: 12px; color: var(--text);">12 months</div>
                                            </td>
                                            <td>
                                                <div class="rent-status">
                                                    <i class="fas fa-clock rent-pending"></i>
                                                    <span>Pending - Apr 2024</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="status-badge status-pending">Pending</span>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button class="btn-action btn-view" data-tenant-id="3">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                    <button class="btn-action btn-edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        <!-- Tenant 4 -->
                                        <tr data-status="active" data-property="westlands">
                                            <td>
                                                <div class="tenant-info">
                                                    <img src="https://placehold.co/40x40/FFB400/FFFFFF?text=AK" alt="Alice Kariuki" class="tenant-avatar">
                                                    <div class="tenant-details">
                                                        <h4>Alice Kariuki</h4>
                                                        <p>ID: TN-004</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>Westlands Apartment</td>
                                            <td>
                                                <div>alice.kariuki@email.com</div>
                                                <div>+254 745 678 901</div>
                                            </td>
                                            <td>
                                                <div>Feb 15, 2024 - Feb 14, 2025</div>
                                                <div style="font-size: 12px; color: var(--text);">12 months</div>
                                            </td>
                                            <td>
                                                <div class="rent-status">
                                                    <i class="fas fa-check-circle rent-paid"></i>
                                                    <span>Paid - Mar 2024</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="status-badge status-active">Active</span>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button class="btn-action btn-view" data-tenant-id="4">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                    <button class="btn-action btn-message">
                                                        <i class="fas fa-envelope"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Tenant Details Modal -->
    <div class="tenant-modal hidden" id="tenant-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user"></i> Tenant Details</h2>
                <button class="close-modal" id="close-modal-btn">&times;</button>
            </div>
            <div class="modal-body">
                <div class="tenant-profile">
                    <div class="profile-avatar">
                        <img src="https://placehold.co/120x120/4285F4/FFFFFF?text=JM" alt="Tenant Avatar" id="modal-avatar">
                        <h3 id="modal-name">John Mwangi</h3>
                        <span class="status-badge status-active" id="modal-status">Active</span>
                    </div>
                    <div class="profile-details">
                        <h3>Contact Information</h3>
                        <p><i class="fas fa-envelope"></i> <span id="modal-email">john.mwangi@email.com</span></p>
                        <p><i class="fas fa-phone"></i> <span id="modal-phone">+254 712 345 678</span></p>
                        <p><i class="fas fa-id-card"></i> Tenant ID: <span id="modal-id">TN-001</span></p>
                        <p><i class="fas fa-calendar"></i> Member since: <span id="modal-join-date">January 2024</span></p>
                    </div>
                </div>
                
                <div class="details-grid">
                    <div class="detail-card">
                        <h4><i class="fas fa-home"></i> Property Information</h4>
                        <div class="detail-item">
                            <span class="detail-label">Property</span>
                            <span class="detail-value" id="modal-property">Westlands Apartment</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Unit Number</span>
                            <span class="detail-value" id="modal-unit">Unit 4B</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Monthly Rent</span>
                            <span class="detail-value" id="modal-rent">KES 35,000</span>
                        </div>
                    </div>
                    
                    <div class="detail-card">
                        <h4><i class="fas fa-file-contract"></i> Lease Information</h4>
                        <div class="detail-item">
                            <span class="detail-label">Lease Start</span>
                            <span class="detail-value" id="modal-lease-start">Jan 15, 2024</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Lease End</span>
                            <span class="detail-value" id="modal-lease-end">Jan 14, 2025</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Duration</span>
                            <span class="detail-value" id="modal-lease-duration">12 months</span>
                        </div>
                    </div>
                    
                    <div class="detail-card">
                        <h4><i class="fas fa-money-bill-wave"></i> Payment Information</h4>
                        <div class="detail-item">
                            <span class="detail-label">Last Payment</span>
                            <span class="detail-value" id="modal-last-payment">Mar 1, 2024</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Next Payment Due</span>
                            <span class="detail-value" id="modal-next-payment">Apr 1, 2024</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Payment Status</span>
                            <span class="detail-value" id="modal-payment-status">Paid</span>
                        </div>
                    </div>
                    
                    <div class="detail-card">
                        <h4><i class="fas fa-sticky-note"></i> Additional Information</h4>
                        <div class="detail-item">
                            <span class="detail-label">Emergency Contact</span>
                            <span class="detail-value" id="modal-emergency-contact">Mary Mwangi - +254 700 000 000</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Special Requirements</span>
                            <span class="detail-value" id="modal-requirements">Pet friendly - 1 cat</span>
                        </div>
                    </div>
                </div>
                
                <div class="modal-actions">
                    <button class="btn-secondary" id="modal-close-btn">Close</button>
                    <button class="btn-primary">
                        <i class="fas fa-envelope"></i> Send Message
                    </button>
                    <button class="btn-primary">
                        <i class="fas fa-edit"></i> Edit Tenant
                    </button>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <img src="assets/icons/smartlogo.png" alt="SmartHunt Logo">
        <p>&copy; 2024 SmartHunt. All rights reserved.</p>
        <p>Making property management smarter and easier.</p>
    </footer>

    <script>
        // Tenant Management System
        class TenantManager {
            constructor() {
                this.tenants = {
                    1: {
                        name: 'John Mwangi',
                        avatar: 'https://placehold.co/120x120/4285F4/FFFFFF?text=JM',
                        email: 'john.mwangi@email.com',
                        phone: '+254 712 345 678',
                        id: 'TN-001',
                        joinDate: 'January 2024',
                        property: 'Westlands Apartment',
                        unit: 'Unit 4B',
                        rent: 'KES 35,000',
                        leaseStart: 'Jan 15, 2024',
                        leaseEnd: 'Jan 14, 2025',
                        leaseDuration: '12 months',
                        lastPayment: 'Mar 1, 2024',
                        nextPayment: 'Apr 1, 2024',
                        paymentStatus: 'Paid',
                        status: 'Active',
                        emergencyContact: 'Mary Mwangi - +254 700 000 000',
                        requirements: 'Pet friendly - 1 cat'
                    },
                    2: {
                        name: 'Sarah Kamau',
                        avatar: 'https://placehold.co/120x120/00A699/FFFFFF?text=SK',
                        email: 'sarah.kamau@email.com',
                        phone: '+254 723 456 789',
                        id: 'TN-002',
                        joinDate: 'March 2024',
                        property: 'Kilimani Studio',
                        unit: 'Unit 2A',
                        rent: 'KES 18,000',
                        leaseStart: 'Mar 1, 2024',
                        leaseEnd: 'Feb 28, 2025',
                        leaseDuration: '12 months',
                        lastPayment: 'Feb 28, 2024',
                        nextPayment: 'Mar 1, 2024',
                        paymentStatus: 'Overdue',
                        status: 'Active',
                        emergencyContact: 'James Kamau - +254 711 111 111',
                        requirements: 'None'
                    },
                    3: {
                        name: 'David Njoroge',
                        avatar: 'https://placehold.co/120x120/FF385C/FFFFFF?text=DN',
                        email: 'david.njoroge@email.com',
                        phone: '+254 734 567 890',
                        id: 'TN-003',
                        joinDate: 'April 2024',
                        property: 'Karen House',
                        unit: 'Main House',
                        rent: 'KES 65,000',
                        leaseStart: 'Apr 1, 2024',
                        leaseEnd: 'Mar 31, 2025',
                        leaseDuration: '12 months',
                        lastPayment: 'N/A',
                        nextPayment: 'Apr 1, 2024',
                        paymentStatus: 'Pending',
                        status: 'Pending',
                        emergencyContact: 'Grace Njoroge - +254 722 222 222',
                        requirements: 'Gardening services required'
                    },
                    4: {
                        name: 'Alice Kariuki',
                        avatar: 'https://placehold.co/120x120/FFB400/FFFFFF?text=AK',
                        email: 'alice.kariuki@email.com',
                        phone: '+254 745 678 901',
                        id: 'TN-004',
                        joinDate: 'February 2024',
                        property: 'Westlands Apartment',
                        unit: 'Unit 3C',
                        rent: 'KES 42,000',
                        leaseStart: 'Feb 15, 2024',
                        leaseEnd: 'Feb 14, 2025',
                        leaseDuration: '12 months',
                        lastPayment: 'Mar 5, 2024',
                        nextPayment: 'Apr 5, 2024',
                        paymentStatus: 'Paid',
                        status: 'Active',
                        emergencyContact: 'Peter Kariuki - +254 733 333 333',
                        requirements: 'Parking space required'
                    }
                };

                this.modal = document.getElementById('tenant-modal');
                this.init();
            }

            init() {
                this.bindEvents();
                console.log('Tenant Manager initialized');
            }

            bindEvents() {
                // View tenant buttons
                document.querySelectorAll('.btn-view').forEach(button => {
                    button.addEventListener('click', (e) => {
                        const tenantId = e.currentTarget.getAttribute('data-tenant-id');
                        this.viewTenant(tenantId);
                    });
                });

                // Close modal buttons
                document.getElementById('close-modal-btn').addEventListener('click', () => this.closeModal());
                document.getElementById('modal-close-btn').addEventListener('click', () => this.closeModal());

                // Close modal when clicking outside
                this.modal.addEventListener('click', (e) => {
                    if (e.target === this.modal) {
                        this.closeModal();
                    }
                });

                // Close modal with Escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && !this.modal.classList.contains('hidden')) {
                        this.closeModal();
                    }
                });

                // Tabs
                document.querySelectorAll('.tenants-tab').forEach(tab => {
                    tab.addEventListener('click', (e) => {
                        this.handleTabClick(e.currentTarget);
                    });
                });

                // Search
                document.getElementById('tenant-search').addEventListener('input', (e) => {
                    this.handleSearch(e.target.value);
                });

                // Filters
                document.getElementById('status-filter').addEventListener('change', (e) => {
                    this.applyFilters();
                });
                document.getElementById('property-filter').addEventListener('change', (e) => {
                    this.applyFilters();
                });

                // Action buttons
                document.getElementById('add-tenant-btn').addEventListener('click', () => {
                    alert('Add tenant functionality would open here');
                });

                document.getElementById('export-tenants').addEventListener('click', () => {
                    alert('Exporting tenant data...');
                });
            }

            viewTenant(tenantId) {
                const tenant = this.tenants[tenantId];
                if (!tenant) return;

                // Populate modal
                Object.keys(tenant).forEach(key => {
                    const element = document.getElementById(`modal-${key}`);
                    if (element) {
                        if (key === 'avatar') {
                            element.src = tenant[key];
                        } else {
                            element.textContent = tenant[key];
                        }
                    }
                });

                // Update status badge
                const statusBadge = document.getElementById('modal-status');
                statusBadge.textContent = tenant.status;
                statusBadge.className = 'status-badge ';
                
                if (tenant.status === 'Active') {
                    statusBadge.classList.add('status-active');
                } else if (tenant.status === 'Pending') {
                    statusBadge.classList.add('status-pending');
                } else {
                    statusBadge.classList.add('status-inactive');
                }

                this.showModal();
            }

            showModal() {
                this.modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            closeModal() {
                this.modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            handleTabClick(tab) {
                // Update active tab
                document.querySelectorAll('.tenants-tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                const tabType = tab.getAttribute('data-tab');
                this.filterTable(tabType);
            }

            filterTable(tabType) {
                const rows = document.querySelectorAll('.tenants-table tbody tr');
                
                rows.forEach(row => {
                    if (tabType === 'all') {
                        row.style.display = 'table-row';
                    } else if (tabType === 'overdue') {
                        const rentStatus = row.querySelector('.rent-status span').textContent;
                        row.style.display = rentStatus.includes('Overdue') ? 'table-row' : 'none';
                    } else {
                        const rowStatus = row.getAttribute('data-status');
                        row.style.display = rowStatus === tabType ? 'table-row' : 'none';
                    }
                });
            }

            handleSearch(searchTerm) {
                const rows = document.querySelectorAll('.tenants-table tbody tr');
                const term = searchTerm.toLowerCase();

                rows.forEach(row => {
                    const tenantName = row.querySelector('.tenant-details h4').textContent.toLowerCase();
                    const tenantEmail = row.querySelector('td:nth-child(3) div:first-child').textContent.toLowerCase();
                    const property = row.querySelector('td:nth-child(2)').textContent.toLowerCase();

                    const matches = tenantName.includes(term) || tenantEmail.includes(term) || property.includes(term);
                    row.style.display = matches ? 'table-row' : 'none';
                });
            }

            applyFilters() {
                const statusValue = document.getElementById('status-filter').value;
                const propertyValue = document.getElementById('property-filter').value;
                const searchValue = document.getElementById('tenant-search').value.toLowerCase();
                const rows = document.querySelectorAll('.tenants-table tbody tr');

                rows.forEach(row => {
                    const rowStatus = row.getAttribute('data-status');
                    const rowProperty = row.getAttribute('data-property');
                    const tenantName = row.querySelector('.tenant-details h4').textContent.toLowerCase();
                    const tenantEmail = row.querySelector('td:nth-child(3) div:first-child').textContent.toLowerCase();
                    const property = row.querySelector('td:nth-child(2)').textContent.toLowerCase();

                    const statusMatch = statusValue === 'all' || rowStatus === statusValue;
                    const propertyMatch = propertyValue === 'all' || rowProperty === propertyValue;
                    const searchMatch = tenantName.includes(searchValue) || tenantEmail.includes(searchValue) || property.includes(searchValue);

                    row.style.display = statusMatch && propertyMatch && searchMatch ? 'table-row' : 'none';
                });
            }
        }

        // Initialize the application when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            new TenantManager();
        });
    </script>
</body>
</html>