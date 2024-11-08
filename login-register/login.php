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
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>

<body>
    <div class="pos">
        <section class="auth-section" id="login-section">
            <h2>Login</h2>

            <?php if (!empty($error)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <form class="auth-form" action="login.php" method="POST" id="login-form">
                <!-- Input for username -->
                <div class="form-group">
                    <label for="login-username">Username:</label>
                    <input type="text" id="login-username" name="login-username" required class="form-input" placeholder="Enter your username">
                </div>

                <!-- Input for password -->
                <div class="form-group">
                    <label for="login-password">Password:</label>
                    <input type="password" id="login-password" name="login-password" required class="form-input" placeholder="Enter your password">
                </div>

                <!-- Submit button -->
                <button type="submit" class="submit-button">Login</button>
            </form>

            <!-- Link to registration page -->
            <p class="auth-link">Don't have an account? <a href="registration.php">Register here</a>.</p>
        </section>
    </div>
</body>

</html>
