<?php

include('config.php');

// Check if the user is logged in and is a hotel admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'hotel_admin') {
    header("Location: login.html");
    exit();
}

// Get the booking_id from the URL
if (isset($_GET['id'])) {
    $booking_id = $_GET['id'];

    // Fetch booking details
    $booking_stmt = $conn->prepare("SELECT * FROM bookings WHERE booking_id = ?");
    $booking_stmt->bind_param("i", $booking_id);
    $booking_stmt->execute();
    $booking_result = $booking_stmt->get_result();
    $booking = $booking_result->fetch_assoc();
    $booking_stmt->close();
} else {
    header("Location: hotel_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Booking Details for Booking ID: <?php echo htmlspecialchars($booking['booking_id']); ?></h2>

        <p><strong>User ID:</strong> <?php echo htmlspecialchars($booking['user_id']); ?></p>
        <p><strong>Start Date:</strong> <?php echo htmlspecialchars($booking['start_date']); ?></p>
        <p><strong>End Date:</strong> <?php echo htmlspecialchars($booking['end_date']); ?></p>

        <!-- Add more booking details here -->

        <a href="view_bookings.php?room_id=<?php echo $booking['room_id']; ?>">Back to Bookings</a>
    </div>
</body>
</html>
