<?php
// Start session (if not already started)
session_start();

// Database connection parameters
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

// Initialize username
$username = $_SESSION['username'];

// Retrieve user data
$user_query = 'SELECT name, email, Phone_Number, age, Married, smoke, drink, home_Town FROM login_details WHERE Username = ?';
$user_stmt = $conn->prepare($user_query);
if ($user_stmt === false) {
    die('Error preparing the user statement: ' . $conn->error);
}
$user_stmt->bind_param('s', $username);
$user_stmt->execute();
$user_stmt->bind_result($name, $email, $phone_number, $age, $marital_status, $smoking, $drinking, $town);

// Fetch the user data
$user_data = [];
if ($user_stmt->fetch()) {
    $user_data = [
        'name' => $name,
        'email' => $email,
        'phone_number' => $phone_number,
        'age' => $age,
        'Married' => $marital_status,
        'smoking' => $smoking,
        'drinking' => $drinking,
        'town' => $town
    ];
}

// Close the user statement
$user_stmt->close();


// Prepare the SQL query to fetch room listings for the specific username
$query = 'SELECT room_title, room_description, location, price, Date_From, Date_To, image FROM listing WHERE username = ?';
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die('Error preparing the listing statement: ' . $conn->error);
}

// Bind the username parameter and execute the statement
$stmt->bind_param('s', $username);
$stmt->execute();

// Bind the result columns
$stmt->bind_result($room_title, $room_description, $location, $price, $available_dates_from, $available_dates_to, $image);

// Initialize an array to store the results
$room_listings = [];

// Fetch the results and store them in the array
while ($stmt->fetch()) {
    $room_listings[] = [
        'title' => $room_title,
        'description' => $room_description,
        'location' => $location,
        'price' => $price,
        'available_dates_from' => $available_dates_from,
        'available_dates_to' => $available_dates_to,
        'image' => $image
    ];
}

// Close the statement and connection
$stmt->close();


$booking_query = 'SELECT room_title, room_description, location, price, number_of_days, Date_From, Date_To, total FROM bookings WHERE username = ?';
$statment = $conn->prepare($booking_query);
if ($statment === false) {
    die('Error preparing the listing statement: ' . $conn->error);
}

// Bind the username parameter and execute the statement
$statment->bind_param('s', $username);
$statment->execute();

// Bind the result columns
$statment->bind_result($room_title, $room_description, $location, $price, $number_of_days, $Date_From, $Date_To, $total);

// Initialize an array to store the results
$room_listing = [];

// Fetch the results and store them in the array
while ($statment->fetch()) {
    $room_listing[] = [
        'title' => $room_title,
        'description' => $room_description,
        'location' => $location,
        'price' => $price,
        'Number_of_days' => $number_of_days,
        'available_dates_from' => $Date_From,
        'available_dates_to' => $Date_To,
        'Total' => $total
    ];
}

// Close the statement and connection
$statment->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - She Shares</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #fce4ec;
        }
        .profile-card {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(255, 128, 171, 0.2);
            border: none;
        }
        .profile-header {
            background: linear-gradient(135deg, #ff80ab 0%, #f8bbd0 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px 15px 0 0;
            text-align: center;
        }
        .listing-card {
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        .listing-card:hover {
            transform: translateY(-5px);
        }
        .nav-pills .nav-link.active {
            background-color: #ff4081;
        }
        .nav-pills .nav-link {
            color: #ff4081;
        }
        .badge-pink {
            background-color: #ff4081;
            color: white;
        }
    </style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="fas fa-home text-pink"></i> She Shares
        </a>
        <div class="d-flex">
            <a href="../frontend/index.php" class="btn btn-outline-pink me-2">Home</a>
            <a href="../login-register/logout.php" class="btn btn-pink">Logout</a>
        </div>
    </div>
</nav>

<div class="container py-5">
    <!-- Profile Section -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card profile-card">
                <div class="profile-header">
                    <i class="fas fa-user-circle fa-4x mb-3"></i>
                    <h3><?php echo htmlspecialchars($user_data['name']); ?></h3>
                    <p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($user_data['town']); ?></p>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-around mb-4">
                        <span class="badge rounded-pill badge-pink">
                            <i class="fas <?php echo $user_data['smoking'] === 'yes' ? 'fa-smoking' : 'fa-smoking-ban'; ?>"></i>
                            <?php echo ucfirst($user_data['smoking']); ?>
                        </span>
                        <span class="badge rounded-pill badge-pink">
                            <i class="fas <?php echo $user_data['drinking'] === 'yes' ? 'fa-wine-glass' : 'fa-ban'; ?>"></i>
                            <?php echo ucfirst($user_data['drinking']); ?>
                        </span>
                        <span class="badge rounded-pill badge-pink">
                            <i class="fas <?php echo $user_data['Married'] === 'yes' ? 'fa-ring' : 'fa-heart'; ?>"></i>
                            <?php echo ucfirst($user_data['Married']); ?>
                        </span>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <i class="fas fa-envelope me-2"></i> <?php echo htmlspecialchars($user_data['email']); ?>
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-phone me-2"></i> <?php echo htmlspecialchars($user_data['phone_number']); ?>
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-birthday-cake me-2"></i> Age: <?php echo htmlspecialchars($user_data['age']); ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <ul class="nav nav-pills mb-4" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#listings">My Listings</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#bookings">My Bookings</button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Listings Tab -->
                <div class="tab-pane fade show active" id="listings">
                    <?php if (!empty($room_listings)) : ?>
                        <div class="row g-4">
                            <?php foreach ($room_listings as $listing) : ?>
                                <div class="col-md-6">
                                    <div class="card listing-card h-100">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($listing['image']); ?>" 
                                             class="card-img-top" alt="Room Image" 
                                             style="height: 200px; object-fit: cover;">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($listing['title']); ?></h5>
                                            <p class="card-text"><?php echo htmlspecialchars($listing['description']); ?></p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-primary">₹<?php echo htmlspecialchars($listing['price']); ?>/night</span>
                                                <span class="text-muted"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($listing['location']); ?></span>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-white">
                                            <small class="text-muted">
                                                Available: <?php echo date('M d', strtotime($listing['available_dates_from'])); ?> - 
                                                <?php echo date('M d, Y', strtotime($listing['available_dates_to'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> You haven't listed any rooms yet.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Bookings Tab -->
                <div class="tab-pane fade" id="bookings">
                    <?php if (!empty($room_listing)) : ?>
                        <div class="row g-4">
                            <?php foreach ($room_listing as $listing) : ?>
                                <div class="col-md-6">
                                    <div class="card listing-card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($listing['title']); ?></h5>
                                            <p class="card-text"><?php echo htmlspecialchars($listing['description']); ?></p>
                                            <ul class="list-unstyled">
                                                <li><i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($listing['location']); ?></li>
                                                <li><i class="fas fa-money-bill me-2"></i>₹<?php echo htmlspecialchars($listing['price']); ?>/night</li>
                                                <li><i class="fas fa-calendar me-2"></i><?php echo htmlspecialchars($listing['Number_of_days']); ?> days</li>
                                            </ul>
                                            <div class="alert alert-success mb-0">
                                                Total Amount: ₹<?php echo htmlspecialchars($listing['Total']); ?>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-white">
                                            <small class="text-muted">
                                                Booked: <?php echo date('M d', strtotime($listing['available_dates_from'])); ?> - 
                                                <?php echo date('M d, Y', strtotime($listing['available_dates_to'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> You haven't booked any rooms yet.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>