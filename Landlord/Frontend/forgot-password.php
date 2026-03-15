<?php
// Landlord/Frontend/forgot-password.php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['landlord_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/models/LandlordUserModel.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Please enter your email address';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        $userModel = new LandlordUserModel();
        $result = $userModel->generateResetToken($email);
        
        if ($result['success']) {
            // In a real application, you would send an email here
            // For now, we'll just show a success message
            $message = 'If your email exists in our system, you will receive a password reset link.';
            
            // You can uncomment this to see the token for testing
            // $message .= '<br><br><small>Debug - Reset link: <a href="reset-password.php?token=' . $result['token'] . '">reset-password.php?token=' . $result['token'] . '</a></small>';
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/icons/smartlogo.png">
    <title>Forgot Password - SmartHunt Landlord</title>
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

        .forgot-password-container {
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

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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

        .btn-reset {
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

        .btn-reset:hover {
            background: var(--primary-light);
        }

        .back-link {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
            color: var(--text);
        }

        .back-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .back-link a:hover {
            text-decoration: underline;
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
            text-align: center;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="forgot-password-container">
        <div class="logo">
            <img src="assets/icons/smartlogo.png" alt="SmartHunt Logo">
            <h2>SmartHunt</h2>
            <p>Landlord Portal</p>
        </div>

        <div class="landlord-badge">
            <i class="fas fa-building"></i> Password Reset
        </div>

        <h2>Forgot Password?</h2>
        <p class="subtitle">Enter your email to reset your password</p>

        <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" id="forgotPasswordForm">
            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                           placeholder="Enter your registered email" required>
                </div>
            </div>

            <button type="submit" class="btn-reset">
                <i class="fas fa-paper-plane"></i> Send Reset Link
            </button>

            <div class="back-link">
                <a href="login.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
            </div>
        </form>

        <div class="info-text">
            <i class="fas fa-info-circle"></i>
            You'll receive an email with instructions to reset your password within a few minutes.
        </div>
    </div>

    <script>
        document.getElementById('forgotPasswordForm')?.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (!email) {
                e.preventDefault();
                alert('Please enter your email address');
            } else if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address');
            }
        });
    </script>
</body>
</html>