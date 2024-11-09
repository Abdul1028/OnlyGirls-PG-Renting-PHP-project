<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

try {
    // Database connection
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $dbname = 'roomrental';

    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $request_id = $_POST['request_id'] ?? 0;
    $action = $_POST['action'] ?? '';
    $host_username = $_SESSION['username'];

    // Verify the request belongs to this host
    $verify_query = "SELECT br.*, l.id as listing_id, l.room_title 
                    FROM booking_requests br 
                    JOIN listing l ON br.listing_id = l.id 
                    WHERE br.id = ? AND br.host_username = ? AND br.status = 'pending'";
    $stmt = $conn->prepare($verify_query);
    $stmt->bind_param("is", $request_id, $host_username);
    $stmt->execute();
    $result = $stmt->get_result();
    $request = $result->fetch_assoc();

    if (!$request) {
        throw new Exception("Invalid request");
    }

    // Update request status
    $status = ($action === 'accept') ? 'accepted' : 'rejected';
    $update_query = "UPDATE booking_requests SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $status, $request_id);
    
    if ($stmt->execute()) {
        if ($action === 'accept') {
            // Redirect to confirmation page for listing deletion
            header("Location: confirm_deletion.php?listing_id=" . $request['listing_id'] . "&title=" . urlencode($request['room_title']));
            exit;
        } else {
            header("Location: profile.php?tab=requests&success=1");
        }
    } else {
        throw new Exception("Error updating request");
    }

} catch (Exception $e) {
    header("Location: profile.php?tab=requests&error=" . urlencode($e->getMessage()));
}

$conn->close();
?> 