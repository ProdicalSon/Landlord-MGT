<?php
// Landlord/Frontend/login.php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['landlord_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/models/LandlordUserModel.php';
$userModel = new LandlordUserModel();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = trim($_POST['username_or_email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username_or_email) || empty($password)) {
        $error = 'Please enter both username/email and password';
    } else {
        $result = $userModel->login($username_or_email, $password);
        
        if ($result['success']) {
            // Set session variables
            $_SESSION['landlord_id'] = $result['user']['id'];
            $_SESSION['landlord_username'] = $result['user']['username'];
            $_SESSION['landlord_email'] = $result['user']['email'];
            $_SESSION['landlord_name'] = $userModel->getFullName($result['user']);
            $_SESSION['user_type'] = 'landlord';
            
            // Redirect to dashboard
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

// Check for registration success
if (isset($_GET['registered'])) {
    $success = 'Registration successful! Please check your email to verify your account.';
}

// Check for verification success
if (isset($_GET['verified'])) {
    $success = 'Email verified successfully! You can now login.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/icons/smartlogo.png">
    <title>Landlord Login - SmartHunt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
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
            max-width: 450px;
            padding: 40px;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo img {
            height: 80px;
            width: 80px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .logo h2 {
            font-size: 24px;
            color: var(--primary);
            font-weight: 600;
        }

        .logo p {
            color: var(--text);
            font-size: 14px;
            margin-top: 5px;
        }

        h2 {
            text-align: center;
            color: var(--dark);
            margin-bottom: 10px;
            font-size: 28px;
        }

        .subtitle {
            text-align: center;
            color: var(--text);
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
            color: var(--dark);
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
            border: 2px solid var(--gray);
            border-radius: 10px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
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
            color: var(--primary);
            text-decoration: none;
            font-size: 14px;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-login:hover {
            background: var(--primary-light);
        }

        .register-link {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
            color: var(--text);
        }

        .register-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: var(--text);
            text-decoration: none;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .back-link a:hover {
            color: var(--primary);
        }

        .info-text {
            font-size: 13px;
            color: #666;
            margin-top: 20px;
            padding: 15px;
            background: var(--light-gray);
            border-radius: 8px;
            text-align: center;
        }

        .info-text i {
            color: var(--primary);
            margin-right: 5px;
        }

        .landlord-badge {
            background: var(--primary);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-block;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="assets/icons/smartlogo.png" alt="SmartHunt Logo">
            <h2>SmartHunt</h2>
            <p>Landlord Portal</p>
        </div>

        <div class="landlord-badge">
            <i class="fas fa-building"></i> Landlord Login
        </div>

        <h2>Welcome Back</h2>
        <p class="subtitle">Sign in to manage your properties</p>

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

        <form method="POST" action="" id="loginForm">
            <div class="form-group">
                <label for="username_or_email">Username or Email</label>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username_or_email" name="username_or_email" 
                           value="<?php echo htmlspecialchars($_POST['username_or_email'] ?? ''); ?>" 
                           placeholder="Enter your username or email" required>
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
                Don't have a landlord account? <a href="registerlandlord.php">Register as Landlord</a>
            </div>

            <div class="back-link">
                <a href="./index.php"><i class="fas fa-arrow-left"></i> Back to Main Site</a>
            </div>
        </form>

        <div class="info-text">
            <i class="fas fa-info-circle"></i>
            This portal is for property owners and managers only.
        </div>
    </div>

    <script>
        document.getElementById('loginForm')?.addEventListener('submit', function(e) {
            const username = document.getElementById('username_or_email').value.trim();
            const password = document.getElementById('password').value;
            
            if (!username || !password) {
                e.preventDefault();
                alert('Please fill in all fields');
            }
        });
    </script>
</body>
</html>