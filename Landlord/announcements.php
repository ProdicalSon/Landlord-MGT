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

        /* Announcements Styles - Maintaining existing styling patterns */
        .announcements-container {
            background: var(--light);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .announcements-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 25px 30px;
            text-align: center;
        }

        .announcements-header h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .announcements-header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .announcements-controls {
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

        .announcements-stats {
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

        .create-announcement-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .create-announcement-btn:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
        }

        .announcements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 25px;
            padding: 30px;
        }

        .announcement-card {
            background: var(--light);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-left: 4px solid var(--primary);
        }

        .announcement-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .announcement-card.urgent {
            border-left-color: var(--danger);
            background: linear-gradient(135deg, var(--light) 0%, rgba(255, 90, 95, 0.05) 100%);
        }

        .announcement-card.info {
            border-left-color: var(--secondary);
            background: linear-gradient(135deg, var(--light) 0%, rgba(66, 133, 244, 0.05) 100%);
        }

        .announcement-card.warning {
            border-left-color: var(--warning);
            background: linear-gradient(135deg, var(--light) 0%, rgba(255, 180, 0, 0.05) 100%);
        }

        .announcement-card.success {
            border-left-color: var(--success);
            background: linear-gradient(135deg, var(--light) 0%, rgba(0, 166, 153, 0.05) 100%);
        }

        .announcement-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .announcement-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .announcement-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
        }

        .announcement-type {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: white;
        }

        .type-urgent {
            background-color: var(--danger);
        }

        .type-info {
            background-color: var(--secondary);
        }

        .type-warning {
            background-color: var(--warning);
        }

        .type-success {
            background-color: var(--success);
        }

        .announcement-date {
            font-size: 12px;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .announcement-content {
            margin-bottom: 20px;
        }

        .announcement-content p {
            color: var(--text);
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .announcement-target {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text);
            margin-bottom: 15px;
        }

        .announcement-target i {
            color: var(--primary);
        }

        .announcement-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid var(--light-gray);
        }

        .announcement-stats {
            display: flex;
            gap: 15px;
            font-size: 12px;
            color: var(--text);
        }

        .stat {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .stat i {
            color: var(--primary);
        }

        .announcement-btns {
            display: flex;
            gap: 8px;
        }

        .announcement-btn {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            display: flex;
            align-items: center;
            gap: 5px;
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
            background-color: var(--light-gray);
            color: var(--text);
        }

        .btn-view:hover {
            background-color: var(--gray);
        }

        /* Create Announcement Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: var(--light);
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 20px 30px;
            border-radius: 12px 12px 0 0;
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
            padding: 5px;
        }

        .modal-body {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
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
            min-height: 120px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .target-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .target-option {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px;
            border: 1px solid var(--gray);
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .target-option:hover {
            border-color: var(--primary);
        }

        .target-option.selected {
            border-color: var(--primary);
            background: rgba(255, 56, 92, 0.05);
        }

        .modal-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 25px;
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
            .announcements-controls {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }
            .announcements-stats {
                justify-content: center;
            }
            .filter-controls {
                justify-content: center;
            }
            .announcements-grid {
                grid-template-columns: 1fr;
            }
            .form-row {
                grid-template-columns: 1fr;
            }
            .modal-content {
                width: 95%;
                margin: 20px;
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
                <li><a href="#" class="active" data-content="announcements"><i class="fas fa-bullhorn"></i> Announcements</a></li>
                <li><a href="reports.php" data-content="reports"><i class="fas fa-chart-bar"></i> Reports</a></li>
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

            <!-- Announcements Section -->
            <section class="content" id="announcements-content">
                <div class="announcements-container">
                    <div class="announcements-header">
                        <h1><i class="fas fa-bullhorn"></i> Announcements</h1>
                        <p>Create and manage announcements for your tenants</p>
                    </div>
                    
                    <div class="announcements-controls">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="search-announcements" placeholder="Search announcements...">
                        </div>
                        
                        <div class="announcements-stats">
                            <div class="stat-card">
                                <div class="value" id="total-announcements">8</div>
                                <div class="label">Total</div>
                            </div>
                            <div class="stat-card">
                                <div class="value" id="active-announcements">5</div>
                                <div class="label">Active</div>
                            </div>
                            <div class="stat-card">
                                <div class="value" id="scheduled-announcements">3</div>
                                <div class="label">Scheduled</div>
                            </div>
                        </div>
                        
                        <div class="filter-controls">
                            <select id="announcement-status-filter">
                                <option value="all">All Announcements</option>
                                <option value="active">Active</option>
                                <option value="scheduled">Scheduled</option>
                                <option value="expired">Expired</option>
                            </select>
                            <select id="announcement-type-filter">
                                <option value="all">All Types</option>
                                <option value="urgent">Urgent</option>
                                <option value="info">Information</option>
                                <option value="warning">Warning</option>
                                <option value="success">Success</option>
                            </select>
                        </div>
                        
                        <button class="create-announcement-btn" onclick="openCreateModal()">
                            <i class="fas fa-plus"></i> Create Announcement
                        </button>
                    </div>
                    
                    <div class="announcements-grid" id="announcements-grid">
                        <!-- Announcements will be dynamically populated here -->
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Create Announcement Modal -->
    <div class="modal" id="createAnnouncementModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-bullhorn"></i> Create New Announcement</h2>
                <button class="close-modal" onclick="closeCreateModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="announcement-title">Announcement Title</label>
                    <input type="text" id="announcement-title" placeholder="Enter announcement title">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="announcement-type">Type</label>
                        <select id="announcement-type">
                            <option value="info">Information</option>
                            <option value="urgent">Urgent</option>
                            <option value="warning">Warning</option>
                            <option value="success">Success</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="announcement-priority">Priority</label>
                        <select id="announcement-priority">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="announcement-content">Announcement Content</label>
                    <textarea id="announcement-content" placeholder="Enter announcement details..."></textarea>
                </div>
                
                <div class="form-group">
                    <label>Target Audience</label>
                    <div class="target-options">
                        <div class="target-option" onclick="toggleTargetOption(this)">
                            <i class="fas fa-users"></i>
                            <span>All Tenants</span>
                        </div>
                        <div class="target-option" onclick="toggleTargetOption(this)">
                            <i class="fas fa-building"></i>
                            <span>Specific Property</span>
                        </div>
                        <div class="target-option" onclick="toggleTargetOption(this)">
                            <i class="fas fa-user"></i>
                            <span>Specific Tenant</span>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="announcement-start">Start Date</label>
                        <input type="datetime-local" id="announcement-start">
                    </div>
                    <div class="form-group">
                        <label for="announcement-end">End Date</label>
                        <input type="datetime-local" id="announcement-end">
                    </div>
                </div>
                
                <div class="modal-actions">
                    <button class="btn-cancel" onclick="closeCreateModal()">Cancel</button>
                    <button class="btn-submit" onclick="createAnnouncement()">Create Announcement</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <img src="https://placehold.co/100x30/FFFFFF/FF385C?text=SmartHunt" alt="SmartHunt Logo"> 
        <h6>&copy; Algorithm-X Softwares. <br>All rights reserved</h6>
    </footer>

    <script>
        // Sample announcements data
        const announcementsData = [
            {
                id: 1,
                title: "Water Maintenance Notice",
                content: "There will be scheduled water maintenance on Saturday from 8:00 AM to 12:00 PM. Please store enough water for your needs during this period.",
                type: "info",
                priority: "medium",
                target: "All Tenants",
                properties: ["All Properties"],
                startDate: "2023-06-10T00:00:00",
                endDate: "2023-06-12T23:59:59",
                status: "active",
                views: 45,
                createdAt: "2023-06-08T10:00:00",
                createdBy: "Admin"
            },
            {
                id: 2,
                title: "URGENT: Security Alert",
                content: "Please be advised that there have been reports of suspicious activity in the area. Ensure all doors and windows are locked, and report any unusual activity to security immediately.",
                type: "urgent",
                priority: "high",
                target: "All Tenants",
                properties: ["All Properties"],
                startDate: "2023-06-15T18:00:00",
                endDate: "2023-06-17T23:59:59",
                status: "active",
                views: 89,
                createdAt: "2023-06-15T17:30:00",
                createdBy: "Security"
            },
            {
                id: 3,
                title: "Rent Payment Reminder",
                content: "This is a friendly reminder that rent payments for June are due by the 5th. Late payments will incur a penalty fee as per the rental agreement.",
                type: "warning",
                priority: "medium",
                target: "All Tenants",
                properties: ["All Properties"],
                startDate: "2023-06-01T00:00:00",
                endDate: "2023-06-05T23:59:59",
                status: "expired",
                views: 67,
                createdAt: "2023-05-28T09:00:00",
                createdBy: "Admin"
            },
            {
                id: 4,
                title: "New Gym Equipment Installed",
                content: "We're excited to announce that new gym equipment has been installed in the common area gym. The gym will be available for use from tomorrow onwards.",
                type: "success",
                priority: "low",
                target: "Tripple A Apartments",
                properties: ["Tripple A Apartments"],
                startDate: "2023-06-20T00:00:00",
                endDate: "2023-07-20T23:59:59",
                status: "active",
                views: 23,
                createdAt: "2023-06-19T14:00:00",
                createdBy: "Management"
            },
            {
                id: 5,
                title: "Parking Lot Resurfacing",
                content: "The parking lot will be resurfaced next week. Please ensure your vehicles are moved from the parking area by Sunday evening. Alternative parking will be available at the adjacent lot.",
                type: "info",
                priority: "medium",
                target: "Green Valley Homes",
                properties: ["Green Valley Homes"],
                startDate: "2023-06-25T00:00:00",
                endDate: "2023-06-30T23:59:59",
                status: "scheduled",
                views: 18,
                createdAt: "2023-06-18T11:00:00",
                createdBy: "Maintenance"
            },
            {
                id: 6,
                title: "Internet Upgrade Complete",
                content: "The internet upgrade has been completed successfully. All tenants should now experience faster and more reliable internet connectivity.",
                type: "success",
                priority: "low",
                target: "Campus View Apartments",
                properties: ["Campus View Apartments"],
                startDate: "2023-06-12T00:00:00",
                endDate: "2023-06-19T23:59:59",
                status: "expired",
                views: 34,
                createdAt: "2023-06-11T16:00:00",
                createdBy: "IT Department"
            },
            {
                id: 7,
                title: "Fire Drill Scheduled",
                content: "A mandatory fire drill will be conducted on Friday at 10:00 AM. All tenants are required to participate. Please follow instructions from security personnel.",
                type: "warning",
                priority: "high",
                target: "All Tenants",
                properties: ["All Properties"],
                startDate: "2023-06-23T00:00:00",
                endDate: "2023-06-23T23:59:59",
                status: "scheduled",
                views: 12,
                createdAt: "2023-06-20T09:00:00",
                createdBy: "Security"
            },
            {
                id: 8,
                title: "Common Area Cleaning",
                content: "The common areas will be deep cleaned this weekend. Please remove any personal items from these areas by Friday evening.",
                type: "info",
                priority: "low",
                target: "Sunset Heights",
                properties: ["Sunset Heights"],
                startDate: "2023-06-16T00:00:00",
                endDate: "2023-06-18T23:59:59",
                status: "active",
                views: 29,
                createdAt: "2023-06-15T08:00:00",
                createdBy: "Cleaning Staff"
            }
        ];

        // Function to render announcements
        function renderAnnouncements(announcements = announcementsData) {
            const announcementsGrid = document.getElementById('announcements-grid');
            
            if (announcements.length === 0) {
                announcementsGrid.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-bullhorn"></i>
                        <h3>No Announcements Found</h3>
                        <p>You don't have any announcements matching your search criteria.</p>
                        <button class="create-announcement-btn" onclick="openCreateModal()">
                            <i class="fas fa-plus"></i> Create Your First Announcement
                        </button>
                    </div>
                `;
                return;
            }
            
            // Update announcement statistics
            updateAnnouncementStats(announcements);
            
            announcementsGrid.innerHTML = announcements.map(announcement => `
                <div class="announcement-card ${announcement.type}" data-id="${announcement.id}">
                    <div class="announcement-header">
                        <div>
                            <div class="announcement-title">${announcement.title}</div>
                            <div class="announcement-meta">
                                <span class="announcement-type type-${announcement.type}">
                                    ${getAnnouncementTypeDisplay(announcement.type)}
                                </span>
                                <span class="announcement-date">
                                    <i class="far fa-clock"></i>
                                    ${formatDate(announcement.createdAt)}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="announcement-content">
                        <p>${announcement.content}</p>
                    </div>
                    
                    <div class="announcement-target">
                        <i class="fas fa-bullseye"></i>
                        Target: ${announcement.target}
                    </div>
                    
                    <div class="announcement-actions">
                        <div class="announcement-stats">
                            <div class="stat">
                                <i class="far fa-eye"></i>
                                <span>${announcement.views} views</span>
                            </div>
                            <div class="stat">
                                <i class="far fa-calendar"></i>
                                <span>${formatDate(announcement.startDate)}</span>
                            </div>
                        </div>
                        
                        <div class="announcement-btns">
                            <button class="announcement-btn btn-view" onclick="viewAnnouncement(${announcement.id})">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="announcement-btn btn-edit" onclick="editAnnouncement(${announcement.id})">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="announcement-btn btn-delete" onclick="deleteAnnouncement(${announcement.id})">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Function to update announcement statistics
        function updateAnnouncementStats(announcements) {
            const totalAnnouncements = announcements.length;
            const activeAnnouncements = announcements.filter(ann => ann.status === 'active').length;
            const scheduledAnnouncements = announcements.filter(ann => ann.status === 'scheduled').length;
            
            document.getElementById('total-announcements').textContent = totalAnnouncements;
            document.getElementById('active-announcements').textContent = activeAnnouncements;
            document.getElementById('scheduled-announcements').textContent = scheduledAnnouncements;
        }

        // Helper function to get announcement type display name
        function getAnnouncementTypeDisplay(type) {
            const types = {
                urgent: 'Urgent',
                info: 'Information',
                warning: 'Warning',
                success: 'Success'
            };
            return types[type] || type;
        }

        // Function to format date
        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('en-US', options);
        }

        // Function to filter announcements
        function filterAnnouncements() {
            const searchTerm = document.getElementById('search-announcements').value.toLowerCase();
            const statusFilter = document.getElementById('announcement-status-filter').value;
            const typeFilter = document.getElementById('announcement-type-filter').value;
            
            const filteredAnnouncements = announcementsData.filter(announcement => {
                const matchesSearch = announcement.title.toLowerCase().includes(searchTerm) || 
                                     announcement.content.toLowerCase().includes(searchTerm);
                const matchesStatus = statusFilter === 'all' || announcement.status === statusFilter;
                const matchesType = typeFilter === 'all' || announcement.type === typeFilter;
                
                return matchesSearch && matchesStatus && matchesType;
            });
            
            renderAnnouncements(filteredAnnouncements);
        }

        // Modal functions
        function openCreateModal() {
            document.getElementById('createAnnouncementModal').classList.add('active');
        }

        function closeCreateModal() {
            document.getElementById('createAnnouncementModal').classList.remove('active');
            resetForm();
        }

        function toggleTargetOption(element) {
            document.querySelectorAll('.target-option').forEach(option => {
                option.classList.remove('selected');
            });
            element.classList.add('selected');
        }

        function resetForm() {
            document.getElementById('announcement-title').value = '';
            document.getElementById('announcement-type').value = 'info';
            document.getElementById('announcement-priority').value = 'low';
            document.getElementById('announcement-content').value = '';
            document.getElementById('announcement-start').value = '';
            document.getElementById('announcement-end').value = '';
            document.querySelectorAll('.target-option').forEach(option => {
                option.classList.remove('selected');
            });
        }

        // Announcement action functions
        function createAnnouncement() {
            const title = document.getElementById('announcement-title').value;
            const content = document.getElementById('announcement-content').value;
            
            if (!title || !content) {
                alert('Please fill in all required fields');
                return;
            }
            
            const newAnnouncement = {
                id: announcementsData.length + 1,
                title: title,
                content: content,
                type: document.getElementById('announcement-type').value,
                priority: document.getElementById('announcement-priority').value,
                target: 'All Tenants',
                properties: ['All Properties'],
                startDate: document.getElementById('announcement-start').value || new Date().toISOString(),
                endDate: document.getElementById('announcement-end').value || new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString(),
                status: 'active',
                views: 0,
                createdAt: new Date().toISOString(),
                createdBy: 'Admin'
            };
            
            announcementsData.unshift(newAnnouncement);
            renderAnnouncements();
            closeCreateModal();
            alert('Announcement created successfully!');
        }

        function viewAnnouncement(announcementId) {
            const announcement = announcementsData.find(a => a.id === announcementId);
            if (announcement) {
                alert(`Viewing: ${announcement.title}\n\n${announcement.content}\n\nTarget: ${announcement.target}\nStatus: ${announcement.status}`);
                // In real application, this would open a detailed view modal
            }
        }

        function editAnnouncement(announcementId) {
            const announcement = announcementsData.find(a => a.id === announcementId);
            if (announcement) {
                alert(`Editing announcement: ${announcement.title}`);
                // In real application, this would open the edit modal with pre-filled data
            }
        }

        function deleteAnnouncement(announcementId) {
            if (confirm('Are you sure you want to delete this announcement?')) {
                const index = announcementsData.findIndex(a => a.id === announcementId);
                if (index !== -1) {
                    announcementsData.splice(index, 1);
                    renderAnnouncements();
                    alert('Announcement deleted successfully!');
                }
            }
        }

        // Initialize announcements when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Set announcements as active
            document.querySelectorAll('.sidebar-menu a').forEach(link => {
                link.classList.remove('active');
            });
            document.querySelector('[data-content="announcements"]').classList.add('active');
            
            // Hide all content sections except announcements
            document.querySelectorAll('.content').forEach(section => {
                if (section.id !== 'announcements-content') {
                    section.classList.add('hidden');
                }
            });
            
            renderAnnouncements();
            
            // Add event listeners for filtering
            document.getElementById('search-announcements').addEventListener('input', filterAnnouncements);
            document.getElementById('announcement-status-filter').addEventListener('change', filterAnnouncements);
            document.getElementById('announcement-type-filter').addEventListener('change', filterAnnouncements);
        });
    </script>
</body>
</html>