<?php

include('config.php');

$hotel_id = $_GET['hotel_id'];

// Fetch rooms associated with this hotel
$stmt = $conn->prepare("SELECT * FROM rooms WHERE hotel_id = ?");
$stmt->bind_param("i", $hotel_id);
$stmt->execute();
$rooms = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="rooms-container">
        <h2>Manage Rooms</h2>

        <section>
            <h3>Add New Room</h3>
            <form action="add_room.php" method="POST">
                <label for="room_number">Room Number</label>
                <input type="text" id="room_number" name="room_number" required>

                <label for="capacity_adults">Capacity (Adults)</label>
                <input type="number" id="capacity_adults" name="capacity_adults" required>

                <label for="capacity_children">Capacity (Children)</label>
                <input type="number" id="capacity_children" name="capacity_children" required>

                <label for="facilities">Facilities</label>
                <textarea id="facilities" name="facilities" required></textarea>

                <label for="price_per_night">Price per Night</label>
                <input type="number" id="price_per_night" name="price_per_night" step="0.01" required>

                <button type="submit">Add Room</button>
            </form> 
        </section>

        <section>
            <h3>Rooms</h3>
            <div class="rooms-list">
                <?php while($room = $rooms->fetch_assoc()): ?>
                    <div class="room">
                        <p>Room Number: <?php echo $room['room_number']; ?></p>
                        <p>Max Adults: <?php echo $room['capacity_adults']; ?></p>
                        <p>Max Children: <?php echo $room['capacity_children']; ?></p>
                        <p>Facilities: <?php echo $room['facilities']; ?></p>
                        <p>Status: <?php echo $room['booked'] ? 'Booked' : 'Available'; ?></p>
                        <a href="view_booking.php?room_id=<?php echo $room['id']; ?>" class="booking-link">View Booking Details</a>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
    </div>
</body>
</html>
