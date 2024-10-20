<?php
include('config.php');

// Check if booking_id is set
if (isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];

    // Get the room_id before deleting the booking
    $room_stmt = $conn->prepare("SELECT room_id FROM bookings WHERE booking_id = ?");
    $room_stmt->bind_param("i", $booking_id);
    $room_stmt->execute();
    $room_stmt->bind_result($room_id);
    $room_stmt->fetch();
    $room_stmt->close();

    // Delete the booking from the database
    $delete_stmt = $conn->prepare("DELETE FROM bookings WHERE booking_id = ?");
    $delete_stmt->bind_param("i", $booking_id);

    if ($delete_stmt->execute()) {
        // Redirect to the edit room page
        header("Location: edit_room.php?room_id=" . $room_id);
        exit();
    } else {
        echo "Error removing the booking.";
    }

    $delete_stmt->close();
} else {
    echo "No booking ID provided.";
}
?>