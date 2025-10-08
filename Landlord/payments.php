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

        /* Payments Styles - Maintaining existing styling patterns */
        .payments-container {
            background: var(--light);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .payments-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 25px 30px;
            text-align: center;
        }

        .payments-header h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .payments-header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .payments-controls {
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

        .payments-stats {
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

        .payments-overview {
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

        .overview-card.pending .icon {
            background: rgba(255, 180, 0, 0.1);
            color: var(--warning);
        }

        .overview-card.overdue .icon {
            background: rgba(255, 90, 95, 0.1);
            color: var(--danger);
        }

        .overview-card.tenants .icon {
            background: rgba(66, 133, 244, 0.1);
            color: var(--secondary);
        }

        .overview-card .amount {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .overview-card.income .amount {
            color: var(--success);
        }

        .overview-card.pending .amount {
            color: var(--warning);
        }

        .overview-card.overdue .amount {
            color: var(--danger);
        }

        .overview-card.tenants .amount {
            color: var(--secondary);
        }

        .overview-card .label {
            font-size: 14px;
            color: var(--text);
        }

        .payments-table-container {
            padding: 30px;
        }

        .payments-table {
            width: 100%;
            border-collapse: collapse;
        }

        .payments-table th,
        .payments-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--light-gray);
        }

        .payments-table th {
            background-color: var(--light-gray);
            font-weight: 600;
            color: var(--dark);
        }

        .payments-table tr:hover {
            background-color: var(--light-gray);
        }

        .payment-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: white;
        }

        .status-paid {
            background-color: var(--success);
        }

        .status-pending {
            background-color: var(--warning);
        }

        .status-overdue {
            background-color: var(--danger);
        }

        .status-partial {
            background-color: var(--secondary);
        }

        .payment-actions {
            display: flex;
            gap: 8px;
        }

        .payment-btn {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
        }

        .btn-remind {
            background-color: var(--warning);
            color: white;
        }

        .btn-remind:hover {
            background-color: #e6a200;
        }

        .btn-receipt {
            background-color: var(--secondary);
            color: white;
        }

        .btn-receipt:hover {
            background-color: #3367d6;
        }

        .btn-record {
            background-color: var(--success);
            color: white;
        }

        .btn-record:hover {
            background-color: #008a7d;
        }

        .tenant-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .tenant-avatar-small {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            overflow: hidden;
        }

        .tenant-avatar-small img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .tenant-details-small h4 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 2px;
            color: var(--dark);
        }

        .tenant-details-small p {
            font-size: 12px;
            color: var(--text);
        }

        .amount-cell {
            font-weight: 600;
            color: var(--dark);
        }

        .due-date-cell {
            font-size: 14px;
        }

        .overdue {
            color: var(--danger);
            font-weight: 600;
        }

        /* Payment History Styles */
        .payment-history-container {
            background: var(--light);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .payment-history-header {
            background: linear-gradient(135deg, var(--secondary) 0%, #5e97f6 100%);
            color: white;
            padding: 25px 30px;
            text-align: center;
        }

        .payment-history-header h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .payment-history-header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .history-filters {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 30px;
            border-bottom: 1px solid var(--light-gray);
        }

        .date-range {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .date-range input {
            padding: 8px 15px;
            border: 1px solid var(--gray);
            border-radius: 20px;
            font-size: 14px;
        }

        .export-btn {
            background-color: var(--success);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .export-btn:hover {
            background-color: #008a7d;
        }

        /* Charts for payments */
        .payment-charts {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            padding: 30px;
        }

        .chart-card {
            background: var(--light);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .chart-card h3 {
            font-size: 18px;
            margin-bottom: 15px;
            color: var(--dark);
            text-align: center;
        }

        .chart-container-payment {
            position: relative;
            height: 250px;
            width: 100%;
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
            .payments-controls {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }
            .payments-stats {
                justify-content: center;
            }
            .filter-controls {
                justify-content: center;
            }
            .payment-charts {
                grid-template-columns: 1fr;
            }
            .payments-table {
                display: block;
                overflow-x: auto;
            }
            .history-filters {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }
            .date-range {
                justify-content: center;
            }
            .payments-overview {
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
                <li><a href="#" class="active" data-content="payments"><i class="fas fa-credit-card"></i> Payments <span class="notification-badge">2</span></a></li>
                <li><a href="#" data-content="location"><i class="fas fa-map-marked-alt"></i> Location</a></li>
                <li><a href="announcements.php" data-content="announcements"><i class="fas fa-bullhorn"></i> Announcements</a></li>
                <li><a href="reports.php" data-content="reports"><i class="fas fa-chart-bar"></i> Reports</a></li>
                <li><a href="#" data-content="profile-settings"><i class="fas fa-user-cog"></i> Profile Setting</a></li>
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

            <!-- Payments Section -->
            <section class="content" id="payments-content">
                <div class="payments-container">
                    <div class="payments-header">
                        <h1><i class="fas fa-credit-card"></i> Payment Management</h1>
                        <p>Track and manage all rental payments in one place</p>
                    </div>
                    
                    <div class="payments-overview">
                        <div class="overview-card income">
                            <div class="icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div class="amount" id="total-income">Ksh 145,000</div>
                            <div class="label">Total Income This Month</div>
                        </div>
                        
                        <div class="overview-card pending">
                            <div class="icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="amount" id="pending-amount">Ksh 35,000</div>
                            <div class="label">Pending Payments</div>
                        </div>
                        
                        <div class="overview-card overdue">
                            <div class="icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="amount" id="overdue-amount">Ksh 15,000</div>
                            <div class="label">Overdue Payments</div>
                        </div>
                        
                        <div class="overview-card tenants">
                            <div class="icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="amount" id="paid-tenants">8/12</div>
                            <div class="label">Tenants Paid</div>
                        </div>
                    </div>
                    
                    <div class="payments-controls">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="search-payments" placeholder="Search payments...">
                        </div>
                        
                        <div class="filter-controls">
                            <select id="payment-status-filter">
                                <option value="all">All Payments</option>
                                <option value="paid">Paid</option>
                                <option value="pending">Pending</option>
                                <option value="overdue">Overdue</option>
                                <option value="partial">Partial</option>
                            </select>
                            <select id="payment-month-filter">
                                <option value="all">All Months</option>
                                <option value="january">January</option>
                                <option value="february">February</option>
                                <option value="march">March</option>
                                <option value="april">April</option>
                                <option value="may">May</option>
                                <option value="june" selected>June</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="payments-table-container">
                        <table class="payments-table">
                            <thead>
                                <tr>
                                    <th>Tenant</th>
                                    <th>Property</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="payments-table-body">
                                <!-- Payments will be dynamically populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Payment History Section -->
                <div class="payment-history-container">
                    <div class="payment-history-header">
                        <h1><i class="fas fa-history"></i> Payment History</h1>
                        <p>View and export payment records</p>
                    </div>
                    
                    <div class="history-filters">
                        <div class="date-range">
                            <input type="month" id="start-date" value="2023-01">
                            <span>to</span>
                            <input type="month" id="end-date" value="2023-06">
                        </div>
                        
                        <button class="export-btn">
                            <i class="fas fa-download"></i> Export Report
                        </button>
                    </div>
                    
                    <div class="payment-charts">
                        <div class="chart-card">
                            <h3>Payment Status Distribution</h3>
                            <div class="chart-container-payment">
                                <canvas id="paymentStatusChart"></canvas>
                            </div>
                        </div>
                        
                        <div class="chart-card">
                            <h3>Monthly Income Trend</h3>
                            <div class="chart-container-payment">
                                <canvas id="incomeTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <div class="payments-table-container">
                        <table class="payments-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Tenant</th>
                                    <th>Property</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Reference</th>
                                </tr>
                            </thead>
                            <tbody id="payment-history-body">
                                <!-- Payment history will be dynamically populated here -->
                            </tbody>
                        </table>
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
        // Sample payments data
        const paymentsData = [
            {
                id: 1,
                tenant: {
                    name: "John Mwangi",
                    avatar: "https://placehold.co/35x35/4285F4/FFFFFF?text=JM",
                    phone: "+254 712 345 678"
                },
                property: "Tripple A Apartments",
                amount: 5000,
                dueDate: "2023-06-05",
                status: "paid",
                paidDate: "2023-06-01",
                method: "M-Pesa",
                reference: "MP23456789"
            },
            {
                id: 2,
                tenant: {
                    name: "Sarah Wanjiku",
                    avatar: "https://placehold.co/35x35/00A699/FFFFFF?text=SW",
                    phone: "+254 723 456 789"
                },
                property: "Green Valley Homes",
                amount: 7000,
                dueDate: "2023-06-05",
                status: "pending",
                paidDate: null,
                method: "",
                reference: ""
            },
            {
                id: 3,
                tenant: {
                    name: "David Ochieng",
                    avatar: "https://placehold.co/35x35/FF385C/FFFFFF?text=DO",
                    phone: "+254 734 567 890"
                },
                property: "Campus View Apartments",
                amount: 12000,
                dueDate: "2023-06-05",
                status: "paid",
                paidDate: "2023-06-03",
                method: "Bank Transfer",
                reference: "BT98765432"
            },
            {
                id: 4,
                tenant: {
                    name: "Grace Akinyi",
                    avatar: "https://placehold.co/35x35/FFB400/FFFFFF?text=GA",
                    phone: "+254 745 678 901"
                },
                property: "Sunset Heights",
                amount: 15000,
                dueDate: "2023-06-05",
                status: "overdue",
                paidDate: null,
                method: "",
                reference: ""
            },
            {
                id: 5,
                tenant: {
                    name: "Peter Kamau",
                    avatar: "https://placehold.co/35x35/4285F4/FFFFFF?text=PK",
                    phone: "+254 756 789 012"
                },
                property: "River Side Apartments",
                amount: 4500,
                dueDate: "2023-06-05",
                status: "partial",
                paidDate: "2023-06-04",
                method: "M-Pesa",
                reference: "MP34567890",
                paidAmount: 3000
            },
            {
                id: 6,
                tenant: {
                    name: "Mary Njeri",
                    avatar: "https://placehold.co/35x35/00A699/FFFFFF?text=MN",
                    phone: "+254 767 890 123"
                },
                property: "Tripple A Apartments",
                amount: 5000,
                dueDate: "2023-06-05",
                status: "paid",
                paidDate: "2023-06-02",
                method: "Cash",
                reference: "CASH001"
            }
        ];

        // Sample payment history data
        const paymentHistoryData = [
            {
                id: 1,
                date: "2023-06-01",
                tenant: "John Mwangi",
                property: "Tripple A Apartments",
                amount: 5000,
                method: "M-Pesa",
                reference: "MP23456789"
            },
            {
                id: 2,
                date: "2023-06-02",
                tenant: "Mary Njeri",
                property: "Tripple A Apartments",
                amount: 5000,
                method: "Cash",
                reference: "CASH001"
            },
            {
                id: 3,
                date: "2023-06-03",
                tenant: "David Ochieng",
                property: "Campus View Apartments",
                amount: 12000,
                method: "Bank Transfer",
                reference: "BT98765432"
            },
            {
                id: 4,
                date: "2023-06-04",
                tenant: "Peter Kamau",
                property: "River Side Apartments",
                amount: 3000,
                method: "M-Pesa",
                reference: "MP34567890"
            },
            {
                id: 5,
                date: "2023-05-28",
                tenant: "James Mutiso",
                property: "Green Valley Homes",
                amount: 7000,
                method: "M-Pesa",
                reference: "MP45678901"
            },
            {
                id: 6,
                date: "2023-05-25",
                tenant: "Lucy Adhiambo",
                property: "Campus View Apartments",
                amount: 12000,
                method: "Bank Transfer",
                reference: "BT87654321"
            }
        ];

        // Function to render payments
        function renderPayments(payments = paymentsData) {
            const paymentsTableBody = document.getElementById('payments-table-body');
            
            if (payments.length === 0) {
                paymentsTableBody.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px;">
                            <div class="empty-state">
                                <i class="fas fa-credit-card"></i>
                                <h3>No Payments Found</h3>
                                <p>You don't have any payments matching your search criteria.</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }
            
            // Update payment statistics
            updatePaymentStats(payments);
            
            paymentsTableBody.innerHTML = payments.map(payment => `
                <tr>
                    <td>
                        <div class="tenant-info">
                            <div class="tenant-avatar-small">
                                <img src="${payment.tenant.avatar}" alt="${payment.tenant.name}">
                            </div>
                            <div class="tenant-details-small">
                                <h4>${payment.tenant.name}</h4>
                                <p>${payment.tenant.phone}</p>
                            </div>
                        </div>
                    </td>
                    <td>${payment.property}</td>
                    <td class="amount-cell">
                        ${payment.status === 'partial' ? 
                            `Ksh ${payment.paidAmount.toLocaleString()} / Ksh ${payment.amount.toLocaleString()}` : 
                            `Ksh ${payment.amount.toLocaleString()}`
                        }
                    </td>
                    <td class="due-date-cell ${isOverdue(payment.dueDate) && payment.status !== 'paid' ? 'overdue' : ''}">
                        ${formatDate(payment.dueDate)}
                        ${isOverdue(payment.dueDate) && payment.status !== 'paid' ? '<br><small>(Overdue)</small>' : ''}
                    </td>
                    <td>
                        <span class="payment-status status-${payment.status}">
                            ${payment.status.charAt(0).toUpperCase() + payment.status.slice(1)}
                        </span>
                    </td>
                    <td>
                        <div class="payment-actions">
                            ${payment.status === 'pending' || payment.status === 'overdue' ? `
                                <button class="payment-btn btn-remind" onclick="sendReminder(${payment.id})">
                                    <i class="fas fa-bell"></i> Remind
                                </button>
                            ` : ''}
                            ${payment.status === 'paid' ? `
                                <button class="payment-btn btn-receipt" onclick="viewReceipt(${payment.id})">
                                    <i class="fas fa-receipt"></i> Receipt
                                </button>
                            ` : ''}
                            <button class="payment-btn btn-record" onclick="recordPayment(${payment.id})">
                                <i class="fas fa-edit"></i> Record
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // Function to render payment history
        function renderPaymentHistory(history = paymentHistoryData) {
            const paymentHistoryBody = document.getElementById('payment-history-body');
            
            paymentHistoryBody.innerHTML = history.map(payment => `
                <tr>
                    <td>${formatDate(payment.date)}</td>
                    <td>${payment.tenant}</td>
                    <td>${payment.property}</td>
                    <td class="amount-cell">Ksh ${payment.amount.toLocaleString()}</td>
                    <td>${payment.method}</td>
                    <td>${payment.reference}</td>
                </tr>
            `).join('');
        }

        // Function to update payment statistics
        function updatePaymentStats(payments) {
            const totalIncome = payments
                .filter(p => p.status === 'paid')
                .reduce((sum, payment) => sum + (payment.paidAmount || payment.amount), 0);
            
            const pendingAmount = payments
                .filter(p => p.status === 'pending')
                .reduce((sum, payment) => sum + payment.amount, 0);
            
            const overdueAmount = payments
                .filter(p => p.status === 'overdue')
                .reduce((sum, payment) => sum + payment.amount, 0);
            
            const paidTenants = payments.filter(p => p.status === 'paid').length;
            const totalTenants = payments.length;
            
            document.getElementById('total-income').textContent = `Ksh ${totalIncome.toLocaleString()}`;
            document.getElementById('pending-amount').textContent = `Ksh ${pendingAmount.toLocaleString()}`;
            document.getElementById('overdue-amount').textContent = `Ksh ${overdueAmount.toLocaleString()}`;
            document.getElementById('paid-tenants').textContent = `${paidTenants}/${totalTenants}`;
        }

        // Function to filter payments
        function filterPayments() {
            const searchTerm = document.getElementById('search-payments').value.toLowerCase();
            const statusFilter = document.getElementById('payment-status-filter').value;
            const monthFilter = document.getElementById('payment-month-filter').value;
            
            const filteredPayments = paymentsData.filter(payment => {
                const matchesSearch = payment.tenant.name.toLowerCase().includes(searchTerm) || 
                                     payment.property.toLowerCase().includes(searchTerm);
                const matchesStatus = statusFilter === 'all' || payment.status === statusFilter;
                const matchesMonth = monthFilter === 'all' || true; // Simplified for demo
                
                return matchesSearch && matchesStatus && matchesMonth;
            });
            
            renderPayments(filteredPayments);
        }

        // Function to check if payment is overdue
        function isOverdue(dueDate) {
            const today = new Date();
            const due = new Date(dueDate);
            return due < today;
        }

        // Function to format date
        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('en-US', options);
        }

        // Payment action functions
        function sendReminder(paymentId) {
            const payment = paymentsData.find(p => p.id === paymentId);
            if (payment) {
                alert(`Sending reminder to ${payment.tenant.name} about Ksh ${payment.amount.toLocaleString()} payment`);
                // In real application, this would send SMS/email reminder
            }
        }

        function viewReceipt(paymentId) {
            const payment = paymentsData.find(p => p.id === paymentId);
            if (payment) {
                alert(`Showing receipt for ${payment.tenant.name}'s payment of Ksh ${payment.amount.toLocaleString()}`);
                // In real application, this would open receipt modal
            }
        }

        function recordPayment(paymentId) {
            const payment = paymentsData.find(p => p.id === paymentId);
            if (payment) {
                alert(`Recording payment for ${payment.tenant.name}`);
                // In real application, this would open payment recording form
            }
        }

        // Initialize charts for payments
        function initializePaymentCharts() {
            // Payment Status Chart
            const statusCtx = document.getElementById('paymentStatusChart').getContext('2d');
            const statusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Paid', 'Pending', 'Overdue', 'Partial'],
                    datasets: [{
                        data: [3, 1, 1, 1], // Based on sample data
                        backgroundColor: [
                            '#00A699',
                            '#FFB400',
                            '#FF5A5F',
                            '#4285F4'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Income Trend Chart
            const incomeCtx = document.getElementById('incomeTrendChart').getContext('2d');
            const incomeChart = new Chart(incomeCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Monthly Income (Ksh)',
                        data: [120000, 135000, 142000, 138000, 148000, 145000],
                        borderColor: '#00A699',
                        backgroundColor: 'rgba(0, 166, 153, 0.1)',
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

        // Initialize payments when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Set payments as active
            document.querySelectorAll('.sidebar-menu a').forEach(link => {
                link.classList.remove('active');
            });
            document.querySelector('[data-content="payments"]').classList.add('active');
            
            // Hide all content sections except payments
            document.querySelectorAll('.content').forEach(section => {
                if (section.id !== 'payments-content') {
                    section.classList.add('hidden');
                }
            });
            
            renderPayments();
            renderPaymentHistory();
            initializePaymentCharts();
            
            // Add event listeners for filtering
            document.getElementById('search-payments').addEventListener('input', filterPayments);
            document.getElementById('payment-status-filter').addEventListener('change', filterPayments);
            document.getElementById('payment-month-filter').addEventListener('change', filterPayments);
            
            // Export button functionality
            document.querySelector('.export-btn').addEventListener('click', function() {
                alert('Exporting payment report...');
                // In real application, this would generate and download PDF/Excel report
            });
        });
    </script>
</body>
</html>