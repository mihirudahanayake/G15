<?php

include('config.php');

// Check if the admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Fetch hotel details
if (isset($_GET['id'])) {
    $hotel_id = $_GET['id'];

    // Fetch hotel details
    $hotel_stmt = $conn->prepare("SELECT * FROM hotels WHERE hotel_id = ?");
    $hotel_stmt->bind_param("i", $hotel_id);
    $hotel_stmt->execute();
    $hotel = $hotel_stmt->get_result()->fetch_assoc();
    $hotel_stmt->close();

    // Fetch rooms for this hotel
    $rooms_stmt = $conn->prepare("SELECT * FROM rooms WHERE hotel_id = ?");
    $rooms_stmt->bind_param("i", $hotel_id);
    $rooms_stmt->execute();
    $rooms = $rooms_stmt->get_result();
    $rooms_stmt->close();
} else {
    header("Location: admin_panel.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Details</title>
    <link rel="stylesheet" href="admin_hotel_details.css">
</head>
<body>
    <div class="admin-container">
        <h2>Hotel Details</h2>

        <h3><?php echo htmlspecialchars($hotel['hotel_name']); ?></h3>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($hotel['location']); ?></p>

        <h3>Rooms in this Hotel</h3>
        <table>
            <tr>
                <th>Room Name</th>
                <th>Availability</th>
                <th>Actions</th>
            </tr>
            <?php while ($room = $rooms->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($room['room_name']); ?></td>
                <td><?php echo htmlspecialchars($room['availability']); ?></td>
                <td>
                    <a href="admin_edit_room.php?id=<?php echo $room['room_id']; ?>">Edit</a>
                    <a href="admin_delete_room.php?id=<?php echo $room['room_id']; ?>" onclick="return confirm('Are you sure you want to delete this room?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <a href="admin_panel.php">Back to Admin Panel</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>
