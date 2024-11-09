<?php
// Start session (if not already started)
session_start();

// Initialize username first
$username = $_SESSION['username'] ?? '';
if (empty($username)) {
    header("Location: ../login-register/login.php");
    exit;
}

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

// Debug query to check images
function debugImages($conn, $listing_id) {
    $debug_query = "SELECT COUNT(*) as count FROM listing_images WHERE listing_id = ?";
    $stmt = $conn->prepare($debug_query);
    $stmt->bind_param("i", $listing_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    return $count;
}

// Retrieve user data
$user_query = 'SELECT name, email, Phone_Number, age, Married, Smoke, Drink, home_Town FROM login_details WHERE Username = ?';
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
        'Smoke' => $smoking,
        'Drink' => $drinking,
        'town' => $town
    ];
}
$user_stmt->close();

// Fetch listings with their IDs
$listings_query = 'SELECT id, room_title, room_description, location, price, Date_From, Date_To FROM listing WHERE username = ?';
$listings_stmt = $conn->prepare($listings_query);
if ($listings_stmt === false) {
    die('Error preparing listings statement: ' . $conn->error);
}

$listings_stmt->bind_param('s', $username);
$listings_stmt->execute();
$result = $listings_stmt->get_result();

$room_listings = [];
while ($row = $result->fetch_assoc()) {
    $image_count = debugImages($conn, $row['id']);
    $room_listings[] = [
        'id' => $row['id'],
        'title' => $row['room_title'],
        'description' => $row['room_description'],
        'location' => $row['location'],
        'price' => $row['price'],
        'available_dates_from' => $row['Date_From'],
        'available_dates_to' => $row['Date_To'],
        'image_count' => $image_count
    ];
}
$listings_stmt->close();

// Fetch bookings
$booking_query = 'SELECT room_title, room_description, location, price, number_of_days, Date_From, Date_To, total FROM bookings WHERE username = ?';
$booking_stmt = $conn->prepare($booking_query);
if ($booking_stmt === false) {
    die('Error preparing booking statement: ' . $conn->error);
}

$booking_stmt->bind_param('s', $username);
$booking_stmt->execute();
$booking_result = $booking_stmt->get_result();

$room_booking = [];
while ($row = $booking_result->fetch_assoc()) {
    $room_booking[] = [
        'title' => $row['room_title'],
        'description' => $row['room_description'],
        'location' => $row['location'],
        'price' => $row['price'],
        'Number_of_days' => $row['number_of_days'],
        'available_dates_from' => $row['Date_From'],
        'available_dates_to' => $row['Date_To'],
        'Total' => $row['total']
    ];
}
$booking_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - She Shares</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #fce4ec; }
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
        .carousel-item img {
            height: 200px;
            object-fit: cover;
            border-radius: 15px 15px 0 0;
        }

        .carousel-control-prev,
        .carousel-control-next {
            width: 10%;
            background: rgba(0,0,0,0.2);
            border-radius: 15px;
            margin: 0 10px;
        }

        .carousel-indicators {
            margin-bottom: 0.5rem;
        }

        .carousel-indicators button {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.7);
        }

        .carousel-indicators button.active {
            background-color: #fff;
        }

        .border-warning {
            border-width: 2px !important;
        }

        .border-success {
            border-width: 2px !important;
        }

        .border-danger {
            border-width: 2px !important;
        }

        .card-header {
            border-bottom: none;
        }

        .badge {
            padding: 0.5em 1em;
        }

        /* Request Card Styles */
        .request-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(255, 128, 171, 0.2);
            transition: transform 0.2s;
        }

        .request-card:hover {
            transform: translateY(-5px);
        }

        .request-status {
            padding: 12px;
            color: white;
            text-align: center;
            font-weight: 500;
        }

        .request-status.pending {
            background: linear-gradient(135deg, #ffd54f 0%, #ffb300 100%);
        }

        .request-status.accepted {
            background: linear-gradient(135deg, #81c784 0%, #4caf50 100%);
        }

        .request-status.rejected {
            background: linear-gradient(135deg, #e57373 0%, #f44336 100%);
        }

        .status-icon {
            margin-right: 8px;
        }

        .request-content {
            padding: 20px;
        }

        .request-title {
            color: #ff4081;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .guest-info {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #fff5f8;
            border-radius: 10px;
        }

        .guest-avatar {
            width: 50px;
            height: 50px;
            background: #ff4081;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 15px;
        }

        .guest-details h6 {
            margin: 0;
            color: #333;
            font-weight: 600;
        }

        .contact-info {
            display: flex;
            gap: 15px;
            font-size: 0.9rem;
            color: #666;
            margin-top: 5px;
        }

        .contact-info i {
            color: #ff4081;
        }

        .booking-details {
            background: #fafafa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .detail-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .detail-item:last-child {
            margin-bottom: 0;
        }

        .detail-item i {
            color: #ff4081;
            width: 24px;
            margin-right: 10px;
            margin-top: 3px;
        }

        .detail-item strong {
            display: block;
            font-size: 0.85rem;
            color: #666;
        }

        .detail-item span {
            display: block;
            color: #333;
        }

        .nights {
            font-size: 0.85rem;
            color: #ff4081;
            margin-top: 2px;
        }

        .request-actions {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .action-form {
            flex: 1;
        }

        .btn-accept, .btn-reject {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .btn-accept {
            background: #ff4081;
            color: white;
        }

        .btn-reject {
            background: #f8f9fa;
            color: #666;
        }

        .btn-accept:hover, .btn-reject:hover {
            opacity: 0.9;
        }

        .request-timestamp {
            font-size: 0.85rem;
            color: #999;
            text-align: center;
        }

        .request-timestamp i {
            margin-right: 5px;
        }

        .no-requests {
            text-align: center;
            padding: 40px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(255, 128, 171, 0.1);
        }

        .no-requests i {
            font-size: 3rem;
            color: #ff4081;
            margin-bottom: 15px;
        }

        .no-requests h5 {
            color: #333;
            margin-bottom: 10px;
        }

        .no-requests p {
            color: #666;
            margin: 0;
        }

        .custom-alert {
            border: none;
            border-radius: 12px;
            padding: 15px 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .custom-alert.alert-success {
            background: #fff5f8;
            color: #ff4081;
            border-left: 4px solid #ff4081;
        }

        .custom-alert.alert-info {
            background: #f8f9fa;
            color: #666;
            border-left: 4px solid #666;
        }

        .custom-alert .btn-close {
            padding: 15px;
            opacity: 0.5;
        }

        .custom-alert .btn-close:hover {
            opacity: 1;
        }

        .booking-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(255, 128, 171, 0.2);
            transition: transform 0.2s;
        }

        .booking-card:hover {
            transform: translateY(-5px);
        }

        .booking-status {
            background: linear-gradient(135deg, #ff4081 0%, #ff80ab 100%);
            color: white;
            padding: 12px 20px;
            font-weight: 500;
        }

        .booking-status i {
            margin-right: 8px;
        }

        .booking-content {
            padding: 20px;
        }

        .booking-title {
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .booking-details {
            background: #fff5f8;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .detail-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .detail-item:last-child {
            margin-bottom: 0;
        }

        .detail-item i {
            color: #ff4081;
            margin-right: 15px;
            margin-top: 4px;
        }

        .detail-item strong {
            display: block;
            color: #666;
            font-size: 0.9rem;
        }

        .total-amount {
            color: #ff4081;
            font-weight: 600;
            display: block;
            margin-top: 5px;
        }

        .host-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .host-info h6 {
            color: #333;
            margin-bottom: 10px;
        }

        .host-info p {
            margin-bottom: 5px;
            color: #666;
        }

        .booking-timestamp {
            font-size: 0.85rem;
            color: #999;
            text-align: center;
        }

        .no-bookings {
            text-align: center;
            padding: 40px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(255, 128, 171, 0.1);
        }

        .no-bookings i {
            font-size: 3rem;
            color: #ff4081;
            margin-bottom: 15px;
        }

        .no-bookings h5 {
            color: #333;
            margin-bottom: 10px;
        }

        .no-bookings p {
            color: #666;
            margin: 0;
        }


        
    </style>
</head>
<body>

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
    <div class="row">
        <!-- Profile Card -->
        <div class="col-md-4 mb-4">
            <div class="card profile-card">
                <div class="profile-header">
                    <i class="fas fa-user-circle fa-4x mb-3"></i>
                    <h3><?php echo htmlspecialchars($user_data['name']); ?></h3>
                    <p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($user_data['town']); ?></p>
                </div>
                <div class="card-body">
                    <!-- User details here -->
                    <div class="d-flex justify-content-around mb-4">
                        <span class="badge rounded-pill badge-pink">
                            <i class="fas <?php echo strtolower($user_data['Smoke']) === 'yes' ? 'fa-smoking' : 'fa-smoking-ban'; ?>"></i>
                            <?php echo strtolower($user_data['Smoke']) === 'yes' ? 'Smoker' : 'Non-Smoker'; ?>
                        </span>
                        <span class="badge rounded-pill badge-pink">
                            <i class="fas <?php echo strtolower($user_data['Drink']) === 'yes' ? 'fa-wine-glass-alt' : 'fa-ban'; ?>"></i>
                            <?php echo strtolower($user_data['Drink']) === 'yes' ? 'Drinker' : 'Non-Drinker'; ?>
                        </span>
                        <span class="badge rounded-pill badge-pink">
                            <i class="fas <?php echo strtolower($user_data['Married']) === 'yes' ? ' fa-solid fa-children' : 'fa-user'; ?>"></i>
                            <?php echo strtolower($user_data['Married']) === 'yes' ? 'Married' : 'Single'; ?>
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

        <!-- Listings and Bookings -->
        <div class="col-md-8">
            <ul class="nav nav-pills mb-4" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#listings">My Listings</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#bookings">My Bookings</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#requests">
                        My Requests 
                        <?php
                        // Count pending requests
                        $pending_query = "SELECT COUNT(*) as count FROM booking_requests WHERE host_username = ? AND status = 'pending'";
                        $pending_stmt = $conn->prepare($pending_query);
                        $pending_stmt->bind_param('s', $username);
                        $pending_stmt->execute();
                        $pending_count = $pending_stmt->get_result()->fetch_assoc()['count'];
                        if ($pending_count > 0):
                        ?>
                            <span class="badge bg-danger ms-2"><?php echo $pending_count; ?></span>
                        <?php endif; ?>
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <?php if (isset($_GET['message'])): ?>
                    <?php if ($_GET['message'] === 'deleted'): ?>
                        <div class="alert custom-alert alert-success alert-dismissible fade show mb-4" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            Listing has been successfully deleted
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php elseif ($_GET['message'] === 'kept'): ?>
                        <div class="alert custom-alert alert-info alert-dismissible fade show mb-4" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            Listing has been kept in your profile
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Listings Tab -->
                <div class="tab-pane fade show active" id="listings">
                    <?php if (!empty($room_listings)) : ?>
                        <div class="row g-4">
                            <?php foreach ($room_listings as $listing) : ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card listing-card h-100">
                                        <?php
                                        // Fetch images for this listing
                                        $images_query = "SELECT image FROM listing_images WHERE listing_id = ?";
                                        $images_stmt = $conn->prepare($images_query);
                                        $images_stmt->bind_param("i", $listing['id']);
                                        $images_stmt->execute();
                                        $images_result = $images_stmt->get_result();
                                        
                                        // Debug output
                                        echo "<!-- Debug: Found " . $images_result->num_rows . " images for listing " . $listing['id'] . " -->";

                                        if ($images_result && $images_result->num_rows > 0) {
                                            // Store all images in an array first
                                            $images = [];
                                            while ($image = $images_result->fetch_assoc()) {
                                                $images[] = $image['image'];
                                            }
                                            
                                            // Debug output
                                            echo "<!-- Debug: Processing " . count($images) . " images -->";
                                            ?>
                                            <div id="carousel-<?php echo $listing['id']; ?>" class="carousel slide" >
                                                <div class="carousel-inner">
                                                    <?php foreach ($images as $index => $image): ?>
                                                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($image); ?>" 
                                                                 class="d-block w-100" 
                                                                 alt="Room Image <?php echo $index + 1; ?>"
                                                                 style="height: 200px; object-fit: cover;">
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                                
                                                <?php if (count($images) > 1): ?>
                                                    <button class="carousel-control-prev" type="button" 
                                                            data-bs-target="#carousel-<?php echo $listing['id']; ?>" 
                                                            data-bs-slide="prev">
                                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                        <span class="visually-hidden">Previous</span>
                                                    </button>
                                                    <button class="carousel-control-next" type="button" 
                                                            data-bs-target="#carousel-<?php echo $listing['id']; ?>" 
                                                            data-bs-slide="next">
                                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                        <span class="visually-hidden">Next</span>
                                                    </button>
                                                    
                                                    <div class="carousel-indicators">
                                                        <?php for($i = 0; $i < count($images); $i++): ?>
                                                            <button type="button" 
                                                                    data-bs-target="#carousel-<?php echo $listing['id']; ?>" 
                                                                    data-bs-slide-to="<?php echo $i; ?>" 
                                                                    <?php echo $i === 0 ? 'class="active" aria-current="true"' : ''; ?>
                                                                    aria-label="Slide <?php echo $i + 1; ?>">
                                                            </button>
                                                        <?php endfor; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <?php
                                        } else {
                                            // No images - show placeholder
                                            ?>
                                            <div class="card-img-top d-flex align-items-center justify-content-center" 
                                                 style="height: 200px; background-color: #fce4ec;">
                                                <i class="fas fa-home" style="font-size: 3rem; color: #ff4081;"></i>
                                            </div>
                                            <?php
                                        }
                                        $images_stmt->close();
                                        ?>
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($listing['title']); ?></h5>
                                            <p class="card-text"><?php echo htmlspecialchars($listing['description']); ?></p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-primary">₹<?php echo htmlspecialchars($listing['price']); ?>/night</span>
                                                <span class="text-muted">
                                                    <i class="fas fa-map-marker-alt"></i> 
                                                    <?php echo htmlspecialchars($listing['location']); ?>
                                                </span>
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
                    <?php
                    // Fetch user's bookings where status is 'accepted'
                    $bookings_query = "SELECT br.*, 
                                     COALESCE(l.room_title, br.listing_title) as room_title,
                                     COALESCE(l.location, br.listing_location) as location,
                                     COALESCE(l.price, br.listing_price) as price,
                                     ld.Name as host_name,
                                     ld.Phone_Number as host_phone,
                                     ld.Email as host_email,
                                     DATEDIFF(br.check_out, br.check_in) as total_nights
                              FROM booking_requests br
                              LEFT JOIN listing l ON br.listing_id = l.id
                              JOIN login_details ld ON br.host_username = ld.Username
                              WHERE br.requester_username = ? 
                              AND br.status = 'accepted'
                              ORDER BY br.request_date DESC";
    
                    $bookings_stmt = $conn->prepare($bookings_query);
                    $bookings_stmt->bind_param('s', $username);
                    $bookings_stmt->execute();
                    $bookings_result = $bookings_stmt->get_result();
    
                    if ($bookings_result->num_rows > 0):
                    ?>
                        <div class="row g-4">
                            <?php while ($booking = $bookings_result->fetch_assoc()): 
                                $total_amount = $booking['price'] * $booking['total_nights'];
                            ?>
                                <div class="col-md-6 mb-4">
                                    <div class="booking-card">
                                        <div class="booking-status">
                                            <i class="fas fa-check-circle"></i> Confirmed Booking
                                        </div>
                                        
                                        <div class="booking-content">
                                            <h5 class="booking-title">
                                                <?php echo htmlspecialchars($booking['room_title']); ?>
                                            </h5>

                                            <div class="booking-details">
                                                <div class="detail-item">
                                                    <i class="fas fa-calendar"></i>
                                                    <div>
                                                        <strong>Stay Duration</strong>
                                                        <span><?php echo date('M d', strtotime($booking['check_in'])); ?> - <?php echo date('M d, Y', strtotime($booking['check_out'])); ?></span>
                                                        <span class="nights"><?php echo $booking['total_nights']; ?> nights</span>
                                                    </div>
                                                </div>

                                                <div class="detail-item">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    <div>
                                                        <strong>Location</strong>
                                                        <span><?php echo htmlspecialchars($booking['location']); ?></span>
                                                    </div>
                                                </div>

                                                <div class="detail-item">
                                                    <i class="fas fa-rupee-sign"></i>
                                                    <div>
                                                        <strong>Price Details</strong>
                                                        <span>₹<?php echo number_format($booking['price']); ?> × <?php echo $booking['total_nights']; ?> nights</span>
                                                        <span class="total-amount">Total: ₹<?php echo number_format($total_amount); ?></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="host-info">
                                                <h6><i class="fas fa-user me-2"></i>Host Details</h6>
                                                <p><strong><?php echo htmlspecialchars($booking['host_name']); ?></strong></p>
                                                <p><i class="fas fa-phone me-2"></i><?php echo htmlspecialchars($booking['host_phone']); ?></p>
                                                <p><i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($booking['host_email']); ?></p>
                                            </div>

                                            <div class="booking-timestamp">
                                                <i class="fas fa-clock"></i>
                                                Booked on <?php echo date('M d, Y g:i A', strtotime($booking['request_date'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-bookings">
                            <i class="fas fa-calendar-times"></i>
                            <h5>No Confirmed Bookings</h5>
                            <p>When hosts accept your booking requests, they'll appear here.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Requests Tab -->
                <div class="tab-pane fade" id="requests">
                    <?php
                    $requests_query = "SELECT br.*, 
                                         COALESCE(l.room_title, br.listing_title) as room_title,
                                         COALESCE(l.location, br.listing_location) as location,
                                         COALESCE(l.price, br.listing_price) as price,
                                         ld.Name as requester_name, 
                                         ld.Email as requester_email,
                                         ld.Phone_Number as requester_phone,
                                         DATEDIFF(br.check_out, br.check_in) as total_nights
                                  FROM booking_requests br
                                  LEFT JOIN listing l ON br.listing_id = l.id
                                  JOIN login_details ld ON br.requester_username = ld.Username
                                  WHERE br.host_username = ?
                                  ORDER BY br.status = 'pending' DESC, br.request_date DESC";
    
                    $requests_stmt = $conn->prepare($requests_query);
                    $requests_stmt->bind_param('s', $username);
                    $requests_stmt->execute();
                    $requests_result = $requests_stmt->get_result();
    
                    if ($requests_result->num_rows > 0):
                    ?>
                        <div class="row g-4">
                            <?php while ($request = $requests_result->fetch_assoc()): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="request-card">
                                        <!-- Request Status Banner -->
                                        <div class="request-status <?php echo $request['status']; ?>">
                                            <span class="status-icon">
                                                <?php if($request['status'] === 'pending'): ?>
                                                    <i class="fas fa-clock"></i>
                                                <?php elseif($request['status'] === 'accepted'): ?>
                                                    <i class="fas fa-check-circle"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-times-circle"></i>
                                                <?php endif; ?>
                                            </span>
                                            <?php echo ucfirst($request['status']); ?> Request
                                        </div>

                                        <!-- Request Content -->
                                        <div class="request-content">
                                            <h5 class="request-title">
                                                <?php 
                                                // If listing still exists, use room_title from listing table
                                                echo htmlspecialchars($request['listing_title'] ?? $request['room_title']); 
                                                ?>
                                            </h5>

                                            <!-- Guest Info -->
                                            <div class="guest-info">
                                                <div class="guest-avatar">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <div class="guest-details">
                                                    <h6><?php echo htmlspecialchars($request['requester_name']); ?></h6>
                                                    <div class="contact-info">
                                                        <span><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($request['requester_email']); ?></span>
                                                        <span><i class="fas fa-phone"></i> <?php echo htmlspecialchars($request['requester_phone']); ?></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Booking Details -->
                                            <div class="booking-details">
                                                <div class="detail-item">
                                                    <i class="fas fa-calendar"></i>
                                                    <div>
                                                        <strong>Stay Duration</strong>
                                                        <span><?php echo date('M d', strtotime($request['check_in'])); ?> - <?php echo date('M d, Y', strtotime($request['check_out'])); ?></span>
                                                        <span class="nights"><?php echo $request['total_nights']; ?> nights</span>
                                                    </div>
                                                </div>
                                                <div class="detail-item">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    <div>
                                                        <strong>Location</strong>
                                                        <span>
                                                            <?php 
                                                            echo htmlspecialchars($request['listing_location'] ?? $request['location']); 
                                                            ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="detail-item">
                                                    <i class="fas fa-rupee-sign"></i>
                                                    <div>
                                                        <strong>Price per Night</strong>
                                                        <span>₹<?php 
                                                            echo number_format($request['listing_price'] ?? $request['price'], 2); 
                                                        ?></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Action Buttons for Pending Requests -->
                                            <?php if ($request['status'] === 'pending'): ?>
                                                <div class="request-actions">
                                                    <form action="process_request.php" method="POST" class="action-form">
                                                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                        <input type="hidden" name="action" value="accept">
                                                        <button type="submit" class="btn-accept">
                                                            <i class="fas fa-check"></i> Accept Request
                                                        </button>
                                                    </form>
                                                    <form action="process_request.php" method="POST" class="action-form">
                                                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                        <input type="hidden" name="action" value="reject">
                                                        <button type="submit" class="btn-reject">
                                                            <i class="fas fa-times"></i> Decline
                                                        </button>
                                                    </form>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Request Timestamp -->
                                            <div class="request-timestamp">
                                                <i class="fas fa-clock"></i>
                                                Requested on <?php echo date('M d, Y g:i A', strtotime($request['request_date'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-requests">
                            <i class="fas fa-inbox"></i>
                            <h5>No Requests Yet</h5>
                            <p>When guests request to book your listings, they'll appear here.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all carousels
    var carousels = document.querySelectorAll('.carousel');
    carousels.forEach(function(carousel) {
        new bootstrap.Carousel(carousel, {
            interval: false // Disable auto-sliding
        });
    });
});
</script>
</body>
</html>