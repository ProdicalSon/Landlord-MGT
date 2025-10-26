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

        /* Reports Styles - Maintaining existing styling patterns */
        .reports-container {
            background: var(--light);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .reports-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 25px 30px;
            text-align: center;
        }

        .reports-header h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .reports-header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .reports-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 30px;
            border-bottom: 1px solid var(--light-gray);
        }

        .date-range {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .date-range input {
            padding: 8px 15px;
            border: 1px solid var(--gray);
            border-radius: 20px;
            font-size: 14px;
        }

        .report-actions {
            display: flex;
            gap: 15px;
        }

        .report-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .report-btn:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
        }

        .report-btn.secondary {
            background: var(--secondary);
        }

        .report-btn.secondary:hover {
            background: #3367d6;
        }

        .report-btn.success {
            background: var(--success);
        }

        .report-btn.success:hover {
            background: #008a7d;
        }

        .reports-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 30px;
            background: var(--light-gray);
        }

        .overview-card {
            background: var(--light);
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .overview-card .icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 24px;
        }

        .overview-card.income .icon {
            background: rgba(0, 166, 153, 0.1);
            color: var(--success);
        }

        .overview-card.occupancy .icon {
            background: rgba(66, 133, 244, 0.1);
            color: var(--secondary);
        }

        .overview-card.expenses .icon {
            background: rgba(255, 90, 95, 0.1);
            color: var(--danger);
        }

        .overview-card.tenants .icon {
            background: rgba(255, 56, 92, 0.1);
            color: var(--primary);
        }

        .overview-card .amount {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .overview-card.income .amount {
            color: var(--success);
        }

        .overview-card.occupancy .amount {
            color: var(--secondary);
        }

        .overview-card.expenses .amount {
            color: var(--danger);
        }

        .overview-card.tenants .amount {
            color: var(--primary);
        }

        .overview-card .label {
            font-size: 14px;
            color: var(--text);
        }

        .reports-charts {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            padding: 30px;
        }

        .chart-card {
            background: var(--light);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .chart-card h3 {
            font-size: 18px;
            margin-bottom: 20px;
            color: var(--dark);
            text-align: center;
        }

        .chart-container-report {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            padding: 30px;
        }

        .report-card {
            background: var(--light);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-left: 4px solid var(--primary);
        }

        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .report-card.financial {
            border-left-color: var(--success);
        }

        .report-card.occupancy {
            border-left-color: var(--secondary);
        }

        .report-card.performance {
            border-left-color: var(--warning);
        }

        .report-card.tenant {
            border-left-color: var(--primary);
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .report-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .report-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
        }

        .report-type {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: white;
        }

        .type-financial {
            background-color: var(--success);
        }

        .type-occupancy {
            background-color: var(--secondary);
        }

        .type-performance {
            background-color: var(--warning);
        }

        .type-tenant {
            background-color: var(--primary);
        }

        .report-date {
            font-size: 12px;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .report-content {
            margin-bottom: 20px;
        }

        .report-content p {
            color: var(--text);
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .report-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .report-stat {
            text-align: center;
            padding: 15px;
            background: var(--light-gray);
            border-radius: 8px;
        }

        .stat-value {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-value.positive {
            color: var(--success);
        }

        .stat-value.negative {
            color: var(--danger);
        }

        .stat-value.neutral {
            color: var(--secondary);
        }

        .stat-label {
            font-size: 12px;
            color: var(--text);
        }

        .report-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid var(--light-gray);
        }

        .report-info {
            display: flex;
            gap: 15px;
            font-size: 12px;
            color: var(--text);
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .info-item i {
            color: var(--primary);
        }

        .report-btns {
            display: flex;
            gap: 8px;
        }

        .report-action-btn {
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

        .btn-download {
            background-color: var(--success);
            color: white;
        }

        .btn-download:hover {
            background-color: #008a7d;
        }

        .btn-view {
            background-color: var(--secondary);
            color: white;
        }

        .btn-view:hover {
            background-color: #3367d6;
        }

        .btn-share {
            background-color: var(--light-gray);
            color: var(--text);
        }

        .btn-share:hover {
            background-color: var(--gray);
        }

        /* Quick Stats Section */
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 30px;
            background: var(--light-gray);
        }

        .stat-item {
            background: var(--light);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .stat-item .value {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-item .label {
            font-size: 14px;
            color: var(--text);
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
            .reports-controls {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }
            .date-range {
                justify-content: center;
            }
            .report-actions {
                justify-content: center;
            }
            .reports-charts {
                grid-template-columns: 1fr;
            }
            .reports-grid {
                grid-template-columns: 1fr;
            }
            .reports-overview {
                grid-template-columns: 1fr;
            }
            .quick-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .quick-stats {
                grid-template-columns: 1fr;
            }
            .report-stats {
                grid-template-columns: 1fr;
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
                <li><a href="announcements.php" data-content="announcements"><i class="fas fa-bullhorn"></i> Announcements</a></li>
                <li><a href="reports.php" class="active" data-content="reports"><i class="fas fa-chart-bar"></i> Reports</a></li>
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

            <!-- Reports Section -->
            <section class="content" id="reports-content">
                <div class="reports-container">
                    <div class="reports-header">
                        <h1><i class="fas fa-chart-bar"></i> Reports & Analytics</h1>
                        <p>Comprehensive insights and analytics for your property portfolio</p>
                    </div>
                    
                    <div class="reports-controls">
                        <div class="date-range">
                            <input type="month" id="start-date" value="2025-01">
                            <span>to</span>
                            <input type="month" id="end-date" value="2025-06">
                        </div>
                        
                        <div class="report-actions">
                            <button class="report-btn" onclick="generateReport()">
                                <i class="fas fa-sync-alt"></i> Refresh Data
                            </button>
                            <button class="report-btn secondary" onclick="exportReport()">
                                <i class="fas fa-download"></i> Export PDF
                            </button>
                            <button class="report-btn success" onclick="scheduleReport()">
                                <i class="fas fa-calendar-plus"></i> Schedule Report
                            </button>
                        </div>
                    </div>
                    
                    <div class="reports-overview">
                        <div class="overview-card income">
                            <div class="icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div class="amount">Ksh 845,000</div>
                            <div class="label">Total Revenue (6 Months)</div>
                        </div>
                        
                        <div class="overview-card occupancy">
                            <div class="icon">
                                <i class="fas fa-home"></i>
                            </div>
                            <div class="amount">78%</div>
                            <div class="label">Average Occupancy Rate</div>
                        </div>
                        
                        <div class="overview-card expenses">
                            <div class="icon">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <div class="amount">Ksh 156,200</div>
                            <div class="label">Total Expenses</div>
                        </div>
                        
                        <div class="overview-card tenants">
                            <div class="icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="amount">32</div>
                            <div class="label">Active Tenants</div>
                        </div>
                    </div>
                    
                    <div class="reports-charts">
                        <div class="chart-card">
                            <h3>Revenue vs Expenses</h3>
                            <div class="chart-container-report">
                                <canvas id="revenueExpensesChart"></canvas>
                            </div>
                        </div>
                        
                        <div class="chart-card">
                            <h3>Occupancy Rate Trend</h3>
                            <div class="chart-container-report">
                                <canvas id="occupancyTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <div class="quick-stats">
                        <div class="stat-item">
                            <div class="value">94%</div>
                            <div class="label">On-time Payments</div>
                        </div>
                        <div class="stat-item">
                            <div class="value">12</div>
                            <div class="label">New Inquiries</div>
                        </div>
                        <div class="stat-item">
                            <div class="value">Ksh 688,800</div>
                            <div class="label">Net Profit</div>
                        </div>
                        <div class="stat-item">
                            <div class="value">4.2/5</div>
                            <div class="label">Tenant Satisfaction</div>
                        </div>
                    </div>
                    
                    <div class="reports-grid" id="reports-grid">
                        <!-- Reports will be dynamically populated here -->
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
        // Sample reports data
        const reportsData = [
            {
                id: 1,
                title: "Monthly Financial Report",
                type: "financial",
                period: "October 2025",
                generatedDate: "2025-10-01T10:00:00",
                description: "Comprehensive financial overview including revenue, expenses, and profit analysis for October 2025.",
                stats: [
                    { value: "Ksh 145,000", label: "Total Revenue", type: "positive" },
                    { value: "Ksh 28,500", label: "Total Expenses", type: "negative" },
                    { value: "Ksh 116,500", label: "Net Profit", type: "positive" },
                    { value: "+12%", label: "Growth", type: "positive" }
                ],
                views: 45,
                downloads: 12,
                status: "completed"
            },
            {
                id: 2,
                title: "Occupancy Analysis Report",
                type: "occupancy",
                period: "Q2 2025",
                generatedDate: "2025-10-05T14:30:00",
                description: "Detailed analysis of property occupancy rates, tenant turnover, and vacancy trends for Q2 2025.",
                stats: [
                    { value: "78%", label: "Avg Occupancy", type: "neutral" },
                    { value: "92%", label: "Retention Rate", type: "positive" },
                    { value: "8%", label: "Vacancy Rate", type: "negative" },
                    { value: "+5%", label: "vs Last Quarter", type: "positive" }
                ],
                views: 32,
                downloads: 8,
                status: "completed"
            },
            {
                id: 3,
                title: "Property Performance Report",
                type: "performance",
                period: "June - October 2025",
                generatedDate: "2025-10-10T09:15:00",
                description: "Performance comparison across all properties including revenue per property and maintenance costs.",
                stats: [
                    { value: "Ksh 845,000", label: "Total Revenue", type: "positive" },
                    { value: "Ksh 156,200", label: "Maintenance Cost", type: "negative" },
                    { value: "94%", label: "Payment Rate", type: "positive" },
                    { value: "4.2/5", label: "Satisfaction", type: "neutral" }
                ],
                views: 28,
                downloads: 15,
                status: "completed"
            },
            {
                id: 4,
                title: "Tenant Analytics Report",
                type: "tenant",
                period: "Year 2025",
                generatedDate: "2025-10-15T16:45:00",
                description: "Comprehensive tenant analytics including demographics, payment behavior, and satisfaction metrics.",
                stats: [
                    { value: "32", label: "Active Tenants", type: "neutral" },
                    { value: "4.2/5", label: "Avg Rating", type: "positive" },
                    { value: "94%", label: "On-time Payments", type: "positive" },
                    { value: "6%", label: "Turnover", type: "negative" }
                ],
                views: 38,
                downloads: 10,
                status: "completed"
            },
            {
                id: 5,
                title: "Maintenance Cost Analysis",
                type: "financial",
                period: "Q2 2025",
                generatedDate: "2025-10-08T11:20:00",
                description: "Breakdown of maintenance costs by property and category with recommendations for cost optimization.",
                stats: [
                    { value: "Ksh 45,200", label: "Total Cost", type: "negative" },
                    { value: "-8%", label: "vs Last Quarter", type: "positive" },
                    { value: "12", label: "Maintenance Jobs", type: "neutral" },
                    { value: "92%", label: "Completed", type: "positive" }
                ],
                views: 24,
                downloads: 7,
                status: "completed"
            },
            {
                id: 6,
                title: "Marketing Performance Report",
                type: "performance",
                period: "June 2025",
                generatedDate: "2025-10-03T13:10:00",
                description: "Analysis of marketing channels effectiveness and lead conversion rates for property listings.",
                stats: [
                    { value: "156", label: "Total Leads", type: "positive" },
                    { value: "23%", label: "Conversion Rate", type: "positive" },
                    { value: "Ksh 12,500", label: "Ad Spend", type: "negative" },
                    { value: "8.2x", label: "ROI", type: "positive" }
                ],
                views: 19,
                downloads: 5,
                status: "completed"
            }
        ];

        // Function to render reports
        function renderReports(reports = reportsData) {
            const reportsGrid = document.getElementById('reports-grid');
            
            if (reports.length === 0) {
                reportsGrid.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-chart-bar"></i>
                        <h3>No Reports Found</h3>
                        <p>You don't have any reports matching your search criteria.</p>
                    </div>
                `;
                return;
            }
            
            reportsGrid.innerHTML = reports.map(report => `
                <div class="report-card ${report.type}" data-id="${report.id}">
                    <div class="report-header">
                        <div>
                            <div class="report-title">${report.title}</div>
                            <div class="report-meta">
                                <span class="report-type type-${report.type}">
                                    ${getReportTypeDisplay(report.type)}
                                </span>
                                <span class="report-date">
                                    <i class="far fa-calendar"></i>
                                    ${report.period}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="report-content">
                        <p>${report.description}</p>
                    </div>
                    
                    <div class="report-stats">
                        ${report.stats.map(stat => `
                            <div class="report-stat">
                                <div class="stat-value ${stat.type}">${stat.value}</div>
                                <div class="stat-label">${stat.label}</div>
                            </div>
                        `).join('')}
                    </div>
                    
                    <div class="report-actions">
                        <div class="report-info">
                            <div class="info-item">
                                <i class="far fa-eye"></i>
                                <span>${report.views} views</span>
                            </div>
                            <div class="info-item">
                                <i class="far fa-calendar-alt"></i>
                                <span>${formatDate(report.generatedDate)}</span>
                            </div>
                        </div>
                        
                        <div class="report-btns">
                            <button class="report-action-btn btn-download" onclick="downloadReport(${report.id})">
                                <i class="fas fa-download"></i> PDF
                            </button>
                            <button class="report-action-btn btn-view" onclick="viewReport(${report.id})">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="report-action-btn btn-share" onclick="shareReport(${report.id})">
                                <i class="fas fa-share"></i> Share
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Helper function to get report type display name
        function getReportTypeDisplay(type) {
            const types = {
                financial: 'Financial',
                occupancy: 'Occupancy',
                performance: 'Performance',
                tenant: 'Tenant Analytics'
            };
            return types[type] || type;
        }

        // Function to format date
        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('en-US', options);
        }

        // Initialize charts for reports
        function initializeReportCharts() {
            // Revenue vs Expenses Chart
            const revenueCtx = document.getElementById('revenueExpensesChart').getContext('2d');
            const revenueChart = new Chart(revenueCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [
                        {
                            label: 'Revenue',
                            data: [120000, 135000, 142000, 138000, 148000, 145000],
                            backgroundColor: '#00A699',
                            borderColor: '#00A699',
                            borderWidth: 1
                        },
                        {
                            label: 'Expenses',
                            data: [25000, 28000, 32000, 29500, 31000, 28500],
                            backgroundColor: '#FF5A5F',
                            borderColor: '#FF5A5F',
                            borderWidth: 1
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
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'Ksh ' + value.toLocaleString();
                                }
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

            // Occupancy Trend Chart
            const occupancyCtx = document.getElementById('occupancyTrendChart').getContext('2d');
            const occupancyChart = new Chart(occupancyCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Occupancy Rate (%)',
                        data: [72, 75, 78, 76, 79, 78],
                        borderColor: '#4285F4',
                        backgroundColor: 'rgba(66, 133, 244, 0.1)',
                        tension: 0.3,
                        fill: true
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
                            max: 100,
                            grid: {
                                drawBorder: false
                            },
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
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

        // Report action functions
        function generateReport() {
            alert('Refreshing report data...');
            // In real application, this would fetch latest data and update charts
        }

        function exportReport() {
            alert('Exporting comprehensive report as PDF...');
            // In real application, this would generate and download PDF report
        }

        function scheduleReport() {
            alert('Opening report scheduling options...');
            // In real application, this would open scheduling modal
        }

        function downloadReport(reportId) {
            const report = reportsData.find(r => r.id === reportId);
            if (report) {
                alert(`Downloading ${report.title} as PDF...`);
                // In real application, this would download the specific report
            }
        }

        function viewReport(reportId) {
            const report = reportsData.find(r => r.id === reportId);
            if (report) {
                alert(`Viewing detailed report: ${report.title}\n\nPeriod: ${report.period}\nDescription: ${report.description}`);
                // In real application, this would open a detailed report view
            }
        }

        function shareReport(reportId) {
            const report = reportsData.find(r => r.id === reportId);
            if (report) {
                alert(`Sharing report: ${report.title}`);
                // In real application, this would open share options
            }
        }

        // Initialize reports when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Set reports as active
            document.querySelectorAll('.sidebar-menu a').forEach(link => {
                link.classList.remove('active');
            });
            document.querySelector('[data-content="reports"]').classList.add('active');
            
            // Hide all content sections except reports
            document.querySelectorAll('.content').forEach(section => {
                if (section.id !== 'reports-content') {
                    section.classList.add('hidden');
                }
            });
            
            renderReports();
            initializeReportCharts();
        });
    </script>
</body>
</html>