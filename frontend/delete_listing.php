<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

try {
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $dbname = 'roomrental';

    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $listing_id = $_POST['listing_id'] ?? 0;
    $username = $_SESSION['username'];

    // Verify ownership
    $verify_query = "SELECT id, room_title, location, price FROM listing WHERE id = ? AND Username = ?";
    $stmt = $conn->prepare($verify_query);
    $stmt->bind_param("is", $listing_id, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $listing = $result->fetch_assoc();
    
    if (!$listing) {
        throw new Exception("Invalid listing");
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Store listing details in booking_requests for accepted requests
        $update_requests = "UPDATE booking_requests 
                          SET listing_title = ?,
                              listing_location = ?,
                              listing_price = ?,
                              listing_id = NULL
                          WHERE listing_id = ? AND status = 'accepted'";
        $stmt = $conn->prepare($update_requests);
        $stmt->bind_param("ssdi", $listing['room_title'], $listing['location'], $listing['price'], $listing_id);
        $stmt->execute();

        // Delete pending requests
        $delete_pending = "DELETE FROM booking_requests WHERE listing_id = ? AND status = 'pending'";
        $stmt = $conn->prepare($delete_pending);
        $stmt->bind_param("i", $listing_id);
        $stmt->execute();

        // Delete listing images
        $delete_images = "DELETE FROM listing_images WHERE listing_id = ?";
        $stmt = $conn->prepare($delete_images);
        $stmt->bind_param("i", $listing_id);
        $stmt->execute();

        // Delete the listing
        $delete_listing = "DELETE FROM listing WHERE id = ?";
        $stmt = $conn->prepare($delete_listing);
        $stmt->bind_param("i", $listing_id);
        $stmt->execute();

        // Commit transaction
        $conn->commit();
        header("Location: profile.php?tab=listings&message=deleted");
        
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        throw new Exception("Error deleting listing: " . $e->getMessage());
    }

} catch (Exception $e) {
    header("Location: profile.php?tab=listings&error=" . urlencode($e->getMessage()));
}

$conn->close();
?> 