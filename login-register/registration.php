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
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow" style=" border-radius: 10px;
    background-color: #F8BBD0;
    border: 1px solid #ff80ab; 
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);"
     >
                    <div class="card-body">
                        <h2 class="text-center mb-4">Register</h2>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <div class="image-upload-container">
                            <label for="profile-image">
                                <div id="image-preview" class="image-upload-placeholder">
                                    <i class="fas fa-camera"></i>
                                </div>
                            </label>
                            <input type="file" 
                                   id="profile-image" 
                                   name="profile-image" 
                                   accept="image/*"
                                   required
                                   onchange="previewImage(this)">
                        </div>
                        <p class="image-upload-text">Click to upload profile picture</p>

                        <form action="registration.php" method="POST" id="register-form" enctype="multipart/form-data" >
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label" style="margin: 10px;">Full Name:</label>
                                    <input type="text" id="name" name="name" required 
                                           style="padding: 12px;
                                                  margin-top: 10px;
                                                  margin-bottom: 10px;
                                                  background-color: #f096b7;
                                                  border-radius: 15px;
                                                  border: none;
                                                  width: 95%;
                                                  transition: background-color 0.3s ease;
                                                  text-align: center;">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="register-username" class="form-label" style="margin: 10px;">Username:</label>
                                    <input type="text" id="register-username" name="register-username" required 
                                           style="padding: 12px;
                                                  margin-top: 10px;
                                                  margin-bottom: 10px;
                                                  background-color: #f096b7;
                                                  border-radius: 15px;
                                                  border: none;
                                                  width: 95%;
                                                  transition: background-color 0.3s ease;
                                                  text-align: center;">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number:</label>
                                    <input type="tel" id="phone" name="phone" class="form-control" 
                                           pattern="[0-9]{10,15}" title="Please enter a valid phone number (10-15 digits)" 
                                           required placeholder="Enter your phone number"
                                           style="background-color: #f096b7;">
                                    <small class="text-muted">Enter numbers only (10-15 digits)</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="register-email" class="form-label">Email:</label>
                                    <input type="email" id="register-email" name="register-email" class="form-control" required 
                                           style="background-color: #f096b7;">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="register-password" class="form-label">Password:</label>
                                    <input type="password" id="register-password" name="register-password" class="form-control" required 
                                           style="background-color: #f096b7;">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="confirm-password" class="form-label">Confirm Password:</label>
                                    <input type="password" id="confirm-password" name="confirm-password" class="form-control" required 
                                           style="background-color: #f096b7;">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="age" class="form-label">Age:</label>
                                    <input type="number" id="age" name="age" class="form-control" required 
                                           style="background-color: #f096b7;">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="hometown" class="form-label">Home Town:</label>
                                    <input type="text" id="hometown" name="hometown" class="form-control" required 
                                           style="background-color: #f096b7;">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label" style="margin: 10px;">Drink:</label>
                                    <select name="drink" required 
                                            style="padding: 12px;
                                                   margin-top: 10px;
                                                   margin-bottom: 10px;
                                                   background-color: #f096b7;
                                                   border-radius: 15px;
                                                   border: none;
                                                   width: 95%;
                                                   transition: background-color 0.3s ease;
                                                   text-align: center;">
                                        <option value="single">Single</option>
                                        <option value="married">Married</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Smoke:</label>
                                    <select name="smoke" class="form-select" required 
                                            style="background-color: #f096b7;">
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Married:</label>
                                    <select name="married" class="form-select" required 
                                            style="background-color: #f096b7;">
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" name="register" 
                                    style="background-color: #ff4081;
                                           color: white;
                                           padding: 10px 20px;
                                           border-radius: 10px;
                                           border: none;
                                           margin-top: 10px;
                                           cursor: pointer;
                                           transition: background-color 0.3s ease;
                                           text-align: center;
                                           width: 100%;">
                                Register
                            </button>
                        </form>

                        <p class="text-center mt-3">
                            Already have an account? <a href="login.php">Login here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                var preview = document.getElementById('image-preview');
                preview.innerHTML = `<img src="${e.target.result}" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid #ff4081;">`;
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
</body>
</html>