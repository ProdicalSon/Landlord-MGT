<?php
// logout.php
session_start();

require_once __DIR__ . '/models/UserModel.php';

// Clear remember me cookie
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// If user is logged in, clear their sessions from database
if (isset($_SESSION['user_id'])) {
    $userModel = new UserModel();
    $userModel->logout($_SESSION['user_id']);
}

// Clear all session data
$_SESSION = array();

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: login.php?logged_out=1');
exit;
?>