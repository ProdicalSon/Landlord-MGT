<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once __DIR__ . '/includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $rememberMe = isset($_POST['remember']);
    
    $errors = [];

    // Validate input
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        try {
            $pdo = getDBConnection();

            // Fetch user
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                if (!$user['is_verified']) {
                    $errors[] = "Please verify your email before logging in.";
                } else {
                    // Successful login
                    $_SESSION['user_id']   = $user['id'];
                    $_SESSION['username']  = $user['username'];
                    $_SESSION['email']     = $user['email'];
                    $_SESSION['user_type'] = $user['user_type'];

                    // Remember me option
                    if ($rememberMe) {
                        $token   = bin2hex(random_bytes(32));
                        $expires = date('Y-m-d H:i:s', strtotime('+30 days'));

                        $stmt = $pdo->prepare("INSERT INTO sessions (user_id, session_token, ip_address, user_agent, expires_at) 
                                               VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([
                            $user['id'], 
                            $token, 
                            $_SERVER['REMOTE_ADDR'], 
                            $_SERVER['HTTP_USER_AGENT'], 
                            $expires
                        ]);

                        setcookie("remember_token", $token, time() + (30 * 24 * 60 * 60), "/");
                    }

                    // Redirect user
                    if ($user['user_type'] === 'landlord') {
                        header("Location: landlord_dashboard.php");
                    } else {
                        header("Location: student_dashboard.php");
                    }
                    exit();
                }
            } else {
                $errors[] = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }

    // If errors exist, save them to session and go back
    $_SESSION['errors'] = $errors;
    $_SESSION['login_email'] = $email;

    header("Location: login.php");
    exit();
}
