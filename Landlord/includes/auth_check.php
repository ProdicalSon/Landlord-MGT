<?php
require_once 'config.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function requireLandlord() {
    requireLogin();
    if ($_SESSION['user_type'] !== 'landlord') {
        header("Location: unauthorized.php");
        exit();
    }
}

function requireStudent() {
    requireLogin();
    if ($_SESSION['user_type'] !== 'student') {
        header("Location: unauthorized.php");
        exit();
    }
}


if (!isLoggedIn() && isset($_COOKIE['remember_token'])) {
    try {
        $pdo = getDBConnection();
        $token = $_COOKIE['remember_token'];
        
        $stmt = $pdo->prepare("SELECT u.* FROM sessions s JOIN users u ON s.user_id = u.id WHERE s.session_token = ? AND s.expires_at > NOW()");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['user_type'] = $user['user_type'];
        }
    } catch(PDOException $e) {
       
    }
}
?>