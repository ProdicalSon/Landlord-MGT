<?php
session_start();
if (!isset($_SESSION['registration_success'])) {
    header("Location: register.php");
    exit();
}

unset($_SESSION['registration_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful - SmartHunt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            background-color: #f5f7f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .success-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        .success-icon {
            font-size: 64px;
            color: #00A699;
            margin-bottom: 20px;
        }
        h1 {
            color: #222222;
            margin-bottom: 15px;
        }
        p {
            color: #484848;
            margin-bottom: 25px;
            line-height: 1.6;
        }
        .btn {
            background: #FF385C;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #e61e4d;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1>Registration Successful!</h1>
        <p>Your account has been created successfully. You can now login to your account and start using SmartHunt.</p>
        <a href="login.php" class="btn">Go to Login</a>
    </div>
</body>
</html>