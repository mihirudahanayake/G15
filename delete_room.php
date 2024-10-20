<?php

include('config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'hotel_admin') {
    header("Location: login.html");
    exit();
}

$hotel_id = $_SESSION['hotel_id'];
$room_id = $_POST['room_id'];

// Check if there are any bookings for this room
$stmt = $conn->prepare("SELECT * FROM bookings WHERE room_id = ?");
$stmt->bind_param("i", $room_id);
$stmt->execute();
$bookings = $stmt->get_result();
$stmt->close();

if ($bookings->num_rows > 0) {
    // There are existing bookings, so don't delete the room
    $_SESSION['error_message'] = "Cannot delete this room as there are existing bookings.";
    $_SESSION['booking_info'] = [];
    while ($booking = $bookings->fetch_assoc()) {
        $_SESSION['booking_info'][] = [
            'start_date' => $booking['start_date'],
            'end_date' => $booking['end_date']
        ];
    }
    header("Location: hotel_dashboard.php");
    exit();
} else {
    // No bookings, proceed to delete the room
    $conn->begin_transaction();
    
    try {
        // Delete associated notifications
        $stmt = $conn->prepare("DELETE FROM notifications WHERE room_id = ?");
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        $stmt->close();

        // Delete the room
        $stmt = $conn->prepare("DELETE FROM rooms WHERE room_id = ? AND hotel_id = ?");
        $stmt->bind_param("ii", $room_id, $hotel_id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error_message'] = "An error occurred: " . $e->getMessage();
        header("Location: hotel_dashboard.php");
        exit();
    }

    header("Location: hotel_dashboard.php");
    exit();
}
?>
