<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/icons/smartlogo.png">
    <title>SmartHunt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

        /* Modal Styles */
        .modal-overlay {
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

        /* Add Tenant Form Styles */
        .form-section {
            margin-bottom: 30px;
        }

        .form-section-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--light-gray);
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
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--gray);
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255, 56, 92, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-hint {
            font-size: 12px;
            color: var(--text);
            margin-top: 5px;
        }

        .form-required::after {
            content: " *";
            color: var(--danger);
        }

        .file-upload {
            border: 2px dashed var(--gray);
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
        }

        .file-upload:hover {
            border-color: var(--primary);
        }

        .file-upload i {
            font-size: 48px;
            color: var(--gray);
            margin-bottom: 15px;
        }

        .file-upload input {
            display: none;
        }

        .form-row {
            display: flex;
            gap: 15px;
            align-items: flex-end;
        }

        .form-row .form-group {
            flex: 1;
            margin-bottom: 0;
        }

        .btn-add {
            background: var(--success);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .btn-add:hover {
            background: #009688;
        }

        .document-list {
            background: var(--light-gray);
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
        }

        .document-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid var(--gray);
        }

        .document-item:last-child {
            border-bottom: none;
        }

        .document-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .document-info i {
            color: var(--primary);
        }

        .btn-remove {
            background: var(--danger);
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
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
            .form-grid {
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
            .modal-content {
                max-width: 95%;
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
            .form-row {
                flex-direction: column;
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
                <div class="navbar-brand">Tenant Management</div>
                
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
                                <option value="Jogoo">Jogoo Apartment</option>
                                <option value="Milimani">Milimani Studio</option>
                                <option value="Mwembe">Mwembe House</option>
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
                                    <tbody id="tenants-table-body">
                                        <!-- Tenant rows will be populated by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Add Tenant Modal -->
    <div class="modal-overlay hidden" id="add-tenant-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-plus"></i> Add New Tenant</h2>
                <button class="close-modal" id="close-add-tenant-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="add-tenant-form">
                    <!-- Personal Information Section -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-user"></i> Personal Information
                        </h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label form-required">First Name</label>
                                <input type="text" class="form-input" name="firstName" required placeholder="Enter first name">
                            </div>
                            <div class="form-group">
                                <label class="form-label form-required">Last Name</label>
                                <input type="text" class="form-input" name="lastName" required placeholder="Enter last name">
                            </div>
                            <div class="form-group">
                                <label class="form-label form-required">Email Address</label>
                                <input type="email" class="form-input" name="email" required placeholder="Enter email address">
                            </div>
                            <div class="form-group">
                                <label class="form-label form-required">Phone Number</label>
                                <input type="tel" class="form-input" name="phone" required placeholder="+254 XXX XXX XXX">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" class="form-input" name="dob">
                            </div>
                            <div class="form-group">
                                <label class="form-label">National ID</label>
                                <input type="text" class="form-input" name="nationalId" placeholder="Enter national ID number">
                            </div>
                        </div>
                    </div>

                    <!-- Property Information Section -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-home"></i> Property Information
                        </h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label form-required">Property</label>
                                <select class="form-select" name="property" required>
                                    <option value="">Select Property</option>
                                    <option value="Jogoo">Jogoo Apartment</option>
                                    <option value="Milimani">Milimani Studio</option>
                                    <option value="Mwembe">Mwembe House</option>
                                    <option value="lavington">Lavington Villa</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label form-required">Unit Number</label>
                                <input type="text" class="form-input" name="unitNumber" required placeholder="e.g., Unit 4B">
                            </div>
                            <div class="form-group">
                                <label class="form-label form-required">Monthly Rent (KES)</label>
                                <input type="number" class="form-input" name="monthlyRent" required placeholder="35000" min="0">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Security Deposit (KES)</label>
                                <input type="number" class="form-input" name="securityDeposit" placeholder="35000" min="0">
                            </div>
                        </div>
                    </div>

                    <!-- Lease Information Section -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-file-contract"></i> Lease Information
                        </h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label form-required">Lease Start Date</label>
                                <input type="date" class="form-input" name="leaseStart" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label form-required">Lease End Date</label>
                                <input type="date" class="form-input" name="leaseEnd" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label form-required">Lease Duration</label>
                                <select class="form-select" name="leaseDuration" required>
                                    <option value="">Select Duration</option>
                                    <option value="6">6 Months</option>
                                    <option value="12">12 Months</option>
                                    <option value="24">24 Months</option>
                                    <option value="month-to-month">Month to Month</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Rent Due Date</label>
                                <select class="form-select" name="rentDueDate">
                                    <option value="1">1st of the month</option>
                                    <option value="5">5th of the month</option>
                                    <option value="10" selected>10th of the month</option>
                                    <option value="15">15th of the month</option>
                                    <option value="20">20th of the month</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact Section -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-address-book"></i> Emergency Contact
                        </h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Contact Name</label>
                                <input type="text" class="form-input" name="emergencyName" placeholder="Enter emergency contact name">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Contact Phone</label>
                                <input type="tel" class="form-input" name="emergencyPhone" placeholder="+254 XXX XXX XXX">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Relationship</label>
                                <input type="text" class="form-input" name="emergencyRelationship" placeholder="e.g., Spouse, Parent">
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information Section -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-sticky-note"></i> Additional Information
                        </h3>
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label class="form-label">Special Requirements</label>
                                <textarea class="form-textarea" name="specialRequirements" placeholder="Any special requirements, pets, parking needs, etc."></textarea>
                            </div>
                            <div class="form-group full-width">
                                <label class="form-label">Notes</label>
                                <textarea class="form-textarea" name="notes" placeholder="Additional notes about the tenant"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Document Upload Section -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-file-upload"></i> Documents
                        </h3>
                        <div class="form-group full-width">
                            <div class="file-upload" id="document-upload-area">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click to upload documents or drag and drop</p>
                                <p class="form-hint">Supported files: PDF, JPG, PNG (Max 10MB each)</p>
                                <input type="file" id="document-upload" multiple accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                            <div class="document-list hidden" id="document-list">
                                <!-- Documents will be listed here -->
                            </div>
                        </div>
                    </div>

                    <div class="modal-actions">
                        <button type="button" class="btn-secondary" id="cancel-add-tenant">Cancel</button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Save Tenant
                        </button>
                    </div>
                </form>
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
                        avatar: 'https://placehold.co/40x40/4285F4/FFFFFF?text=JM',
                        email: 'john.mwangi@email.com',
                        phone: '+254 712 345 678',
                        id: 'TN-001',
                        joinDate: 'January 2024',
                        property: 'Jogoo Apartment',
                        unit: 'Unit 4B',
                        rent: 'KES 35,000',
                        leaseStart: 'Jan 15, 2024',
                        leaseEnd: 'Jan 14, 2025',
                        leaseDuration: '12 months',
                        lastPayment: 'Mar 1, 2024',
                        nextPayment: 'Apr 1, 2024',
                        paymentStatus: 'Paid',
                        status: 'active',
                        emergencyContact: 'Mary Mwangi - +254 700 000 000',
                        requirements: 'Pet friendly - 1 cat'
                    },
                    2: {
                        name: 'Sarah Kamau',
                        avatar: 'https://placehold.co/40x40/00A699/FFFFFF?text=SK',
                        email: 'sarah.kamau@email.com',
                        phone: '+254 723 456 789',
                        id: 'TN-002',
                        joinDate: 'March 2024',
                        property: 'Milimani Studio',
                        unit: 'Unit 2A',
                        rent: 'KES 18,000',
                        leaseStart: 'Mar 1, 2024',
                        leaseEnd: 'Feb 28, 2025',
                        leaseDuration: '12 months',
                        lastPayment: 'Feb 28, 2024',
                        nextPayment: 'Mar 1, 2024',
                        paymentStatus: 'Overdue',
                        status: 'active',
                        emergencyContact: 'James Kamau - +254 711 111 111',
                        requirements: 'None'
                    },
                    3: {
                        name: 'David Njoroge',
                        avatar: 'https://placehold.co/40x40/FF385C/FFFFFF?text=DN',
                        email: 'david.njoroge@email.com',
                        phone: '+254 734 567 890',
                        id: 'TN-003',
                        joinDate: 'April 2024',
                        property: 'Mwembe House',
                        unit: 'Main House',
                        rent: 'KES 65,000',
                        leaseStart: 'Apr 1, 2024',
                        leaseEnd: 'Mar 31, 2025',
                        leaseDuration: '12 months',
                        lastPayment: 'N/A',
                        nextPayment: 'Apr 1, 2024',
                        paymentStatus: 'Pending',
                        status: 'pending',
                        emergencyContact: 'Grace Njoroge - +254 722 222 222',
                        requirements: 'Gardening services required'
                    },
                    4: {
                        name: 'Alice Kariuki',
                        avatar: 'https://placehold.co/40x40/FFB400/FFFFFF?text=AK',
                        email: 'alice.kariuki@email.com',
                        phone: '+254 745 678 901',
                        id: 'TN-004',
                        joinDate: 'February 2024',
                        property: 'Jogoo Apartment',
                        unit: 'Unit 3C',
                        rent: 'KES 42,000',
                        leaseStart: 'Feb 15, 2024',
                        leaseEnd: 'Feb 14, 2025',
                        leaseDuration: '12 months',
                        lastPayment: 'Mar 5, 2024',
                        nextPayment: 'Apr 5, 2024',
                        paymentStatus: 'Paid',
                        status: 'active',
                        emergencyContact: 'Peter Kariuki - +254 733 333 333',
                        requirements: 'Parking space required'
                    },
                    5: {
                        name: 'Michael Ochieng',
                        avatar: 'https://placehold.co/40x40/FF385C/FFFFFF?text=MO',
                        email: 'michael.ochieng@email.com',
                        phone: '+254 756 789 012',
                        id: 'TN-005',
                        joinDate: 'March 2024',
                        property: 'Milimani Studio',
                        unit: 'Unit 1B',
                        rent: 'KES 22,000',
                        leaseStart: 'Mar 10, 2024',
                        leaseEnd: 'Mar 9, 2025',
                        leaseDuration: '12 months',
                        lastPayment: 'Feb 28, 2024',
                        nextPayment: 'Mar 10, 2024',
                        paymentStatus: 'Overdue',
                        status: 'active',
                        emergencyContact: 'Susan Ochieng - +254 744 444 444',
                        requirements: 'None'
                    },
                    6: {
                        name: 'Grace Wambui',
                        avatar: 'https://placehold.co/40x40/00A699/FFFFFF?text=GW',
                        email: 'grace.wambui@email.com',
                        phone: '+254 767 890 123',
                        id: 'TN-006',
                        joinDate: 'January 2024',
                        property: 'Mwembe House',
                        unit: 'Guest House',
                        rent: 'KES 28,000',
                        leaseStart: 'Jan 1, 2024',
                        leaseEnd: 'Dec 31, 2024',
                        leaseDuration: '12 months',
                        lastPayment: 'Mar 1, 2024',
                        nextPayment: 'Apr 1, 2024',
                        paymentStatus: 'Paid',
                        status: 'active',
                        emergencyContact: 'James Wambui - +254 755 555 555',
                        requirements: 'Weekly cleaning service'
                    }
                };

                this.modal = document.getElementById('tenant-modal');
                this.addTenantModal = document.getElementById('add-tenant-modal');
                this.tenantsTableBody = document.getElementById('tenants-table-body');
                this.currentFilter = 'all';
                this.init();
            }

            init() {
                this.renderTenantsTable();
                this.bindEvents();
                console.log('Tenant Manager initialized');
            }

            renderTenantsTable() {
                this.tenantsTableBody.innerHTML = '';
                
                Object.values(this.tenants).forEach(tenant => {
                    const row = this.createTenantRow(tenant);
                    this.tenantsTableBody.appendChild(row);
                });
            }

            createTenantRow(tenant) {
                const row = document.createElement('tr');
                row.setAttribute('data-status', tenant.status);
                row.setAttribute('data-property', tenant.property.toLowerCase().replace(' ', '-'));
                
                // Determine rent status class and icon
                let rentStatusClass = '';
                let rentStatusIcon = '';
                let rentStatusText = '';
                
                if (tenant.paymentStatus === 'Paid') {
                    rentStatusClass = 'rent-paid';
                    rentStatusIcon = 'fa-check-circle';
                    rentStatusText = `Paid - ${tenant.lastPayment}`;
                } else if (tenant.paymentStatus === 'Overdue') {
                    rentStatusClass = 'rent-overdue';
                    rentStatusIcon = 'fa-exclamation-triangle';
                    rentStatusText = `Overdue - ${tenant.lastPayment}`;
                } else {
                    rentStatusClass = 'rent-pending';
                    rentStatusIcon = 'fa-clock';
                    rentStatusText = `Pending - ${tenant.nextPayment}`;
                }

                // Determine status badge class
                let statusBadgeClass = '';
                if (tenant.status === 'active') {
                    statusBadgeClass = 'status-active';
                } else if (tenant.status === 'pending') {
                    statusBadgeClass = 'status-pending';
                } else {
                    statusBadgeClass = 'status-inactive';
                }

                row.innerHTML = `
                    <td>
                        <div class="tenant-info">
                            <img src="${tenant.avatar}" alt="${tenant.name}" class="tenant-avatar">
                            <div class="tenant-details">
                                <h4>${tenant.name}</h4>
                                <p>ID: ${tenant.id}</p>
                            </div>
                        </div>
                    </td>
                    <td>${tenant.property}</td>
                    <td>
                        <div>${tenant.email}</div>
                        <div>${tenant.phone}</div>
                    </td>
                    <td>
                        <div>${tenant.leaseStart} - ${tenant.leaseEnd}</div>
                        <div style="font-size: 12px; color: var(--text);">${tenant.leaseDuration}</div>
                    </td>
                    <td>
                        <div class="rent-status">
                            <i class="fas ${rentStatusIcon} ${rentStatusClass}"></i>
                            <span>${rentStatusText}</span>
                        </div>
                    </td>
                    <td>
                        <span class="status-badge ${statusBadgeClass}">${tenant.status.charAt(0).toUpperCase() + tenant.status.slice(1)}</span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-action btn-view" data-tenant-id="${tenant.id}">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="btn-action btn-message">
                                <i class="fas fa-envelope"></i>
                            </button>
                        </div>
                    </td>
                `;

                return row;
            }

            bindEvents() {
                // Add Tenant Button
                document.getElementById('add-tenant-btn').addEventListener('click', () => {
                    this.showAddTenantModal();
                });

                // Close Add Tenant Modal
                document.getElementById('close-add-tenant-modal').addEventListener('click', () => this.closeAddTenantModal());
                document.getElementById('cancel-add-tenant').addEventListener('click', () => this.closeAddTenantModal());

                // Close modal when clicking outside
                this.addTenantModal.addEventListener('click', (e) => {
                    if (e.target === this.addTenantModal) {
                        this.closeAddTenantModal();
                    }
                });

                // Close modal with Escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && !this.addTenantModal.classList.contains('hidden')) {
                        this.closeAddTenantModal();
                    }
                });

                // Form submission
                document.getElementById('add-tenant-form').addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.handleAddTenantForm(e);
                });

                // File upload
                const fileUpload = document.getElementById('document-upload');
                const uploadArea = document.getElementById('document-upload-area');
                const documentList = document.getElementById('document-list');

                uploadArea.addEventListener('click', () => fileUpload.click());
                
                uploadArea.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    uploadArea.style.borderColor = 'var(--primary)';
                    uploadArea.style.backgroundColor = 'var(--light-gray)';
                });

                uploadArea.addEventListener('dragleave', () => {
                    uploadArea.style.borderColor = 'var(--gray)';
                    uploadArea.style.backgroundColor = 'transparent';
                });

                uploadArea.addEventListener('drop', (e) => {
                    e.preventDefault();
                    uploadArea.style.borderColor = 'var(--gray)';
                    uploadArea.style.backgroundColor = 'transparent';
                    
                    const files = e.dataTransfer.files;
                    this.handleFileUpload(files);
                });

                fileUpload.addEventListener('change', (e) => {
                    this.handleFileUpload(e.target.files);
                });

                // Tab filtering
                document.querySelectorAll('.tenants-tab').forEach(tab => {
                    tab.addEventListener('click', (e) => {
                        const tabType = e.currentTarget.getAttribute('data-tab');
                        this.handleTabFilter(tabType);
                    });
                });

                // Search functionality
                document.getElementById('tenant-search').addEventListener('input', (e) => {
                    this.handleSearch(e.target.value);
                });

                // Status filter
                document.getElementById('status-filter').addEventListener('change', (e) => {
                    this.applyFilters();
                });

                // Property filter
                document.getElementById('property-filter').addEventListener('change', (e) => {
                    this.applyFilters();
                });

                // View tenant buttons (delegated event)
                document.addEventListener('click', (e) => {
                    if (e.target.closest('.btn-view')) {
                        const tenantId = e.target.closest('.btn-view').getAttribute('data-tenant-id');
                        this.viewTenant(tenantId);
                    }
                });
            }

            handleTabFilter(tabType) {
                // Update active tab
                document.querySelectorAll('.tenants-tab').forEach(tab => {
                    tab.classList.remove('active');
                });
                document.querySelector(`[data-tab="${tabType}"]`).classList.add('active');

                this.currentFilter = tabType;
                this.applyFilters();
            }

            handleSearch(searchTerm) {
                const rows = document.querySelectorAll('#tenants-table-body tr');
                const term = searchTerm.toLowerCase();

                rows.forEach(row => {
                    const tenantName = row.querySelector('.tenant-details h4').textContent.toLowerCase();
                    const tenantEmail = row.querySelector('td:nth-child(3) div:first-child').textContent.toLowerCase();
                    const property = row.querySelector('td:nth-child(2)').textContent.toLowerCase();

                    const matches = tenantName.includes(term) || tenantEmail.includes(term) || property.includes(term);
                    row.style.display = matches ? '' : 'none';
                });
            }

            applyFilters() {
                const rows = document.querySelectorAll('#tenants-table-body tr');
                const statusValue = document.getElementById('status-filter').value;
                const propertyValue = document.getElementById('property-filter').value;
                const searchValue = document.getElementById('tenant-search').value.toLowerCase();

                rows.forEach(row => {
                    const rowStatus = row.getAttribute('data-status');
                    const rowProperty = row.getAttribute('data-property');
                    const tenantName = row.querySelector('.tenant-details h4').textContent.toLowerCase();
                    const tenantEmail = row.querySelector('td:nth-child(3) div:first-child').textContent.toLowerCase();
                    const property = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                    const rentStatus = row.querySelector('.rent-status span').textContent;

                    // Tab filter
                    let tabMatch = true;
                    if (this.currentFilter === 'overdue') {
                        tabMatch = rentStatus.includes('Overdue');
                    } else if (this.currentFilter !== 'all') {
                        tabMatch = rowStatus === this.currentFilter;
                    }

                    // Status filter
                    const statusMatch = statusValue === 'all' || rowStatus === statusValue;
                    
                    // Property filter
                    const propertyMatch = propertyValue === 'all' || rowProperty === propertyValue;
                    
                    // Search filter
                    const searchMatch = tenantName.includes(searchValue) || tenantEmail.includes(searchValue) || property.includes(searchValue);

                    row.style.display = tabMatch && statusMatch && propertyMatch && searchMatch ? '' : 'none';
                });
            }

            showAddTenantModal() {
                this.addTenantModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                // Reset form
                document.getElementById('add-tenant-form').reset();
                document.getElementById('document-list').innerHTML = '';
                document.getElementById('document-list').classList.add('hidden');
            }

            closeAddTenantModal() {
                this.addTenantModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            handleAddTenantForm(e) {
                const formData = new FormData(e.target);
                const tenantData = Object.fromEntries(formData.entries());
                
                // Basic validation
                if (!this.validateTenantForm(tenantData)) {
                    return;
                }

                // Generate tenant ID
                const tenantId = this.generateTenantId();
                tenantData.id = tenantId;
                tenantData.status = 'active';
                tenantData.joinDate = new Date().toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

                // Add to tenants object
                this.tenants[tenantId] = tenantData;

                // Show success message
                alert('Tenant added successfully!');
                this.closeAddTenantModal();
                
                // Update the table
                this.renderTenantsTable();
                console.log('New tenant added:', tenantData);
            }

            validateTenantForm(data) {
                const requiredFields = ['firstName', 'lastName', 'email', 'phone', 'property', 'unitNumber', 'monthlyRent', 'leaseStart', 'leaseEnd', 'leaseDuration'];
                
                for (const field of requiredFields) {
                    if (!data[field] || data[field].trim() === '') {
                        alert(`Please fill in the ${field.replace(/([A-Z])/g, ' $1').toLowerCase()} field.`);
                        return false;
                    }
                }

                // Email validation
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(data.email)) {
                    alert('Please enter a valid email address.');
                    return false;
                }

                return true;
            }

            generateTenantId() {
                const existingIds = Object.keys(this.tenants).map(id => parseInt(id));
                const maxId = Math.max(...existingIds);
                return maxId + 1;
            }

            handleFileUpload(files) {
                const documentList = document.getElementById('document-list');
                
                for (const file of files) {
                    // Validate file type and size
                    if (!this.validateFile(file)) {
                        continue;
                    }

                    // Create document item
                    const documentItem = this.createDocumentItem(file);
                    documentList.appendChild(documentItem);
                }

                if (documentList.children.length > 0) {
                    documentList.classList.remove('hidden');
                }
            }

            validateFile(file) {
                const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
                const maxSize = 10 * 1024 * 1024; // 10MB

                if (!allowedTypes.includes(file.type)) {
                    alert(`File type not supported: ${file.name}. Please upload PDF, JPG, or PNG files.`);
                    return false;
                }

                if (file.size > maxSize) {
                    alert(`File too large: ${file.name}. Maximum size is 10MB.`);
                    return false;
                }

                return true;
            }

            createDocumentItem(file) {
                const item = document.createElement('div');
                item.className = 'document-item';
                
                const fileIcon = file.type === 'application/pdf' ? 'fa-file-pdf' : 'fa-file-image';
                const fileSize = this.formatFileSize(file.size);

                item.innerHTML = `
                    <div class="document-info">
                        <i class="fas ${fileIcon}"></i>
                        <span>${file.name} (${fileSize})</span>
                    </div>
                    <button type="button" class="btn-remove" onclick="this.closest('.document-item').remove(); updateDocumentList();">
                        <i class="fas fa-times"></i>
                    </button>
                `;

                return item;
            }

            formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            viewTenant(tenantId) {
                const tenant = this.tenants[tenantId];
                if (!tenant) return;

                alert(`Viewing tenant: ${tenant.name}\nEmail: ${tenant.email}\nPhone: ${tenant.phone}\nProperty: ${tenant.property}`);
                // In a real application, you would open the tenant details modal here
            }
        }

        // Helper function to update document list visibility
        function updateDocumentList() {
            const documentList = document.getElementById('document-list');
            if (documentList.children.length === 0) {
                documentList.classList.add('hidden');
            }
        }

        // Initialize the application when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            new TenantManager();
        });
    </script>
</body>
</html>