<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>She Shares Vacation Rentals</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Animate.css for animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="index.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

<!-- Add this right after your <body> tag -->
<?php
// Check for success parameter in URL
if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo '<div class="alert alert-success alert-dismissible fade show text-center" 
             role="alert" 
             style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 1050; min-width: 300px; background-color: #ff80ab; color: white; border: none;">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Success!</strong> Your room has been listed successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <script>
            setTimeout(function() {
                document.querySelector(".alert").remove();
            }, 5000); // Alert will disappear after 5 seconds
        </script>';
}

// Check for error parameter in URL
if (isset($_GET['error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show text-center" 
             role="alert" 
             style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 1050; min-width: 300px;">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Error!</strong> ' . htmlspecialchars($_GET['error']) . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <script>
            setTimeout(function() {
                document.querySelector(".alert").remove();
            }, 5000); // Alert will disappear after 5 seconds
        </script>';
}
?>

    <?php if (isset($_SESSION['registration_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show text-center" 
             role="alert" 
             style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 1050; min-width: 300px; background-color: #ff80ab; color: white; border: none;">
            <strong>Welcome to She Shares!</strong> Your registration was successful.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <script>
            setTimeout(function() {
                document.querySelector('.alert').remove();
            }, 5000); // Alert will disappear after 5 seconds
        </script>
        <?php unset($_SESSION['registration_success']); ?>
    <?php endif; ?>
    
    <!-- Navbar -->
    <nav style="background-color: #f8bbd0; position: sticky; top: 0; z-index: 1000; transition: all 0.3s ease" class="navbar navbar-expand-lg navbar-light animate__animated animate__fadeIn mb-3">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Only Girlsss</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span> 
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Safety</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Adventure</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Community</a>
                    </li>
                </ul>
                
                <div class="nav-auth-buttons">
                    <?php
                    session_start();
                    if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
                        echo '<a href="/She-Shares-Vacation-Rentals/login-register/login.php" class="btn btn-outline-light me-2">Login</a>';
                        echo '<a href="/She-Shares-Vacation-Rentals/login-register/registration.php" class="btn btn-light">Register</a>';
                    } else {
                        echo '<a href="\\She-Shares-Vacation-Rentals\\frontend\\profile.php" class="btn btn-outline-light me-2">Profile</a>';
                        echo '<a href="\\She-Shares-Vacation-Rentals\\login-register\\logout.php" class="btn btn-light">Logout</a>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Welcome Section -->



    <!-- Welcome Section -->
<section class="welcome-section py-5 animate__animated animate__fadeIn" 
    style="background: linear-gradient(135deg, #ff80ab 0%, #f8bbd0 100%); color: white; padding: 4rem 0 !important; margin-bottom: 2rem; border-radius: 0 0 50px 50px; box-shadow: 0 4px 20px rgba(255, 128, 171, 0.3);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <?php
                if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
                    echo '
                    <h1 class="display-4 mb-4" style="font-family: \'Dancing Script\', cursive; color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.1);">
                        Welcome to She Shares
                    </h1>
                    ';
                } else {
                    $username = $_SESSION['username'];
                    echo "
                    <h1 class='display-4 mb-4' style='font-family: \"Dancing Script\", cursive; color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.1);'>
                        Welcome back, $username!
                    </h1>
                    ";
                }
                ?>
                <p class="lead mb-4" style="font-size: 1.25rem; font-weight: 300; text-shadow: 1px 1px 2px rgba(0,0,0,0.1);">
                    Your safe and secure PG accommodation platform exclusively for women
                </p>
                <div class="row justify-content-center mb-4">
                    <div class="col-md-4 mb-3">
                        <div style="background: rgba(255,255,255,0.2); padding: 1rem; border-radius: 15px; backdrop-filter: blur(5px);">
                            <i class="fas fa-shield-alt mb-2" style="font-size: 2rem;"></i>
                            <h5>Safe & Secure</h5>
                            <p class="small">Verified accommodations for women</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div style="background: rgba(255,255,255,0.2); padding: 1rem; border-radius: 15px; backdrop-filter: blur(5px);">
                            <i class="fas fa-female mb-2" style="font-size: 2rem;"></i>
                            <h5>Women Only</h5>
                            <p class="small">Exclusive female community</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div style="background: rgba(255,255,255,0.2); padding: 1rem; border-radius: 15px; backdrop-filter: blur(5px);">
                            <i class="fas fa-home mb-2" style="font-size: 2rem;"></i>
                            <h5>Comfortable Stay</h5>
                            <p class="small">Handpicked PG accommodations</p>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="#" class="btn btn-light btn-lg me-3" 
                        style="background: white; color: #ff80ab; border: none; padding: 0.8rem 2rem; border-radius: 25px; font-weight: 600; transition: all 0.3s ease;"
                        onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 6px 20px rgba(255,255,255,0.3)';"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        Learn More
                    </a>
                    <a href="#" class="btn btn-outline-light btn-lg" 
                        style="border: 2px solid white; padding: 0.8rem 2rem; border-radius: 25px; font-weight: 600; transition: all 0.3s ease;"
                        onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 6px 20px rgba(255,255,255,0.3)';"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        Contact Us
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- Options Section -->
    <section class="options-section animate__animated animate__fadeIn">
        <div class="container py-4">
            <div class="row justify-content-center g-4">
                <div class="col-md-6 col-lg-5">
                    <div style="background-color: #f8bbd0; padding: 2rem; border-radius: 20px; box-shadow: 0 4px 15px var(--shadow-color); border: 4px solid white;" class="option-card-custom animate__animated animate__fadeInLeft" onclick="showSection('sharing')"
                    onmouseover="this.style.transform='scale(1.5)'; this.style.boxShadow='0 12px 30px rgba(255, 128, 171, 0.5)';"
                    onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(255, 128, 171, 0.2)';"
                    >
                        <i class="fas fa-home"></i>
                        <h3>Share Your Room</h3>
                        <p>List your space for other travelers</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-5">
                    <div  style="background-color: #f8bbd0; border: 4px solid white; padding: 2rem; border-radius: 20px;" class="option-card-custom animate__animated animate__fadeInRight" onclick="showSection('renting')"
                    
                    onmouseover="this.style.transform='scale(1.5)'; this.style.boxShadow='0 12px 30px rgba(255, 128, 171, 0.5)';"
                    onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(255, 128, 171, 0.2)';"
                
                    >
                        <i class="fas fa-search"></i>
                        <h3>Find a Room</h3>
                        <p>Discover your perfect stay</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sharing Section -->
    <section class="output-section" id="sharing-output">
        <div class="container">
            <h2 class="text-center mb-5 animate__animated animate__fadeIn">Share Your Room</h2>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="form-container animate__animated animate__fadeInUp">
                        <form class="listing-form" id="listing-form" action="listing.php" enctype="multipart/form-data" method="POST">
                            <div class="mb-4">
                                <label class="form-label">Room Title</label>
                                <input type="text" class="form-control" name="room-title" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="room-description" rows="4" required></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Location</label>
                                    <select class="form-select" name="room-location" required>
                                        <option value="">Select location</option>
                                        <option value="Mumbai">Mumbai</option>
                                        <option value="Pune">Pune</option>
                                        <option value="Delhi">Delhi</option>
                                        <option value="Kolkata">Kolkata</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Price per Night</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" class="form-control" name="room-price" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Available From</label>
                                    <input type="date" class="form-control" name="check-inn" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Available To</label>
                                    <input type="date" class="form-control" name="check-outt" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Room Images (Max 3 images)</label>
                                <input type="file" 
                                       class="form-control" 
                                       name="room-images[]" 
                                       accept="image/*" 
                                       id="room-images" 
                                       multiple 
                                       required
                                       max="3"
                                       data-max-size="5242880"
                                       style="background-color: #f096b7; border: none; border-radius: 15px;">
                                <small class="text-muted">Select up to 3 images (Max 5MB each, JPG/PNG/GIF only)</small>
                                <div id="imageCount" class="mt-2 text-muted"></div>
                                <div id="imagePreviewContainer" class="mt-3 row g-2"></div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg">List Your Room</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Renting Section -->
    <section class="output-section" id="renting-output">
        <div class="container">
            <h2 class="text-center mb-5 animate__animated animate__fadeIn">Find Your Perfect Stay</h2>
            
            <!-- Search Filters -->
            <div class="row justify-content-center mb-4">
                <div class="col-lg-10">
                    <div class="card shadow-sm animate__animated animate__fadeInUp">
                        <div class="card-body">
                            <form action="search_results.php" method="GET">
                                <div class="row g-3">
                                    <!-- Location -->
                                    <div class="col-md-6 col-lg-3">
                                        <label class="form-label">Location</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                            <select class="form-select" name="location" required>
                                                <option value="">Select location</option>
                                                <option value="Mumbai">Mumbai</option>
                                                <option value="Delhi">Delhi</option>
                                                <option value="Kolkata">Kolkata</option>
                                                <option value="Pune">Pune</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Price Range -->
                                    <div class="col-md-6 col-lg-3">
                                        <label class="form-label">Price Range (₹)</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="min_price" placeholder="Min">
                                            <input type="number" class="form-control" name="max_price" placeholder="Max">
                                        </div>
                                    </div>

                                    <!-- Check In -->
                                    <div class="col-md-6 col-lg-3">
                                        <label class="form-label">Check In</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                            <input type="date" class="form-control" name="check_in" required>
                                        </div>
                                    </div>

                                    <!-- Check Out -->
                                    <div class="col-md-6 col-lg-3">
                                        <label class="form-label">Check Out</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                            <input type="date" class="form-control" name="check_out" required>
                                        </div>
                                    </div>

                                    <!-- Search Button -->
                                    <div class="col-12">
                                        <button type="submit" class="btn w-100" 
                                                style="background: linear-gradient(45deg, #ff4081, #ff80ab); color: white; border: none;">
                                            <i class="fas fa-search me-2"></i>Search Rooms
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        function showSection(sectionId) {
            // Add fade out animation to all sections
            document.querySelectorAll('.output-section').forEach(section => {
                section.style.display = 'none';
                section.classList.remove('animate__fadeIn');
            });

            // Show and animate the selected section
            const selectedSection = document.getElementById(sectionId + '-output');
            selectedSection.style.display = 'block';
            selectedSection.classList.add('animate__animated', 'animate__fadeIn');

            // Clear form and previews when switching sections
            if (sectionId === 'sharing') {
                document.getElementById('listing-form').reset();
                document.getElementById('imagePreviewContainer').innerHTML = '';
                document.getElementById('imageCount').textContent = '';
            }
        }

        // Show renting section by default
        window.onload = function() {
            showSection('renting');
        };

        document.getElementById('room-images').addEventListener('change', function() {
            const maxFiles = 3;
            const files = this.files;
            const previewContainer = document.getElementById('imagePreviewContainer');
            const imageCountDiv = document.getElementById('imageCount');
            
            // Clear previous previews
            previewContainer.innerHTML = '';
            
            // Check number of files
            if (files.length > maxFiles) {
                alert(`Please select a maximum of ${maxFiles} images`);
                this.value = '';
                imageCountDiv.textContent = `0/${maxFiles} images selected`;
                return;
            }

            // Update image count
            imageCountDiv.textContent = `${files.length}/${maxFiles} images selected - ${maxFiles - files.length} more allowed`;
            
            // Validate and preview each file
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const fileType = file.type;
                const fileSize = file.size / 1024 / 1024; // Convert to MB

                // Validate file type
                if (!['image/jpeg', 'image/png', 'image/gif'].includes(fileType)) {
                    alert('Please upload only JPG, PNG, or GIF images');
                    this.value = '';
                    previewContainer.innerHTML = '';
                    imageCountDiv.textContent = `0/${maxFiles} images selected`;
                    return;
                }

                // Validate file size
                if (fileSize > 5) {
                    alert('Each image must be less than 5MB');
                    this.value = '';
                    previewContainer.innerHTML = '';
                    imageCountDiv.textContent = `0/${maxFiles} images selected`;
                    return;
                }

                // Create preview
                const col = document.createElement('div');
                col.className = 'col-md-4';
                
                const previewWrapper = document.createElement('div');
                previewWrapper.className = 'position-relative';
                
                const preview = document.createElement('img');
                preview.className = 'img-thumbnail';
                preview.style.width = '100%';
                preview.style.height = '150px';
                preview.style.objectFit = 'cover';
                
                // Create remove button
                const removeBtn = document.createElement('button');
                removeBtn.className = 'btn btn-danger btn-sm position-absolute top-0 end-0 m-1';
                removeBtn.innerHTML = '×';
                removeBtn.onclick = function(e) {
                    e.preventDefault();
                    // Remove this preview
                    col.remove();
                    
                    // Create new FileList without this file
                    const dt = new DataTransfer();
                    const input = document.getElementById('room-images');
                    const { files } = input;
                    
                    for (let i = 0; i < files.length; i++) {
                        const file = files[i];
                        if (file !== files[i]) {
                            dt.items.add(file);
                        }
                    }
                    
                    input.files = dt.files;
                    
                    // Update count
                    imageCountDiv.textContent = `${dt.files.length}/${maxFiles} images selected - ${maxFiles - dt.files.length} more allowed`;
                };

                // Read and display the image
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);

                previewWrapper.appendChild(preview);
                previewWrapper.appendChild(removeBtn);
                col.appendChild(previewWrapper);
                previewContainer.appendChild(col);
            }
        });

        document.getElementById('search-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading spinner
            document.getElementById('loading').style.display = 'block';
            document.getElementById('search-results').innerHTML = '';
            
            // Get form data
            const formData = new FormData(this);
            
            // Make API call
            fetch('search.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Hide loading spinner
                document.getElementById('loading').style.display = 'none';
                
                // Display results
                const resultsContainer = document.getElementById('search-results');
                
                if (data.length === 0) {
                    resultsContainer.innerHTML = `
                        <div class="col-12 text-center">
                            <div class="alert alert-info">
                                No rooms found matching your criteria.
                            </div>
                        </div>
                    `;
                    return;
                }
                
                data.forEach(room => {
                    const roomCard = `
                        <div class="col-md-6 col-lg-4 animate__animated animate__fadeIn">
                            <div class="card h-100 shadow-sm">
                                <img src="data:image/jpeg;base64,${room.thumbnail}" 
                                     class="card-img-top" 
                                     alt="${room.room_title}"
                                     style="height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title">${room.room_title}</h5>
                                    <p class="card-text text-muted">${room.room_location}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">₹${room.room_price}/night</h6>
                                        <a href="room_details.php?id=${room.id}" 
                                           class="btn btn-sm"
                                           style="background: linear-gradient(45deg, #ff4081, #ff80ab); color: white;">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    resultsContainer.insertAdjacentHTML('beforeend', roomCard);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('loading').style.display = 'none';
                document.getElementById('search-results').innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-danger">
                            An error occurred while searching. Please try again.
                        </div>
                    </div>
                `;
            });
        });
    </script>
</body>

</html>