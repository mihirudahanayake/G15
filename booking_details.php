<?php
include('config.php');

// Check if the booking ID is set
if (!isset($_GET['booking_id'])) {
    header("Location: booking_status.php"); // Redirect if booking ID is not provided
    exit();
}

$booking_id = $_GET['booking_id'];

// Fetch booking details from the database
$booking_stmt = $conn->prepare("
    SELECT b.*, u.email AS user_email, u.name AS user_name, u.telephone AS user_telephone, r.room_number, h.hotel_name
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    JOIN rooms r ON b.room_id = r.room_id
    JOIN hotels h ON r.hotel_id = h.hotel_id
    WHERE b.booking_id = ?
");
$booking_stmt->bind_param("i", $booking_id);
$booking_stmt->execute();
$booking_result = $booking_stmt->get_result();

if ($booking_result->num_rows === 0) {
    echo "No booking found.";
    exit();
}

$booking = $booking_result->fetch_assoc();
$booking_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details</title>
    <link rel="stylesheet" href="booking-details.css">
</head>
<body>
    <div class="bg"></div>
    <div class="booking-details-container">
        <h2>Booking Details</h2>
        <p><strong>Booking ID:</strong> <?php echo htmlspecialchars($booking['booking_id']); ?></p>
        <p><strong>User Name:</strong> <?php echo htmlspecialchars($booking['user_name']); ?></p>
        <p><strong>User Email:</strong> <?php echo htmlspecialchars($booking['user_email']); ?></p>
        <p><strong>User Telephone:</strong> <?php echo htmlspecialchars($booking['user_telephone']); ?></p>
        <p><strong>Room Number:</strong> <?php echo htmlspecialchars($booking['room_number']); ?></p>
        <p><strong>Hotel Name:</strong> <?php echo htmlspecialchars($booking['hotel_name']); ?></p>
        <p><strong>Check-in Date:</strong> <?php echo htmlspecialchars($booking['start_date']); ?></p>
        <p><strong>Check-out Date:</strong> <?php echo htmlspecialchars($booking['end_date']); ?></p>

        <!-- Button to go back to the dashboard -->
        <button onclick="location.href='hotel_dashboard.php'">Back to Dashboard</button>

        <!-- Form to remove the booking -->
        <form action="remove_booking.php" method="POST">
            <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
            <button type="submit" class="remove-btn">Remove Booking</button>
        </form>
    </div>
</body>
</html>

