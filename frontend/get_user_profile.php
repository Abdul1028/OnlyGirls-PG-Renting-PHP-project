<?php
header('Content-Type: application/json');

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'roomrental';

try {
    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $username = isset($_GET['username']) ? $conn->real_escape_string($_GET['username']) : '';

    if (empty($username)) {
        throw new Exception("Username is required");
    }

    $query = "SELECT Name, Email, Phone_Number, Age, Smoke, Drink, Married, Home_Town, profile_image 
              FROM login_details 
              WHERE Username = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("User not found");
    }

    $user = $result->fetch_assoc();
    
    // Convert profile image to base64 if exists
    if (!empty($user['profile_image'])) {
        $user['profile_image'] = base64_encode($user['profile_image']);
    }

    echo json_encode(['success' => true, 'data' => $user]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?> 