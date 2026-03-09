<?php
// Landlord/Frontend/verify.php
session_start();

require_once __DIR__ . '/models/LandlordUserModel.php';
$userModel = new LandlordUserModel();

$message = '';
$error = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $result = $userModel->verifyEmail($token);
    
    if ($result['success']) {
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
<html>
<head>
    <title>Email Verification - SmartHunt</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 10px;
            padding: 40px;
            max-width: 400px;
            text-align: center;
        }
        .error {
            color: var(--danger);
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($error): ?>
            <h2>Verification Failed</h2>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <a href="login.php">Go to Login</a>
        <?php endif; ?>
    </div>
</body>
</html>