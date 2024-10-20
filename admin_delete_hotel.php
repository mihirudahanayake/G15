<?php

include('config.php');

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Check if hotel_id is provided
if (isset($_GET['id'])) {
    $hotel_id = $_GET['id']; // Corrected to match the URL parameter 'id'

    // Start a transaction to ensure data integrity
    $conn->begin_transaction();

    try {
        // Check if there are rooms associated with the hotel
        $rooms_check_stmt = $conn->prepare("
            SELECT room_id FROM rooms WHERE hotel_id = ?
        ");
        $rooms_check_stmt->bind_param("i", $hotel_id);
        $rooms_check_stmt->execute();
        $rooms_result = $rooms_check_stmt->get_result();

        if ($rooms_result->num_rows > 0) {
            // There are rooms associated with the hotel, now check if those rooms have bookings
            $room_ids = [];
            while ($room = $rooms_result->fetch_assoc()) {
                $room_ids[] = $room['room_id'];
            }
            $rooms_check_stmt->close();

            // Check for bookings in these rooms
            $room_ids_placeholders = implode(',', array_fill(0, count($room_ids), '?'));
            $bookings_check_stmt = $conn->prepare("
                SELECT * FROM bookings WHERE room_id IN ($room_ids_placeholders)
            ");
            $bookings_check_stmt->bind_param(str_repeat('i', count($room_ids)), ...$room_ids);
            $bookings_check_stmt->execute();
            $bookings_result = $bookings_check_stmt->get_result();

            if ($bookings_result->num_rows > 0) {
                // There are bookings for the rooms, display details
                echo "The hotel has rooms with existing bookings. Here are the details:<br>";
                while ($booking = $bookings_result->fetch_assoc()) {
                    echo "Booking ID: " . htmlspecialchars($booking['booking_id']) . ", Room ID: " . htmlspecialchars($booking['room_id']) . ", Start Date: " . htmlspecialchars($booking['start_date']) . ", End Date: " . htmlspecialchars($booking['end_date']) . "<br>";
                }
                $bookings_check_stmt->close();
                $conn->rollback();
                exit();
            }
            $bookings_check_stmt->close();
        }

        // Proceed to delete the hotel if no issues found
        $delete_hotel_stmt = $conn->prepare("DELETE FROM hotels WHERE hotel_id = ?");
        $delete_hotel_stmt->bind_param("i", $hotel_id);
        $delete_hotel_stmt->execute();
        $delete_hotel_stmt->close();

        // Commit the transaction
        $conn->commit();

        // Redirect to the admin panel after deletion
        header("Location: admin_panel.php");
        exit();

    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $conn->rollback();
        exit();
    }
} else {
    echo "Hotel ID not provided.";
}
?>
