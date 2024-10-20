<?php
include('config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Listing</title>
    <link rel="stylesheet" href="hotel_list.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet"/>
</head>
<body>
    <div class="bg"></div>
    <?php include 'header.php'; ?>

    <!-- Search Form -->
     <div class="search">
    <form action="hotel_list.php" method="GET">
        <label for="city">City</label>
        <select id="city" name="city">
            <option value="">Select a city</option>
            <?php
            // Fetch unique city names for the dropdown list from the cities table
            $cityQuery = "SELECT DISTINCT city_name FROM cities"; // Assuming you have a cities table
            $cityResult = $conn->query($cityQuery);

            if ($cityResult === false) {
                echo '<option value="">Error fetching cities</option>';
            } elseif ($cityResult->num_rows > 0) {
                while ($row = $cityResult->fetch_assoc()) {
                    echo '<option value="' . htmlspecialchars($row['city_name']) . '">' . htmlspecialchars($row['city_name']) . '</option>';
                }
            } else {
                echo '<option value="">No cities available</option>';
            }
            ?>
        </select>
        <button id="search" type="submit">Search</button>
    </form>
    </div>

    <!-- Hotel Listing Section -->
    <main class="hotel-container">
        <?php
        // Base query to fetch unique hotels and their first image (if any)
        $query = "SELECT h.hotel_id, h.hotel_name, h.location, 
                         (SELECT image_path FROM hotel_images WHERE hotel_images.hotel_id = h.hotel_id ORDER BY image_id ASC LIMIT 1) AS first_image 
                  FROM hotels h
                  LEFT JOIN hotel_destinations hd ON h.hotel_id = hd.hotel_id
                  LEFT JOIN destinations d ON hd.destination_id = d.destination_id
                  WHERE 1=1"; // Use 1=1 for easy concatenation of conditions

        // Filtering by city
        if (isset($_GET['city']) && !empty($_GET['city'])) {
            $city = $conn->real_escape_string($_GET['city']);
            $query .= " AND h.location = '$city'"; // Assuming location is in hotels table
        }

        $query .= " GROUP BY h.hotel_id, h.hotel_name, h.location
                     ORDER BY h.hotel_id"; // Use GROUP BY to eliminate duplicates

        $result = $conn->query($query);

        if ($result === false) {
            echo "<p>Error executing query: " . htmlspecialchars($conn->error) . "</p>";
        } elseif ($result->num_rows > 0) {
            while ($hotel = $result->fetch_assoc()) {
                // Link to the room list page with the hotel ID
                echo "<a href='room_list.php?id=" . htmlspecialchars($hotel['hotel_id']) . "' class='view-details'>";
                echo "<div class='hotel'>";
                echo "<img src='" . htmlspecialchars($hotel['first_image']) . "' alt='Hotel Image' class='hotel-image'>";
                echo "<div class='hotel-details'>";
                echo "<h3>" . htmlspecialchars($hotel['hotel_name']) . "</h3>";
                echo "<p>Location: " . htmlspecialchars($hotel['location']) . "</p>";
                echo "</div>";
                echo "</div>";
                echo "</a>";
            }
        } else {
            echo "<p>No hotels available.</p>";
        }

        $conn->close();
        ?>
    </main>

    <script src="script.js"></script>
        <div class="foot"><?php include 'footer.php'; ?></div>
</body>
</html>
