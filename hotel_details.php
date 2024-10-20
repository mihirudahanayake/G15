<?php
include('config.php');

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $hotel_id = $_GET['id'];

    // Fetch hotel details
    $stmt = $conn->prepare("SELECT hotels.hotel_name AS hotel_name, hotels.location, hotels.total_rooms, hotels.description, rooms.price_per_night 
                            FROM hotels 
                            JOIN rooms ON hotels.hotel_id = rooms.hotel_id 
                            WHERE hotels.hotel_id = ?");
    $stmt->bind_param("i", $hotel_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $hotel = $result->fetch_assoc();
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo $hotel['hotel_name']; ?> - Details</title>
            <link rel="stylesheet" href="styles.css">
        </head>
        <body>
            <div class="container">
                <h2><?php echo $hotel['hotel_name']; ?></h2>
                <p><strong>Location:</strong> <?php echo $hotel['location']; ?></p>
                <p><strong>Total Rooms:</strong> <?php echo $hotel['total_rooms']; ?></p>
                <p><strong>Description:</strong> <?php echo $hotel['description']; ?></p>
                <p><strong>Price per Night:</strong> $<?php echo $hotel['price_per_night']; ?></p>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo "<p>Hotel details not found.</p>";
    }

    $stmt->close();
} else {
    echo "<p>Invalid hotel ID.</p>";
}

$conn->close();
?>
