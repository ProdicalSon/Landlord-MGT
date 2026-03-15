<?php
// admin/includes/sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <img src="../assets/icons/smartlogo.png" alt="SmartHunt" onerror="this.src='https://via.placeholder.com/60x60?text=SH'">
        <h2>SmartHunt Admin</h2>
    </div>

    <nav class="sidebar-nav">
        <ul>
            <li class="nav-section">Main</li>
            <li class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                <a href="index.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-section">User Management</li>
            <li class="<?php echo $current_page == 'landlords.php' ? 'active' : ''; ?>">
                <a href="landlords.php">
                    <i class="fas fa-building"></i>
                    <span>Landlords</span>
                </a>
            </li>
            <li class="<?php echo $current_page == 'clients.php' ? 'active' : ''; ?>">
                <a href="clients.php">
                    <i class="fas fa-users"></i>
                    <span>Clients/Students</span>
                </a>
            </li>

            <li class="nav-section">Property Management</li>
            <li class="<?php echo $current_page == 'properties.php' ? 'active' : ''; ?>">
                <a href="properties.php">
                    <i class="fas fa-home"></i>
                    <span>All Properties</span>
                </a>
            </li>
            <li class="<?php echo $current_page == 'tenancies.php' ? 'active' : ''; ?>">
                <a href="tenancies.php">
                    <i class="fas fa-handshake"></i>
                    <span>Tenancies</span>
                </a>
            </li>

            <li class="nav-section">Financial</li>
            <li class="<?php echo $current_page == 'payments.php' ? 'active' : ''; ?>">
                <a href="payments.php">
                    <i class="fas fa-credit-card"></i>
                    <span>Payments</span>
                </a>
            </li>
            <li class="<?php echo $current_page == 'reports.php' ? 'active' : ''; ?>">
                <a href="reports.php">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a>
            </li>

            <li class="nav-section">System</li>
            <li class="<?php echo $current_page == 'settings.php' ? 'active' : ''; ?>">
                <a href="settings.php">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
            <li class="<?php echo $current_page == 'profile.php' ? 'active' : ''; ?>">
                <a href="profile.php">
                    <i class="fas fa-user-circle"></i>
                    <span>My Profile</span>
                </a>
            </li>
            <li>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <div class="admin-info">
            <div class="admin-avatar">
                <?php echo strtoupper(substr($_SESSION['admin_name'] ?? $_SESSION['admin_username'] ?? 'A', 0, 1)); ?>
            </div>
            <div class="admin-details">
                <p class="admin-name"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? $_SESSION['admin_username'] ?? 'Admin'); ?></p>
                <p class="admin-role"><?php echo ucfirst($_SESSION['admin_role'] ?? 'Admin'); ?></p>
            </div>
        </div>
    </div>
</aside>

<main class="admin-main">
    <header class="admin-header">
        <button class="sidebar-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <h1><?php echo $page_title ?? 'Dashboard'; ?></h1>
        <div class="header-actions">
            <a href="../index.php" target="_blank" class="header-btn" title="View Site">
                <i class="fas fa-external-link-alt"></i>
            </a>
            <a href="profile.php" class="header-btn" title="My Profile">
                <i class="fas fa-user-circle"></i>
            </a>
            <a href="logout.php" class="header-btn" title="Logout">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </header>
    <div class="admin-content">