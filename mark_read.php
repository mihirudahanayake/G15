<?php
include('config.php');

// Ensure the user is logged in as a hotel admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'hotel_admin') {
    header("Location: login.html");
    exit();
}

// Check if the notification ID is provided
if (isset($_POST['notification_id'])) {
    $notification_id = $_POST['notification_id'];

    // Update the status of the notification to 'read'
    $stmt = $conn->prepare("UPDATE notifications SET status = 'read' WHERE notification_id = ?");
    $stmt->bind_param("i", $notification_id);
    $stmt->execute();
    $stmt->close();

    // Redirect back to the hotel dashboard after marking as read
    header("Location: hotel_dashboard.php");
    exit();
} else {
    // Redirect back if no notification ID is provided
    header("Location: hotel_dashboard.php");
    exit();
}
?>
