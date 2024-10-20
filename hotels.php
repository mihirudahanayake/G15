<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotels</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Search Hotels</h2>
        <form action="hotels.php" method="GET">
            <label for="city">City</label>
            <input type="text" id="city" name="city" placeholder="Enter city name">

            <label for="min_price">Min Price</label>
            <input type="number" id="min_price" name="min_price" step="0.01" placeholder="Minimum price per night">

            <label for="max_price">Max Price</label>
            <input type="number" id="max_price" name="max_price" step="0.01" placeholder="Maximum price per night">

            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Display the filtered hotels below -->
    <div class="hotels-list">
        <h2>Hotels</h2>
        <?php
        include('config.php');

        // Default query to fetch all hotels
        $query = "SELECT hotels.hotel_id, hotels.hotel_name AS hotel_name, hotels.location, hotels.total_rooms, hotels.description, rooms.price_per_night 
                  FROM hotels 
                  JOIN rooms ON hotels.hotel_id = rooms.hotel_id 
                  WHERE 1=1";

        // Add filters based on the user's input
        if (isset($_GET['city']) && !empty($_GET['city'])) {
            $city = $_GET['city'];
            $query .= " AND hotels.location LIKE '%$city%'";
        }

        if (isset($_GET['min_price']) && !empty($_GET['min_price'])) {
            $min_price = $_GET['min_price'];
            $query .= " AND rooms.price_per_night >= $min_price";
        }

        if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
            $max_price = $_GET['max_price'];
            $query .= " AND rooms.price_per_night <= $max_price";
        }

        // Execute the query
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            while ($room = $result->fetch_assoc()) {
                echo "<div class='room'>";
                echo "<h3>" . $room['hotel_name'] . "</h3>";
                echo "<p>Description: " . $room['description'] . "</p>";
                echo "<p>Price per Night: $" . $room['price_per_night'] . "</p>";
                echo "<p>Hotel: " . $room['hotel_name'] . "</p>";
                echo "<p>Location: " . $room['location'] . "</p>";
                echo '<a href="room_details.php?id=' . $room['room_id'] . '" class="details-link">View Details</a>';
                echo "</div>";
            }
        } else {
            echo "<p>No rooms found matching your criteria.</p>";
        }
        
        $conn->close();
        ?>
    </div>
</body>
</html>
