<?php

include('config.php');

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Check if user_id is provided
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Start a transaction to ensure data integrity
    $conn->begin_transaction();

    try {
        // Check if the user is a hotel admin
        $user_stmt = $conn->prepare("SELECT user_type FROM users WHERE user_id = ?");
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        $user_stmt->bind_result($user_type);
        $user_stmt->fetch();
        $user_stmt->close();

        // Check for bookings if the user is a regular user
        if ($user_type === 'user') {
            $booking_check_stmt = $conn->prepare("SELECT * FROM bookings WHERE user_id = ?");
            $booking_check_stmt->bind_param("i", $user_id);
            $booking_check_stmt->execute();
            $bookings_result = $booking_check_stmt->get_result();

            if ($bookings_result->num_rows > 0) {
                echo "User has active bookings. Here are the details:<br>";
                while ($booking = $bookings_result->fetch_assoc()) {
                    echo "Booking ID: " . htmlspecialchars($booking['booking_id']) . ", Room ID: " . htmlspecialchars($booking['room_id']) . ", Start Date: " . htmlspecialchars($booking['start_date']) . ", End Date: " . htmlspecialchars($booking['end_date']) . "<br>";
                }
                $booking_check_stmt->close();
                $conn->rollback();
                exit();
            }
            $booking_check_stmt->close();

        // Check for hotel associations if the user is a hotel admin
        } elseif ($user_type === 'hotel_admin') {
            $hotel_check_stmt = $conn->prepare("SELECT * FROM hotels WHERE admin_id = ?");
            $hotel_check_stmt->bind_param("i", $user_id);
            $hotel_check_stmt->execute();
            $hotels_result = $hotel_check_stmt->get_result();

            if ($hotels_result->num_rows > 0) {
                echo "Hotel Admin has associated hotels. Here are the details:<br>";
                while ($hotel = $hotels_result->fetch_assoc()) {
                    echo "Hotel ID: " . htmlspecialchars($hotel['hotel_id']) . ", Hotel Name: " . htmlspecialchars($hotel['hotel_name']) . ", Location: " . htmlspecialchars($hotel['location']) . "<br>";
                }
                $hotel_check_stmt->close();
                $conn->rollback();
                exit();
            }
            $hotel_check_stmt->close();
        }

        // Proceed to delete the user's bookings and the user
        $booking_stmt = $conn->prepare("DELETE FROM bookings WHERE user_id = ?");
        $booking_stmt->bind_param("i", $user_id);
        $booking_stmt->execute();
        $booking_stmt->close();

        $user_stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        $user_stmt->close();

        // Commit the transaction
        $conn->commit();

        // Redirect back to the admin panel with a success message
        header("Location: admin_panel.php?message=User+deleted+successfully");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $conn->rollback();
        // Redirect back with an error message
        header("Location: admin_panel.php?error=Failed+to+delete+user");
        exit();
    }
} else {
    echo "User ID not provided.";
}
?>
