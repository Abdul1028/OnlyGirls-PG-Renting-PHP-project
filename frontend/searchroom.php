<?php
// Include the database connection
require 'db_connection.php';

// Get the search parameters from the URL
$location = $_GET['location'] ?? '';
$checkIn = $_GET['checkIn'] ?? '';
$checkOut = $_GET['checkOut'] ?? '';

// Prepare the SQL query to fetch room listings based on the selected location and available dates
$query = 'SELECT room_title, room_description, location, price, Date_From as check_in, Date_To as check_out, image FROM listing WHERE location = ? ';

// Prepare the statement
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die('Error preparing the query: ' . $conn->error);
}

// Bind the parameters and execute the query
$stmt->bind_param('s', $location);
$stmt->execute();

// Fetch the results
$results = [];
$stmt->bind_result($room_title, $room_description, $location, $price, $check_in, $check_out, $image);
while ($stmt->fetch()) {
    $results[] = [
        'room_title' => $room_title,
        'room_description' => $room_description,
        'location' => $location,
        'price' => $price,
        'check_in' => $check_in,
        'check_out' => $check_out,
        'image' => base64_encode($image)
    ];
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Return the results as JSON
header('Content-Type: application/json');
echo json_encode($results);
?>
