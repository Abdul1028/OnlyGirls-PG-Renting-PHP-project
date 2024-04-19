<!DOCTYPE html>
<html>
<style>
    #listed-rooms {
        display: flex;
        flex-wrap: wrap;
        
    }
</style>

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
// Perform the database query to fetch room listings based on the form data
$query = 'SELECT room_title, room_description, location, price, Date_From, Date_To, image FROM listing WHERE location = ? ';

// Prepare the statement
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $location);
$stmt->execute();
$stmt->bind_result($room_title, $room_description, $location, $price, $dateFrom, $dateTo, $image);

// Initialize an empty string to hold the HTML content
$htmlContent = '';

// Fetch results and generate HTML content using a loop
while ($stmt->fetch()) {
    $htmlContent .= '
    <section id="listed-rooms">
    <div class="room-listing" style="flex: 1; max-width: 300px; border: 1px solid #ccc; padding: 10px; margin: 10px; border-radius: 8px; background-color: #f8f9fa; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.15);">
        <h3 style="color: #0056b3; margin-top: 0; margin-bottom: 10px;">' . htmlspecialchars($room_title) . '</h3>
        <img src="' . htmlspecialchars($image) . '" alt="' . htmlspecialchars($room_title) . '" style="width: 100%; border-radius: 8px; margin-bottom: 10px;">
        <p style="margin: 5px 0; color: #333;">' . htmlspecialchars($room_description) . '</p>
        <p style="margin: 5px 0; color: #333;"><strong>Location:</strong> ' . htmlspecialchars($location) . '</p>
        <p style="margin: 5px 0; color: #333;"><strong>Price:</strong> $' . htmlspecialchars($price) . '</p>
        <p style="margin: 5px 0; color: #333;"><strong>Available:</strong> ' . htmlspecialchars($dateFrom) . ' to ' . htmlspecialchars($dateTo) . '</p>
    </div>
    </section>
';
}


// Close the statement
$stmt->close();

// Output the generated HTML content
echo $htmlContent;


// Close the database connection
$conn->close();
