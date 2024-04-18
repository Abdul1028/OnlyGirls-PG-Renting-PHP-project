<!-- <?php
        // session_start();
        // $_POST['login'] = False;
        ?> -->

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
            echo "<a href='/profile.php'>Profile</a>"; // Replace with your profile page link
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
        <form class="listing-form" action="#" id="listing-form">
            <!-- Input for room title -->
            <div class="form-group">
                <label for="room-title">Room Title</label>
                <input type="text" id="room-title" name="room-title" required class="form-input">
            </div>

            <!-- Input for room description -->
            <div class="form-group">
                <label for="room-description">Room Description</label>
                <textarea id="room-description" name="room-description" required class="form-input"></textarea>
            </div>

            <!-- Dropdown for room location -->
            <div class="form-group">
                <label for="room-location">Location</label>
                <select id="room-location" name="room-location" required class="form-select">
                    <option value="">Select location</option>
                    <option value="New York">New York</option>
                    <option value="Los Angeles">Los Angeles</option>
                    <option value="Chicago">Chicago</option>
                    <option value="Miami">Miami</option>
                    <!-- Add more locations as needed -->
                </select>
                <span class="input-icon">📍</span>
            </div>

            <!-- Input for room price -->
            <div class="form-group">
                <label for="room-price">Price (per night)</label>
                <input type="number" id="room-price" name="room-price" required class="form-input">
            </div>

            <!-- Input for available dates -->
            <div class="form-group">
                <label for="available-dates">Available Dates</label>
                <input type="text" id="available-dates" name="available-dates" required class="form-input" placeholder="e.g., April 1 - 7">
            </div>

            <!-- Input for room image -->
            <div class="form-group">
                <label for="room-image">Room Image</label>
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
        <form class="renting-form" action="#" id="renting-form">
            <!-- Dropdown for searching location -->
            <div class="input-wrapper">
                <label for="location">Location</label>
                <select id="location" name="location" class="input-select">
                    <option value="">Select location</option>
                    <option value="New York">Mumbai</option>
                    <option value="Los Angeles">Delhi</option>
                    <option value="Chicago">Kolkata</option>
                    <option value="Miami">Pune</option>
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
            <button type="submit" class="submit-button">Search</button>
        </form>
        <div id="search-results" style="margin-top: 20px;">
            <!-- Search results will be populated here -->
        </div>
    </section>

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

        // JavaScript code to handle the form submission and display search results

        // Function to simulate fetching search results based on form input
        function fetchSearchResults(location, persons, checkIn, checkOut) {
            // Simulated search results (you can replace this with an actual API call)
            const results = [
                `Result 1: Room in ${location} for ${persons} person(s) from ${checkIn} to ${checkOut}`,
                `Result 2: Room in ${location} for ${persons} person(s) from ${checkIn} to ${checkOut}`,
                `Result 3: Room in ${location} for ${persons} person(s) from ${checkIn} to ${checkOut}`,
                // Add more results as needed
            ];
            return results;
        }

        // Function to handle form submission
        function handleFormSubmit(event) {
            event.preventDefault(); // Prevent default form submission

            // Get form values
            const location = document.getElementById('location').value;
            const persons = document.getElementById('persons').value;
            const checkIn = document.getElementById('check-in').value;
            const checkOut = document.getElementById('check-out').value;

            // Fetch search results based on form values
            const searchResults = fetchSearchResults(location, persons, checkIn, checkOut);

            // Get the search results container
            const searchResultsContainer = document.getElementById('search-results');

            // Clear any previous results
            searchResultsContainer.innerHTML = '';

            // Populate the search results
            searchResults.forEach(result => {
                const resultElement = document.createElement('p');
                resultElement.textContent = result;
                searchResultsContainer.appendChild(resultElement);
            });
        }

        // Attach the form submission event listener
        document.getElementById('renting-form').addEventListener('submit', handleFormSubmit);
    </script>
</body>


</html>