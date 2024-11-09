<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'roomrental';

try {
    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Get form data
    $room_id = $_POST['room_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $requester_username = $_SESSION['username'];

    // Validate dates
    if (empty($check_in) || empty($check_out)) {
        throw new Exception("Please select both check-in and check-out dates");
    }

    if ($check_in > $check_out) {
        throw new Exception("Check-out date must be after check-in date");
    }

    // Get listing details
    $query = "SELECT Username, Date_From, Date_To FROM listing WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $listing = $result->fetch_assoc();

    if (!$listing) {
        throw new Exception("Room not found");
    }

    // Check if dates are within available range
    if ($check_in < $listing['Date_From'] || $check_out > $listing['Date_To']) {
        throw new Exception("Selected dates are outside the available range");
    }

    // Check if user is trying to book their own listing
    if ($listing['Username'] === $requester_username) {
        throw new Exception("You cannot book your own listing");
    }

    // Check for existing pending requests
    $query = "SELECT id FROM booking_requests 
              WHERE listing_id = ? 
              AND requester_username = ? 
              AND status = 'pending'
              AND ((check_in BETWEEN ? AND ?) 
                   OR (check_out BETWEEN ? AND ?))";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssss", $room_id, $requester_username, $check_in, $check_out, $check_in, $check_out);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        throw new Exception("You already have a pending request for these dates");
    }

    // Create booking request
    $query = "INSERT INTO booking_requests 
              (listing_id, requester_username, host_username, check_in, check_out) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issss", $room_id, $requester_username, $listing['Username'], $check_in, $check_out);
    
    if ($stmt->execute()) {
        // Set a session flag for the alert
        $_SESSION['show_booking_alert'] = true;
        header("Location: room_details.php?id=$room_id&success=1");
        exit;
    } else {
        throw new Exception("Error creating booking request");
    }

} catch (Exception $e) {
    header("Location: room_details.php?id=$room_id&error=" . urlencode($e->getMessage()));
    exit;
}

$conn->close();
?> 