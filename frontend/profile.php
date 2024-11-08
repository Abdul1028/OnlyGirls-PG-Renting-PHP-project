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

        <!-- Listings and Bookings -->
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
                    <?php if (!empty($room_booking)) : ?>
                        <div class="row g-4">
                            <?php foreach ($room_booking as $booking) : ?>
                                <div class="col-md-6">
                                    <div class="card listing-card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($booking['title']); ?></h5>
                                            <p class="card-text"><?php echo htmlspecialchars($booking['description']); ?></p>
                                            <ul class="list-unstyled">
                                                <li><i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($booking['location']); ?></li>
                                                <li><i class="fas fa-money-bill me-2"></i>₹<?php echo htmlspecialchars($booking['price']); ?>/night</li>
                                                <li><i class="fas fa-calendar me-2"></i><?php echo htmlspecialchars($booking['Number_of_days']); ?> days</li>
                                            </ul>
                                            <div class="alert alert-success mb-0">
                                                Total Amount: ₹<?php echo htmlspecialchars($booking['Total']); ?>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-white">
                                            <small class="text-muted">
                                                Booked: <?php echo date('M d', strtotime($booking['available_dates_from'])); ?> - 
                                                <?php echo date('M d, Y', strtotime($booking['available_dates_to'])); ?>
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