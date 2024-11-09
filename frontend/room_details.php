<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

try {
    // Database connection
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $dbname = 'roomrental';

    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        throw new Exception("Database Connection failed: " . $conn->connect_error);
    }

    // Get room ID
    $room_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if (!$room_id) {
        throw new Exception("Room ID not provided");
    }

    // Get room details
    $query = "SELECT * FROM listing WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $room = $result->fetch_assoc();

    if (!$room) {
        throw new Exception("Room not found");
    }

    // Get room images from listing_images table
    $query = "SELECT image FROM listing_images WHERE listing_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $images_result = $stmt->get_result();
    $images = [];
    while ($image = $images_result->fetch_assoc()) {
        $images[] = $image['image'];
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($room['Room_Title']); ?> - She Shares</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fce4ec;
            font-family: 'Poppins', sans-serif;
        }

        .breadcrumb-section {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .breadcrumb-item a {
            color: #ff4081;
            text-decoration: none;
        }

        .gallery-section {
            background: white;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .main-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 20px;
        }

        .thumbnail-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
            padding: 10px 0;
        }

        .thumbnail {
            width: 100%;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s;
            border: 3px solid transparent;
        }

        .thumbnail:hover {
            transform: scale(1.05);
            border-color: #ff4081;
        }

        .details-section {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .room-title {
            color: #333;
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .feature-badge {
            background: linear-gradient(45deg, #ff4081, #ff80ab);
            color: white;
            padding: 8px 15px;
            border-radius: 25px;
            font-size: 0.9rem;
            margin-right: 10px;
            margin-bottom: 10px;
            display: inline-block;
        }

        .description-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
            margin: 20px 0;
        }

        .booking-card {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            position: sticky;
            top: 20px;
        }

        .price-display {
            background: linear-gradient(45deg, #ff4081, #ff80ab);
            color: white;
            padding: 15px;
            border-radius: 15px;
            text-align: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        .book-btn {
            background: linear-gradient(45deg, #ff4081, #ff80ab);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 10px;
            width: 100%;
            font-size: 1.1rem;
            transition: transform 0.2s;
        }

        .book-btn:hover {
            transform: scale(1.02);
        }

        .host-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .host-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #ff4081;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .amenity-icon {
            color: #ff4081;
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .date-picker {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 10px;
        }

        .availability-calendar {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .custom-alert-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .custom-alert-box {
            background: #fff5f8;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(255, 64, 129, 0.2);
            animation: slideIn 0.3s ease-out;
        }

        .alert-icon {
            font-size: 3rem;
            color: #ff4081;
            margin-bottom: 15px;
        }

        .alert-message {
            color: #ff4081;
            font-size: 1.2rem;
            font-weight: 500;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <!-- Custom Alert Overlay -->
    <div id="customAlert" class="custom-alert-overlay" style="display: none;">
        <div class="custom-alert-box">
            <i class="fas fa-check-circle alert-icon"></i>
            <div class="alert-message">Booking request has been sent successfully!</div>
        </div>
    </div>

    <div class="container mt-5 mb-5">
        <!-- Breadcrumb -->
        <div class="breadcrumb-section mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="javascript:history.back()">Search Results</a></li>
                    <li class="breadcrumb-item active"><?php echo htmlspecialchars($room['Room_Title']); ?></li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <!-- Gallery Section -->
            <div class="col-12 mb-4">
                <div class="gallery-section">
                    <?php if (!empty($images)): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($images[0]); ?>" 
                             id="mainImage" 
                             class="main-image" 
                             alt="Room Main View">
                        
                        <div class="thumbnail-container">
                            <?php foreach ($images as $index => $image): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($image); ?>" 
                                     class="thumbnail" 
                                     onclick="changeMainImage(this.src)"
                                     alt="Room View <?php echo $index + 1; ?>">
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center p-5">
                            <i class="fas fa-image fa-3x text-muted"></i>
                            <p class="mt-2">No images available</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Details Section -->
            <div class="col-md-8">
                <div class="details-section">
                    <h1 class="room-title"><?php echo htmlspecialchars($room['Room_Title']); ?></h1>
                    
                    <div class="features mb-4">
                        <span class="feature-badge">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            <?php echo htmlspecialchars($room['Location']); ?>
                        </span>
                        <span class="feature-badge">
                            <i class="fas fa-calendar me-2"></i>
                            Available Now
                        </span>
                    </div>

                    <div class="description-section">
                        <h4 class="mb-3">About this space</h4>
                        <p class="text-muted">
                            <?php echo nl2br(htmlspecialchars($room['Room_Description'])); ?>
                        </p>
                    </div>

                    <div class="availability-details mt-4">
                        <h4 class="mb-3">Availability</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p><i class="fas fa-calendar-check amenity-icon"></i>
                                   From: <?php echo date('F j, Y', strtotime($room['Date_From'])); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><i class="fas fa-calendar-times amenity-icon"></i>
                                   To: <?php echo date('F j, Y', strtotime($room['Date_To'])); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Section -->
            <div class="col-md-4">
                <div class="booking-card">
                    <div class="price-display">
                        ₹<?php echo number_format($room['Price'], 2); ?> <span class="fs-6">/ night</span>
                    </div>

                    <?php if(isset($_SESSION['username'])): ?>
                        <form action="process_booking.php" method="POST">
                            <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">Check In</label>
                                <input type="date" 
                                       class="form-control date-picker" 
                                       name="check_in" 
                                       required
                                       min="<?php echo $room['Date_From']; ?>"
                                       max="<?php echo $room['Date_To']; ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Check Out</label>
                                <input type="date" 
                                       class="form-control date-picker" 
                                       name="check_out" 
                                       required
                                       min="<?php echo $room['Date_From']; ?>"
                                       max="<?php echo $room['Date_To']; ?>">
                            </div>

                            <?php if(isset($_GET['error'])): ?>
                                <div class="alert alert-danger mb-3">
                                    <?php echo htmlspecialchars($_GET['error']); ?>
                                </div>
                            <?php endif; ?>

                            <?php if(isset($_GET['success'])): ?>
                                <div class="alert alert-success mb-3">
                                    Booking request sent successfully!
                                </div>
                            <?php endif; ?>

                            <button type="submit" class="book-btn">
                                <i class="fas fa-calendar-check me-2"></i>Book Now
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            Please <a href="login.php" class="alert-link">login</a> to book this room.
                        </div>
                    <?php endif; ?>

                    <div class="host-section">
                        <div class="d-flex align-items-center">
                            <div class="host-avatar me-3">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Hosted by</h5>
                                <p class="mb-0"><?php echo htmlspecialchars($room['Username']); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="availability-calendar">
                        <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Availability</h6>
                        <p class="mb-0 small text-muted">
                            This property is available between<br>
                            <?php echo date('M j, Y', strtotime($room['Date_From'])); ?> - 
                            <?php echo date('M j, Y', strtotime($room['Date_To'])); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function changeMainImage(src) {
            document.getElementById('mainImage').src = src;
        }

        // Add date validation
        document.addEventListener('DOMContentLoaded', function() {
            const checkInInput = document.querySelector('input[name="check_in"]');
            const checkOutInput = document.querySelector('input[name="check_out"]');
            
            checkInInput.addEventListener('change', function() {
                checkOutInput.min = this.value;
                if (checkOutInput.value && checkOutInput.value < this.value) {
                    checkOutInput.value = this.value;
                }
            });
        });

        // Check for success parameter and session flag
        <?php if(isset($_GET['success']) && isset($_SESSION['show_booking_alert'])): ?>
            // Show the alert
            document.getElementById('customAlert').style.display = 'flex';
            
            // Hide after 2 seconds
            setTimeout(function() {
                document.getElementById('customAlert').style.display = 'none';
            }, 2000);
            
            <?php 
            // Clear the session flag
            unset($_SESSION['show_booking_alert']); 
            ?>
        <?php endif; ?>
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
    $conn->close();
} catch (Exception $e) {
    ?>
    <div class="container mt-5">
        <div class="alert alert-danger">
            <h4 class="alert-heading">Error Occurred!</h4>
            <p><?php echo $e->getMessage(); ?></p>
            <hr>
            <p class="mb-0">Please try again or contact support if the problem persists.</p>
        </div>
        <div class="text-center mt-3">
            <a href="index.php" class="btn btn-primary">Back to Home</a>
        </div>
    </div>
    <?php
}
?> 