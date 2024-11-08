<?php
session_start();

if (!isset($_SESSION['temp_registration'])) {
    header('Location: registration.php');
    exit;
}

$error = '';
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'roomrental';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted_otp = $_POST['otp'] ?? '';
    $registration = $_SESSION['temp_registration'];

    if (time() > $registration['expires']) {
        $error = 'OTP has expired. Please register again.';
        unset($_SESSION['temp_registration']);
    } elseif ($submitted_otp == $registration['otp']) {
        $conn = new mysqli($host, $user, $password, $dbname);

        if ($conn->connect_error) {
            die('Database connection failed: ' . $conn->connect_error);
        }

        $stmt = $conn->prepare('INSERT INTO login_details (username, password, email, verified) VALUES (?, ?, ?, 1)');
        $stmt->bind_param('sss', 
            $registration['username'],
            $registration['password'],
            $registration['email']
        );

        if ($stmt->execute()) {
            $_SESSION['user_logged_in'] = true;
            $_SESSION['username'] = $registration['username'];
            unset($_SESSION['temp_registration']);
            header('Location: ../frontend/index.php');
            exit;
        } else {
            $error = 'Registration failed. Please try again.';
        }

        $stmt->close();
        $conn->close();
    } else {
        $error = 'Invalid OTP. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Verify Your Email</h2>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <p class="text-center">
                            We've sent an OTP to your email address. Please enter it below to verify your account.
                        </p>

                        <form action="verify_otp.php" method="POST">
                            <div class="mb-3">
                                <label for="otp" class="form-label">Enter OTP:</label>
                                <input type="text" 
                                       id="otp" 
                                       name="otp" 
                                       class="form-control" 
                                       required 
                                       placeholder="Enter 6-digit OTP">
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Verify OTP</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 