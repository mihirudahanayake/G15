<?php

include('config.php');

$room_id = $_GET['room_id'];

// Fetch booking details for this room
$stmt = $conn->prepare("
    SELECT b.*, u.username 
    FROM bookings b 
    JOIN users u ON b.user_id = u.id 
    WHERE b.room_id = ?
");
$stmt->bind_param("i", $room_id);
$stmt->execute();
$bookings = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="booking-details-container">
        <h2>Booking Details</h2>

        <?php while($booking = $bookings->fetch_assoc()): ?>
            <div class="booking">
                <p>User: <?php echo $booking['username']; ?></p>
                <p>Check-in Date: <?php echo $booking['check_in_date']; ?></p>
                <p>Check-out Date: <?php echo $booking['check_out_date']; ?></p>
                <p>Adults: <?php echo $booking['adults']; ?></p>
                <p>Children: <?php echo $booking['children']; ?></p>
                <p>Booking Date: <?php echo $booking['booking_date']; ?></p>
            </div>
        <?php endwhile; ?>

        <a href="manage_rooms.php?hotel_id=<?php echo $hotel_id; ?>" class="back-link">Back to Rooms</a>
    </div>
</body>
</html>
