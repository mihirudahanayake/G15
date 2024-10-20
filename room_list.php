<?php
include('config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Listing</title>
    <link rel="stylesheet" href="room_list.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet"/>
</head>
<body>
    <div class="bg"></div>
    <?php include 'header.php'; ?>

    <!-- Search Form -->
    <form action="room_list.php" method="GET">
        <input type="hidden" name="id" value="<?php echo isset($_GET['id']) ? htmlspecialchars($_GET['id']) : ''; ?>"> <!-- Include hotel ID in the form -->

        <label for="min_price">Min Price</label>
        <input type="number" id="min_price" name="min_price" step="0.01" placeholder="Minimum price per night">

        <label for="max_price">Max Price</label>
        <input type="number" id="max_price" name="max_price" step="0.01" placeholder="Maximum price per night">

        <button type="submit">Search</button>
    </form>

    <?php
    // Make sure the hotel ID is set from the URL
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $hotel_id = $conn->real_escape_string($_GET['id']); // Escape the hotel_id for security

        // Query to fetch hotel details (name and location)
        $hotelQuery = "SELECT hotel_name, location FROM hotels WHERE hotel_id = '$hotel_id'";
        $hotelResult = $conn->query($hotelQuery);
        echo "<div class='hotel-container'>"; // Centered container start
        if ($hotelResult->num_rows > 0) {
            $hotel = $hotelResult->fetch_assoc();
            echo "<h1 class='hotel-name'>" . htmlspecialchars($hotel['hotel_name']) . "</h1>";
            echo "<p class='hotel-location'>Location: " . htmlspecialchars($hotel['location']) . "</p>";

            // Query to fetch hotel images from hotel_images table
            $hotelImagesQuery = "SELECT image_path FROM hotel_images WHERE hotel_id = '$hotel_id'";
            $hotelImagesResult = $conn->query($hotelImagesQuery);

            if ($hotelImagesResult->num_rows > 0) {
                echo "<div class='hotel-images'>";
                while ($image = $hotelImagesResult->fetch_assoc()) {
                    echo "<img src='" . htmlspecialchars($image['image_path']) . "' alt='Hotel Image' class='hotel-image'>";
                }
                echo "</div>";
            } else {
                echo "<p>No images available for this hotel.</p>";
            }
        } else {
            echo "<p>Hotel not found.</p>";
        }
        echo "</div>"; // Centered container end

        // Base query to fetch rooms, hotel details, and the first image for each room
        $query = "SELECT DISTINCT rooms.room_id, rooms.room_number, rooms.price_per_night, rooms.max_adults, rooms.max_children,
                          hotels.hotel_name, hotels.location, 
                          (SELECT image_path FROM room_images WHERE room_images.room_id = rooms.room_id ORDER BY image_id ASC LIMIT 1) AS first_image 
                  FROM rooms 
                  JOIN hotels ON rooms.hotel_id = hotels.hotel_id
                  LEFT JOIN hotel_destinations ON hotels.hotel_id = hotel_destinations.hotel_id
                  LEFT JOIN destinations ON hotel_destinations.destination_id = destinations.destination_id
                  WHERE rooms.hotel_id = '$hotel_id'"; // Ensure only rooms from the specific hotel are shown
?>

    <!-- Room Listing Section -->
    <div class="room-container">
        <?php
            // Price range filters
            if (isset($_GET['min_price']) && !empty($_GET['min_price'])) {
                $min_price = $conn->real_escape_string($_GET['min_price']);
                $query .= " AND rooms.price_per_night >= $min_price";
            }

            if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
                $max_price = $conn->real_escape_string($_GET['max_price']);
                $query .= " AND rooms.price_per_night <= $max_price";
            }

            $query .= " ORDER BY rooms.room_id";

            echo "<!-- SQL Query: $query -->"; // For debugging

            $result = $conn->query($query);

            if ($result === false) {
                echo "<p>Error executing query: " . $conn->error . "</p>";
            } else if ($result->num_rows > 0) {
                while ($room = $result->fetch_assoc()) {
                    echo "<a href='room_details.php?id=" . $room['room_id'] . "' class='view-details'>";
                    echo "<div class='room'>";
                    if (!empty($room['first_image'])) {
                        echo "<img src='" . htmlspecialchars($room['first_image']) . "' alt='Room Image' class='room-image'>";
                    } else {
                        echo "<img src='default_room_image.jpg' alt='Room Image' class='room-image'>"; // Fallback image
                    }
                    echo "<div class='room-details'>";
                    echo "<h3>Room No : " . htmlspecialchars($room['room_number']) . "</h3>";
                    echo "<p>Price per Night: LKR " . htmlspecialchars($room['price_per_night']) . "</p>";
                    echo "<p>Max Adults: " . htmlspecialchars($room['max_adults']) . "</p>";
                    echo "<p>Max Child: " . htmlspecialchars($room['max_children']) . "</p>";
                    echo "</div>";
                    echo "</div>";
                    echo "</a>";
                }
            } else {
                echo "<p>No rooms available.</p>";
            }

            // Fetch all destinations associated with the hotel
            $stmt = $conn->prepare("SELECT destinations.desti_name, destinations.destination_id
                                    FROM hotel_destinations
                                    JOIN destinations ON hotel_destinations.destination_id = destinations.destination_id
                                    WHERE hotel_destinations.hotel_id = ?");
            $stmt->bind_param("i", $hotel_id); // Bind $hotel_id to the query
            $stmt->execute();
            $destResult = $stmt->get_result();

            $destinations = [];
            if ($destResult->num_rows > 0) {
                while ($row = $destResult->fetch_assoc()) {
                    // Add destination name and id to the array
                    $destinations[] = $row;
                }
            }
        } else {
            echo "<p>No hotel selected. Please choose a hotel.</p>";
        }
        ?>
    </div>

    <section class="destinations" id="destinations">
    <h2>Near Traveling Places</h2>
    <?php if (!empty($destinations)): ?>
        <div class="destination-container">
            <?php foreach ($destinations as $destination): ?>
                <div class="destination-box">
                    <a href="destination_details.php?id=<?php echo $destination['destination_id']; ?>">
                        <?php
                        // Fetch the first image URL for the current destination
                        $destination_id = $destination['destination_id'];

                        // Check connection
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        // Query to fetch the first image URL for the destination
                        $sql = "SELECT image_url FROM destination_images WHERE destination_id = ? LIMIT 1";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $destination_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Fetch the image URL
                        $first_image_url = null;
                        if ($row = $result->fetch_assoc()) {
                            $first_image_url = $row['image_url'];
                        }

                        // Close the statement and connection
                        $stmt->close();
                        ?>

                        <img src="<?php echo htmlspecialchars($first_image_url); ?>" alt="<?php echo htmlspecialchars($destination['desti_name']); ?>" class="destination-image">
                        <p class="destination-name"><?php echo htmlspecialchars($destination['desti_name']); ?></p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No near places found</p>
    <?php endif; ?>
</section>
<div class="foot">
    <?php include 'footer.php'; ?>
</div>
    <!-- Close connection after all queries -->
    <?php $conn->close(); ?>

    <script src="script.js"></script>
</body>
</html>
