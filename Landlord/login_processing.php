<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $rememberMe = isset($_POST['remember']);
    
    // Validate input
    $errors = [];
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required.";
    }
    
    // If no errors, process login
    if (empty($errors)) {
        try {
            $pdo = getDBConnection();
            
            // Get user by email
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Check if account is verified
                if (!$user['is_verified']) {
                    $errors[] = "Please verify your email address before logging in.";
                } else {
                    // Record successful login attempt
                    $stmt = $pdo->prepare("INSERT INTO login_attempts (user_id, ip_address, success) VALUES (?, ?, ?)");
                    $stmt->execute([$user['id'], $_SERVER['REMOTE_ADDR'], true]);
                    
                    // Create session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['user_type'] = $user['user_type'];
                    
                    // Create remember me token if requested
                    if ($rememberMe) {
                        $token = bin2hex(random_bytes(32));
                        $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
                        
                        $stmt = $pdo->prepare("INSERT INTO sessions (user_id, session_token, ip_address, user_agent, expires_at) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$user['id'], $token, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], $expires]);
                        
                        setcookie("remember_token", $token, time() + (30 * 24 * 60 * 60), "/");
                    }
                    
                    // Redirect based on user type
                    if ($user['user_type'] === 'landlord') {
                        header("Location: landlord_dashboard.php");
                    } else {
                        header("Location: student_dashboard.php");
                    }
                    exit();
                }
            } else {
                // Record failed login attempt
                if ($user) {
                    $stmt = $pdo->prepare("INSERT INTO login_attempts (user_id, ip_address, success) VALUES (?, ?, ?)");
                    $stmt->execute([$user['id'], $_SERVER['REMOTE_ADDR'], false]);
                }
                
                $errors[] = "Invalid email or password.";
            }
        } catch(PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
    
    // If there are errors, redirect back to login form with error messages
    $_SESSION['errors'] = $errors;
    $_SESSION['login_email'] = $email;
    header("Location: login.php");
    exit();
}
?>