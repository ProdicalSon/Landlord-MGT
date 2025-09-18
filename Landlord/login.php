<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartHunt - Register & Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="assets/icons/smartlogo.png">
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
            position: relative;
            overflow: hidden;
        }

        .login-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 30px;
            color: white;
            text-align: center;
        }

        .image-overlay h2 {
            font-size: 28px;
            margin-bottom: 15px;
            font-weight: 600;
            color: var(--secondary);
        }

        .image-overlay p {
            font-size: 16px;
            max-width: 80%;
            
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
            font-weight: 600;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--dark);
            display: flex;
            align-items: center;
        }

        label i {
            margin-right: 10px;
            color: var(--primary);
        }

        input {
            padding: 14px 16px;
            margin-bottom: 20px;
            border: 1px solid var(--gray);
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }

        input:focus {
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
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btnsubmit:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 56, 92, 0.3);
        }

        .form-a-container {
            text-align: center;
            margin-top: 20px;
        }

        .form-a-container a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .form-a-container a:hover {
            color: var(--primary-light);
            text-decoration: underline;
        }

        .social-login {
            margin-top: 30px;
            text-align: center;
        }

        .social-login p {
            margin-bottom: 15px;
            color: var(--text);
            position: relative;
        }

        .social-login p::before,
        .social-login p::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 30%;
            height: 1px;
            background-color: var(--gray);
        }

        .social-login p::before {
            left: 0;
        }

        .social-login p::after {
            right: 0;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            transition: all 0.3s;
            cursor: pointer;
        }

        .social-icon.google {
            background: #DB4437;
        }

        .social-icon.facebook {
            background: #4267B2;
        }

        .social-icon.twitter {
            background: #1DA1F2;
        }

        .social-icon:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 45px;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text);
        }

        /* Responsive Design */
        @media (max-width: 900px) {
            .login-form {
                flex-direction: column;
                max-width: 500px;
            }
            
            .login-image-container {
                display: none;
            }
            
            .form-container {
                padding: 30px;
            }
        }

        @media (max-width: 480px) {
            .form-container {
                padding: 20px;
            }
            
            h1 {
                font-size: 24px;
            }
            
            input {
                padding: 12px 14px;
            }
            
            .btnsubmit {
                padding: 12px;
            }
        }

        /* Animation for form switching */
        .form-switch {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Additional styles for better UX */
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .remember-me {
            display: flex;
            align-items: center;
        }

        .remember-me input {
            margin: 0 8px 0 0;
            width: auto;
        }

        .forgot-password {
            color: var(--primary);
            text-decoration: none;
            font-size: 14px;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-form">
        <div class="login-image-container">
            <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8YXBhcnRtZW50fGVufDB8fDB8fHww&auto=format&fit=crop&w=500&q=80" alt="SmartHunt Property">
            <div class="image-overlay">
                <h2>Welcome to SmartHunt</h2>
                <p>Find your perfect student accommodation with our platform designed specifically for landlords and students.</p>
            </div>
        </div>
        
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
                    
                    <div class="social-login">
                        <p>Or login with</p>
                        <div class="social-icons">
                            <div class="social-icon google">
                                <i class="fab fa-google"></i>
                            </div>
                            <div class="social-icon facebook">
                                <i class="fab fa-facebook-f"></i>
                            </div>
                            <div class="social-icon twitter">
                                <i class="fab fa-twitter"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-a-container">
                        <p>Don't have an account? <a href="#" onclick="showRegisterForm()">Create Account!</a></p>
                    </div>
                </form>
            </div>

            <!-- Registration Form -->
            <div class="register-details form-switch" id="register-form" style="display: none;">
                <h1><i class="fas fa-user-plus"></i> Sign Up</h1>
                <form action="processing.php" method="post">
                    <label for="username"><i class="fas fa-user"></i> Username</label>
                    <input type="text" id="username" placeholder="Your username" required name="username">

                    <label for="email-reg"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email-reg" placeholder="example@gmail.com" required name="email">

                    <div class="password-container">
                        <label for="password-reg"><i class="fas fa-lock"></i> Password</label>
                        <input type="password" id="password-reg" placeholder="Create a strong password" required name="passwordd">
                        <span class="toggle-password" onclick="togglePassword('password-reg')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>

                    <div class="password-container">
                        <label for="confirm-password"><i class="fas fa-lock"></i> Confirm Password</label>
                        <input type="password" id="confirm-password" placeholder="Confirm your password" required>
                        <span class="toggle-password" onclick="togglePassword('confirm-password')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>

                    <div class="remember-me">
                        <input type="checkbox" id="terms" required>
                        <label for="terms" style="display: inline; font-size: 14px;">I agree to the <a href="#" style="color: var(--primary);">Terms & Conditions</a></label>
                    </div>

                    <button type="submit" class="btnsubmit">Register</button>
                    
                    <div class="social-login">
                        <p>Or sign up with</p>
                        <div class="social-icons">
                            <div class="social-icon google">
                                <i class="fab fa-google"></i>
                            </div>
                            <div class="social-icon facebook">
                                <i class="fab fa-facebook-f"></i>
                            </div>
                            <div class="social-icon twitter">
                                <i class="fab fa-twitter"></i>
                            </div>
                        </div>
                    </div>
                    
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

       
        document.addEventListener('DOMContentLoaded', function() {
            const registerForm = document.querySelector('.register-details form');
            
            if (registerForm) {
                registerForm.addEventListener('submit', function(e) {
                    const password = document.getElementById('password-reg').value;
                    const confirmPassword = document.getElementById('confirm-password').value;
                    
                    if (password !== confirmPassword) {
                        e.preventDefault();
                        alert('Passwords do not match!');
                    }
                    
                   
                });
            }
        });
    </script>
</body>
</html>