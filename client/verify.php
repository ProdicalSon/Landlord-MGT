<?php
// verify.php
session_start();

require_once __DIR__ . '/models/UserModel.php';
$userModel = new UserModel();

$message = '';
$error = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $result = $userModel->verifyEmail($token);
    
    if ($result['success']) {
        $message = $result['message'];
        header('Location: login.php?verified=1');
        exit;
    } else {
        $error = $result['message'];
    }
} else {
    $error = 'No verification token provided';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - SmartHunt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .verification-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 40px;
            text-align: center;
        }

        .icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .icon.success {
            color: #27ae60;
        }

        .icon.error {
            color: #e74c3c;
        }

        h2 {
            color: #333;
            margin-bottom: 10px;
        }

        p {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #0077b6;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #005a8c;
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <?php if ($error): ?>
            <div class="icon error">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h2>Verification Failed</h2>
            <p><?php echo htmlspecialchars($error); ?></p>
            <a href="index.php" class="btn">Go to Homepage</a>
        <?php else: ?>
            <div class="icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2>Verifying...</h2>
            <p>Please wait while we verify your email.</p>
        <?php endif; ?>
    </div>
</body>
</html>