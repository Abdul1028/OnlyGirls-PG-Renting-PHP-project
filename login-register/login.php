<?php

// Start session
session_start();

// Database connection parameters
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'roomrental';

// Create a connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Initialize error message
$error = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $username = $_POST['login-username'] ?? '';
    $password = $_POST['login-password'] ?? '';

    // Prepare and execute the query
    $stmt = $conn->prepare('SELECT password FROM login_details WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();

    // Check if the user exists
    if ($stmt->num_rows > 0) {
        // Bind the results
        $stmt->bind_result($storedPassword);
        $stmt->fetch();

        // Compare the input password with the stored password
        if ($password === $storedPassword) {
            // Password is correct, start the session
            $_SESSION['user_logged_in'] = true;
            $_SESSION['username'] = $username;

            // Include the alert component
            require_once 'alert.php';
            
            // Show the alert
            showAlert(
                'Login Successful!', 
                'Welcome back to She Shares!', 
                '../frontend/index.php'
            );
            exit;
        } else {
            // Incorrect password
            $error = 'Invalid username or password.';
        }
    } else {
        // User not found
        $error = 'Invalid username or password.';
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - She Shares</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #ff80ab;
            --primary-dark: #ff4081;
            --secondary-color: #f8bbd0;
        }

        body {
            background: linear-gradient(135deg, #ff80ab 0%, #f8bbd0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Poppins', sans-serif;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            padding: 2rem;
            max-width: 400px;
            width: 90%;
            margin: 0 auto;
            transform: translateY(-5%);
        }

        .brand-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #ff4081, #ff80ab);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            box-shadow: 0 5px 15px rgba(255, 64, 129, 0.3);
        }

        .form-floating > .form-control {
            border: 2px solid #f8bbd0;
            border-radius: 10px;
            background-color: rgba(248, 187, 208, 0.1);
        }

        .form-floating > .form-control:focus {
            border-color: #ff4081;
            box-shadow: 0 0 0 0.25rem rgba(255, 64, 129, 0.25);
        }

        .btn-login {
            background: linear-gradient(45deg, #ff4081, #ff80ab);
            border: none;
            border-radius: 10px;
            padding: 12px;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 64, 129, 0.4);
        }

        .social-login {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .social-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #f8bbd0;
            color: #ff4081;
            transition: all 0.3s ease;
        }

        .social-btn:hover {
            background: #ff4081;
            color: white;
            transform: translateY(-2px);
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #f8bbd0;
        }

        .divider span {
            padding: 0 1rem;
            color: #666;
            font-size: 0.9rem;
        }

        .error-message {
            background: rgba(255, 82, 82, 0.1);
            border: 1px solid #ff5252;
            color: #ff5252;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .register-link {
            color: #ff4081;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .register-link:hover {
            color: #ff80ab;
            text-decoration: underline;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card {
            animation: fadeIn 0.5s ease;
        }

        .fa-eye, .fa-eye-slash {
            color: #ff80ab;
            transition: all 0.3s ease;
        }

        .fa-eye:hover, .fa-eye-slash:hover {
            color: #ff4081;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-card">
                    <div class="brand-logo">
                        <i class="fas fa-user fa-2x text-white"></i>
                    </div>
                    
                    <h3 class="text-center mb-4">Welcome Back!</h3>

                    <?php if (!empty($error)): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="POST" class="needs-validation" novalidate>
                        <div class="form-floating mb-3">
                            <input type="text" 
                                   class="form-control" 
                                   id="login-username" 
                                   name="login-username" 
                                   placeholder="Username"
                                   required>
                            <label for="login-username">Username</label>
                        </div>

                        <div class="form-floating mb-4 position-relative">
                            <input type="password" 
                                   class="form-control" 
                                   id="login-password" 
                                   name="login-password" 
                                   placeholder="Password"
                                   required>
                            <label for="login-password">Password</label>
                            <span class="position-absolute top-50 end-0 translate-middle-y me-3" 
                                  style="cursor: pointer;"
                                  onclick="togglePassword()">
                                <i class="fas fa-eye" id="togglePassword"></i>
                            </span>
                        </div>

                        <div class="d-flex justify-content-end mb-4">
                            <a href="#" class="text-decoration-none register-link">Forgot password?</a>
                        </div>

                        <button type="submit" class="btn btn-login w-100">
                            <i class="fas fa-sign-in-alt me-2"></i> Login
                        </button>
                    </form>

                    <div class="divider">
                        <span>or continue with</span>
                    </div>

                    <div class="social-login">
                        <a href="#" class="social-btn">
                            <i class="fab fa-google"></i>
                        </a>
                        <a href="#" class="social-btn">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-btn">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>

                    <p class="text-center mt-4">
                        Don't have an account? 
                        <a href="registration.php" class="register-link">Register here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function () {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()

        function togglePassword() {
            const passwordInput = document.getElementById('login-password');
            const toggleIcon = document.getElementById('togglePassword');
            
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
