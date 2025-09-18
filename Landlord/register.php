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
    <title>Register - SmartHunt</title>
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
        }

        body {
            background-color: #f5f7f9;
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-form {
            display: flex;
            width: 100%;
            max-width: 1000px;
            background: var(--light);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .login-image-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            padding: 40px;
        }

        .login-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .form-container {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-details, .register-details {
            width: 100%;
        }

        h1 {
            font-size: 28px;
            color: var(--primary);
            margin-bottom: 30px;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--dark);
        }

        input, select {
            padding: 14px 16px;
            margin-bottom: 20px;
            border: 1px solid var(--gray);
            border-radius: 8px;
            font-size: 16px;
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255, 56, 92, 0.2);
        }

        .btnsubmit {
            background: var(--primary);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 10px;
        }

        .btnsubmit:hover {
            background: var(--primary-light);
        }

        .form-a-container {
            text-align: center;
            margin-top: 20px;
        }

        .form-a-container a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .form-a-container a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #ffeeee;
            border-radius: 5px;
            border-left: 4px solid red;
        }

        @media (max-width: 900px) {
            .login-form {
                flex-direction: column;
                max-width: 500px;
            }
            
            .login-image-container {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="login-form">
        <!-- Left Image -->
        <div class="login-image-container">
            <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=500&q=80" alt="SmartHunt Property">
            <div class="image-overlay">
                <h2>Welcome to SmartHunt</h2>
                <p>Find your perfect student accommodation with our platform designed specifically for landlords and students.</p>
            </div>
        </div>
        
        <!-- Right Form -->
        <div class="form-container">
            <!-- Login Form -->
            <div class="login-details form-switch" id="login-form">
                <h1><i class="fas fa-sign-in-alt"></i> Login</h1>
                <form action="login_processing.php" method="post">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" placeholder="example@gmail.com" required name="email">

                    <div class="password-container">
                        <label for="password"><i class="fas fa-lock"></i> Password</label>
                        <input type="password" id="password" placeholder="Enter your password" required name="password">
                        <span class="toggle-password" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>

                    <div class="remember-forgot">
                        <div class="remember-me">
                            <input type="checkbox" id="remember">
                            <label for="remember" style="display: inline; font-size: 14px;">Remember me</label>
                        </div>
                        <a href="#" class="forgot-password">Forgot Password?</a>
                    </div>

                    <button type="submit" class="btnsubmit">Login</button>

                    <div class="form-a-container">
                        <p>Don't have an account? <a href="#" onclick="showRegisterForm()">Create Account!</a></p>
                    </div>
                </form>
            </div>

            <!-- Registration Form -->
            <div class="register-details form-switch" id="register-form" style="display: none;">
                <h1><i class="fas fa-user-plus"></i> Sign Up</h1>
                <form action="register_process.php" method="post">
                    <label for="username"><i class="fas fa-user"></i> Username</label>
                    <input type="text" id="username" placeholder="Your username" required name="username">

                    <label for="email-reg"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email-reg" placeholder="example@gmail.com" required name="email">

                    <div class="password-container">
                        <label for="password-reg"><i class="fas fa-lock"></i> Password</label>
                        <input type="password" id="password-reg" placeholder="Create a strong password" required name="password">
                        <span class="toggle-password" onclick="togglePassword('password-reg')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>

                    <div class="password-container">
                        <label for="confirm-password"><i class="fas fa-lock"></i> Confirm Password</label>
                        <input type="password" id="confirm-password" placeholder="Confirm your password" required name="confirm_password">
                        <span class="toggle-password" onclick="togglePassword('confirm-password')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>

                    <div class="remember-me">
                        <input type="checkbox" id="terms" required>
                        <label for="terms" style="display: inline; font-size: 14px;">I agree to the <a href="#" style="color: var(--primary);">Terms & Conditions</a></label>
                    </div>

                    <button type="submit" class="btnsubmit">Register</button>

                    <div class="form-a-container">
                        <p>Already have an account? <a href="#" onclick="showLoginForm()">Login here!</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showRegisterForm() {
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('register-form').style.display = 'block';
        }

        function showLoginForm() {
            document.getElementById('register-form').style.display = 'none';
            document.getElementById('login-form').style.display = 'block';
        }

        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = passwordInput.nextElementSibling.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>