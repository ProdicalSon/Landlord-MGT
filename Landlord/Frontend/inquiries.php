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

        /* Hidden class for toggling content */
        .hidden {
            display: none;
        }

        /* Notification Badge */
        .notification-badge {
            background-color: var(--danger);
            color: white;
            border-radius: 50%;
            padding: 3px 8px;
            font-size: 12px;
            margin-left: 5px;
        }

        /* Inquiries Styles */
        .inquiries-container {
            background: var(--light);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .inquiries-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 25px 30px;
            text-align: center;
        }

        .inquiries-header h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .inquiries-header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .inquiries-controls {
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

        .inquiries-stats {
            display: flex;
            gap: 20px;
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

        .inquiries-list {
            padding: 0;
        }

        .inquiry-item {
            display: flex;
            align-items: center;
            padding: 20px 30px;
            border-bottom: 1px solid var(--light-gray);
            transition: all 0.3s;
            cursor: pointer;
        }

        .inquiry-item:hover {
            background-color: var(--light-gray);
        }

        .inquiry-item.unread {
            background-color: rgba(255, 56, 92, 0.05);
        }

        .inquiry-item.unread:hover {
            background-color: rgba(255, 56, 92, 0.08);
        }

        .inquiry-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 15px;
        }

        .inquiry-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .inquiry-content {
            flex: 1;
        }

        .inquiry-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }

        .inquiry-sender {
            font-weight: 600;
            color: var(--dark);
            display: flex;
            align-items: center;
        }

        .inquiry-time {
            font-size: 12px;
            color: var(--text);
        }

        .inquiry-subject {
            font-weight: 500;
            margin-bottom: 5px;
            color: var(--dark);
        }

        .inquiry-preview {
            font-size: 14px;
            color: var(--text);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .inquiry-property {
            font-size: 12px;
            color: var(--primary);
            margin-top: 5px;
        }

        .inquiry-actions {
            display: flex;
            gap: 10px;
        }

        .inquiry-btn {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
        }

        .btn-reply {
            background-color: var(--secondary);
            color: white;
        }

        .btn-reply:hover {
            background-color: #3367d6;
        }

        .btn-archive {
            background-color: var(--light-gray);
            color: var(--text);
        }

        .btn-archive:hover {
            background-color: var(--gray);
        }

        .inquiry-badge {
            background-color: var(--primary);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }

        /* Chat Styles */
        .chat-container {
            background: var(--light);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
            height: 70vh;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 20px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chat-header-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .chat-header-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
        }

        .chat-header-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .chat-header-details h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .chat-header-details p {
            font-size: 14px;
            opacity: 0.9;
        }

        .chat-header-actions {
            display: flex;
            gap: 10px;
        }

        .chat-header-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .chat-header-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .message {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 18px;
            position: relative;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .message.received {
            align-self: flex-start;
            background-color: var(--light-gray);
            border-bottom-left-radius: 4px;
        }

        .message.sent {
            align-self: flex-end;
            background-color: var(--primary);
            color: white;
            border-bottom-right-radius: 4px;
        }

        .message-time {
            font-size: 11px;
            opacity: 0.7;
            margin-top: 5px;
            text-align: right;
        }

        .message.received .message-time {
            text-align: left;
        }

        .chat-input-container {
            padding: 20px;
            border-top: 1px solid var(--light-gray);
            background: var(--light);
        }

        .chat-input {
            display: flex;
            gap: 10px;
            align-items: flex-end;
        }

        .chat-input textarea {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid var(--gray);
            border-radius: 20px;
            resize: none;
            font-size: 14px;
            max-height: 100px;
            min-height: 40px;
        }

        .chat-input textarea:focus {
            outline: none;
            border-color: var(--primary);
        }

        .chat-send-btn {
            background-color: var(--primary);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .chat-send-btn:hover {
            background-color: var(--primary-light);
            transform: scale(1.05);
        }

        .chat-send-btn:disabled {
            background-color: var(--gray);
            cursor: not-allowed;
            transform: none;
        }

        .chat-attachment-btn {
            background: none;
            border: none;
            color: var(--text);
            font-size: 18px;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.3s;
        }

        .chat-attachment-btn:hover {
            background-color: var(--light-gray);
        }

        /* Empty state for inquiries */
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
            .inquiries-controls {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }
            .inquiries-stats {
                justify-content: center;
            }
            .filter-controls {
                justify-content: center;
            }
            .inquiry-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            .inquiry-actions {
                align-self: flex-end;
            }
            .message {
                max-width: 85%;
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
                        <a href="#" data-content="manage-location"><i class="fas fa-map-marker-alt"></i> Manage Location</a>
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
                    <a href="#" class="active" data-content="inquiries"><i class="fas fa-question-circle"></i> Inquiries <span class="notification-badge">5</span></a>
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

            <!-- Inquiries List Section -->
            <section class="content" id="inquiries-list-content">
                <div class="inquiries-container">
                    <div class="inquiries-header">
                        <h1><i class="fas fa-inbox"></i> Property Inquiries</h1>
                        <p>Manage and respond to all property inquiries in one place</p>
                    </div>
                    
                    <div class="inquiries-controls">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="search-inquiries" placeholder="Search inquiries...">
                        </div>
                        <div class="inquiries-stats">
                            <div class="stat-card">
                                <div class="value" id="total-inquiries">12</div>
                                <div class="label">Total</div>
                            </div>
                            <div class="stat-card">
                                <div class="value" id="unread-inquiries">5</div>
                                <div class="label">Unread</div>
                            </div>
                            <div class="stat-card">
                                <div class="value" id="pending-inquiries">7</div>
                                <div class="label">Pending Reply</div>
                            </div>
                        </div>
                        
                        <div class="filter-controls">
                            <select id="inquiry-status-filter">
                                <option value="all">All Inquiries</option>
                                <option value="unread">Unread</option>
                                <option value="read">Read</option>
                                <option value="replied">Replied</option>
                                <option value="archived">Archived</option>
                            </select>
                            <select id="inquiry-property-filter">
                                <option value="all">All Properties</option>
                                <option value="tripple-a">Tripple A Apartments</option>
                                <option value="green-valley">Green Valley Homes</option>
                                <option value="campus-view">Campus View Apartments</option>
                                <option value="sunset">Sunset Heights</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="inquiries-list" id="inquiries-list">
                        <!-- Inquiries will be dynamically populated here -->
                    </div>
                </div>
            </section>

            <!-- Chat Section -->
            <section class="content hidden" id="chat-content">
                <div class="chat-container">
                    <div class="chat-header">
                        <div class="chat-header-info">
                            <div class="chat-header-avatar">
                                <img src="https://placehold.co/50x50/4285F4/FFFFFF?text=JM" alt="John Mwangi">
                            </div>
                            <div class="chat-header-details">
                                <h3>John Mwangi</h3>
                                <p>Interested in: Tripple A Apartments</p>
                            </div>
                        </div>
                        <div class="chat-header-actions">
                            <button class="chat-header-btn" title="Call">
                                <i class="fas fa-phone"></i>
                            </button>
                            <button class="chat-header-btn" title="Video Call">
                                <i class="fas fa-video"></i>
                            </button>
                            <button class="chat-header-btn" title="More Options">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="chat-messages" id="chat-messages">
                        <!-- Messages will be dynamically populated here -->
                    </div>
                    
                    <div class="chat-input-container">
                        <div class="chat-input">
                            <button class="chat-attachment-btn" title="Attach File">
                                <i class="fas fa-paperclip"></i>
                            </button>
                            <textarea id="chat-message-input" placeholder="Type your message here..." rows="1"></textarea>
                            <button class="chat-send-btn" id="chat-send-btn" title="Send Message">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
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
        // Sample inquiries data
        const inquiriesData = [
            {
                id: 1,
                sender: "John Mwangi",
                avatar: "https://placehold.co/50x50/4285F4/FFFFFF?text=JM",
                subject: "Inquiry about Tripple A Apartments",
                preview: "Hello, I'm interested in renting a single room at your property. Could you please share more details about availability and pricing?",
                property: "Tripple A Apartments",
                time: "2 hours ago",
                status: "unread",
                unreadCount: 3
            },
            {
                id: 2,
                sender: "Sarah Wanjiku",
                avatar: "https://placehold.co/50x50/00A699/FFFFFF?text=SW",
                subject: "Bedsitter Availability",
                preview: "I saw your listing for Green Valley Homes and would like to know if there are any bedsitters available for immediate occupation.",
                property: "Green Valley Homes",
                time: "5 hours ago",
                status: "read",
                unreadCount: 0
            },
            {
                id: 3,
                sender: "David Ochieng",
                avatar: "https://placehold.co/50x50/FF385C/FFFFFF?text=DO",
                subject: "1 Bedroom Apartment Tour",
                preview: "Could I schedule a viewing of the 1-bedroom apartment at Campus View Apartments? I'm available this weekend.",
                property: "Campus View Apartments",
                time: "1 day ago",
                status: "replied",
                unreadCount: 0
            },
            {
                id: 4,
                sender: "Grace Akinyi",
                avatar: "https://placehold.co/50x50/FFB400/FFFFFF?text=GA",
                subject: "Rental Agreement Questions",
                preview: "I have some questions about the rental agreement terms for Sunset Heights. Could we discuss this further?",
                property: "Sunset Heights",
                time: "2 days ago",
                status: "unread",
                unreadCount: 1
            },
            {
                id: 5,
                sender: "Peter Kamau",
                avatar: "https://placehold.co/50x50/4285F4/FFFFFF?text=PK",
                subject: "Room Availability Next Month",
                preview: "Do you have any single rooms available starting next month at River Side Apartments? What's the application process?",
                property: "River Side Apartments",
                time: "3 days ago",
                status: "read",
                unreadCount: 0
            },
            {
                id: 6,
                sender: "Mary Njeri",
                avatar: "https://placehold.co/50x50/00A699/FFFFFF?text=MN",
                subject: "Utilities Included?",
                preview: "Are water and electricity bills included in the rent for the bedsitters at Green Valley Homes?",
                property: "Green Valley Homes",
                time: "4 days ago",
                status: "archived",
                unreadCount: 0
            }
        ];

        // Sample chat messages data
        const chatMessages = [
            {
                id: 1,
                sender: "John Mwangi",
                message: "Hello, I'm interested in renting a single room at Tripple A Apartments. Could you please share more details about availability and pricing?",
                time: "2023-06-15T10:30:00",
                type: "received"
            },
            {
                id: 2,
                sender: "You",
                message: "Hi John! Thanks for your interest. We have several single rooms available starting at Ksh 5,000 per month. What's your preferred move-in date?",
                time: "2023-06-15T10:35:00",
                type: "sent"
            },
            {
                id: 3,
                sender: "John Mwangi",
                message: "I'm looking to move in by the 1st of next month. Are utilities like water and electricity included in the rent?",
                time: "2023-06-15T10:40:00",
                type: "received"
            },
            {
                id: 4,
                sender: "You",
                message: "Yes, water is included. Electricity is metered separately. Would you like to schedule a viewing to see the available rooms?",
                time: "2023-06-15T10:42:00",
                type: "sent"
            },
            {
                id: 5,
                sender: "John Mwangi",
                message: "That would be great! I'm available this Saturday afternoon. What time works for you?",
                time: "2023-06-15T10:45:00",
                type: "received"
            }
        ];

        // Function to render inquiries
        function renderInquiries(inquiries = inquiriesData) {
            const inquiriesList = document.getElementById('inquiries-list');
            
            if (inquiries.length === 0) {
                inquiriesList.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h3>No Inquiries Found</h3>
                        <p>You don't have any inquiries matching your search criteria.</p>
                    </div>
                `;
                return;
            }
            
            // Update inquiry stats
            const totalInquiries = inquiries.length;
            const unreadInquiries = inquiries.filter(inquiry => inquiry.status === 'unread').length;
            const pendingInquiries = inquiries.filter(inquiry => inquiry.status === 'read' || inquiry.status === 'unread').length;
            
            document.getElementById('total-inquiries').textContent = totalInquiries;
            document.getElementById('unread-inquiries').textContent = unreadInquiries;
            document.getElementById('pending-inquiries').textContent = pendingInquiries;
            
            inquiriesList.innerHTML = inquiries.map(inquiry => `
                <div class="inquiry-item ${inquiry.status === 'unread' ? 'unread' : ''}" data-id="${inquiry.id}" onclick="openChat(${inquiry.id})">
                    <div class="inquiry-avatar">
                        <img src="${inquiry.avatar}" alt="${inquiry.sender}">
                    </div>
                    <div class="inquiry-content">
                        <div class="inquiry-header">
                            <div class="inquiry-sender">
                                ${inquiry.sender}
                                ${inquiry.unreadCount > 0 ? `<span class="inquiry-badge">${inquiry.unreadCount}</span>` : ''}
                            </div>
                            <div class="inquiry-time">${inquiry.time}</div>
                        </div>
                        <div class="inquiry-subject">${inquiry.subject}</div>
                        <div class="inquiry-preview">${inquiry.preview}</div>
                        <div class="inquiry-property">
                            <i class="fas fa-building"></i> ${inquiry.property}
                        </div>
                    </div>
                    <div class="inquiry-actions">
                        <button class="inquiry-btn btn-reply" onclick="event.stopPropagation(); replyToInquiry(${inquiry.id})">
                            <i class="fas fa-reply"></i> Reply
                        </button>
                        <button class="inquiry-btn btn-archive" onclick="event.stopPropagation(); archiveInquiry(${inquiry.id})">
                            <i class="fas fa-archive"></i> Archive
                        </button>
                    </div>
                </div>
            `).join('');
        }

        // Function to render chat messages
        function renderChatMessages(messages = chatMessages) {
            const chatMessagesContainer = document.getElementById('chat-messages');
            
            chatMessagesContainer.innerHTML = messages.map(msg => `
                <div class="message ${msg.type}">
                    <div class="message-text">${msg.message}</div>
                    <div class="message-time">${formatChatTime(msg.time)}</div>
                </div>
            `).join('');
            
            // Scroll to bottom
            chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;
        }

        // Function to filter inquiries
        function filterInquiries() {
            const searchTerm = document.getElementById('search-inquiries').value.toLowerCase();
            const statusFilter = document.getElementById('inquiry-status-filter').value;
            const propertyFilter = document.getElementById('inquiry-property-filter').value;
            
            const filteredInquiries = inquiriesData.filter(inquiry => {
                const matchesSearch = inquiry.sender.toLowerCase().includes(searchTerm) || 
                                     inquiry.subject.toLowerCase().includes(searchTerm) ||
                                     inquiry.preview.toLowerCase().includes(searchTerm);
                const matchesStatus = statusFilter === 'all' || inquiry.status === statusFilter;
                const matchesProperty = propertyFilter === 'all' || inquiry.property.toLowerCase().includes(propertyFilter.toLowerCase());
                
                return matchesSearch && matchesStatus && matchesProperty;
            });
            
            renderInquiries(filteredInquiries);
        }

        // Function to open chat with an inquiry
        function openChat(inquiryId) {
            const inquiry = inquiriesData.find(i => i.id === inquiryId);
            if (inquiry) {
                // Mark as read
                inquiry.status = 'read';
                inquiry.unreadCount = 0;
                
                // Update the inquiries list
                renderInquiries();
                
                // Show chat section
                document.querySelectorAll('.content').forEach(section => {
                    section.classList.add('hidden');
                });
                document.getElementById('chat-content').classList.remove('hidden');
                
                // Update chat header with inquiry info
                document.querySelector('.chat-header-details h3').textContent = inquiry.sender;
                document.querySelector('.chat-header-details p').textContent = `Interested in: ${inquiry.property}`;
                document.querySelector('.chat-header-avatar img').src = inquiry.avatar;
                
                // Render chat messages
                renderChatMessages();
            }
        }

        // Function to reply to an inquiry
        function replyToInquiry(id) {
            const inquiry = inquiriesData.find(i => i.id === id);
            if (inquiry) {
                openChat(id);
                document.getElementById('chat-message-input').focus();
            }
        }

        // Function to archive an inquiry
        function archiveInquiry(id) {
            if (confirm('Are you sure you want to archive this inquiry?')) {
                const inquiry = inquiriesData.find(i => i.id === id);
                if (inquiry) {
                    inquiry.status = 'archived';
                    renderInquiries();
                    alert(`Inquiry from ${inquiry.sender} has been archived.`);
                }
            }
        }

        // Function to format chat time
        function formatChatTime(timeString) {
            const date = new Date(timeString);
            return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        }

        // Function to send a message
        function sendMessage() {
            const messageInput = document.getElementById('chat-message-input');
            const message = messageInput.value.trim();
            
            if (message) {
                const newMessage = {
                    id: chatMessages.length + 1,
                    sender: "You",
                    message: message,
                    time: new Date().toISOString(),
                    type: "sent"
                };
                
                chatMessages.push(newMessage);
                renderChatMessages();
                
                // Clear input
                messageInput.value = '';
                
                // Simulate reply after 1-3 seconds
                setTimeout(() => {
                    const replies = [
                        "Thanks for the information!",
                        "I'll get back to you after discussing with my roommate.",
                        "Could you please send me the rental agreement?",
                        "What's the security deposit amount?",
                        "Are pets allowed in the property?"
                    ];
                    
                    const randomReply = replies[Math.floor(Math.random() * replies.length)];
                    
                    const replyMessage = {
                        id: chatMessages.length + 1,
                        sender: document.querySelector('.chat-header-details h3').textContent,
                        message: randomReply,
                        time: new Date().toISOString(),
                        type: "received"
                    };
                    
                    chatMessages.push(replyMessage);
                    renderChatMessages();
                }, 1000 + Math.random() * 2000);
            }
        }

        // Initialize inquiries when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Set inquiries as active
            document.querySelectorAll('.sidebar-menu a').forEach(link => {
                link.classList.remove('active');
            });
            document.querySelector('[data-content="inquiries"]').classList.add('active');
            
            // Hide all content sections except inquiries
            document.querySelectorAll('.content').forEach(section => {
                if (section.id !== 'inquiries-list-content') {
                    section.classList.add('hidden');
                }
            });
            
            renderInquiries();
            renderChatMessages();
            
            // Add event listeners for filtering
            document.getElementById('search-inquiries').addEventListener('input', filterInquiries);
            document.getElementById('inquiry-status-filter').addEventListener('change', filterInquiries);
            document.getElementById('inquiry-property-filter').addEventListener('change', filterInquiries);
            
            // Chat functionality
            const messageInput = document.getElementById('chat-message-input');
            const sendButton = document.getElementById('chat-send-btn');
            
            // Send message on button click
            sendButton.addEventListener('click', sendMessage);
            
            // Send message on Enter key (but allow Shift+Enter for new line)
            messageInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });
            
            // Auto-resize textarea
            messageInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
            
            // Navigation between inquiries and chat
            const sidebarLinks = document.querySelectorAll('.sidebar-menu a');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const contentId = this.getAttribute('data-content') + '-content';
                    
                    // Hide all content sections
                    document.querySelectorAll('.content').forEach(section => {
                        section.classList.add('hidden');
                    });
                    
                    // Show the selected content section
                    const targetSection = document.getElementById(contentId);
                    if (targetSection) {
                        targetSection.classList.remove('hidden');
                    }
                    
                    // Update sidebar active state
                    sidebarLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        });
    </script>
</body>
</html>