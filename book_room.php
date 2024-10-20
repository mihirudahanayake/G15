<?php

include('config.php');

// Check if the form was submitted
if (isset($_POST['book_now'])) {
    // Ensure user is logged in and session is set
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id']; // Assume user ID is stored in session
        $room_id = $conn->real_escape_string($_POST['room_id']);
        $start_date = $conn->real_escape_string($_POST['start_date']);
        $end_date = $conn->real_escape_string($_POST['end_date']);

        // Check if the room is already booked in the desired date range
        $check_query = "SELECT COUNT(*) FROM bookings WHERE room_id = ? AND (
                            (start_date <= ? AND end_date >= ?) OR
                            (start_date <= ? AND end_date >= ?)
                        )";
        $stmt_check = $conn->prepare($check_query);
        $stmt_check->bind_param("issss", $room_id, $end_date, $start_date, $start_date, $end_date);
        $stmt_check->execute();
        $stmt_check->bind_result($count);
        $stmt_check->fetch();
        $stmt_check->close();

        if ($count > 0) {
            echo "<p>Error: Room is already booked for the selected dates.</p>";
        } else {
            // Insert booking into the database
            $query = "INSERT INTO bookings (user_id, room_id, start_date, end_date, booking_status)
                      VALUES (?, ?, ?, ?, 'confirmed')";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iiss", $user_id, $room_id, $start_date, $end_date);

            if ($stmt->execute()) {
                // Get the ID of the newly created booking
                $booking_id = $stmt->insert_id;

                // Fetch room details (including room number and hotel_id) for the notification
                $stmt_room = $conn->prepare("SELECT room_number, hotel_id FROM rooms WHERE room_id = ?");
                $stmt_room->bind_param("i", $room_id);
                $stmt_room->execute();
                $result = $stmt_room->get_result();
                $room = $result->fetch_assoc();

                // Now insert the notification with hotel_id
                $hotel_id = $room['hotel_id'];
                $message = "A new booking has been made for room " . $room['room_number'];
                $stmt_notif = $conn->prepare("INSERT INTO notifications (user_id, room_id, booking_id, hotel_id, message, status) VALUES (?, ?, ?, ?, ?, 'unread')");
                $stmt_notif->bind_param("iiiis", $user_id, $room_id, $booking_id, $hotel_id, $message);
                $stmt_notif->execute();

                // Close statements
                $stmt->close();
                $stmt_room->close();
                $stmt_notif->close();

                // Redirect to room details page after booking success
                header("Location: room_details.php?id=$room_id");
                exit();
            } else {
                echo "<p>Error: " . $stmt->error . "</p>";
            }
        }
    } else {
        // Redirect to room details if the user is not logged in
        $room_id = $conn->real_escape_string($_POST['room_id']);
        header("Location: room_details.php?id=$room_id");
        exit();
    }
}

$conn->close();
?>
