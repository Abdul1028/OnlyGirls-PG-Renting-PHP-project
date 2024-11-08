<!DOCTYPE html>
<html>
<style>
    /* Styling the listed-rooms section to display room listings in two columns */
    /* Styling the listed-rooms section to display room listings in two rows */
    #listed-rooms {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(500px, 1fr));
        /* Automatically fit columns with a minimum width of 200px */
        grid-template-rows: repeat(2, auto);
        /* Two rows */
        /* gap: 20px; */
        /* Optional: Adds space between each room listing */
        margin-top: 20px;
        /* Optional: Adds margin above the container */
    }

    /* CSS class for room listing cards */
    .room-listing {
        flex: 1;
        text-align: center;
        max-width: 900px;

        /* Increased max-width */
        border: 1px solid #ccc;
        padding: 30px;
        margin: 20px;
        border-radius: 8px;
        background-color: #f8f9fa;
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.15);
    }
</style>
<link rel="stylesheet" href="index.css">
<header class="header">
    <h1>She Shares Vacation Rentals</h1>
    <?php
    session_start();

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
        echo "<a href='\\She-Shares-Vacation-Rentals\\frontend\\profile.php'>Profile</a>"; // Replace with your profile page link
        echo "<a href='\\She-Shares-Vacation-Rentals\\login-register\\logout.php'>Logout</a>"; // Replace with your logout page link
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

</html>
<?php
// Include the database connection
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

// Initialize an array to hold the results
$results = [];

// Check if the form data is sent via POST request
// Retrieve form data safely
$location = $_POST['location'];
$checkOutDate = $_POST['check-out'];
$checkInDate = $_POST['check-in'];
// Perform the database query to fetch room listings based on the form data
$query = 'SELECT room_title, room_description, location, price, Date_From, Date_To, image FROM listing WHERE location = ? ';

// Prepare the statement
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $location);
$stmt->execute();
$stmt->bind_result($room_title, $room_description, $location, $price, $dateFrom, $dateTo, $image);

// Initialize an empty string to hold the HTML content

// $checkInDate = '2024-04-15'; // Format should be YYYY-MM-DD
// $checkOutDate = '2024-04-20'; // Format should be YYYY-MM-DD

// Convert the dates to DateTime objects
$checkIn = new DateTime($checkInDate);
$checkOut = new DateTime($checkOutDate);

// Calculate the difference between the two dates
$interval = $checkIn->diff($checkOut);

// Get the number of days from the interval
$numberOfDays = $interval->days;
// $totalprice = $numberOfDays * 
// Display the number of days
//echo "Number of days: " . $numberOfDays;
// Fetch results and generate HTML content using a loop
$htmlContent = '<section id="listed-rooms">';
while ($stmt->fetch()) {
    $htmlContent .= '
        <div class="room-listing">
            <form action="booking.php" method="POST">
                <h3 style="color: #0056b3; margin-top: 0; margin-bottom: 10px;">' . htmlspecialchars($room_title) . '</h3>
                <img src="' . htmlspecialchars($image) . '" alt="room_image" style="width: 100%; border-radius: 8px; margin-bottom: 10px;">
                <p style="margin: 5px 0; color: #333;">' . htmlspecialchars($room_description) . '</p>
                <p style="margin: 5px 0; color: #333;"><strong>Location:</strong> ' . htmlspecialchars($location) . '</p>
                <p style="margin: 5px 0; color: #333;"><strong>Price per night: </strong> Rs' . htmlspecialchars($price) . '</p>
                <p style="margin: 5px 0; color: #333;"><strong>Number of days: </strong> ' . htmlspecialchars($numberOfDays) . '</p>
                <p style="margin: 5px 0; color: #333;"><strong>Total cost: </strong> Rs ' . htmlspecialchars($price)*$numberOfDays . '</p>
                <p style="margin: 5px 0; color: #333;"><strong>Available: </strong> ' . htmlspecialchars($dateFrom) . ' to ' . htmlspecialchars($dateTo) . '</p>

                <!-- Hidden input fields to pass booking details to the booking page -->
                <input type="hidden" name="room_title" value="' . htmlspecialchars($room_title) . '">
                <input type="hidden" name="image" value="' . htmlspecialchars($image) . '">
                <input type="hidden" name="room_description" value="' . htmlspecialchars($room_description) . '">
                <input type="hidden" name="location" value="' . htmlspecialchars($location) . '">
                <input type="hidden" name="price" value="' . htmlspecialchars($price) . '">
                <input type="hidden" name="number_of_days" value="' . htmlspecialchars($numberOfDays) . '">
                <input type="hidden" name="total" value="' .  htmlspecialchars($price)*$numberOfDays . '">
                <input type="hidden" name="date_from" value="' . htmlspecialchars($dateFrom) . '">
                <input type="hidden" name="date_to" value="' . htmlspecialchars($dateTo) . '">

                <button type="submit" class="submit-button">Book</button>
            </form>
        </div>
    ';
}

$htmlContent .= '</section>'; // Closing the section tag

echo $htmlContent; // Outputting the HTML content


// Close the database connection
$conn->close();
