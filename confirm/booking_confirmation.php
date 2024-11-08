<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <link rel="stylesheet" href="booking.css">
    <style>

    </style>
</head>

<body>
    <header class="header">
        <h1>She Shares Vacation Rentals</h1>
        <?php
        session_start();

        // Check if the user is logged in
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
            // The user is not logged in, display login and register buttons
            echo "<div class='auth-buttons'>";
            echo "<a href='\\login-register\\login.php'>Login</a>";
            echo "<a href='\\login-register\\registration.php'>Register</a>";
            echo "</div>";
        } else {
            // The user is logged in, do not display the buttons
            // You may also want to display a logout button or user profile link instead
            echo "<div class='auth-buttons'>";
            echo "<a href='\\frontend\\profile.php'>Profile</a>"; // Replace with your profile page link
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
    <div class="menu" id="menu">
        <a href="#">Home</a>
        <a href="#">Welcome</a>
        <a href="#">Safety</a>
        <a href="#">Adventure</a>
        <a href="#">Community</a>
    </div>

    <div class="container">
        <h1>Booking Successful</h1>
        <div class="booking-details">
            <?php
        
            $username = $_SESSION['username'];
            $host = 'localhost';
            $user = 'root';
            $password = 'Pass@123';
            $dbname = 'roomrental';

            // Connect to the database
            $conn = new mysqli($host, $user, $password, $dbname);

            // Check for connection errors
            if ($conn->connect_error) {
                die('Database connection failed: ' . $conn->connect_error);
            }

            // Prepare and execute the SQL statement
            $stmt = $conn->prepare('SELECT room_title, room_description, location, price, number_of_days, date_from, date_to, Total FROM bookings WHERE username = ?');
            $stmt->bind_param('s', $username);
            $stmt->execute();

            // Bind the result variables
            $stmt->bind_result($room_title, $room_description, $location, $price, $number_of_days, $date_from, $date_to, $total);

            // Fetch and display the booking details
            while ($stmt->fetch()) {
                echo "<p><strong>Room Title:</strong> " . htmlspecialchars($room_title) . "</p>";
                echo "<p><strong>Location:</strong> " . htmlspecialchars($location) . "</p>";
                echo "<p><strong>Price per Night:</strong> Rs " . htmlspecialchars($price) . "</p>";
                echo "<p><strong>Number of Days:</strong> " . htmlspecialchars($number_of_days) . "</p>";
                echo "<p><strong>Date From:</strong> " . htmlspecialchars($date_from) . "</p>";
                echo "<p><strong>Date To:</strong> " . htmlspecialchars($date_to) . "</p>";
                echo "<p><strong>Date To:</strong> rs " . htmlspecialchars($total) . "</p>";
            }

            // Close the statement and connection
            $stmt->close();
            $conn->close();
            ?>
        </div>
        <div class="button-container">
            <button class="button" onclick="window.location.href='\\frontend\\index.php';">Back to Home</button>
        </div>
    </div>
</body>
<script>
    document.getElementById('menu').style.display = 'none';

    function showSection(sectionId) {
        // Hide all output sections
        document.getElementById('sharing-output').style.display = 'none';
        document.getElementById('renting-output').style.display = 'none';

        // Show the selected section
        document.getElementById(sectionId + '-output').style.display = 'block';
    }

    // Automatically show the "Sharing Your Room" content by default
    window.onload = function() {
        showSection('renting');
    };
</script>

</html>