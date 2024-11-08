<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'roomrental';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register'])) {
        // Get all form data
        $name = $_POST['name'] ?? '';
        $username = $_POST['register-username'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $email = $_POST['register-email'] ?? '';
        $password = $_POST['register-password'] ?? '';
        $confirmPassword = $_POST['confirm-password'] ?? '';
        $age = $_POST['age'] ?? '';
        $drink = $_POST['drink'] ?? '';
        $smoke = $_POST['smoke'] ?? '';
        $married = $_POST['married'] ?? '';
        $hometown = $_POST['hometown'] ?? '';

        // Handle image upload
        $profile_image = null;
        if (isset($_FILES['profile-image']) && $_FILES['profile-image']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = $_FILES['profile-image']['type'];
            
            if (!in_array($file_type, $allowed_types)) {
                $errors[] = 'Invalid image format. Please upload JPG, PNG or GIF.';
            } else {
                $profile_image = file_get_contents($_FILES['profile-image']['tmp_name']);
            }
        } else {
            $errors[] = 'Profile image is required.';
        }

        // Validations
        $errors = [];

        // Username validation
        if (strlen($username) < 6) {
            $errors[] = 'Username must be at least 6 characters long.';
        }

        // Password validation
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        }

        // Email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        }

        // Phone validation
        if (!is_numeric($phone) || strlen($phone) < 10) {
            $errors[] = 'Invalid phone number.';
        }

        // Age validation
        if (!is_numeric($age) || $age < 18) {
            $errors[] = 'You must be at least 18 years old.';
        }

        if (empty($errors)) {
            try {
                // Check if username or email already exists
                $check_stmt = $conn->prepare('SELECT Username FROM login_details WHERE Username = ? OR Email = ?');
                if (!$check_stmt) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                
                $check_stmt->bind_param('ss', $username, $email);
                $check_stmt->execute();
                $check_stmt->store_result();

                if ($check_stmt->num_rows > 0) {
                    $error = 'Username or email already exists.';
                } else {
                    // Insert user data
                    $insert_sql = "INSERT INTO login_details (Name, Username, Phone_Number, Email, Password, Age, Drink, Smoke, Married, Home_Town, profile_image) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    
                    $insert_stmt = $conn->prepare($insert_sql);
                    if (!$insert_stmt) {
                        throw new Exception("Prepare failed: " . $conn->error);
                    }

                    // Phone number validation
                    $phone = preg_replace('/[^0-9]/', '', $phone); // Remove non-numeric characters
                    if (strlen($phone) < 10 || strlen($phone) > 15) {
                        throw new Exception("Invalid phone number length");
                    }

                    $age_int = (int)$age;

                    $insert_stmt->bind_param('sssssissssb', 
                        $name,
                        $username,
                        $phone,      // Changed to string
                        $email,
                        $password,
                        $age_int,
                        $drink,
                        $smoke,
                        $married,
                        $hometown,
                        $profile_image
                    );

                    if ($insert_stmt->execute()) {
                        $_SESSION['user_logged_in'] = true;
                        $_SESSION['username'] = $username;
                        
                        // Include the alert component
                        require_once 'alert.php';
                        
                        // Show the alert
                        showAlert(
                            'Registration Successful!', 
                            'Welcome to She Shares!', 
                            '../frontend/index.php'
                        );
                        exit;
                    } else {
                        throw new Exception("Execute failed: " . $insert_stmt->error);
                    }
                    $insert_stmt->close();
                }
                $check_stmt->close();
            } catch (Exception $e) {
                $error = 'Registration failed: ' . $e->getMessage();
                // For debugging:
                error_log("Registration error: " . $e->getMessage());
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - She Shares</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #ff80ab;
            --primary-dark: #ff4081;
            --secondary-color: #f8bbd0;
            --background-color: #FCE7EA;
        }

        body {
            background: linear-gradient(135deg, #ff80ab 0%, #f8bbd0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .registration-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            padding: 2rem;
            margin: 2rem auto;
        }

        .form-control, .form-select {
            border: 2px solid #f8bbd0;
            border-radius: 10px;
            padding: 12px;
            transition: all 0.3s ease;
            background-color: rgba(248, 187, 208, 0.1);
        }

        .form-control:focus, .form-select:focus {
            border-color: #ff4081;
            box-shadow: 0 0 0 0.25rem rgba(255, 64, 129, 0.25);
        }

        .profile-upload {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 2rem;
            position: relative;
            cursor: pointer;
            overflow: hidden;
            background: #f8bbd0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .profile-upload:hover {
            transform: scale(1.05);
        }

        .profile-upload img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-upload .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 64, 129, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .profile-upload:hover .overlay {
            opacity: 1;
        }

        .btn-register {
            background: linear-gradient(45deg, #ff4081, #ff80ab);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 64, 129, 0.4);
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }

        .step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #f8bbd0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            position: relative;
            z-index: 1;
        }

        .step.active {
            background: #ff4081;
        }

        .step-line {
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 2px;
            background: #f8bbd0;
            z-index: 0;
        }

        .form-section {
            display: none;
        }

        .form-section.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="registration-card">
                    <h2 class="text-center mb-4">Join She Shares</h2>
                    
                    <div class="progress-steps mb-4">
                        <div class="step-line"></div>
                        <div class="step active" data-step="1">1</div>
                        <div class="step" data-step="2">2</div>
                        <div class="step" data-step="3">3</div>
                    </div>

                    <form action="registration.php" method="POST" id="register-form" enctype="multipart/form-data">
                        <!-- Step 1: Basic Info -->
                        <div class="form-section active" data-step="1">
                            <div class="profile-upload" onclick="document.getElementById('profile-image').click()">
                                <div id="image-preview">
                                    <i class="fas fa-user-plus fa-2x text-white"></i>
                                </div>
                                <div class="overlay">
                                    <i class="fas fa-camera fa-lg text-white"></i>
                                </div>
                            </div>
                            <input type="file" id="profile-image" name="profile-image" hidden accept="image/*">
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" required>
                                        <label for="name">Full Name</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="register-username" name="register-username" placeholder="Username" required>
                                        <label for="register-username">Username</label>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-register next-step">Next <i class="fas fa-arrow-right ms-2"></i></button>
                            </div>
                        </div>

                        <!-- Step 2: Contact Info -->
                        <div class="form-section" data-step="2">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="Phone Number" required>
                                        <label for="phone">Phone Number</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" id="register-email" name="register-email" placeholder="Email" required>
                                        <label for="register-email">Email</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="password" class="form-control" id="register-password" name="register-password" placeholder="Password" required>
                                        <label for="register-password">Password</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="password" class="form-control" id="confirm-password" name="confirm-password" placeholder="Confirm Password" required>
                                        <label for="confirm-password">Confirm Password</label>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary prev-step"><i class="fas fa-arrow-left me-2"></i> Back</button>
                                <button type="button" class="btn btn-register next-step">Next <i class="fas fa-arrow-right ms-2"></i></button>
                            </div>
                        </div>

                        <!-- Step 3: Personal Details -->
                        <div class="form-section" data-step="3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="age" name="age" placeholder="Age" required>
                                        <label for="age">Age</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="hometown" name="hometown" placeholder="Hometown" required>
                                        <label for="hometown">Hometown</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <select class="form-select" name="drink" required>
                                            <option value="">Select...</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                        <label>Do you drink?</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <select class="form-select" name="smoke" required>
                                            <option value="">Select...</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                        <label>Do you smoke?</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <select class="form-select" name="married" required>
                                            <option value="">Select...</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                        <label>Marital Status</label>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary prev-step"><i class="fas fa-arrow-left me-2"></i> Back</button>
                                <button type="submit" name="register" class="btn btn-register">
                                    <i class="fas fa-user-plus me-2"></i> Complete Registration
                                </button>
                            </div>
                        </div>
                    </form>

                    <p class="text-center mt-4">
                        Already have an account? <a href="login.php" class="text-decoration-none">Login here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image preview functionality
        document.getElementById('profile-image').addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('image-preview').innerHTML = 
                        `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover;">`;
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        });

        // Multi-step form functionality
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('register-form');
            const sections = form.querySelectorAll('.form-section');
            const steps = document.querySelectorAll('.step');

            // Next button functionality
            document.querySelectorAll('.next-step').forEach(button => {
                button.addEventListener('click', function() {
                    const currentSection = this.closest('.form-section');
                    const nextSection = currentSection.nextElementSibling;
                    
                    // Validate current section
                    const inputs = currentSection.querySelectorAll('input, select');
                    let isValid = true;
                    inputs.forEach(input => {
                        if (input.required && !input.value) {
                            isValid = false;
                            input.classList.add('is-invalid');
                        } else {
                            input.classList.remove('is-invalid');
                        }
                    });

                    if (!isValid) {
                        return;
                    }

                    // Move to next section
                    currentSection.classList.remove('active');
                    nextSection.classList.add('active');
                    
                    // Update steps
                    const nextStep = parseInt(nextSection.dataset.step);
                    steps.forEach(step => {
                        if (parseInt(step.dataset.step) <= nextStep) {
                            step.classList.add('active');
                        }
                    });
                });
            });

            // Previous button functionality
            document.querySelectorAll('.prev-step').forEach(button => {
                button.addEventListener('click', function() {
                    const currentSection = this.closest('.form-section');
                    const prevSection = currentSection.previousElementSibling;
                    
                    currentSection.classList.remove('active');
                    prevSection.classList.add('active');
                    
                    // Update steps
                    const prevStep = parseInt(prevSection.dataset.step);
                    steps.forEach(step => {
                        if (parseInt(step.dataset.step) > prevStep) {
                            step.classList.remove('active');
                        }
                    });
                });
            });

            // Form validation on submit
            form.addEventListener('submit', function(e) {
                const inputs = form.querySelectorAll('input, select');
                let isValid = true;
                inputs.forEach(input => {
                    if (input.required && !input.value) {
                        isValid = false;
                        input.classList.add('is-invalid');
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>