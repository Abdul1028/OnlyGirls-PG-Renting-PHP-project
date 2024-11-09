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

    // Get additional filter parameters
    $smoking = isset($_GET['smoking']) ? $conn->real_escape_string($_GET['smoking']) : '';
    $drinking = isset($_GET['drinking']) ? $conn->real_escape_string($_GET['drinking']) : '';
    $marital_status = isset($_GET['marital_status']) ? $conn->real_escape_string($_GET['marital_status']) : '';

    // Build query
    $query = "SELECT l.*, li.image, ld.Smoke, ld.Drink, ld.Married 
              FROM listing l 
              LEFT JOIN listing_images li ON l.id = li.listing_id
              LEFT JOIN login_details ld ON l.Username = ld.Username 
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

    if (!empty($smoking)) {
        $query .= " AND ld.Smoke = '$smoking'";
    }

    if (!empty($drinking)) {
        $query .= " AND ld.Drink = '$drinking'";
    }

    if (!empty($marital_status)) {
        $query .= " AND ld.Married = '$marital_status'";
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

        .host-preferences {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
        }

        .preference-badge {
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 15px;
            background: white;
            color: #666;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .preference-badge i {
            color: #ff4081;
            margin-right: 4px;
        }

        .host-link {
            color: #666;
            transition: color 0.2s ease;
        }

        .host-link:hover {
            color: #ff4081;
        }

        .user-profile-card {
            padding: 20px;
        }

        .profile-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto;
            display: block;
        }

        .default-profile-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-size: 3rem;
            color: #adb5bd;
        }

        .user-info {
            text-align: left;
        }

        .info-item {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-item i {
            color: #ff4081;
            width: 20px;
        }

        .preferences-section {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .preference-item {
            padding: 8px 15px;
            border-radius: 20px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .preference-item.active {
            background: #ffe0eb;
            color: #ff4081;
        }

        .preference-item i {
            color: #ff4081;
        }

        .host-link {
            color: #666;
            transition: color 0.2s ease;
        }

        .host-link:hover {
            color: #ff4081;
        }

        .user-profile-card {
            padding: 20px;
        }

        .profile-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto;
            display: block;
        }

        .default-profile-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-size: 3rem;
            color: #adb5bd;
        }

        .user-info {
            text-align: left;
        }

        .info-item {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-item i {
            color: #ff4081;
            width: 20px;
        }

        .compatibility-filters {
            background: #fff5f8;
            padding: 8px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(255, 64, 129, 0.1);
            width: 100%;
        }

        .filter-section-title {
            color: #ff4081;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ffe0eb;
        }

        .preference-filter {
            margin-bottom: 20px;
            width: 100%;
        }

        .preference-filter .form-label {
            color: #666;
            font-weight: 500;
            margin-bottom: 10px;
            display: block;
        }

        .preference-filter .form-label i {
            color: #ff4081;
        }

        .preference-buttons {
            display: flex;
            flex-wrap: nowrap;
            gap: 8px;
            width: 100%;
        }

        .preference-btn {
            background: white;
            border: 1px solid #ddd;
            color: #666;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            flex: 1;
            text-align: center;
            white-space: nowrap;
        }

        .preference-btn:hover {
            background: #fff5f8;
            border-color: #ff4081;
            color: #ff4081;
        }

        .btn-check:checked + .preference-btn {
            background: #ff4081;
            border-color: #ff4081;
            color: white;
        }

        .btn-check:focus + .preference-btn {
            box-shadow: 0 0 0 0.2rem rgba(255, 64, 129, 0.25);
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
                        
                        <!-- Compatibility Filters -->
                        <div class="compatibility-filters  mb-4">
                            <h6 class="filter-section-title">
                                <i class="fas fa-user-check me-2"></i>
                                Host Preferences
                            </h6>
                            
                            <!-- Smoking Preference -->
                            <div class="preference-filter mb-3">
                                <label class="form-label">
                                    <i class="fas fa-smoking me-2"></i>
                                    Smoking Preference
                                </label>
                                <div class="preference-buttons">
                                    <input type="radio" class="btn-check" name="smoking" id="smoking-any" value="" 
                                           <?php echo !isset($_GET['smoking']) || $_GET['smoking'] === '' ? 'checked' : ''; ?>>
                                    <label class="btn preference-btn" for="smoking-any">Any</label>

                                    <input type="radio" class="btn-check" name="smoking" id="smoking-yes" value="Yes"
                                           <?php echo isset($_GET['smoking']) && $_GET['smoking'] === 'Yes' ? 'checked' : ''; ?>>
                                    <label class="btn preference-btn" for="smoking-yes">Smoker</label>

                                    <input type="radio" class="btn-check" name="smoking" id="smoking-no" value="No"
                                           <?php echo isset($_GET['smoking']) && $_GET['smoking'] === 'No' ? 'checked' : ''; ?>>
                                    <label class="btn preference-btn" for="smoking-no">Non-Smoker</label>
                                </div>
                            </div>

                            <!-- Drinking Preference -->
                            <div class="preference-filter mb-3">
                                <label class="form-label">
                                    <i class="fas fa-wine-glass-alt me-2"></i>
                                    Drinking Preference
                                </label>
                                <div class="preference-buttons">
                                    <input type="radio" class="btn-check" name="drinking" id="drinking-any" value="" 
                                           <?php echo !isset($_GET['drinking']) || $_GET['drinking'] === '' ? 'checked' : ''; ?>>
                                    <label class="btn preference-btn" for="drinking-any">Any</label>

                                    <input type="radio" class="btn-check" name="drinking" id="drinking-yes" value="Yes"
                                           <?php echo isset($_GET['drinking']) && $_GET['drinking'] === 'Yes' ? 'checked' : ''; ?>>
                                    <label class="btn preference-btn" for="drinking-yes">Drinker</label>

                                    <input type="radio" class="btn-check" name="drinking" id="drinking-no" value="No"
                                           <?php echo isset($_GET['drinking']) && $_GET['drinking'] === 'No' ? 'checked' : ''; ?>>
                                    <label class="btn preference-btn" for="drinking-no">Non-Drinker</label>
                                </div>
                            </div>

                            <!-- Marital Status -->
                            <div class="preference-filter mb-3">
                                <label class="form-label">
                                    <i class="fas fa-heart me-2"></i>
                                    Marital Status
                                </label>
                                <div class="preference-buttons">
                                    <input type="radio" class="btn-check" name="marital_status" id="marital-any" value="" 
                                           <?php echo !isset($_GET['marital_status']) || $_GET['marital_status'] === '' ? 'checked' : ''; ?>>
                                    <label class="btn preference-btn" for="marital-any">Any</label>

                                    <input type="radio" class="btn-check" name="marital_status" id="marital-yes" value="Yes"
                                           <?php echo isset($_GET['marital_status']) && $_GET['marital_status'] === 'Yes' ? 'checked' : ''; ?>>
                                    <label class="btn preference-btn" for="marital-yes">Married</label>

                                    <input type="radio" class="btn-check" name="marital_status" id="marital-no" value="No"
                                           <?php echo isset($_GET['marital_status']) && $_GET['marital_status'] === 'No' ? 'checked' : ''; ?>>
                                    <label class="btn preference-btn" for="marital-no">Single</label>
                                </div>
                            </div>
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
                                            <a href="#" class="text-decoration-none host-link" 
                                               data-bs-toggle="modal" 
                                               data-bs-target="#userModal"
                                               data-username="<?php echo htmlspecialchars($room['Username']); ?>">
                                                <?php echo htmlspecialchars($room['Username']); ?>
                                            </a>
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

    <!-- User Profile Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="userModalLabel">Host Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" id="userProfileContent">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const userModal = document.getElementById('userModal');
        const profileContent = document.getElementById('userProfileContent');

        userModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const username = button.getAttribute('data-username');
            
            // Clear previous content and show loading spinner
            profileContent.innerHTML = `
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>`;

            // Fetch user profile data
            fetch(`get_user_profile.php?username=${encodeURIComponent(username)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const user = data.data;
                        const profileHtml = `
                            <div class="user-profile-card">
                                ${user.profile_image ? 
                                    `<img src="data:image/jpeg;base64,${user.profile_image}" 
                                          class="profile-image mb-3" 
                                          alt="Profile Picture">` : 
                                    `<div class="default-profile-image mb-3">
                                        <i class="fas fa-user-circle"></i>
                                     </div>`
                                }
                                <h4 class="mb-3">${user.Name}</h4>
                                <div class="user-info">
                                    <div class="info-item">
                                        <i class="fas fa-envelope"></i>
                                        <span>${user.Email}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-phone"></i>
                                        <span>${user.Phone_Number}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-home"></i>
                                        <span>${user.Home_Town}</span>
                                    </div>
                                    <div class="preferences-section mt-3">
                                        <div class="preference-item ${user.Smoke === 'yes' ? 'active' : ''}">
                                            <i class="fas ${user.Smoke === 'yes' ? 'fa-smoking' : 'fa-smoking-ban'}"></i>
                                            <span>${user.Smoke === 'yes' ? 'Smoker' : 'Non-Smoker'}</span>
                                        </div>
                                        <div class="preference-item ${user.Drink === 'yes' ? 'active' : ''}">
                                            <i class="fas ${user.Drink === 'Yes' ? 'fa-wine-glass' : 'fa-ban'}"></i>
                                            <span>${user.Drink === 'yes' ? 'Drinker' : 'Non-Drinker'}</span>
                                        </div>
                                        <div class="preference-item">
                                            <i class="fas ${user.Married === 'yes' ? 'fa-rings-wedding' : 'fa-user'}"></i>
                                            <span>${user.Married === 'yes' ? 'Married' : 'Single'}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                        profileContent.innerHTML = profileHtml;
                    } else {
                        profileContent.innerHTML = `
                            <div class="alert alert-danger">
                                Error loading profile: ${data.error}
                            </div>`;
                    }
                })
                .catch(error => {
                    profileContent.innerHTML = `
                        <div class="alert alert-danger">
                            Error loading profile. Please try again.
                        </div>`;
                });
        });
    });
    </script>
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