<?php

include('config.php');

// Check if the user is logged in and is a hotel admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'hotel_admin') {
    header("Location: login.html");
    exit();
}

// Get the room_id from the URL
if (isset($_GET['room_id'])) {
    $room_id = $_GET['room_id'];

    // Fetch room details
    $room_stmt = $conn->prepare("SELECT * FROM rooms WHERE room_id = ?");
    $room_stmt->bind_param("i", $room_id);
    $room_stmt->execute();
    $room_result = $room_stmt->get_result();
    $room = $room_result->fetch_assoc();
    $room_stmt->close();

    // Fetch bookings for this room including user email
    $bookings_stmt = $conn->prepare("
        SELECT bookings.booking_id, bookings.start_date, bookings.end_date, users.email
        FROM bookings
        JOIN users ON bookings.user_id = users.user_id
        WHERE bookings.room_id = ?
    ");
    $bookings_stmt->bind_param("i", $room_id);
    $bookings_stmt->execute();
    $bookings_result = $bookings_stmt->get_result();
    $bookings_stmt->close();
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
    <title>View Bookings for Room <?php echo htmlspecialchars($room['room_name']); ?></title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Bookings for Room: <?php echo htmlspecialchars($room['room_name']); ?></h2>

        <table>
            <tr>
                <th>Booking ID</th>
                <th>User Email</th>
                <th>Start Date</th>
                <th>End Date</th>
            </tr>
            <?php while($booking = $bookings_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                <td><?php echo htmlspecialchars($booking['email']); ?></td>
                <td><?php echo htmlspecialchars($booking['start_date']); ?></td>
                <td><?php echo htmlspecialchars($booking['end_date']); ?></td>
                
            </tr>
            <?php endwhile; ?>
        </table>

        <a href="hotel_dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
