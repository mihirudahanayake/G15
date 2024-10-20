<?php

include('config.php');

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Check if room_id is provided
if (isset($_GET['id'])) {
    $room_id = $_GET['id'];

    // Check if the room has bookings
    $booking_stmt = $conn->prepare("SELECT * FROM bookings WHERE room_id = ?");
    $booking_stmt->bind_param("i", $room_id);
    $booking_stmt->execute();
    $booking_result = $booking_stmt->get_result();

    if ($booking_result->num_rows > 0) {
        echo "<h3>Room cannot be deleted. The following bookings are associated with this room:</h3>";
        echo "<table border='1'>
                <tr>
                    <th>Booking ID</th>
                    <th>User ID</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                </tr>";
        while ($booking = $booking_result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($booking['booking_id']) . "</td>
                    <td>" . htmlspecialchars($booking['user_id']) . "</td>
                    <td>" . htmlspecialchars($booking['start_date']) . "</td>
                    <td>" . htmlspecialchars($booking['end_date']) . "</td>
                  </tr>";
        }
        echo "</table>";
        echo "<p><a href='admin_panel.php'>Back to Admin Panel</a></p>";
    } else {
        // No bookings found, proceed to delete the room
        $stmt = $conn->prepare("DELETE FROM rooms WHERE room_id = ?");
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        $stmt->close();

        // Redirect back to the admin panel
        header("Location: admin_hotel_details.php");
        exit();
    }

    $booking_stmt->close();
} else {
    echo "Room ID not provided.";
}
?>
