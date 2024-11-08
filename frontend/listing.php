<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session and get the username
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../login-register/login.php");
    exit;
}

$username = $_SESSION['username'];

// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'roomrental';

// Create simple connection
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Get form input data
$roomTitle = trim($_POST['room-title'] ?? '');
$roomDescription = trim($_POST['room-description'] ?? '');
$roomLocation = trim($_POST['room-location'] ?? '');
$roomPrice = floatval($_POST['room-price'] ?? 0);
$checkIn = $_POST['check-inn'] ?? '';
$checkOut = $_POST['check-outt'] ?? '';

try {
    // Start transaction
    $conn->begin_transaction();

    // Insert listing details
    $stmt = $conn->prepare("INSERT INTO listing (Username, Room_Title, Room_Description, Location, Price, Date_From, Date_To) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $checkInFormatted = date('Y-m-d', strtotime($checkIn));
    $checkOutFormatted = date('Y-m-d', strtotime($checkOut));

    $stmt->bind_param("ssssdss", 
        $username,
        $roomTitle,
        $roomDescription,
        $roomLocation,
        $roomPrice,
        $checkInFormatted,
        $checkOutFormatted
    );

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $listing_id = $conn->insert_id;

    // Handle image uploads
    if (isset($_FILES['room-images']) && !empty($_FILES['room-images']['name'][0])) {
        $image_stmt = $conn->prepare("INSERT INTO listing_images (listing_id, image) VALUES (?, ?)");
        
        if (!$image_stmt) {
            throw new Exception("Image prepare failed: " . $conn->error);
        }

        foreach ($_FILES['room-images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['room-images']['error'][$key] === UPLOAD_ERR_OK) {
                // Validate file type and size
                $file_type = $_FILES['room-images']['type'][$key];
                $file_size = $_FILES['room-images']['size'][$key];
                
                if ($file_size > 2 * 1024 * 1024) { // 2MB limit
                    continue;
                }
                
                if (!in_array($file_type, ['image/jpeg', 'image/png', 'image/gif'])) {
                    continue;
                }
                
                // Read and compress image
                $imageData = file_get_contents($tmp_name);
                if ($imageData === false) {
                    continue;
                }
                
                $image_stmt->bind_param("is", $listing_id, $imageData);
                $image_stmt->execute();
            }
        }
        $image_stmt->close();
    }

    // Commit transaction
    $conn->commit();
    
    // Redirect back to index page
    header("Location: index.php?success=1");
    exit;

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    error_log("Error in listing.php: " . $e->getMessage());
    header("Location: index.php?error=" . urlencode($e->getMessage()));
    exit;

} finally {
    // Close connection
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
?>
