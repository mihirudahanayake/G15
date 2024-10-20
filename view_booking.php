<?php
include('config.php'); // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Fetch user bookings
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT bookings.booking_id, bookings.start_date, bookings.end_date, rooms.room_name, hotels.hotel_name 
                        FROM bookings 
                        JOIN rooms ON bookings.room_id = rooms.room_id 
                        JOIN hotels ON rooms.hotel_id = hotels.hotel_id 
                        WHERE bookings.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookings_result = $stmt->get_result();
$stmt->close();

// Handle booking cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];

    // Prepare the SQL statement to delete the booking
    $delete_stmt = $conn->prepare("DELETE FROM bookings WHERE booking_id = ?");
    $delete_stmt->bind_param("i", $booking_id);
    
    if ($delete_stmt->execute()) {
        // Redirect to the same page with a success message
        header("Location: view_booking.php?status=success");
        exit;
    } else {
        // Handle error (this can be displayed in the user interface if needed)
        header("Location: view_booking.php?status=error&message=" . urlencode($conn->error));
        exit;
    }
}

// Check for status messages in the URL
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        echo "<script>alert('Booking cancelled successfully.');</script>";
    } elseif ($_GET['status'] == 'error' && isset($_GET['message'])) {
        echo "<script>alert('Error cancelling booking: " . htmlspecialchars($_GET['message']) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
    <link rel="stylesheet" href="view-booking.css">
</head>
<body>
    <div class="bg"></div>
    <div class="profile-container">
        <h2>Your Bookings</h2>
        <?php if ($bookings_result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Hotel Name</th>
                    <th>Room Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Action</th>
                </tr>
                <?php while($booking = $bookings_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($booking['hotel_name']); ?></td>
                    <td><?php echo htmlspecialchars($booking['room_name']); ?></td>
                    <td><?php echo htmlspecialchars($booking['start_date']); ?></td>
                    <td><?php echo htmlspecialchars($booking['end_date']); ?></td>
                    <td>
                        <form method="POST" action="view_booking.php">
                            <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['booking_id']); ?>">
                            <button type="submit" name="cancel_booking" onclick="return confirm('Are you sure you want to cancel this booking?');">Cancel Booking</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>You have no bookings.</p>
        <?php endif; ?>

        <button onclick="location.href='profile.php'" id="back-link">Back to Profile</button>
    </div>
</body>
</html>

<?php
$conn->close();
?>
