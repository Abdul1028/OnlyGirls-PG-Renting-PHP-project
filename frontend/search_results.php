<?php
// Enable error reporting
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

    // Get search parameters
    $location = isset($_GET['location']) ? $conn->real_escape_string($_GET['location']) : '';
    $min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (int)$_GET['min_price'] : 0;
    $max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (int)$_GET['max_price'] : 0;
    $check_in = isset($_GET['check_in']) ? $conn->real_escape_string($_GET['check_in']) : '';
    $check_out = isset($_GET['check_out']) ? $conn->real_escape_string($_GET['check_out']) : '';
    $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : '';

    // Build query
    $query = "SELECT l.*, li.image 
              FROM listing l 
              LEFT JOIN listing_images li ON l.id = li.listing_id 
              WHERE 1=1";

    if (!empty($location)) {
        $query .= " AND l.Location LIKE '%$location%'";
    }

    if ($min_price > 0) {
        $query .= " AND l.Price >= $min_price";
    }

    if ($max_price > 0) {
        $query .= " AND l.Price <= $max_price";
    }

    if (!empty($check_in) && !empty($check_out)) {
        $query .= " AND (('$check_in' BETWEEN l.Date_From AND l.Date_To) 
                   OR ('$check_out' BETWEEN l.Date_From AND l.Date_To))";
    }

    // Group by listing id to get one image per listing
    $query .= " GROUP BY l.id";

    // Add sorting
    if ($sort_by == 'price_asc') {
        $query .= " ORDER BY l.Price ASC";
    } elseif ($sort_by == 'price_desc') {
        $query .= " ORDER BY l.Price DESC";
    }

    // Add this debug line to check the query
    // echo "<p>Debug Query: " . $query . "</p>";

    // Execute query
    $result = $conn->query($query);

    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - She Shares</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fce4ec;
            font-family: 'Poppins', sans-serif;
        }
        
        .search-summary {
            background: linear-gradient(45deg, #ff4081, #ff80ab);
            color: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(255, 64, 129, 0.2);
        }

        .room-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(255, 64, 129, 0.2);
        }

        .room-image {
            height: 200px;
            object-fit: cover;
            background-color: #f8f9fa;
        }

        .room-image.no-image {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #adb5bd;
        }

        .room-details {
            padding: 20px;
        }

        .price-badge {
            background: linear-gradient(45deg, #ff4081, #ff80ab);
            color: white;
            padding: 8px 15px;
            border-radius: 25px;
            font-weight: 600;
        }

        .location-text {
            color: #666;
            font-size: 0.9rem;
        }

        .amenities {
            margin: 15px 0;
            display: flex;
            gap: 15px;
        }

        .amenity {
            font-size: 0.85rem;
            color: #666;
        }

        .view-btn {
            background: linear-gradient(45deg, #ff4081, #ff80ab);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 25px;
            transition: transform 0.2s ease;
        }

        .view-btn:hover {
            transform: scale(1.05);
            color: white;
        }

        .filters-section {
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            position: sticky;
            top: 20px;
        }

        .section-title {
            color: #ff4081;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .no-results {
            text-align: center;
            padding: 50px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .no-results i {
            font-size: 3rem;
            color: #ff4081;
            margin-bottom: 20px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #ff4081;
            box-shadow: 0 0 0 0.2rem rgba(255, 64, 129, 0.25);
        }

        .form-control::placeholder {
            color: #adb5bd;
        }

        .price-range-inputs {
            display: flex;
            gap: 10px;
        }

        .price-range-inputs input {
            width: 50%;
        }
    </style>
</head>
<body>
    <div class="container mt-5 mb-5">
        <!-- Search Summary -->
        <div class="search-summary">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-2">Search Results</h4>
                    <p class="mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($location); ?>
                        <?php if ($min_price > 0 || $max_price > 0): ?>
                            <span class="mx-2">|</span>
                            <i class="fas fa-rupee-sign me-2"></i>₹<?php echo $min_price; ?> - ₹<?php echo $max_price; ?>
                        <?php endif; ?>
                        <?php if (!empty($check_in) && !empty($check_out)): ?>
                            <span class="mx-2">|</span>
                            <i class="fas fa-calendar me-2"></i><?php echo $check_in; ?> to <?php echo $check_out; ?>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="index.php" class="btn btn-light">
                        <i class="fas fa-search me-2"></i>New Search
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Filters Section -->
            <div class="col-md-3">
                <div class="filters-section">
                    <h5 class="section-title">Filters</h5>
                    <form method="GET" action="search_results.php">
                        <!-- Preserve existing search parameters -->
                        <input type="hidden" name="location" value="<?php echo htmlspecialchars($location); ?>">
                        <input type="hidden" name="check_in" value="<?php echo htmlspecialchars($check_in); ?>">
                        <input type="hidden" name="check_out" value="<?php echo htmlspecialchars($check_out); ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Price Range</label>
                            <div class="d-flex gap-2">
                                <input type="number" 
                                       class="form-control" 
                                       name="min_price" 
                                       placeholder="Min"
                                       value="<?php echo $min_price ?: ''; ?>">
                                <input type="number" 
                                       class="form-control" 
                                       name="max_price" 
                                       placeholder="Max"
                                       value="<?php echo $max_price ?: ''; ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Sort By</label>
                            <select class="form-select" name="sort_by">
                                <option value="">Select sorting</option>
                                <option value="price_asc" <?php echo isset($_GET['sort_by']) && $_GET['sort_by'] == 'price_asc' ? 'selected' : ''; ?>>
                                    Price: Low to High
                                </option>
                                <option value="price_desc" <?php echo isset($_GET['sort_by']) && $_GET['sort_by'] == 'price_desc' ? 'selected' : ''; ?>>
                                    Price: High to Low
                                </option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn view-btn w-100">Apply Filters</button>
                    </form>
                </div>
            </div>

            <!-- Results Grid -->
            <div class="col-md-9">
                <div class="row g-4">
                    <?php 
                    if ($result && $result->num_rows > 0) {
                        while ($room = $result->fetch_assoc()) {
                    ?>
                        <div class="col-md-6">
                            <div class="room-card h-100">
                                <?php if (!empty($room['image'])): ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($room['image']); ?>" 
                                         class="room-image w-100" 
                                         alt="<?php echo htmlspecialchars($room['Room_Title']); ?>">
                                <?php else: ?>
                                    <!-- Default image when no image is available -->
                                    <div class="room-image w-100 d-flex align-items-center justify-content-center bg-light">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="room-details">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="mb-0"><?php echo htmlspecialchars($room['Room_Title']); ?></h5>
                                        <span class="price-badge">₹<?php echo number_format($room['Price']); ?></span>
                                    </div>
                                    
                                    <p class="location-text mb-2">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        <?php echo htmlspecialchars($room['Location']); ?>
                                    </p>
                                    
                                    <div class="amenities">
                                        <span class="amenity">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?php echo date('M j', strtotime($room['Date_From'])); ?> - 
                                            <?php echo date('M j', strtotime($room['Date_To'])); ?>
                                        </span>
                                    </div>
                                    
                                    <p class="description mb-3">
                                        <?php echo substr(htmlspecialchars($room['Room_Description']), 0, 100); ?>...
                                    </p>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="host">
                                            <i class="fas fa-user-circle me-1"></i>
                                            <?php echo htmlspecialchars($room['Username']); ?>
                                        </div>
                                        <a href="room_details.php?id=<?php echo $room['id']; ?>" 
                                           class="view-btn text-decoration-none">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php 
                        }
                    } else {
                    ?>
                        <div class="col-12">
                            <div class="no-results">
                                <i class="fas fa-search mb-3"></i>
                                <h4>No Rooms Found</h4>
                                <p class="text-muted">Try adjusting your search criteria</p>
                                <a href="index.php" class="btn view-btn">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Search
                                </a>
                            </div>
                        </div>
                    <?php 
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

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
            <a href="index.php" class="btn btn-primary">Back to Search</a>
        </div>
    </div>
    <?php
}
?> 