<?php

include('config.php');

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Check if user_id is provided
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Fetch user bookings
    $stmt = $conn->prepare("SELECT bookings.booking_id, bookings.room_id, bookings.start_date, bookings.end_date, rooms.room_name, hotels.hotel_name 
                            FROM bookings 
                            JOIN rooms ON bookings.room_id = rooms.room_id 
                            JOIN hotels ON rooms.hotel_id = hotels.hotel_id 
                            WHERE bookings.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    echo "User ID not provided.";
    exit();
}

if ($user_id) {

    // Fetch the user's name
    $stmt = $conn->prepare("SELECT name FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_result = $stmt->get_result();

    if ($user_result->num_rows > 0) {
        $user = $user_result->fetch_assoc();
        $user_name = $user['name'];
    } else {
        $user_name = "User"; // Fallback if user not found
    }

    $stmt->close();
    $conn->close();
} else {
    $user_name = "User"; // Fallback if user ID is missing
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User Bookings</title>
    <link rel="stylesheet" href="admin-view-user-booking.css">
</head>
<body>
    <div class="admin-container">
    <h2><?php echo htmlspecialchars($user_name); ?>'s Booking Details</h2>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Booking ID</th>
                    <th>Room ID</th>
                    <th>Hotel Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                </tr>
                <?php while ($booking = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                        <td><?php echo htmlspecialchars($booking['room_id']); ?></td>
                        <td><?php echo htmlspecialchars($booking['hotel_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['start_date']); ?></td>
                        <td><?php echo htmlspecialchars($booking['end_date']); ?></td>
                        <td>
                            <!-- Form to remove booking -->
                            <form action="remove_booking_admin.php" method="POST">
                                <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                <button type="submit" class="remove-btn">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No bookings found for this user.</p>
        <?php endif; ?>
        <button><a href="admin_panel.php">Back to Admin Panel</a></button>
    </div>
</body>
</html>
