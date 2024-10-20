<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Destinations</title>
    <link rel="stylesheet" href="travel_destination.css">
</head>
<body>
<?php include('header.php'); ?>
<div class="bg"></div>
    <div class="container">
        <h1>Travel Destinations</h1>

        <!-- Search Form -->
        <form action="travel_destination.php" method="GET">
            <div class="search-filters">
                <div class="filter">
                    <select id="city" name="city">
                        <option value="">Select a city</option>
                        <?php
                        include('config.php');

                        // Fetch cities from the cities table
                        $cityQuery = "SELECT DISTINCT city_name FROM cities";
                        $cityResult = $conn->query($cityQuery);

                        if ($cityResult->num_rows > 0) {
                            while ($row = $cityResult->fetch_assoc()) {
                                echo '<option value="' . htmlspecialchars($row['city_name']) . '">' . htmlspecialchars($row['city_name']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="filter">
                    <input type="text" id="desti_name" name="desti_name" placeholder="Enter destination name">
                </div>

                <!-- Add the search button in the same line -->
                <div class="filter search-button">
                    <button type="submit">Search</button>
                </div>
            </div>
        </form>




        <div class="destinations-grid">
    <?php
    // Base query to fetch all destinations
    $query = "SELECT d.destination_id, d.desti_name, d.desti_description, d.city 
              FROM destinations d WHERE 1=1";

    // Apply city filter if provided
    if (isset($_GET['city']) && !empty($_GET['city'])) {
        $city = $conn->real_escape_string($_GET['city']);
        $query .= " AND d.city LIKE '%$city%'";
    }

    // Apply destination name filter if provided
    if (isset($_GET['desti_name']) && !empty($_GET['desti_name'])) {
        $desti_name = $conn->real_escape_string($_GET['desti_name']);
        $query .= " AND d.desti_name LIKE '%$desti_name%'";
    }

    // Execute the destination query
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $index = 0; // Initialize index for animation delay
        while ($destination = $result->fetch_assoc()) {
            // Calculate animation delay based on the index
            $animationDelay = $index * 0.1; // delay for each subsequent item
            echo "<div class='destination' style='animation-delay: {$animationDelay}s;'>";
            echo "<h2>" . htmlspecialchars($destination['desti_name']) . "</h2>";
            echo "<p>" . htmlspecialchars($destination['city']) . "</p>";

            // Fetch images for the current destination
            $destination_id = intval($destination['destination_id']);
            $imageQuery = "SELECT image_url FROM destination_images WHERE destination_id = $destination_id LIMIT 1";
            $imageResult = $conn->query($imageQuery);

            // Display the first image if found
            if ($imageResult->num_rows > 0) {
                $image = $imageResult->fetch_assoc();
                echo "<img src='" . htmlspecialchars($image['image_url']) . "' alt='" . htmlspecialchars($destination['desti_name']) . "'>";
            } else {
                // If no image, show a placeholder
                echo "<img src='default_image.jpg' alt='No image available'>";
            }

            // Limit the description length
            $maxDescriptionLength = 100; // Set the maximum length of the description
            $shortDescription = strlen($destination['desti_description']) > $maxDescriptionLength 
                ? substr($destination['desti_description'], 0, $maxDescriptionLength) . '...' 
                : $destination['desti_description'];

            echo "<p>" . htmlspecialchars($shortDescription) . "</p>";
            // Add a View Details button
            echo '<a href="destination_details.php?id=' . htmlspecialchars($destination['destination_id']) . '" class="details-link">View Details</a>';
            echo "</div>";
            $index++; // Increment index for the next destination
        }
    } else {
        echo "<p>No destinations found matching your criteria.</p>";
    }

    $conn->close();
    ?>
</div>

    </div>
    <?php include('footer.php'); ?>
</body>
</html>
