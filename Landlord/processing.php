<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['passwordd'];
    $userType = isset($_POST['user_type']) ? $_POST['user_type'] : 'student';
    
    // Validate input
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Username is required.";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters long.";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    
    if (empty($password) || strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }
    
    // If no errors, process registration
    if (empty($errors)) {
        try {
            $pdo = getDBConnection();
            
            // Check if username or email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->rowCount() > 0) {
                $errors[] = "Username or email already exists.";
            } else {
                // Hash password
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                
                // Generate verification token
                $verificationToken = bin2hex(random_bytes(32));
                $verificationExpires = date('Y-m-d H:i:s', strtotime('+24 hours'));
                
                // Insert user into database
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, user_type, verification_token, verification_expires) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$username, $email, $passwordHash, $userType, $verificationToken, $verificationExpires]);
                
                $userId = $pdo->lastInsertId();
                
                // Create profile based on user type
                if ($userType === 'landlord') {
                    $stmt = $pdo->prepare("INSERT INTO landlords (user_id) VALUES (?)");
                    $stmt->execute([$userId]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO students (user_id) VALUES (?)");
                    $stmt->execute([$userId]);
                }
                
                // TODO: Send verification email
                
                // Redirect to success page
                header("Location: registration_success.php");
                exit();
            }
        } catch(PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
    
    // If there are errors, redirect back to form with error messages
    $_SESSION['errors'] = $errors;
    $_SESSION['form_data'] = $_POST;
    header("Location: register.php");
    exit();
}
?>