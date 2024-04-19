<?php
// Start session (if not already started)
session_start();

// Database connection parameters
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'roomrental';

// Connect to the database
$conn = new mysqli($host, $user, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Initialize username
$username = $_SESSION['username'];

// Retrieve user data
$user_query = 'SELECT name, email, Phone_Number, age, Married, smoke, drink, home_Town FROM login_details WHERE Username = ?';
$user_stmt = $conn->prepare($user_query);
if ($user_stmt === false) {
    die('Error preparing the user statement: ' . $conn->error);
}
$user_stmt->bind_param('s', $username);
$user_stmt->execute();
$user_stmt->bind_result($name, $email, $phone_number, $age, $marital_status, $smoking, $drinking, $town);

// Fetch the user data
$user_data = [];
if ($user_stmt->fetch()) {
    $user_data = [
        'name' => $name,
        'email' => $email,
        'phone_number' => $phone_number,
        'age' => $age,
        'Married' => $marital_status,
        'smoking' => $smoking,
        'drinking' => $drinking,
        'town' => $town
    ];
}

// Close the user statement
$user_stmt->close();

// Prepare the SQL query to fetch room listings for the specific username
$query = 'SELECT room_title, room_description, location, price, Date_From, Date_To, image FROM listing WHERE username = ?';
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die('Error preparing the listing statement: ' . $conn->error);
}

// Bind the username parameter and execute the statement
$stmt->bind_param('s', $username);
$stmt->execute();

// Bind the result columns
$stmt->bind_result($room_title, $room_description, $location, $price, $available_dates_from, $available_dates_to, $image);

// Initialize an array to store the results
$room_listings = [];

// Fetch the results and store them in the array
while ($stmt->fetch()) {
    $room_listings[] = [
        'title' => $room_title,
        'description' => $room_description,
        'location' => $location,
        'price' => $price,
        'available_dates_from' => $available_dates_from,
        'available_dates_to' => $available_dates_to,
        'image' => $image
    ];
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile and Room Listings</title>
    <link rel="stylesheet" href="styles.css">
</head>
<link rel="stylesheet" href="index.css">


<body>
<header class="header">
        <h1>She Shares Vacation Rentals</h1>
        <?php

        // Check if the user is logged in
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
            // The user is not logged in, display login and register buttons
            echo "<div class='auth-buttons'>";
            echo "<a href='/login-register/login.php'>Login</a>";
            echo "<a href='/login-register/registration.php'>Register</a>";
            echo "</div>";
        } else {
            // The user is logged in, do not display the buttons
            // You may also want to display a logout button or user profile link instead
            echo "<div class='auth-buttons'>";
            echo "<a href='\\frontend\\index.php'>Home</a>"; // Replace with your profile page link
            echo "<a href='\\login-register\\logout.php'>Logout</a>"; // Replace with your logout page link
            echo "</div>";
        }
        ?>

        <!-- Hamburger menu -->
        <div class="hamburger" id="hamburger" onclick="toggleMenu()">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </header>

    <div class="container">
        <section class="profile">
            <div class="profile-section">
                <div class="h">
                    <h1>Your Profile</h1>
                </div>
                <div class="first">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($user_data['name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user_data['email']); ?></p>
                    <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($user_data['phone_number']); ?></p>
                    <p><strong>Age:</strong> <?php echo htmlspecialchars($user_data['age']); ?></p>
                </div>
                <div class="second">

                    <p><strong>Married:</strong> <?php echo htmlspecialchars($user_data['Married']); ?></p>
                    <p><strong>Smoking:</strong> <?php echo htmlspecialchars($user_data['smoking']); ?></p>
                    <p><strong>Drinking:</strong> <?php echo htmlspecialchars($user_data['drinking']); ?></p>
                </div>
                <!-- <p><strong>Town:</strong> <?php echo htmlspecialchars($user_data['town']); ?></p> -->
            </div>
        </section>

        <div class="room-listings-section">
            <h2>Your Room Listings</h2>

            <!-- Display room listings -->
            <?php if (!empty($room_listings)) : ?>
                <?php foreach ($room_listings as $listing) : ?>
                    <div class="room-listing">
                        <section class="listing">
                            <div class="image-item">
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($listing['image']); ?>" alt="Room Image">
                            </div>
                            <h3><?php echo htmlspecialchars($listing['title']); ?></h3>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($listing['description']); ?></p>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($listing['location']); ?></p>
                            <p><strong>Price (per night):</strong> <?php echo htmlspecialchars($listing['price']); ?></p>
                            <p><strong>Available Dates:</strong> <?php echo htmlspecialchars($listing['available_dates_from']); ?> to <?php echo htmlspecialchars($listing['available_dates_to']); ?></p>

                        </section>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p>No room listings found for your username.</p>
            <?php endif; ?>
        </div>

    </div>
</body>

</html>