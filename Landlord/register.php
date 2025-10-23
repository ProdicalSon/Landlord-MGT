<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <link rel="icon" href="assets/icons/logoX.png">
    <title>SmartHunt - Authentication</title>
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
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .login-form {
            display: flex;
            width: 100%;
            max-width: 1000px;
            background: var(--light);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease;
        }

        .login-form:hover {
            transform: translateY(-5px);
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
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            transition: transform 0.5s ease;
        }

        .login-image-container:hover img {
            transform: scale(1.03);
        }

        .image-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.7));
            color: white;
            padding: 30px;
            text-align: center;
        }

        .image-overlay h2 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .image-overlay p {
            font-size: 16px;
            opacity: 0.9;
        }

        .form-container {
            flex: 1;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .login-details, .register-details {
            width: 100%;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h1 {
            font-size: 32px;
            color: var(--primary);
            margin-bottom: 30px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
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
            gap: 8px;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        input, select {
            width: 100%;
            padding: 16px 20px;
            border: 1px solid var(--gray);
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s;
            background: var(--light);
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255, 56, 92, 0.2);
            transform: translateY(-2px);
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text);
            transition: color 0.3s;
        }

        .toggle-password:hover {
            color: var(--primary);
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 15px 0 25px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-me input {
            width: auto;
            margin: 0;
        }

        .remember-me label {
            margin: 0;
            font-size: 14px;
            color: var(--text);
        }

        .forgot-password {
            color: var(--primary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.3s;
        }

        .forgot-password:hover {
            color: var(--primary-light);
            text-decoration: underline;
        }

        .btnsubmit {
            background: var(--primary);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(255, 56, 92, 0.3);
        }

        .btnsubmit:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 56, 92, 0.4);
        }

        .btnsubmit:active {
            transform: translateY(0);
        }

        .form-a-container {
            text-align: center;
            margin-top: 25px;
        }

        .form-a-container p {
            color: var(--text);
        }

        .form-a-container a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .form-a-container a:hover {
            color: var(--primary-light);
            text-decoration: underline;
        }

        .error-message {
            color: var(--danger);
            margin-bottom: 15px;
            padding: 12px 15px;
            background-color: rgba(255, 90, 95, 0.1);
            border-radius: 8px;
            border-left: 4px solid var(--danger);
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.5s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .success-message {
            color: var(--success);
            margin-bottom: 15px;
            padding: 12px 15px;
            background-color: rgba(0, 166, 153, 0.1);
            border-radius: 8px;
            border-left: 4px solid var(--success);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .password-strength {
            margin-top: 5px;
            height: 4px;
            border-radius: 2px;
            background: var(--light-gray);
            overflow: hidden;
        }

        .strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s;
            border-radius: 2px;
        }

        .strength-weak {
            background: var(--danger);
            width: 33%;
        }

        .strength-medium {
            background: var(--warning);
            width: 66%;
        }

        .strength-strong {
            background: var(--success);
            width: 100%;
        }

        .password-requirements {
            font-size: 12px;
            color: var(--text);
            margin-top: 5px;
            display: none;
        }

        .requirement {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 3px;
        }

        .requirement.valid {
            color: var(--success);
        }

        .requirement.invalid {
            color: var(--danger);
        }

        .social-login {
            margin: 25px 0;
            text-align: center;
        }

        .social-login p {
            color: var(--text);
            margin-bottom: 15px;
            position: relative;
        }

        .social-login p::before,
        .social-login p::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 30%;
            height: 1px;
            background: var(--gray);
        }

        .social-login p::before {
            left: 0;
        }

        .social-login p::after {
            right: 0;
        }

        .social-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .social-btn {
            flex: 1;
            padding: 12px;
            border: 1px solid var(--gray);
            border-radius: 8px;
            background: var(--light);
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 500;
        }

        .social-btn.google {
            color: #DB4437;
            border-color: #DB4437;
        }

        .social-btn.facebook {
            color: #4267B2;
            border-color: #4267B2;
        }

        .social-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .social-btn.google:hover {
            background: rgba(219, 68, 55, 0.1);
        }

        .social-btn.facebook:hover {
            background: rgba(66, 103, 178, 0.1);
        }

        .user-type-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .user-type {
            flex: 1;
            text-align: center;
            padding: 15px;
            border: 2px solid var(--light-gray);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .user-type.selected {
            border-color: var(--primary);
            background: rgba(255, 56, 92, 0.05);
        }

        .user-type i {
            font-size: 24px;
            margin-bottom: 8px;
            color: var(--primary);
        }

        .user-type h3 {
            font-size: 16px;
            margin-bottom: 5px;
            color: var(--dark);
        }

        .user-type p {
            font-size: 12px;
            color: var(--text);
        }

        /* Loading spinner */
        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 900px) {
            .login-form {
                flex-direction: column;
                max-width: 500px;
            }
            
            .login-image-container {
                display: none;
            }
            
            .form-container {
                padding: 40px 30px;
            }
        }

        @media (max-width: 480px) {
            .form-container {
                padding: 30px 20px;
            }
            
            .remember-forgot {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .social-buttons {
                flex-direction: column;
            }
            
            .user-type-selector {
                flex-direction: column;
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
                
                <!-- Error/Success Messages -->
                <div class="error-message" id="login-error" style="display: none;">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="login-error-text"></span>
                </div>
                
                <form id="loginForm" method="post">
                    <div class="input-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" id="email" placeholder="example@gmail.com" required name="email">
                    </div>

                    <div class="input-group">
                        <label for="password"><i class="fas fa-lock"></i> Password</label>
                        <div class="password-container">
                            <input type="password" id="password" placeholder="Enter your password" required name="password">
                            <span class="toggle-password" onclick="togglePassword('password')">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>

                    <div class="remember-forgot">
                        <div class="remember-me">
                            <input type="checkbox" id="remember">
                            <label for="remember">Remember me</label>
                        </div>
                        <a href="#" class="forgot-password">Forgot Password?</a>
                    </div>

                    <button type="submit" class="btnsubmit" id="login-btn">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>

                    <!-- Social Login -->
                    <div class="social-login">
                        <p>Or continue with</p>
                        <div class="social-buttons">
                            <button type="button" class="social-btn google" id="google-login-btn">
                                <i class="fab fa-google"></i> Google
                            </button>
                            <button type="button" class="social-btn facebook">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </button>
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
                
                <!-- Error/Success Messages -->
                <div class="error-message" id="register-error" style="display: none;">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="register-error-text"></span>
                </div>
                
                <form id="registerForm" method="post">
                    <!-- User Type Selection -->
                    <div class="user-type-selector">
                        <div class="user-type selected" data-type="landlord" onclick="selectUserType(this)">
                            <i class="fas fa-user-tie"></i>
                            <h3>Landlord</h3>
                            <p>List and manage properties</p>
                        </div>
                        <div class="user-type" data-type="tenant" onclick="selectUserType(this)">
                            <i class="fas fa-user"></i>
                            <h3>Tenant</h3>
                            <p>Find and book properties</p>
                        </div>
                    </div>
                    <input type="hidden" id="user-type" name="user_type" value="landlord">

                    <div class="input-group">
                        <label for="username"><i class="fas fa-user"></i> Username</label>
                        <input type="text" id="username" placeholder="Your username" required name="username">
                    </div>

                    <div class="input-group">
                        <label for="email-reg"><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" id="email-reg" placeholder="example@gmail.com" required name="email">
                    </div>

                    <div class="input-group">
                        <label for="password-reg"><i class="fas fa-lock"></i> Password</label>
                        <div class="password-container">
                            <input type="password" id="password-reg" placeholder="Create a strong password" required name="password" onkeyup="checkPasswordStrength(this.value)">
                            <span class="toggle-password" onclick="togglePassword('password-reg')">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <div class="password-strength">
                            <div class="strength-bar" id="password-strength-bar"></div>
                        </div>
                        <div class="password-requirements" id="password-requirements">
                            <div class="requirement" id="req-length">At least 8 characters</div>
                            <div class="requirement" id="req-uppercase">One uppercase letter</div>
                            <div class="requirement" id="req-lowercase">One lowercase letter</div>
                            <div class="requirement" id="req-number">One number</div>
                            <div class="requirement" id="req-special">One special character</div>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="confirm-password"><i class="fas fa-lock"></i> Confirm Password</label>
                        <div class="password-container">
                            <input type="password" id="confirm-password" placeholder="Confirm your password" required name="confirm_password" onkeyup="checkPasswordMatch()">
                            <span class="toggle-password" onclick="togglePassword('confirm-password')">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <div id="password-match" style="font-size: 12px; margin-top: 5px;"></div>
                    </div>

                    <div class="remember-me">
                        <input type="checkbox" id="terms" required>
                        <label for="terms">I agree to the <a href="#" style="color: var(--primary);">Terms & Conditions</a></label>
                    </div>

                    <button type="submit" class="btnsubmit" id="register-btn">
                        <i class="fas fa-user-plus"></i> Register
                    </button>

                    <!-- Social Registration -->
                    <div class="social-login">
                        <p>Or continue with</p>
                        <div class="social-buttons">
                            <button type="button" class="social-btn google" id="google-register-btn">
                                <i class="fab fa-google"></i> Google
                            </button>
                            <button type="button" class="social-btn facebook">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </button>
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
        // Form switching
        function showRegisterForm() {
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('register-form').style.display = 'block';
            resetForms();
        }

        function showLoginForm() {
            document.getElementById('register-form').style.display = 'none';
            document.getElementById('login-form').style.display = 'block';
            resetForms();
        }

        function resetForms() {
            document.getElementById('login-error').style.display = 'none';
            document.getElementById('register-error').style.display = 'none';
            document.getElementById('loginForm').reset();
            document.getElementById('registerForm').reset();
            document.getElementById('password-strength-bar').className = 'strength-bar';
            document.getElementById('password-match').innerHTML = '';
            document.getElementById('password-requirements').style.display = 'none';
        }

        // Password visibility toggle
        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = passwordInput.parentNode.querySelector('.toggle-password i');
            
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

        // User type selection
        function selectUserType(element) {
            document.querySelectorAll('.user-type').forEach(el => {
                el.classList.remove('selected');
            });
            element.classList.add('selected');
            document.getElementById('user-type').value = element.getAttribute('data-type');
        }

        // Password strength checker
        function checkPasswordStrength(password) {
            const strengthBar = document.getElementById('password-strength-bar');
            const requirements = document.getElementById('password-requirements');
            
            // Reset
            strengthBar.className = 'strength-bar';
            requirements.style.display = 'none';
            
            if (password.length === 0) return;
            
            requirements.style.display = 'block';
            
            let strength = 0;
            
            // Check length
            if (password.length >= 8) {
                strength += 1;
                document.getElementById('req-length').classList.add('valid');
                document.getElementById('req-length').classList.remove('invalid');
            } else {
                document.getElementById('req-length').classList.add('invalid');
                document.getElementById('req-length').classList.remove('valid');
            }
            
            // Check uppercase
            if (/[A-Z]/.test(password)) {
                strength += 1;
                document.getElementById('req-uppercase').classList.add('valid');
                document.getElementById('req-uppercase').classList.remove('invalid');
            } else {
                document.getElementById('req-uppercase').classList.add('invalid');
                document.getElementById('req-uppercase').classList.remove('valid');
            }
            
            // Check lowercase
            if (/[a-z]/.test(password)) {
                strength += 1;
                document.getElementById('req-lowercase').classList.add('valid');
                document.getElementById('req-lowercase').classList.remove('invalid');
            } else {
                document.getElementById('req-lowercase').classList.add('invalid');
                document.getElementById('req-lowercase').classList.remove('valid');
            }
            
            // Check numbers
            if (/[0-9]/.test(password)) {
                strength += 1;
                document.getElementById('req-number').classList.add('valid');
                document.getElementById('req-number').classList.remove('invalid');
            } else {
                document.getElementById('req-number').classList.add('invalid');
                document.getElementById('req-number').classList.remove('valid');
            }
            
            // Check special characters
            if (/[^A-Za-z0-9]/.test(password)) {
                strength += 1;
                document.getElementById('req-special').classList.add('valid');
                document.getElementById('req-special').classList.remove('invalid');
            } else {
                document.getElementById('req-special').classList.add('invalid');
                document.getElementById('req-special').classList.remove('valid');
            }
            
            // Update strength bar
            if (strength <= 2) {
                strengthBar.className = 'strength-bar strength-weak';
            } else if (strength <= 4) {
                strengthBar.className = 'strength-bar strength-medium';
            } else {
                strengthBar.className = 'strength-bar strength-strong';
            }
        }

        // Password match checker
        function checkPasswordMatch() {
            const password = document.getElementById('password-reg').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const matchElement = document.getElementById('password-match');
            
            if (confirmPassword.length === 0) {
                matchElement.innerHTML = '';
                return;
            }
            
            if (password === confirmPassword) {
                matchElement.innerHTML = '<i class="fas fa-check" style="color: var(--success);"></i> Passwords match';
                matchElement.style.color = 'var(--success)';
            } else {
                matchElement.innerHTML = '<i class="fas fa-times" style="color: var(--danger);"></i> Passwords do not match';
                matchElement.style.color = 'var(--danger)';
            }
        }

        // Form submission handlers
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const loginBtn = document.getElementById('login-btn');
            const originalText = loginBtn.innerHTML;
            
            // Show loading state
            loginBtn.innerHTML = '<div class="spinner"></div> Logging in...';
            loginBtn.disabled = true;
            
            // Simulate login process
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            // Simple validation
            if (email && password) {
                // In a real app, you would send this to your backend
                setTimeout(() => {
                    console.log('Login attempt:', { email, password });
                    // Show success and redirect
                    showSuccess('login', 'Login successful! Redirecting...');
                    
                    // Redirect to dashboard after 2 seconds
                    setTimeout(() => {
                        window.location.href = 'dashboard.html';
                    }, 2000);
                }, 1500);
            } else {
                setTimeout(() => {
                    showError('login', 'Please fill in all fields');
                    loginBtn.innerHTML = originalText;
                    loginBtn.disabled = false;
                }, 500);
            }
        });

        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const registerBtn = document.getElementById('register-btn');
            const originalText = registerBtn.innerHTML;
            
            // Show loading state
            registerBtn.innerHTML = '<div class="spinner"></div> Creating account...';
            registerBtn.disabled = true;
            
            // Simulate registration process
            const username = document.getElementById('username').value;
            const email = document.getElementById('email-reg').value;
            const password = document.getElementById('password-reg').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const userType = document.getElementById('user-type').value;
            
            // Validation
            if (!username || !email || !password || !confirmPassword) {
                setTimeout(() => {
                    showError('register', 'Please fill in all fields');
                    registerBtn.innerHTML = originalText;
                    registerBtn.disabled = false;
                }, 500);
                return;
            }
            
            if (password !== confirmPassword) {
                setTimeout(() => {
                    showError('register', 'Passwords do not match');
                    registerBtn.innerHTML = originalText;
                    registerBtn.disabled = false;
                }, 500);
                return;
            }
            
            if (password.length < 8) {
                setTimeout(() => {
                    showError('register', 'Password must be at least 8 characters long');
                    registerBtn.innerHTML = originalText;
                    registerBtn.disabled = false;
                }, 500);
                return;
            }
            
            // In a real app, you would send this to your backend
            setTimeout(() => {
                console.log('Registration attempt:', { username, email, password, userType });
                // Show success message
                showSuccess('register', 'Registration successful! Redirecting to dashboard...');
                
                // Redirect to dashboard after 2 seconds
                setTimeout(() => {
                    window.location.href = 'dashboard.html';
                }, 2000);
            }, 1500);
        });

        // Google Authentication
        function initGoogleAuth() {
            // In a real implementation, you would initialize Google Sign-In API
            console.log('Google Sign-In initialized');
        }

        // Google Login Handler
        document.getElementById('google-login-btn').addEventListener('click', function() {
            const btn = this;
            const originalText = btn.innerHTML;
            
            // Show loading state
            btn.innerHTML = '<div class="spinner"></div> Connecting...';
            btn.disabled = true;
            
            // Simulate Google OAuth flow
            setTimeout(() => {
                console.log('Google login initiated');
                
                // In a real implementation, this would redirect to Google OAuth
                // For demo purposes, we'll simulate a successful login
                simulateGoogleAuth('login');
                
                // Reset button after a delay
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }, 2000);
            }, 1000);
        });

        // Google Register Handler
        document.getElementById('google-register-btn').addEventListener('click', function() {
            const btn = this;
            const originalText = btn.innerHTML;
            
            // Show loading state
            btn.innerHTML = '<div class="spinner"></div> Connecting...';
            btn.disabled = true;
            
            // Simulate Google OAuth flow
            setTimeout(() => {
                console.log('Google registration initiated');
                
                // In a real implementation, this would redirect to Google OAuth
                // For demo purposes, we'll simulate a successful registration
                simulateGoogleAuth('register');
                
                // Reset button after a delay
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }, 2000);
            }, 1000);
        });

        // Simulate Google Authentication
        function simulateGoogleAuth(action) {
            // Show success message
            showSuccess(action, `Google ${action} successful! Redirecting...`);
            
            // Simulate user data from Google
            const userData = {
                name: 'Google User',
                email: 'user@gmail.com',
                picture: 'https://placehold.co/100x100/4285F4/FFFFFF?text=G'
            };
            
            console.log(`Google ${action} data:`, userData);
            
            // In a real app, you would send this data to your backend
            // and handle the authentication response
            
            // Redirect to dashboard after 2 seconds
            setTimeout(() => {
                window.location.href = 'dashboard.html';
            }, 2000);
        }

        // Facebook Login Handler (placeholder)
        document.querySelectorAll('.social-btn.facebook').forEach(btn => {
            btn.addEventListener('click', function() {
                alert('Facebook authentication would be implemented similarly to Google');
            });
        });

        function showError(formType, message) {
            const errorElement = document.getElementById(`${formType}-error`);
            const errorText = document.getElementById(`${formType}-error-text`);
            
            errorText.textContent = message;
            errorElement.style.display = 'flex';
            
            // Hide error after 5 seconds
            setTimeout(() => {
                errorElement.style.display = 'none';
            }, 5000);
        }

        function showSuccess(formType, message) {
            // Create a temporary success message
            const successElement = document.createElement('div');
            successElement.className = 'success-message';
            successElement.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
            
            const form = document.getElementById(`${formType}-form`);
            form.insertBefore(successElement, form.querySelector('form'));
            
            // Remove success message after 3 seconds
            setTimeout(() => {
                successElement.remove();
            }, 3000);
        }

        // Initialize Google Auth when page loads
        window.addEventListener('load', initGoogleAuth);
    </script>
</body>
</html>