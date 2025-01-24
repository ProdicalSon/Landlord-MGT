<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <link rel="icon" href="assets/icons/logoX.png">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-form">
        <div class="login-image-container">
            <img src="assets/icons/house1.jpeg" alt="Login Image">
        </div>
        

        <div class="form-container">
            <!-- Login Form -->
            <div class="login-details" id="login-form">
                <h1>Login</h1>
                <form action="">
                    <label for="email">Email</label>
                    <input type="email" id="email" placeholder="example@gmail.com" required name="email">

                    <label for="password">Password</label>
                    <input type="password" id="password" placeholder="........." required name="password">

                    <button type="submit" class="btnsubmit">Submit</button>
                    <div class="form-a-container">
                        <a href="#" onclick="showRegisterForm()">Create Account!!</a>
                    </div>
                   
                </form>
            </div>

            <!-- Registration Form -->
            <div class="register-details" id="register-form" style="display: none;">
                <h1>Sign Up</h1>
                <form action="processing.php" method="post">
                    <label for="username">Username</label>
                    <input type="text" id="username" placeholder="Your username" required name="username">

                    <label for="email-reg">Email</label>
                    <input type="email" id="email-reg" placeholder="example@gmail.com" required name="email">

                    <label for="password-reg">Password</label>
                    <input type="password" id="password-reg" placeholder=".........." required name="passwordd">

                    <button type="submit" class="btnsubmit">Register</button>
                    <div class="form-a-container">
                        <a href="#" onclick="showLoginForm()">Already have an account? Login here!</a>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
