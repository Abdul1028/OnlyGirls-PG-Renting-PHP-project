<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>She Shares Vacation Rentals</title>
    <link rel="stylesheet" href="index.css">
    <style>
        /* Reset some default styles */
    </style>
</head>

<body>
    <header class="header">
        <h1>She Shares Vacation Rentals</h1>
        <?php
        session_start();

        // Check if the user is logged in
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
            // The user is not logged in, display login and register buttons
            echo "<div class='auth-buttons'>";
            echo "<a href='/login-register/login.php'>Login</a>";
            echo "<a href='/login-register/registration.php'>Register</a>";
            echo "</div>";
        } else {
            // The user is logged in, do not display the buttons
            // You may also want to display a logout button or user profile link instead
            echo "<div class='auth-buttons'>";
            echo "<a href='\\frontend\\profile.php'>Profile</a>"; // Replace with your profile page link
            echo "<a href='\\login-register\\logout.php'>Logout</a>"; // Replace with your logout page link
            echo "</div>";
        }
        ?>

        <!-- Hamburger menu -->
        <div class="hamburger" id="hamburger" onclick="toggleMenu()">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </header>

    <!-- Slide-down menu -->
    <div class="menu" id="menu">
        <a href="#">Home</a>
        <a href="#">Welcome</a>
        <a href="#">Safety</a>
        <a href="#">Adventure</a>
        <a href="#">Community</a>
    </div>
    <section class="option-section">
        <?php

        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
            // The user is not logged in, display login and register buttons

        } else {
            $usernaem = $_SESSION['username'];
            echo "<h2>Welcome $usernaem</h2>";
        }
        ?>
    </section>
    <section class="options-section">
        <a href="#" class="option-button" onclick="showSection('sharing')">Sharing Your Room</a>
        <a href="#" class="option-button" onclick="showSection('renting')">Renting a Room</a>
    </section>

    <!-- Output sections -->
    <section class="output-section" id="sharing-output">
        <h2>Sharing Your Room</h2>
        <!-- Form for listing rooms -->
        <form class="listing-form" id="listing-form" action="listing.php" enctype="multipart/form-data" method="POST">
            <!-- Input for room title -->
            <div class="form-group">
                <label for="room-title">Room Title:</label>
                <input type="text" id="room-title" name="room-title" required class="form-input">
            </div>

            <!-- Input for room description -->
            <div class="form-group">
                <label for="room-description">Room Description:</label>
                <textarea id="room-description" name="room-description" required class="form-input"></textarea>
            </div>

            <!-- Dropdown for room location -->
            <div class="form-group">
                <label for="room-location">Location:</label>
                <select id="room-location" name="room-location" required class="form-select">
                    <option value="">Select location</option>
                    <option value="Mumbai">Mumbai</option>
                    <option value="Pune">Pune</option>
                    <option value="Delhi">Delhi</option>
                    <option value="Kolkata">Kolkata</option>
                    <!-- Add more locations as needed -->
                </select>
            </div>

            <!-- Input for room price -->
            <div class="form-group">
                <label for="room-price">Price (per night):</label>
                <input type="number" id="room-price" name="room-price" required class="form-input">
            </div>

            <!-- Input for available dates -->
            <div class="form-group">
                <label for="available-dates">Available Dates:</label><br>
                <label>From:</label>
                <input type="date" id="check-in" name="check-inn" required class="form-input">
                <label>To:</label>
                <input type="date" id="check-out" name="check-outt" required class="form-input">
                <!-- Container for displaying the number of days -->
                <p id="number-of-days" style="margin-top: 10px;"></p>
            </div>

            <!-- Input for room image -->
            <div class="form-group">
                <label for="room-image">Room Image:</label>
                <input type="file" id="room-image" name="room-image" accept="image/*" required class="form-input">
            </div>

            <!-- Submit button -->
            <button type="submit" class="submit-button">List Room</button>
        </form>

        <!-- Container for listed rooms -->
        <div id="listed-rooms" style="margin-top: 20px;">
            <!-- Listed rooms will be displayed here -->
        </div>
    </section>

    <section class="output-section" id="renting-output">
        <h2>Find a Room You Want to Renting</h2>
        <form class="renting-form" id="renting-form" action="fetch_content.php" method="post">
            <!-- Dropdown for searching location -->
            <div class="input-wrapper">
                <label for="location">Location</label>
                <select id="location" name="location" class="input-select">
                    <option value="Mumbai">Select location</option>
                    <option value="Mumbai">Mumbai</option>
                    <option value="Delhi">Delhi</option>
                    <option value="Kolkata">Kolkata</option>
                    <option value="Pune">Pune</option>
                    <!-- Add more locations as needed -->
                </select>
                <!-- Icon for location dropdown -->
                <span class="input-icon">📍</span>
            </div>

            <!-- Dropdown for number of persons -->
            <div class="input-wrapper">
                <label for="persons">Number of Persons</label>
                <select id="persons" name="persons" class="input-select">
                    <option value="">Select number of persons</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <!-- Add more options as needed -->
                </select>
                <!-- Icon for persons dropdown -->
                <span class="input-icon">👤</span>
            </div>

            <!-- Input for check-in date -->
            <div class="input-wrapper">
                <label for="check-in">Check-in Date</label>
                <input type="date" id="check-in" name="check-in" required class="input-date">
            </div>

            <!-- Input for check-out date -->
            <div class="input-wrapper">
                <label for="check-out">Check-out Date</label>
                <input type="date" id="check-out" name="check-out" required class="input-date">
            </div>

            <!-- Submit button -->
            <button type="submit" id="search" class="submit-button">Search</button>
        </form>
    </section>
<!-- 
    <section id="contentSection">

    </section> -->



    <script>
        // JavaScript for toggling the menu
        function toggleMenu() {
            var menu = document.getElementById('menu');
            if (menu.style.display === 'none' || menu.style.display === '') {
                menu.style.display = 'block';
            } else {
                menu.style.display = 'none';
            }
        }

        // Hide the menu by default
        document.getElementById('menu').style.display = 'none';

        function showSection(sectionId) {
            // Hide all output sections
            document.getElementById('sharing-output').style.display = 'none';
            document.getElementById('renting-output').style.display = 'none';

            // Show the selected section
            document.getElementById(sectionId + '-output').style.display = 'block';
        }

        // Automatically show the "Sharing Your Room" content by default
        window.onload = function() {
            showSection('renting');
        };




        // $(document).ready(function() {
        //     $('#search').click(function() {
        //         // Show the section 
        //         event.preventDefault();
        //         var formData = $('#renting-form').serialize();

        //         $('#contentSection').show();

        //         // Fetch content using AJAX from PHP script
        //         $.ajax({
        //             url: 'fetch_content.php', // URL of the PHP script
        //             method: 'POST',
        //             data: formData,
        //             success: function(data) {
        //                 // Clear any existing content in the listings section
        //                 $('#contentSection').empty();

        //                 // Parse the JSON data returned from the server
        //                 const roomListings = JSON.parse(data);

        //                 // Loop through the room listings and display them
        //                 roomListings.forEach(room => {
        //                     // Create HTML for each room listing
        //                     const roomHtml = `
        //                         <div class="room-listing">
        //                             <h3>${room.room_title}</h3>
        //                             <img src="${room.image}" alt="${room.room_title}" style="width:100px;">
        //                             <p>${room.room_description}</p>
        //                             <p>Location: ${room.location}</p>
        //                             <p>Price: $${room.price}</p>
        //                             <p>Available: ${room.date_from} to ${room.date_to}</p>
        //                         </div>
        //                     `;

        //                     // Append the HTML to the listings section
        //                     $('#contentSection').append(roomHtml);
        //                 });
        //             },
        //             error: function() {
        //                 // Handle error if something goes wrong
        //                 $('#contentSection').html('Error fetching room listings.');
        //             }
        //         });
        //     });
        // });
    </script>
</body>

</html>