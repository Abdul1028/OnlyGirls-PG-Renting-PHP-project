<?php

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

// Start session and get the username
session_start();
$username = $_SESSION['username'];

// Get form input data
$roomTitle = $_POST['room-title'] ?? '';
$roomDescription = $_POST['room-description'] ?? '';
$roomLocation = $_POST['room-location'] ?? '';
$roomPrice = $_POST['room-price'] ?? '';
$checkIn = $_POST['check-inn'] ?? '';
$checkOut = $_POST['check-outt'] ?? '';

// Convert dates to 'yyyy-mm-dd' format
$checkInFormatted = date('Y-m-d', strtotime($checkIn));
$checkOutFormatted = date('Y-m-d', strtotime($checkOut));

// Retrieve the uploaded file and read its contents
$filename = $_FILES["room-image"];
// $tempname = $_FILES["room-image"]["tmp_name"];
//$imageData = file_get_contents($filename);
//$encodedData = base64_encode($imageData);
// $i=FROM_BASE64('encodedData');

// Prepare the SQL statement to insert data into the database
$stmt = $conn->prepare("INSERT INTO listing (Username, Room_Title, Room_Description, Location, Price, Date_From, Date_To, Image) VALUES ('$username', '$roomTitle', '$roomDescription', '$roomLocation', '$roomPrice','$checkInFormatted', '$checkOutFormatted', '$filename' )");
if (!$stmt) {
    die('Prepare statement failed: ' . $conn->error);
}

// Bind the parameters to the prepared statement
//$stmt->bind_param("sssssssb", , , , , , , ,);

// Execute the statement and check if it was successful
if ($stmt->execute()) {
    // echo "Data inserted successfully.";
    header("Location: \\she-shares-vacation-rentals\\frontend\\index.php");
} else {
    echo "Error inserting data: " . $stmt->error;
}

// Close the statement
$stmt->close();


// Close the database connection
$conn->close();
?>
