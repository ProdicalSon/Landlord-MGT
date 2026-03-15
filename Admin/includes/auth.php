<?php
// admin/includes/auth.php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header('Location: login.php');
    exit;
}

// Optional: Check admin role for specific permissions
function isSuperAdmin() {
    return isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'super_admin';
}

function isAdmin() {
    return isset($_SESSION['admin_role']) && in_array($_SESSION['admin_role'], ['super_admin', 'admin']);
}

function checkPermission($required_role = 'admin') {
    if ($required_role === 'super_admin' && !isSuperAdmin()) {
        header('HTTP/1.0 403 Forbidden');
        die('Access denied. Super Admin privileges required.');
    }
}

// Get admin info for display
function getAdminName() {
    return $_SESSION['admin_name'] ?? $_SESSION['admin_username'] ?? 'Admin';
}

function getAdminRole() {
    return $_SESSION['admin_role'] ?? 'admin';
}
?>