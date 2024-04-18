<?php

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'roomrental';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$feedback = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = $_POST['register-name'] ?? '';
    $email = $_POST['register-email'] ?? '';
    $password = $_POST['register-password'] ?? '';
    $confirmPassword = $_POST['register-confirm-password'] ?? '';
    $age = $_POST['register-age'] ?? '';
    $maritalStatus = $_POST['register-marital-status'] ?? '';
    $smoking = $_POST['register-smoking'] ?? '';
    $drinking = $_POST['register-drinking'] ?? '';
    $Town = $_POST['register-town'] ?? '';
    $Username = $_POST['register-username'] ?? '';
    $Phone_Number = $_POST['register-number'] ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword) || empty($age) || empty($maritalStatus) || empty($smoking) || empty($drinking) ||   empty($Town) || empty($Username)) {
        $error = 'Please fill in all fields.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {

        $stmt = $conn->prepare('INSERT INTO login_details (Name, Username, Phone_Number, Email, Password, Age, Drink, Smoke, Married, Home_Town) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        if ($stmt) {
            // Bind parameters and execute the statement
            $stmt->bind_param('ssssssssss', $name, $Username, $Phone_Number, $email, $password, $age, $maritalStatus, $smoking, $drinking, $Town);

            if ($stmt->execute()) {
                session_start();
                $_SESSION['login'] = True;
                header("Location: \\frontend\\index.php");
            } else {
                $error = 'Error inserting data: ' . $stmt->error;
            }
            // Close the statement
            $stmt->close();
        } else {
            $error = 'Error preparing statement: ' . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>She Shares Vacation Rentals</title>
    <link rel="stylesheet" href="login.css">
    <style>
        /* Add your CSS here */

        /* Styling for the steps */
        .step {
            display: none;
            /* Hide steps by default */
        }

        /* Show the first step by default */
        .step.active {
            display: block;
        }

        /* Add your form and button styling here */
    </style>
</head>

<body>
    <div class="pos1">
        <section class="auth-section" id="registration-section">

            <?php if (!empty($feedback)) : ?>
                <p class="success-message"><?php echo htmlspecialchars($feedback); ?></p>
            <?php endif; ?>
            <?php if (!empty($error)) : ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <!-- Registration form with multiple steps -->
            <form class="auth-form" id="registration-form" action="" method="POST">


                <!-- Step 1: Initial Registration -->
                <div id="step-1" class="step active">
                    <h2>Register</h2>
                    <!-- Input for name -->
                    <div class="form-group">
                        <label for="register-name">Name</label>
                        <input type="text" id="register-name" name="register-name" required class="form-input" placeholder="Enter your name">
                    </div>
                    <div class="form-group">
                        <label for="register-username">Username</label>
                        <input type="text" id="register-username" name="register-username" required class="form-input" placeholder="Enter your Username">
                    </div>
                    <!-- Input for email -->
                    <div class="form-group">
                        <label for="register-email">Email</label>
                        <input type="email" id="register-email" name="register-email" required class="form-input" placeholder="Enter your email">
                    </div>

                    <!-- Input for password -->
                    <div class="form-group">
                        <label for="register-password">Password</label>
                        <input type="password" id="register-password" name="register-password" required class="form-input" placeholder="Create a password">
                    </div>

                    <!-- Input for confirming password -->
                    <div class="form-group">
                        <label for="register-confirm-password">Confirm Password</label>
                        <input type="password" id="register-confirm-password" name="register-confirm-password" required class="form-input" placeholder="Confirm your password">
                    </div>

                    <!-- Next button -->
                    <button type="button" id="next-button" class="submit-button">Next</button>
                </div>

                <!-- Step 2: Additional Information -->
                <div id="step-2" class="step">
                    <h2>Additional Information</h2>

                    <!-- Input for age -->
                    <div class="form-group">
                        <label for="register-age">Age</label>
                        <input type="number" id="register-age" name="register-age" required class="form-input" placeholder="Enter your age">
                    </div>
                    <div class="form-group">
                        <label for="register-number">Phone Number</label>
                        <input type="number" id="register-number" name="register-number" required class="form-input" placeholder="Enter your Number">
                    </div>
                    <div class="form-group">
                        <label for="register-town">Home Town</label>
                        <input type="text" id="register-town" name="register-town" required class="form-input" placeholder="Enter your Home Town">
                    </div>
                    <!-- Input for marital status -->
                    <div class="form-group">
                        <label for="register-marital-status">Marital Status</label>
                        <select id="register-marital-status" name="register-marital-status" required class="form-select">
                            <option value="">Select marital status</option>
                            <option value="single">Single</option>
                            <option value="married">Married</option>
                            <option value="divorced">Divorced</option>
                            <!-- Add other options as needed -->
                        </select>
                    </div>

                    <!-- Input for smoking -->
                    <div class="form-group">
                        <label for="register-smoking">Do you smoke?</label>
                        <select id="register-smoking" name="register-smoking" required class="form-select">
                            <option value="">Select an option</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>

                    <!-- Input for drinking -->
                    <div class="form-group">
                        <label for="register-drinking">Do you drink?</label>
                        <select id="register-drinking" name="register-drinking" required class="form-select">
                            <option value="">Select an option</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>

                    <!-- Submit button -->
                    <button type="submit" id="submit-button" class="submit-button">Submit</button>
                </div>

            </form>

            <!-- Link to login page -->
            <p class="auth-link">Already have an account? <a href="login.php">Login here</a>.</p>
        </section>
    </div>

    <!-- JavaScript to handle form steps -->
    <script>
        // Get references to the steps and buttons
        const step1 = document.getElementById('step-1');
        const step2 = document.getElementById('step-2');
        const nextButton = document.getElementById('next-button');
        const submitButton = document.getElementById('submit-button');

        // Function to handle the "Next" button click
        nextButton.addEventListener('click', () => {
            // Hide step 1
            step1.classList.remove('active');
            // Show step 2
            step2.classList.add('active');
        });

        // // Function to handle the form submission (can add validation and handling)
        // document.getElementById('registration-form').addEventListener('submit', function(event) {
        //     event.preventDefault();
        //     alert('Form submitted!');
        //     // Here you can add code to handle form data, such as sending it to a server
        // });
    </script>
</body>

</html>