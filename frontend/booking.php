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

// Start the session
session_start();

// Retrieve booking details from the form submission
$room_title = filter_input(INPUT_POST, 'room_title', FILTER_SANITIZE_STRING);
$image = filter_input(INPUT_POST, 'image', FILTER_SANITIZE_STRING);
$room_description = filter_input(INPUT_POST, 'room_description', FILTER_SANITIZE_STRING);
$location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
$price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$number_of_days = filter_input(INPUT_POST, 'number_of_days', FILTER_SANITIZE_NUMBER_INT);
$date_from = filter_input(INPUT_POST, 'date_from', FILTER_SANITIZE_STRING);
$date_to = filter_input(INPUT_POST, 'date_to', FILTER_SANITIZE_STRING);
$total = filter_input(INPUT_POST, 'total', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

// Validate form input
$errors = [];

// Check if all required fields have values
if (empty($room_title) || empty($image) || empty($room_description) || empty($location) || empty($price) || empty($number_of_days) || empty($date_from) || empty($date_to) || empty($total)) {
    $errors[] = 'Please fill out all fields.';
}

// If there are no errors, process the booking

try {
    // Prepare the SQL statement for inserting booking information
    $stmt = $conn->prepare('INSERT INTO bookings (room_title, room_description, location, price, total, number_of_days, date_from, date_to, username) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');

    // Check if the statement was prepared successfully
    if ($stmt === false) {
        throw new Exception('Failed to prepare the SQL statement: ' . $conn->error);
    }

    // Bind the parameters to the statement
    $stmt->bind_param(
        'sssdidsss',
        $room_title,
        $room_description,
        $location,
        $price,
        $total,
        $number_of_days,
        $date_from,
        $date_to,
        $_SESSION['username'] // Assuming you have a username stored in the session
    );

    // Execute the statement
    $stmt->execute();

    // Booking successful, redirect to the confirmation page
    header('Location: /She-Shares-Vacation-Rentals/confirm/booking_confirmation.php');
    exit;
} catch (Exception $e) {
    $errors[] = 'Failed to save booking: ' . $e->getMessage();
}

// Ensure the statement is closed
if (isset($stmt)) {
    $stmt->close();
}
?>

