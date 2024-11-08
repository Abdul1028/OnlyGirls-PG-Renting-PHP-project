<?php

// When saving the image
if(isset($_FILES['room_image']) && $_FILES['room_image']['error'] === UPLOAD_ERR_OK) {
    $image_data = file_get_contents($_FILES['room_image']['tmp_name']);
    echo "Image size being uploaded: " . strlen($image_data) . " bytes<br>";
    
    // Add this debug statement
    if($image_data === false) {
        echo "Failed to read image file<br>";
    }
    
    // Your existing insert query
    $stmt = $conn->prepare("INSERT INTO listing (username, room_title, room_description, location, price, Date_From, Date_To, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $username, $room_title, $room_description, $location, $price, $date_from, $date_to, $image_data);
    
    if(!$stmt->execute()) {
        echo "Error saving listing: " . $stmt->error . "<br>";
    }
} 