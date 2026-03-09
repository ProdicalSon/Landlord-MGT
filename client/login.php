<?php
// login.php
session_start();

// If already logged in, redirect to index
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/models/UserModel.php';
$userModel = new UserModel();

$error = '';
$success = '';

// Check for remember me cookie
if (isset($_COOKIE['remember_token'])) {
    // In a real app, you'd validate this token from a user_sessions table
    // For now, we'll skip remember me functionality
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = trim($_POST['username_or_email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if (empty($username_or_email) || empty($password)) {
        $error = 'Please enter both username/email and password';
    } else {
        $result = $userModel->login($username_or_email, $password, $remember);
        
        if ($result['success']) {
            // Set session
            $_SESSION['user_id'] = $result['user']['id'];
            $_SESSION['username'] = $result['user']['username'];
            $_SESSION['user_email'] = $result['user']['email'];
            $_SESSION['user_type'] = $result['user']['user_type'];
            $_SESSION['full_name'] = $userModel->getFullName($result['user']);
            
            // Set remember me cookie if requested
            if ($remember && $result['session_token']) {
                setcookie('remember_token', $result['session_token'], time() + (86400 * 30), '/', '', false, true);
            }
            
            // Redirect to previous page or index
            $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
            unset($_SESSION['redirect_after_login']);
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

// Check for logout message
if (isset($_GET['logged_out'])) {
    $success = 'You have been successfully logged out.';
}

// Check for verification success
if (isset($_GET['verified'])) {
    $success = 'Email verified successfully! You can now login.';
}

// Get redirect URL from query string
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
if ($redirect !== 'index.php') {
    $_SESSION['redirect_after_login'] = $redirect;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SmartHunt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 40px;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo a {
            text-decoration: none;
            font-size: 28px;
            font-weight: 700;
            color: #0077b6;
        }

        .logo i {
            margin-right: 10px;
            color: #0077b6;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-error {
            background-color: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }

        .alert-success {
            background-color: #efe;
            color: #3c3;
            border: 1px solid #cfc;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            color: #999;
            font-size: 16px;
        }

        input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: border-color 0.3s;
            font-family: inherit;
        }

        input:focus {
            outline: none;
            border-color: #0077b6;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-me input {
            width: auto;
            padding: 0;
        }

        .remember-me label {
            margin: 0;
            font-weight: normal;
            font-size: 14px;
        }

        .forgot-password {
            color: #0077b6;
            text-decoration: none;
            font-size: 14px;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: #0077b6;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-login:hover {
            background: #005a8c;
        }

        .register-link {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
            color: #666;
        }

        .register-link a {
            color: #0077b6;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .info-text {
            font-size: 13px;
            color: #666;
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            text-align: center;
        }

        .info-text i {
            color: #0077b6;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <a href="index.php">
                <i class="fas fa-home"></i>
                <span>SmartHunt</span>
            </a>
        </div>

        <h2>Welcome Back</h2>
        <p class="subtitle">Sign in to continue your property search</p>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username_or_email">Username or Email</label>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username_or_email" name="username_or_email" 
                           value="<?php echo htmlspecialchars($_POST['username_or_email'] ?? ''); ?>" 
                           placeholder="Enter username or email" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" 
                           placeholder="Enter your password" required>
                </div>
            </div>

            <div class="form-options">
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>
                <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>

            <div class="register-link">
                Don't have an account? <a href="register.php">Create Account</a>
            </div>
        </form>

        <div class="info-text">
            <i class="fas fa-info-circle"></i>
            You can login with either your username or email address
        </div>
    </div>
</body>
</html>