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

        /* Profile Settings Styles - Maintaining existing styling patterns */
        .profile-container {
            background: var(--light);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .profile-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 25px 30px;
            text-align: center;
        }

        .profile-header h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .profile-header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .profile-content {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 0;
        }

        .profile-sidebar {
            background: var(--light-gray);
            padding: 30px 0;
            border-right: 1px solid var(--gray);
        }

        .profile-nav {
            list-style: none;
            padding: 0;
        }

        .profile-nav-item {
            padding: 15px 30px;
            cursor: pointer;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }

        .profile-nav-item:hover {
            background: rgba(255, 56, 92, 0.05);
        }

        .profile-nav-item.active {
            background: rgba(255, 56, 92, 0.1);
            border-left-color: var(--primary);
            color: var(--primary);
        }

        .profile-nav-item i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }

        .profile-main {
            padding: 30px;
        }

        .profile-section {
            display: none;
        }

        .profile-section.active {
            display: block;
        }

        .profile-section h2 {
            font-size: 24px;
            margin-bottom: 25px;
            color: var(--dark);
            padding-bottom: 15px;
            border-bottom: 2px solid var(--light-gray);
        }

        .profile-card {
            background: var(--light);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 25px;
        }

        .profile-card h3 {
            font-size: 18px;
            margin-bottom: 20px;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile-card h3 i {
            color: var(--primary);
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

        .profile-avatar {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
        }

        .avatar-container {
            position: relative;
        }

        .avatar-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary);
        }

        .avatar-upload {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: var(--primary);
            color: white;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .avatar-upload:hover {
            background: var(--primary-light);
            transform: scale(1.1);
        }

        .avatar-info h4 {
            font-size: 18px;
            margin-bottom: 5px;
            color: var(--dark);
        }

        .avatar-info p {
            color: var(--text);
            font-size: 14px;
        }

        .security-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border: 1px solid var(--light-gray);
            border-radius: 8px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }

        .security-item:hover {
            border-color: var(--primary);
            background: rgba(255, 56, 92, 0.02);
        }

        .security-info h4 {
            font-size: 16px;
            margin-bottom: 5px;
            color: var(--dark);
        }

        .security-info p {
            font-size: 14px;
            color: var(--text);
        }

        .security-status {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-active {
            background: rgba(0, 166, 153, 0.1);
            color: var(--success);
        }

        .status-inactive {
            background: rgba(255, 90, 95, 0.1);
            color: var(--danger);
        }

        .notification-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--light-gray);
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-info h4 {
            font-size: 16px;
            margin-bottom: 5px;
            color: var(--dark);
        }

        .notification-info p {
            font-size: 14px;
            color: var(--text);
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: var(--gray);
            transition: .4s;
            border-radius: 24px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: var(--success);
        }

        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }

        .preference-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--light-gray);
        }

        .preference-item:last-child {
            border-bottom: none;
        }

        .preference-info h4 {
            font-size: 16px;
            margin-bottom: 5px;
            color: var(--dark);
        }

        .preference-info p {
            font-size: 14px;
            color: var(--text);
        }

        .profile-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
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

        .btn-save {
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

        .btn-save:hover {
            background: var(--primary-light);
        }

        .btn-delete {
            background: var(--danger);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-delete:hover {
            background: #e04a50;
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
            .profile-content {
                grid-template-columns: 1fr;
            }
            .profile-sidebar {
                border-right: none;
                border-bottom: 1px solid var(--gray);
            }
            .profile-nav {
                display: flex;
                overflow-x: auto;
                padding: 0 20px;
            }
            .profile-nav-item {
                white-space: nowrap;
                border-left: none;
                border-bottom: 4px solid transparent;
            }
            .profile-nav-item.active {
                border-left: none;
                border-bottom-color: var(--primary);
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
            .form-group.full-width {
                grid-column: span 1;
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
                <li><a href="#" data-content="location"><i class="fas fa-map-marked-alt"></i> Location</a></li>
                <li><a href="#" data-content="announcements"><i class="fas fa-bullhorn"></i> Announcements</a></li>
                <li><a href="#" data-content="reports"><i class="fas fa-chart-bar"></i> Reports</a></li>
                <li><a href="#" class="active" data-content="profile-settings"><i class="fas fa-user-cog"></i> Profile Setting</a></li>
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

            <!-- Profile Settings Section -->
            <section class="content" id="profile-settings-content">
                <div class="profile-container">
                    <div class="profile-header">
                        <h1><i class="fas fa-user-cog"></i> Profile Settings</h1>
                        <p>Manage your account settings and preferences</p>
                    </div>
                    
                    <div class="profile-content">
                        <div class="profile-sidebar">
                            <ul class="profile-nav">
                                <li class="profile-nav-item active" data-section="profile">
                                    <i class="fas fa-user"></i> Profile Information
                                </li>
                                <li class="profile-nav-item" data-section="security">
                                    <i class="fas fa-shield-alt"></i> Security
                                </li>
                                <li class="profile-nav-item" data-section="notifications">
                                    <i class="fas fa-bell"></i> Notifications
                                </li>
                                <li class="profile-nav-item" data-section="preferences">
                                    <i class="fas fa-sliders-h"></i> Preferences
                                </li>
                                <li class="profile-nav-item" data-section="billing">
                                    <i class="fas fa-credit-card"></i> Billing
                                </li>
                            </ul>
                        </div>
                        
                        <div class="profile-main">
                            <!-- Profile Information Section -->
                            <div class="profile-section active" id="profile-section">
                                <h2>Profile Information</h2>
                                
                                <div class="profile-card">
                                    <div class="profile-avatar">
                                        <div class="avatar-container">
                                            <img src="https://placehold.co/100x100/4285F4/FFFFFF?text=JD" alt="Profile Avatar" class="avatar-image">
                                            <button class="avatar-upload">
                                                <i class="fas fa-camera"></i>
                                            </button>
                                        </div>
                                        <div class="avatar-info">
                                            <h4>John Doe</h4>
                                            <p>Landlord Account</p>
                                            <p>Member since January 2022</p>
                                        </div>
                                    </div>
                                    
                                    <div class="form-grid">
                                        <div class="form-group">
                                            <label for="first-name">First Name</label>
                                            <input type="text" id="first-name" value="John">
                                        </div>
                                        <div class="form-group">
                                            <label for="last-name">Last Name</label>
                                            <input type="text" id="last-name" value="Doe">
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email Address</label>
                                            <input type="email" id="email" value="john.doe@example.com">
                                        </div>
                                        <div class="form-group">
                                            <label for="phone">Phone Number</label>
                                            <input type="tel" id="phone" value="+254 712 345 678">
                                        </div>
                                        <div class="form-group full-width">
                                            <label for="bio">Bio</label>
                                            <textarea id="bio" placeholder="Tell us about yourself...">Experienced landlord with 5+ years in property management. Specializing in student accommodations near campus areas.</textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="profile-card">
                                    <h3><i class="fas fa-map-marker-alt"></i> Address Information</h3>
                                    <div class="form-grid">
                                        <div class="form-group full-width">
                                            <label for="street-address">Street Address</label>
                                            <input type="text" id="street-address" value="123 Management Plaza">
                                        </div>
                                        <div class="form-group">
                                            <label for="city">City</label>
                                            <input type="text" id="city" value="Nairobi">
                                        </div>
                                        <div class="form-group">
                                            <label for="state">State/Region</label>
                                            <input type="text" id="state" value="Nairobi County">
                                        </div>
                                        <div class="form-group">
                                            <label for="zip-code">ZIP Code</label>
                                            <input type="text" id="zip-code" value="00100">
                                        </div>
                                        <div class="form-group">
                                            <label for="country">Country</label>
                                            <select id="country">
                                                <option value="kenya" selected>Kenya</option>
                                                <option value="uganda">Uganda</option>
                                                <option value="tanzania">Tanzania</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="profile-actions">
                                    <button class="btn-cancel">Cancel</button>
                                    <button class="btn-save">Save Changes</button>
                                </div>
                            </div>
                            
                            <!-- Security Section -->
                            <div class="profile-section" id="security-section">
                                <h2>Security Settings</h2>
                                
                                <div class="profile-card">
                                    <h3><i class="fas fa-lock"></i> Password & Authentication</h3>
                                    
                                    <div class="security-item">
                                        <div class="security-info">
                                            <h4>Password</h4>
                                            <p>Last changed 3 months ago</p>
                                        </div>
                                        <div class="security-status">
                                            <span class="status-badge status-active">Active</span>
                                            <button class="btn-save" style="padding: 8px 15px;">Change</button>
                                        </div>
                                    </div>
                                    
                                    <div class="security-item">
                                        <div class="security-info">
                                            <h4>Two-Factor Authentication</h4>
                                            <p>Add an extra layer of security to your account</p>
                                        </div>
                                        <div class="security-status">
                                            <span class="status-badge status-inactive">Inactive</span>
                                            <button class="btn-save" style="padding: 8px 15px;">Enable</button>
                                        </div>
                                    </div>
                                    
                                    <div class="security-item">
                                        <div class="security-info">
                                            <h4>Login Activity</h4>
                                            <p>Recent account access information</p>
                                        </div>
                                        <div class="security-status">
                                            <button class="btn-save" style="padding: 8px 15px;">View Logs</button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="profile-card">
                                    <h3><i class="fas fa-desktop"></i> Active Sessions</h3>
                                    <p style="margin-bottom: 20px; color: var(--text);">These are the devices that are currently logged into your account.</p>
                                    
                                    <div class="security-item">
                                        <div class="security-info">
                                            <h4>Chrome on Windows</h4>
                                            <p>Nairobi, Kenya • Current session</p>
                                        </div>
                                        <button class="btn-cancel" style="padding: 8px 15px;">Logout</button>
                                    </div>
                                    
                                    <div class="security-item">
                                        <div class="security-info">
                                            <h4>Safari on iPhone</h4>
                                            <p>Nairobi, Kenya • 2 days ago</p>
                                        </div>
                                        <button class="btn-cancel" style="padding: 8px 15px;">Logout</button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Notifications Section -->
                            <div class="profile-section" id="notifications-section">
                                <h2>Notification Preferences</h2>
                                
                                <div class="profile-card">
                                    <h3><i class="fas fa-envelope"></i> Email Notifications</h3>
                                    
                                    <div class="notification-item">
                                        <div class="notification-info">
                                            <h4>New Inquiry Alerts</h4>
                                            <p>Get notified when someone inquires about your properties</p>
                                        </div>
                                        <label class="toggle-switch">
                                            <input type="checkbox" checked>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                    
                                    <div class="notification-item">
                                        <div class="notification-info">
                                            <h4>Payment Reminders</h4>
                                            <p>Receive reminders for upcoming and overdue payments</p>
                                        </div>
                                        <label class="toggle-switch">
                                            <input type="checkbox" checked>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                    
                                    <div class="notification-item">
                                        <div class="notification-info">
                                            <h4>Maintenance Requests</h4>
                                            <p>Notifications for new maintenance requests from tenants</p>
                                        </div>
                                        <label class="toggle-switch">
                                            <input type="checkbox" checked>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                    
                                    <div class="notification-item">
                                        <div class="notification-info">
                                            <h4>System Updates</h4>
                                            <p>Important updates about the SmartHunt platform</p>
                                        </div>
                                        <label class="toggle-switch">
                                            <input type="checkbox">
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="profile-card">
                                    <h3><i class="fas fa-bell"></i> Push Notifications</h3>
                                    
                                    <div class="notification-item">
                                        <div class="notification-info">
                                            <h4>Mobile Push Notifications</h4>
                                            <p>Receive push notifications on your mobile device</p>
                                        </div>
                                        <label class="toggle-switch">
                                            <input type="checkbox" checked>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                    
                                    <div class="notification-item">
                                        <div class="notification-info">
                                            <h4>Desktop Notifications</h4>
                                            <p>Show notifications on your desktop browser</p>
                                        </div>
                                        <label class="toggle-switch">
                                            <input type="checkbox">
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="profile-actions">
                                    <button class="btn-cancel">Reset to Default</button>
                                    <button class="btn-save">Save Preferences</button>
                                </div>
                            </div>
                            
                            <!-- Preferences Section -->
                            <div class="profile-section" id="preferences-section">
                                <h2>Account Preferences</h2>
                                
                                <div class="profile-card">
                                    <h3><i class="fas fa-globe"></i> Language & Region</h3>
                                    
                                    <div class="form-grid">
                                        <div class="form-group">
                                            <label for="language">Language</label>
                                            <select id="language">
                                                <option value="en" selected>English</option>
                                                <option value="sw">Swahili</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="timezone">Timezone</label>
                                            <select id="timezone">
                                                <option value="east-africa" selected>East Africa Time (GMT+3)</option>
                                                <option value="utc">UTC</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="currency">Currency</label>
                                            <select id="currency">
                                                <option value="kes" selected>Kenyan Shilling (KES)</option>
                                                <option value="usd">US Dollar (USD)</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="date-format">Date Format</label>
                                            <select id="date-format">
                                                <option value="dd/mm/yyyy" selected>DD/MM/YYYY</option>
                                                <option value="mm/dd/yyyy">MM/DD/YYYY</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="profile-card">
                                    <h3><i class="fas fa-eye"></i> Privacy Settings</h3>
                                    
                                    <div class="preference-item">
                                        <div class="preference-info">
                                            <h4>Profile Visibility</h4>
                                            <p>Control who can see your profile information</p>
                                        </div>
                                        <select style="padding: 8px 12px; border-radius: 6px; border: 1px solid var(--gray);">
                                            <option selected>Private</option>
                                            <option>Public</option>
                                        </select>
                                    </div>
                                    
                                    <div class="preference-item">
                                        <div class="preference-info">
                                            <h4>Data Sharing</h4>
                                            <p>Allow anonymous data sharing to improve our services</p>
                                        </div>
                                        <label class="toggle-switch">
                                            <input type="checkbox">
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                    
                                    <div class="preference-item">
                                        <div class="preference-info">
                                            <h4>Marketing Emails</h4>
                                            <p>Receive emails about new features and promotions</p>
                                        </div>
                                        <label class="toggle-switch">
                                            <input type="checkbox">
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>
                                
                                                               <div class="profile-actions">
                                    <button class="btn-cancel">Reset to Default</button>
                                    <button class="btn-save">Save Preferences</button>
                                </div>
                            </div>
                            
                            <!-- Billing Section -->
                            <div class="profile-section" id="billing-section">
                                <h2>Billing Information</h2>
                                
                                <div class="profile-card">
                                    <h3><i class="fas fa-credit-card"></i> Payment Methods</h3>
                                    
                                    <div class="security-item">
                                        <div class="security-info">
                                            <h4>Visa ending in 4242</h4>
                                            <p>Expires 12/2024 • Primary payment method</p>
                                        </div>
                                        <div class="security-status">
                                            <button class="btn-cancel" style="padding: 8px 15px;">Edit</button>
                                            <button class="btn-delete" style="padding: 8px 15px;">Remove</button>
                                        </div>
                                    </div>
                                    
                                    <div class="security-item">
                                        <div class="security-info">
                                            <h4>M-Pesa</h4>
                                            <p>+254 712 345 678 • Mobile money</p>
                                        </div>
                                        <div class="security-status">
                                            <button class="btn-cancel" style="padding: 8px 15px;">Edit</button>
                                            <button class="btn-delete" style="padding: 8px 15px;">Remove</button>
                                        </div>
                                    </div>
                                    
                                    <button class="btn-save" style="margin-top: 15px;">
                                        <i class="fas fa-plus"></i> Add Payment Method
                                    </button>
                                </div>
                                
                                <div class="profile-card">
                                    <h3><i class="fas fa-receipt"></i> Billing History</h3>
                                    <p style="margin-bottom: 20px; color: var(--text);">Your recent transactions and invoices</p>
                                    
                                    <div class="security-item">
                                        <div class="security-info">
                                            <h4>Premium Subscription - March 2024</h4>
                                            <p>March 15, 2024 • KES 2,500</p>
                                        </div>
                                        <button class="btn-save" style="padding: 8px 15px;">Download</button>
                                    </div>
                                    
                                    <div class="security-item">
                                        <div class="security-info">
                                            <h4>Premium Subscription - February 2024</h4>
                                            <p>February 15, 2024 • KES 2,500</p>
                                        </div>
                                        <button class="btn-save" style="padding: 8px 15px;">Download</button>
                                    </div>
                                    
                                    <div class="security-item">
                                        <div class="security-info">
                                            <h4>Premium Subscription - January 2024</h4>
                                            <p>January 15, 2024 • KES 2,500</p>
                                        </div>
                                        <button class="btn-save" style="padding: 8px 15px;">Download</button>
                                    </div>
                                </div>
                                
                                <div class="profile-card">
                                    <h3><i class="fas fa-cube"></i> Subscription Plan</h3>
                                    
                                    <div class="security-item">
                                        <div class="security-info">
                                            <h4>Premium Plan</h4>
                                            <p>KES 2,500/month • Next billing date: April 15, 2024</p>
                                        </div>
                                        <div class="security-status">
                                            <span class="status-badge status-active">Active</span>
                                            <button class="btn-delete" style="padding: 8px 15px;">Cancel</button>
                                        </div>
                                    </div>
                                    
                                    <div class="profile-actions">
                                        <button class="btn-cancel">Upgrade Plan</button>
                                        <button class="btn-save">Update Payment</button>
                                    </div>
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
        // Profile navigation functionality
        document.addEventListener('DOMContentLoaded', function() {
            const profileNavItems = document.querySelectorAll('.profile-nav-item');
            const profileSections = document.querySelectorAll('.profile-section');
            
            profileNavItems.forEach(item => {
                item.addEventListener('click', function() {
                    const targetSection = this.getAttribute('data-section');
                    
                    // Remove active class from all items and sections
                    profileNavItems.forEach(navItem => navItem.classList.remove('active'));
                    profileSections.forEach(section => section.classList.remove('active'));
                    
                    // Add active class to clicked item and corresponding section
                    this.classList.add('active');
                    document.getElementById(`${targetSection}-section`).classList.add('active');
                });
            });
            
            // Save button functionality
            const saveButtons = document.querySelectorAll('.btn-save');
            saveButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Show a temporary success message
                    const originalText = this.textContent;
                    this.textContent = 'Saved!';
                    this.style.backgroundColor = 'var(--success)';
                    
                    setTimeout(() => {
                        this.textContent = originalText;
                        this.style.backgroundColor = '';
                    }, 2000);
                });
            });
            
            // Cancel button functionality
            const cancelButtons = document.querySelectorAll('.btn-cancel');
            cancelButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (confirm('Are you sure you want to discard changes?')) {
                        // Reset form fields to their original values
                        const form = this.closest('.profile-section');
                        const inputs = form.querySelectorAll('input, select, textarea');
                        inputs.forEach(input => {
                            // In a real app, you would reset to original values from database
                            // For demo purposes, we'll just blur the field
                            input.blur();
                        });
                    }
                });
            });
            
            // Avatar upload simulation
            const avatarUpload = document.querySelector('.avatar-upload');
            if (avatarUpload) {
                avatarUpload.addEventListener('click', function() {
                    // In a real app, this would open a file picker
                    alert('Avatar upload feature would open here. In a real application, this would allow you to select and crop a new profile image.');
                });
            }
            
            // Toggle switch functionality
            const toggleSwitches = document.querySelectorAll('.toggle-switch input');
            toggleSwitches.forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const label = this.nextElementSibling;
                    if (this.checked) {
                        label.style.backgroundColor = 'var(--success)';
                    } else {
                        label.style.backgroundColor = 'var(--gray)';
                    }
                });
            });
        });
    </script>
</body>
</html>