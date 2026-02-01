<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/icons/smartlogo.png">
    <title>SmartHunt - Support</title>
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

        /* Support-specific styles */
        .support-container {
            background: var(--light);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .support-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 25px 30px;
            text-align: center;
        }

        .support-header h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .support-header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .support-content {
            padding: 30px;
        }

        .support-quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .support-action-card {
            background: var(--light);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            text-align: center;
            transition: all 0.3s;
            border: 1px solid var(--light-gray);
        }

        .support-action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        }

        .support-action-card i {
            font-size: 40px;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .support-action-card h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .support-action-card p {
            color: var(--text);
            margin-bottom: 20px;
            font-size: 14px;
        }

        .support-action-card button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
            width: 100%;
        }

        .support-action-card button:hover {
            background: var(--primary-light);
        }

        .help-section {
            margin-bottom: 40px;
        }

        .help-section h2 {
            font-size: 22px;
            margin-bottom: 20px;
            color: var(--dark);
            padding-bottom: 10px;
            border-bottom: 2px solid var(--light-gray);
        }

        .faq-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .faq-item {
            background: var(--light);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--light-gray);
        }

        .faq-question {
            padding: 18px 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 500;
            transition: background 0.3s;
        }

        .faq-question:hover {
            background: var(--light-gray);
        }

        .faq-question i {
            color: var(--primary);
            transition: transform 0.3s;
        }

        .faq-answer {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s;
            background: var(--light-gray);
        }

        .faq-item.active .faq-answer {
            padding: 20px;
            max-height: 500px;
        }

        .faq-item.active .faq-question i {
            transform: rotate(180deg);
        }

        .contact-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .contact-card {
            background: var(--light);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            text-align: center;
            border: 1px solid var(--light-gray);
        }

        .contact-card i {
            font-size: 40px;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .contact-card h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .contact-card p {
            color: var(--text);
            margin-bottom: 15px;
            font-size: 14px;
        }

        .contact-info {
            font-size: 16px;
            font-weight: 500;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .contact-card button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
            width: 100%;
        }

        .contact-card button:hover {
            background: var(--primary-light);
        }

        .support-form-container {
            background: var(--light);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .support-form-container h2 {
            font-size: 22px;
            margin-bottom: 20px;
            color: var(--dark);
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

        .submit-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s;
            width: 100%;
        }

        .submit-btn:hover {
            background: var(--primary-light);
        }

        .support-status {
            background: var(--light);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .support-status h2 {
            font-size: 22px;
            margin-bottom: 20px;
            color: var(--dark);
        }

        .status-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--light-gray);
        }

        .status-item:last-child {
            border-bottom: none;
        }

        .status-info h4 {
            font-size: 16px;
            margin-bottom: 5px;
            color: var(--dark);
        }

        .status-info p {
            font-size: 14px;
            color: var(--text);
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-open {
            background: rgba(255, 180, 0, 0.1);
            color: var(--warning);
        }

        .status-in-progress {
            background: rgba(66, 133, 244, 0.1);
            color: var(--secondary);
        }

        .status-resolved {
            background: rgba(0, 166, 153, 0.1);
            color: var(--success);
        }

        .status-closed {
            background: rgba(119, 119, 119, 0.1);
            color: #777;
        }

        /* Live Chat Styles */
        .live-chat-container {
            background: var(--light);
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .chat-header {
            background: var(--primary);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-header h3 {
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chat-status {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
        }

        .status-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--success);
        }

        .chat-messages {
            height: 300px;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
            background: var(--light-gray);
        }

        .message {
            max-width: 70%;
            padding: 12px 15px;
            border-radius: 18px;
            font-size: 14px;
            line-height: 1.4;
        }

        .message.user {
            align-self: flex-end;
            background: var(--primary);
            color: white;
            border-bottom-right-radius: 5px;
        }

        .message.support {
            align-self: flex-start;
            background: white;
            color: var(--text);
            border: 1px solid var(--gray);
            border-bottom-left-radius: 5px;
        }

        .message-time {
            font-size: 11px;
            margin-top: 5px;
            opacity: 0.7;
            text-align: right;
        }

        .chat-input {
            display: flex;
            padding: 15px;
            border-top: 1px solid var(--light-gray);
            background: white;
        }

        .chat-input input {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid var(--gray);
            border-radius: 24px;
            font-size: 14px;
            margin-right: 10px;
        }

        .chat-input button {
            background: var(--primary);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s;
        }

        .chat-input button:hover {
            background: var(--primary-light);
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
            .support-quick-actions,
            .contact-options {
                grid-template-columns: 1fr;
            }
            .form-row {
                grid-template-columns: 1fr;
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
                <li><a href="#" class="active" data-content="support"><i class="fas fa-headset"></i> Support</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <nav class="navbar">
                <div class="navbar-brand">Support Center</div>
                
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

            <!-- Support Section -->
            <section class="content" id="support-content">
                <div class="support-container">
                    <div class="support-header">
                        <h1><i class="fas fa-headset"></i> How Can We Help You?</h1>
                        <p>Get assistance with your property management needs</p>
                    </div>
                    
                    <div class="support-content">
                        <!-- Quick Actions -->
                        <div class="support-quick-actions">
                            <div class="support-action-card">
                                <i class="fas fa-question-circle"></i>
                                <h3>Help Center</h3>
                                <p>Browse our knowledge base for answers to common questions</p>
                                <button id="browse-help">Browse Articles</button>
                            </div>
                            
                            <div class="support-action-card">
                                <i class="fas fa-comments"></i>
                                <h3>Live Chat</h3>
                                <p>Chat with our support team in real-time for immediate help</p>
                                <button id="start-chat">Start Chat</button>
                            </div>
                            
                            <div class="support-action-card">
                                <i class="fas fa-envelope"></i>
                                <h3>Email Support</h3>
                                <p>Send us an email and we'll respond within 24 hours</p>
                                <button id="send-email">Send Email</button>
                            </div>
                            
                            <div class="support-action-card">
                                <i class="fas fa-phone-alt"></i>
                                <h3>Phone Support</h3>
                                <p>Call us directly for urgent matters during business hours</p>
                                <button id="call-support">Call Now</button>
                            </div>
                        </div>
                        
                        <!-- FAQ Section -->
                        <div class="help-section">
                            <h2><i class="fas fa-life-ring"></i> Frequently Asked Questions</h2>
                            <div class="faq-list">
                                <div class="faq-item">
                                    <div class="faq-question">
                                        How do I add a new property to my listings?
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                    <div class="faq-answer">
                                        <p>To add a new property, go to the Properties section in your dashboard and click on "Add Property". Fill in the required details including property type, location, amenities, and upload photos. Once submitted, your property will be reviewed and listed within 24 hours.</p>
                                    </div>
                                </div>
                                
                                <div class="faq-item">
                                    <div class="faq-question">
                                        How can I communicate with potential tenants?
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                    <div class="faq-answer">
                                        <p>You can communicate with potential tenants through the Inquiries section. When someone shows interest in your property, you'll receive a notification. You can then respond directly through the platform's messaging system, which keeps all communication organized and secure.</p>
                                    </div>
                                </div>
                                
                                <div class="faq-item">
                                    <div class="faq-question">
                                        What payment methods are supported?
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                    <div class="faq-answer">
                                        <p>SmartHunt supports multiple payment methods including M-Pesa, bank transfers, credit/debit cards, and PayPal. You can set your preferred payment methods in the Payment Settings section of your dashboard.</p>
                                    </div>
                                </div>
                                
                                <div class="faq-item">
                                    <div class="faq-question">
                                        How do I handle maintenance requests from tenants?
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                    <div class="faq-answer">
                                        <p>When a tenant submits a maintenance request, you'll receive a notification. You can view the request details, assign it to a contractor, track progress, and mark it as completed once resolved. All maintenance history is stored for future reference.</p>
                                    </div>
                                </div>
                                
                                <div class="faq-item">
                                    <div class="faq-question">
                                        Can I generate reports for my properties?
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                    <div class="faq-answer">
                                        <p>Yes, the Reports section allows you to generate various reports including financial statements, occupancy rates, maintenance history, and tenant feedback. You can customize the date range and export reports in PDF or Excel format.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contact Options -->
                        <div class="help-section">
                            <h2><i class="fas fa-address-book"></i> Contact Options</h2>
                            <div class="contact-options">
                                <div class="contact-card">
                                    <i class="fas fa-envelope"></i>
                                    <h3>Email Support</h3>
                                    <p>Send us an email and we'll respond within 24 hours</p>
                                    <div class="contact-info">support@smarthunt.com</div>
                                    <button id="compose-email">Compose Email</button>
                                </div>
                                
                                <div class="contact-card">
                                    <i class="fas fa-phone-alt"></i>
                                    <h3>Phone Support</h3>
                                    <p>Call us during business hours for immediate assistance</p>
                                    <div class="contact-info">+254 700 123 456</div>
                                    <button id="call-now">Call Now</button>
                                </div>
                                
                                <div class="contact-card">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <h3>Visit Our Office</h3>
                                    <p>Schedule an appointment to visit our headquarters</p>
                                    <div class="contact-info">Nairobi, Kenya</div>
                                    <button id="get-directions">Get Directions</button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Support Request Form -->
                        <div class="support-form-container">
                            <h2><i class="fas fa-paper-plane"></i> Submit a Support Request</h2>
                            <form id="support-form">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="name">Your Name</label>
                                        <input type="text" id="name" placeholder="Enter your full name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email Address</label>
                                        <input type="email" id="email" placeholder="Enter your email" required>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="subject">Subject</label>
                                    <input type="text" id="subject" placeholder="Brief description of your issue" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="category">Issue Category</label>
                                    <select id="category" required>
                                        <option value="">Select a category</option>
                                        <option value="property">Property Management</option>
                                        <option value="payment">Payments & Billing</option>
                                        <option value="technical">Technical Issues</option>
                                        <option value="account">Account Settings</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea id="description" placeholder="Please provide detailed information about your issue..." required></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="attachments">Attachments (Optional)</label>
                                    <input type="file" id="attachments" multiple>
                                    <small>You can attach screenshots or documents that might help us understand your issue better.</small>
                                </div>
                                
                                <button type="submit" class="submit-btn">Submit Request</button>
                            </form>
                        </div>
                        
                        <!-- Support Status -->
                        <div class="support-status">
                            <h2><i class="fas fa-tasks"></i> My Support Requests</h2>
                            <div class="status-item">
                                <div class="status-info">
                                    <h4>Payment processing delay</h4>
                                    <p>Submitted on March 15, 2024 • Request #SR-2847</p>
                                </div>
                                <span class="status-badge status-resolved">Resolved</span>
                            </div>
                            
                            <div class="status-item">
                                <div class="status-info">
                                    <h4>Unable to upload property photos</h4>
                                    <p>Submitted on March 18, 2024 • Request #SR-2912</p>
                                </div>
                                <span class="status-badge status-in-progress">In Progress</span>
                            </div>
                            
                            <div class="status-item">
                                <div class="status-info">
                                    <h4>Tenant verification issue</h4>
                                    <p>Submitted on March 20, 2024 • Request #SR-2956</p>
                                </div>
                                <span class="status-badge status-open">Open</span>
                            </div>
                            
                            <div class="status-item">
                                <div class="status-info">
                                    <h4>Report generation error</h4>
                                    <p>Submitted on February 28, 2024 • Request #SR-2678</p>
                                </div>
                                <span class="status-badge status-closed">Closed</span>
                            </div>
                        </div>
                        
                        <!-- Live Chat (Initially Hidden) -->
                        <div class="live-chat-container hidden" id="live-chat">
                            <div class="chat-header">
                                <h3><i class="fas fa-comments"></i> Live Chat Support</h3>
                                <div class="chat-status">
                                    <div class="status-indicator"></div>
                                    <span>Connected</span>
                                </div>
                            </div>
                            <div class="chat-messages">
                                <div class="message support">
                                    Hello! Thank you for contacting SmartHunt Support. How can I help you today?
                                    <div class="message-time">10:02 AM</div>
                                </div>
                            </div>
                            <div class="chat-input">
                                <input type="text" placeholder="Type your message here..." id="chat-input">
                                <button id="send-message"><i class="fas fa-paper-plane"></i></button>
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
            // FAQ Accordion
            const faqItems = document.querySelectorAll('.faq-item');
            
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question');
                
                question.addEventListener('click', () => {
                    // Close all other FAQ items
                    faqItems.forEach(otherItem => {
                        if (otherItem !== item) {
                            otherItem.classList.remove('active');
                        }
                    });
                    
                    // Toggle current item
                    item.classList.toggle('active');
                });
            });
            
            // Support Form Submission
            const supportForm = document.getElementById('support-form');
            
            if (supportForm) {
                supportForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Get form data
                    const formData = {
                        name: document.getElementById('name').value,
                        email: document.getElementById('email').value,
                        subject: document.getElementById('subject').value,
                        category: document.getElementById('category').value,
                        description: document.getElementById('description').value
                    };
                    
                    // In a real application, you would send this data to a server
                    // For this demo, we'll just show a success message
                    alert('Thank you! Your support request has been submitted. We will respond within 24 hours.');
                    supportForm.reset();
                });
            }
            
            // Quick Action Buttons
            const browseHelpBtn = document.getElementById('browse-help');
            const startChatBtn = document.getElementById('start-chat');
            const sendEmailBtn = document.getElementById('send-email');
            const callSupportBtn = document.getElementById('call-support');
            
            if (browseHelpBtn) {
                browseHelpBtn.addEventListener('click', function() {
                    // Scroll to FAQ section
                    document.querySelector('.help-section').scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            }
            
            if (startChatBtn) {
                startChatBtn.addEventListener('click', function() {
                    // Show live chat
                    const liveChat = document.getElementById('live-chat');
                    liveChat.classList.remove('hidden');
                    
                    // Scroll to live chat
                    liveChat.scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            }
            
            if (sendEmailBtn) {
                sendEmailBtn.addEventListener('click', function() {
                    // Scroll to contact options
                    document.querySelector('.contact-options').scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            }
            
            if (callSupportBtn) {
                callSupportBtn.addEventListener('click', function() {
                    // Show phone number with confirmation
                    if (confirm('Call SmartHunt Support at +254 700 123 456?')) {
                        // In a real app, this would initiate a phone call
                        window.location.href = 'tel:+254700123456';
                    }
                });
            }
            
            // Contact Option Buttons
            const composeEmailBtn = document.getElementById('compose-email');
            const callNowBtn = document.getElementById('call-now');
            const getDirectionsBtn = document.getElementById('get-directions');
            
            if (composeEmailBtn) {
                composeEmailBtn.addEventListener('click', function() {
                    // Open email client
                    window.location.href = 'mailto:support@smarthunt.com?subject=SmartHunt Support Request';
                });
            }
            
            if (callNowBtn) {
                callNowBtn.addEventListener('click', function() {
                    // Initiate phone call
                    if (confirm('Call SmartHunt Support at +254 700 123 456?')) {
                        window.location.href = 'tel:+254700123456';
                    }
                });
            }
            
            if (getDirectionsBtn) {
                getDirectionsBtn.addEventListener('click', function() {
                    // Open maps with office location
                    window.open('https://maps.google.com/?q=SmartHunt+Nairobi+Kenya', '_blank');
                });
            }
            
            // Live Chat Functionality
            const chatInput = document.getElementById('chat-input');
            const sendMessageBtn = document.getElementById('send-message');
            const chatMessages = document.querySelector('.chat-messages');
            
            if (sendMessageBtn && chatInput) {
                sendMessageBtn.addEventListener('click', sendChatMessage);
                chatInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        sendChatMessage();
                    }
                });
            }
            
            function sendChatMessage() {
                const message = chatInput.value.trim();
                
                if (message) {
                    // Add user message
                    const userMessage = document.createElement('div');
                    userMessage.className = 'message user';
                    userMessage.innerHTML = `
                        ${message}
                        <div class="message-time">${getCurrentTime()}</div>
                    `;
                    chatMessages.appendChild(userMessage);
                    
                    // Clear input
                    chatInput.value = '';
                    
                    // Scroll to bottom
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                    
                    // Simulate support response after a delay
                    setTimeout(() => {
                        const supportMessage = document.createElement('div');
                        supportMessage.className = 'message support';
                        supportMessage.innerHTML = `
                            Thank you for your message. Our support team is reviewing your inquiry and will respond shortly.
                            <div class="message-time">${getCurrentTime()}</div>
                        `;
                        chatMessages.appendChild(supportMessage);
                        
                        // Scroll to bottom again
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }, 2000);
                }
            }
            
            function getCurrentTime() {
                const now = new Date();
                return now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            }
        });
    </script>
</body>
</html>