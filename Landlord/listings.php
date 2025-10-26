<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/icons/smartlogo.png">
    <title>SmartHunt - Properties Management</title>
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

        /* Properties Management Styles */
        .properties-container {
            background: var(--light);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .properties-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .properties-header h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .properties-header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .properties-actions {
            display: flex;
            gap: 15px;
        }

        .properties-actions button {
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

        .properties-actions button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .properties-content {
            padding: 30px;
        }

        .properties-tabs {
            display: flex;
            background: var(--light-gray);
            border-bottom: 1px solid var(--gray);
            margin-bottom: 25px;
        }

        .properties-tab {
            padding: 15px 25px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
            font-weight: 500;
        }

        .properties-tab.active {
            border-bottom-color: var(--primary);
            color: var(--primary);
            background: white;
        }

        .properties-tab-badge {
            background: var(--primary);
            color: white;
            border-radius: 10px;
            padding: 2px 8px;
            font-size: 12px;
            margin-left: 5px;
        }

        .properties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
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
            transition: transform 0.3s;
        }

        .property-card:hover .property-image img {
            transform: scale(1.05);
        }

        .property-status {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
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

        .property-favorite {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(255, 255, 255, 0.9);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .property-favorite:hover {
            background: white;
            transform: scale(1.1);
        }

        .property-favorite.active {
            background: var(--primary);
            color: white;
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

        .form-content {
            padding: 30px;
        }

        .form-section {
            margin-bottom: 40px;
        }

        .form-section h2 {
            font-size: 20px;
            margin-bottom: 20px;
            color: var(--dark);
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
            grid-column: span 2;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--gray);
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255, 56, 92, 0.2);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }

        .amenity-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .amenity-item input {
            width: auto;
        }

        .image-upload-area {
            border: 2px dashed var(--gray);
            border-radius: 8px;
            padding: 40px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: var(--light-gray);
        }

        .image-upload-area:hover {
            border-color: var(--primary);
            background: rgba(255, 56, 92, 0.05);
        }

        .image-upload-area i {
            font-size: 40px;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .image-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .preview-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            height: 120px;
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
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--light-gray);
        }

        .btn-cancel {
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

        .btn-cancel:hover {
            background: var(--gray);
        }

        .btn-submit {
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

        .btn-submit:hover {
            background: var(--primary-light);
        }

        /* Property Details Modal */
        .property-modal {
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
            max-width: 900px;
            max-height: 90vh;
            overflow-y: auto;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text);
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
            font-size: 15px;
            max-width: 400px;
            margin: 0 auto 25px;
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
            .properties-grid {
                grid-template-columns: 1fr;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
            .form-group.full-width {
                grid-column: span 1;
            }
            .properties-actions {
                flex-direction: column;
                width: 100%;
            }
            .properties-tabs {
                flex-wrap: wrap;
            }
            .properties-tab {
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
                    <a href="#" class="active" data-content="properties"><i class="fas fa-building"></i> Properties</a>
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
                <div class="navbar-brand">Properties Management</div>
                
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

            <!-- Properties Management Section -->
            <section class="content" id="properties-content">
                <div class="properties-container">
                    <div class="properties-header">
                        <div>
                            <h1><i class="fas fa-building"></i> My Properties</h1>
                            <p>Manage your rental properties and listings</p>
                        </div>
                        <div class="properties-actions">
                            <button id="add-property-btn">
                                <i class="fas fa-plus"></i> Add New Property
                            </button>
                            <button id="import-properties">
                                <i class="fas fa-upload"></i> Import Properties
                            </button>
                        </div>
                    </div>
                    
                    <div class="properties-content">
                        <div class="properties-tabs">
                            <div class="properties-tab active" data-tab="all">
                                All Properties <span class="properties-tab-badge">8</span>
                            </div>
                            <div class="properties-tab" data-tab="available">
                                Available <span class="properties-tab-badge">3</span>
                            </div>
                            <div class="properties-tab" data-tab="occupied">
                                Occupied <span class="properties-tab-badge">4</span>
                            </div>
                            <div class="properties-tab" data-tab="maintenance">
                                Maintenance <span class="properties-tab-badge">1</span>
                            </div>
                        </div>
                        
                        <div class="properties-grid">
                            <!-- Property Card 1 -->
                            <div class="property-card" data-status="available">
                                <div class="property-image">
                                    <img src="https://placehold.co/600x400/FF385C/FFFFFF?text=Modern+Apartment" alt="Modern Apartment">
                                    <div class="property-status status-available">Available</div>
                                    <div class="property-favorite">
                                        <i class="far fa-heart"></i>
                                    </div>
                                </div>
                                <div class="property-details">
                                    <div class="property-price">KES 35,000 <span>/month</span></div>
                                    <h3 class="property-title">Luxury 2-Bedroom Apartment</h3>
                                    <div class="property-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        Westlands, Nairobi
                                    </div>
                                    <div class="property-features">
                                        <div class="property-feature">
                                            <i class="fas fa-bed"></i>
                                            <span>2 Beds</span>
                                        </div>
                                        <div class="property-feature">
                                            <i class="fas fa-bath"></i>
                                            <span>2 Baths</span>
                                        </div>
                                        <div class="property-feature">
                                            <i class="fas fa-vector-square"></i>
                                            <span>1200 sqft</span>
                                        </div>
                                    </div>
                                    <div class="property-actions">
                                        <button class="btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn-view">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <button class="btn-delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Property Card 2 -->
                            <div class="property-card" data-status="occupied">
                                <div class="property-image">
                                    <img src="https://placehold.co/600x400/4285F4/FFFFFF?text=Studio+Apartment" alt="Studio Apartment">
                                    <div class="property-status status-occupied">Occupied</div>
                                    <div class="property-favorite active">
                                        <i class="fas fa-heart"></i>
                                    </div>
                                </div>
                                <div class="property-details">
                                    <div class="property-price">KES 18,000 <span>/month</span></div>
                                    <h3 class="property-title">Cozy Studio Apartment</h3>
                                    <div class="property-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        Kilimani, Nairobi
                                    </div>
                                    <div class="property-features">
                                        <div class="property-feature">
                                            <i class="fas fa-bed"></i>
                                            <span>1 Bed</span>
                                        </div>
                                        <div class="property-feature">
                                            <i class="fas fa-bath"></i>
                                            <span>1 Bath</span>
                                        </div>
                                        <div class="property-feature">
                                            <i class="fas fa-vector-square"></i>
                                            <span>600 sqft</span>
                                        </div>
                                    </div>
                                    <div class="property-actions">
                                        <button class="btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn-view">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <button class="btn-delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Property Card 3 -->
                            <div class="property-card" data-status="maintenance">
                                <div class="property-image">
                                    <img src="https://placehold.co/600x400/00A699/FFFFFF?text=Family+House" alt="Family House">
                                    <div class="property-status status-maintenance">Maintenance</div>
                                    <div class="property-favorite">
                                        <i class="far fa-heart"></i>
                                    </div>
                                </div>
                                <div class="property-details">
                                    <div class="property-price">KES 65,000 <span>/month</span></div>
                                    <h3 class="property-title">Spacious 4-Bedroom House</h3>
                                    <div class="property-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        Karen, Nairobi
                                    </div>
                                    <div class="property-features">
                                        <div class="property-feature">
                                            <i class="fas fa-bed"></i>
                                            <span>4 Beds</span>
                                        </div>
                                        <div class="property-feature">
                                            <i class="fas fa-bath"></i>
                                            <span>3 Baths</span>
                                        </div>
                                        <div class="property-feature">
                                            <i class="fas fa-vector-square"></i>
                                            <span>2200 sqft</span>
                                        </div>
                                    </div>
                                    <div class="property-actions">
                                        <button class="btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn-view">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <button class="btn-delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Property Card 4 -->
                            <div class="property-card" data-status="available">
                                <div class="property-image">
                                    <img src="https://placehold.co/600x400/FFB400/FFFFFF?text=Executive+Suite" alt="Executive Suite">
                                    <div class="property-status status-available">Available</div>
                                    <div class="property-favorite">
                                        <i class="far fa-heart"></i>
                                    </div>
                                </div>
                                <div class="property-details">
                                    <div class="property-price">KES 42,000 <span>/month</span></div>
                                    <h3 class="property-title">Executive 3-Bedroom Suite</h3>
                                    <div class="property-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        Lavington, Nairobi
                                    </div>
                                    <div class="property-features">
                                        <div class="property-feature">
                                            <i class="fas fa-bed"></i>
                                            <span>3 Beds</span>
                                        </div>
                                        <div class="property-feature">
                                            <i class="fas fa-bath"></i>
                                            <span>2 Baths</span>
                                        </div>
                                        <div class="property-feature">
                                            <i class="fas fa-vector-square"></i>
                                            <span>1500 sqft</span>
                                        </div>
                                    </div>
                                    <div class="property-actions">
                                        <button class="btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn-view">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <button class="btn-delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Add Property Form (Initially Hidden) -->
                <div class="add-property-container hidden" id="add-property-form">
                    <div class="form-header">
                        <h1><i class="fas fa-plus-circle"></i> Add New Property</h1>
                        <p>Fill in the details to list your property</p>
                    </div>
                    
                    <div class="form-content">
                        <form id="property-form">
                            <!-- Basic Information -->
                            <div class="form-section">
                                <h2><i class="fas fa-info-circle"></i> Basic Information</h2>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="property-title">Property Title</label>
                                        <input type="text" id="property-title" placeholder="e.g., Luxury 2-Bedroom Apartment" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="property-type">Property Type</label>
                                        <select id="property-type" required>
                                            <option value="">Select Type</option>
                                            <option value="apartment">Apartment</option>
                                            <option value="house">House</option>
                                            <option value="studio">Studio</option>
                                            <option value="condo">Condo</option>
                                            <option value="townhouse">Townhouse</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="price">Monthly Rent (KES)</label>
                                        <input type="number" id="price" placeholder="35000" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="property-status">Status</label>
                                        <select id="property-status" required>
                                            <option value="available">Available</option>
                                            <option value="occupied">Occupied</option>
                                            <option value="maintenance">Under Maintenance</option>
                                        </select>
                                    </div>
                                    <div class="form-group full-width">
                                        <label for="description">Property Description</label>
                                        <textarea id="description" placeholder="Describe your property, features, and amenities..." required></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Location Details -->
                            <div class="form-section">
                                <h2><i class="fas fa-map-marker-alt"></i> Location Details</h2>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="address">Street Address</label>
                                        <input type="text" id="address" placeholder="123 Management Plaza" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="city">City</label>
                                        <input type="text" id="city" placeholder="Nairobi" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="neighborhood">Neighborhood/Area</label>
                                        <input type="text" id="neighborhood" placeholder="Westlands" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="zip-code">ZIP Code</label>
                                        <input type="text" id="zip-code" placeholder="00100">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Property Details -->
                            <div class="form-section">
                                <h2><i class="fas fa-home"></i> Property Details</h2>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="bedrooms">Bedrooms</label>
                                        <select id="bedrooms" required>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5+">5+</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="bathrooms">Bathrooms</label>
                                        <select id="bathrooms" required>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5+">5+</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="area">Area (sq ft)</label>
                                        <input type="number" id="area" placeholder="1200" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="year-built">Year Built</label>
                                        <input type="number" id="year-built" placeholder="2020">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Amenities -->
                            <div class="form-section">
                                <h2><i class="fas fa-concierge-bell"></i> Amenities</h2>
                                <div class="amenities-grid">
                                    <div class="amenity-item">
                                        <input type="checkbox" id="wifi" name="amenities">
                                        <label for="wifi">Wi-Fi</label>
                                    </div>
                                    <div class="amenity-item">
                                        <input type="checkbox" id="parking" name="amenities">
                                        <label for="parking">Parking</label>
                                    </div>
                                    <div class="amenity-item">
                                        <input type="checkbox" id="pool" name="amenities">
                                        <label for="pool">Swimming Pool</label>
                                    </div>
                                    <div class="amenity-item">
                                        <input type="checkbox" id="gym" name="amenities">
                                        <label for="gym">Gym</label>
                                    </div>
                                    <div class="amenity-item">
                                        <input type="checkbox" id="ac" name="amenities">
                                        <label for="ac">Air Conditioning</label>
                                    </div>
                                    <div class="amenity-item">
                                        <input type="checkbox" id="heating" name="amenities">
                                        <label for="heating">Heating</label>
                                    </div>
                                    <div class="amenity-item">
                                        <input type="checkbox" id="laundry" name="amenities">
                                        <label for="laundry">Laundry</label>
                                    </div>
                                    <div class="amenity-item">
                                        <input type="checkbox" id="security" name="amenities">
                                        <label for="security">Security</label>
                                    </div>
                                    <div class="amenity-item">
                                        <input type="checkbox" id="balcony" name="amenities">
                                        <label for="balcony">Balcony</label>
                                    </div>
                                    <div class="amenity-item">
                                        <input type="checkbox" id="furnished" name="amenities">
                                        <label for="furnished">Furnished</label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Property Images -->
                            <div class="form-section">
                                <h2><i class="fas fa-images"></i> Property Images</h2>
                                <div class="form-group">
                                    <div class="image-upload-area" id="image-upload">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <h3>Upload Property Images</h3>
                                        <p>Drag & drop images here or click to browse</p>
                                        <input type="file" id="file-input" multiple accept="image/*" style="display: none;">
                                    </div>
                                    <div class="image-preview" id="image-preview">
                                     
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="button" class="btn-cancel" id="cancel-form">Cancel</button>
                                <button type="submit" class="btn-submit">Add Property</button>
                            </div>
                        </form>
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
          
            const propertyTabs = document.querySelectorAll('.properties-tab');
            const propertyCards = document.querySelectorAll('.property-card');
            
            propertyTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabType = this.getAttribute('data-tab');
                 
                    propertyTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                
                    propertyCards.forEach(card => {
                        if (tabType === 'all') {
                            card.style.display = 'block';
                        } else {
                            const cardStatus = card.getAttribute('data-status');
                            if (cardStatus === tabType) {
                                card.style.display = 'block';
                            } else {
                                card.style.display = 'none';
                            }
                        }
                    });
                });
            });
            
        
            const addPropertyBtn = document.getElementById('add-property-btn');
            const addPropertyForm = document.getElementById('add-property-form');
            const propertiesContainer = document.querySelector('.properties-container');
            const cancelFormBtn = document.getElementById('cancel-form');
            
            if (addPropertyBtn) {
                addPropertyBtn.addEventListener('click', function() {
                    propertiesContainer.classList.add('hidden');
                    addPropertyForm.classList.remove('hidden');
                });
            }
            
            if (cancelFormBtn) {
                cancelFormBtn.addEventListener('click', function() {
                    addPropertyForm.classList.add('hidden');
                    propertiesContainer.classList.remove('hidden');
                });
            }
            
           
            const propertyForm = document.getElementById('property-form');
            
            if (propertyForm) {
                propertyForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                   
                    const formData = {
                        title: document.getElementById('property-title').value,
                        type: document.getElementById('property-type').value,
                        price: document.getElementById('price').value,
                        status: document.getElementById('property-status').value,
                        description: document.getElementById('description').value,
                        address: document.getElementById('address').value,
                        city: document.getElementById('city').value,
                        neighborhood: document.getElementById('neighborhood').value,
                        bedrooms: document.getElementById('bedrooms').value,
                        bathrooms: document.getElementById('bathrooms').value,
                        area: document.getElementById('area').value
                    };
                    
                   
                    alert('Property added successfully!');
                    propertyForm.reset();
                    clearImagePreviews();
                    
                  
                    addPropertyForm.classList.add('hidden');
                    propertiesContainer.classList.remove('hidden');
                });
            }
            
       
            const imageUpload = document.getElementById('image-upload');
            const fileInput = document.getElementById('file-input');
            const imagePreview = document.getElementById('image-preview');
            
            if (imageUpload) {
                imageUpload.addEventListener('click', function() {
                    fileInput.click();
                });
                
               
                imageUpload.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    imageUpload.style.borderColor = 'var(--primary)';
                    imageUpload.style.background = 'rgba(255, 56, 92, 0.1)';
                });
                
                imageUpload.addEventListener('dragleave', function() {
                    imageUpload.style.borderColor = 'var(--gray)';
                    imageUpload.style.background = 'var(--light-gray)';
                });
                
                imageUpload.addEventListener('drop', function(e) {
                    e.preventDefault();
                    imageUpload.style.borderColor = 'var(--gray)';
                    imageUpload.style.background = 'var(--light-gray)';
                    
                    const files = e.dataTransfer.files;
                    handleFiles(files);
                });
            }
            
            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    handleFiles(this.files);
                });
            }
            
            function handleFiles(files) {
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    if (!file.type.match('image.*')) continue;
                    
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const previewItem = document.createElement('div');
                        previewItem.className = 'preview-item';
                        previewItem.innerHTML = `
                            <img src="${e.target.result}" alt="Preview">
                            <div class="preview-remove">&times;</div>
                        `;
                        
                        imagePreview.appendChild(previewItem);
                        
                       
                        const removeBtn = previewItem.querySelector('.preview-remove');
                        removeBtn.addEventListener('click', function() {
                            imagePreview.removeChild(previewItem);
                        });
                    };
                    
                    reader.readAsDataURL(file);
                }
            }
            
            function clearImagePreviews() {
                imagePreview.innerHTML = '';
            }
            
   
            const favoriteButtons = document.querySelectorAll('.property-favorite');
            const editButtons = document.querySelectorAll('.btn-edit');
            const viewButtons = document.querySelectorAll('.btn-view');
            const deleteButtons = document.querySelectorAll('.btn-delete');
  
            favoriteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const icon = this.querySelector('i');
                    
                    if (icon.classList.contains('far')) {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        this.classList.add('active');
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        this.classList.remove('active');
                    }
                });
            });
            
       
            editButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    alert('Edit property functionality would open here');
                });
            });
            
  
            viewButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    alert('View property details functionality would open here');
                });
            });
            
       
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const propertyCard = this.closest('.property-card');
                    const propertyTitle = propertyCard.querySelector('.property-title').textContent;
                    
                    if (confirm(`Are you sure you want to delete "${propertyTitle}"? This action cannot be undone.`)) {
                        propertyCard.style.opacity = '0';
                        propertyCard.style.transform = 'scale(0.8)';
                        
                        setTimeout(() => {
                            propertyCard.remove();
                            updatePropertyCounts();
                        }, 300);
                    }
                });
            });
            
        
            function updatePropertyCounts() {
                const totalProperties = document.querySelectorAll('.property-card').length;
                const availableProperties = document.querySelectorAll('.property-card[data-status="available"]').length;
                const occupiedProperties = document.querySelectorAll('.property-card[data-status="occupied"]').length;
                const maintenanceProperties = document.querySelectorAll('.property-card[data-status="maintenance"]').length;
                
           
                const allBadge = document.querySelector('.properties-tab[data-tab="all"] .properties-tab-badge');
                const availableBadge = document.querySelector('.properties-tab[data-tab="available"] .properties-tab-badge');
                const occupiedBadge = document.querySelector('.properties-tab[data-tab="occupied"] .properties-tab-badge');
                const maintenanceBadge = document.querySelector('.properties-tab[data-tab="maintenance"] .properties-tab-badge');
                
                if (allBadge) allBadge.textContent = totalProperties;
                if (availableBadge) availableBadge.textContent = availableProperties;
                if (occupiedBadge) occupiedBadge.textContent = occupiedProperties;
                if (maintenanceBadge) maintenanceBadge.textContent = maintenanceProperties;
            }
            
           
            const importBtn = document.getElementById('import-properties');
            if (importBtn) {
                importBtn.addEventListener('click', function() {
                    alert('Import properties functionality would open here. This would allow bulk uploading of properties via CSV or connecting to external platforms.');
                });
            }
        });
    </script>
</body>
</html>