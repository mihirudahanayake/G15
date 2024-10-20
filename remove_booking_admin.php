<?php
include('config.php');

// Check if the admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Check if booking_id is set
if (isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];

    // Delete the booking from the database
    $stmt = $conn->prepare("DELETE FROM bookings WHERE booking_id = ?");
    $stmt->bind_param("i", $booking_id);

    if ($stmt->execute()) {
        // Redirect back to the view user bookings page with a success message
        header("Location: admin_view_user_bookings.php?user_id=" . $_GET['user_id'] . "&message=BookingRemoved");
        exit();
    } else {
        echo "Error removing the booking.";
    }

    $stmt->close();
} else {
    echo "No booking ID provided.";
}
?>
