<?php

include('config.php');

// Check if the user is logged in and is a hotel admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Fetch room details
$room_id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM rooms WHERE room_id = ?");
$stmt->bind_param("i", $room_id);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handle room update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_number = $_POST['room_number'];
    $room_name = $_POST['room_name'];
    $facilities = $_POST['facilities'];
    $price_per_night = $_POST['price_per_night'];
    $max_adults = $_POST['max_adults'];
    $max_children = $_POST['max_children'];
    $availability = $_POST['availability'];

    $stmt = $conn->prepare("UPDATE rooms SET room_number = ?, room_name = ?, facilities = ?, price_per_night = ?, max_adults = ?, max_children = ?, availability = ? WHERE room_id = ?");
    $stmt->bind_param("sssdiisi", $room_number, $room_name, $facilities, $price_per_night, $max_adults, $max_children, $availability, $room_id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_hotel_details.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Room</title>
    <link rel="stylesheet" href="edit_room.css">
</head>
<body>
    <h2>Edit Room</h2>
    <form action="admin_edit_room.php?id=<?php echo $room_id; ?>" method="POST">
        <label for="room_number">Room Number</label>
        <input type="text" id="room_number" name="room_number" value="<?php echo htmlspecialchars($room['room_number']); ?>" required>

        <label for="room_name">Room Name</label>
        <input type="text" id="room_name" name="room_name" value="<?php echo htmlspecialchars($room['room_name']); ?>" required>

        <label for="facilities">Facilities</label>
        <textarea id="facilities" name="facilities" required><?php echo htmlspecialchars($room['facilities']); ?></textarea>

        <label for="price_per_night">Price per Night</label>
        <input type="number" id="price_per_night" name="price_per_night" value="<?php echo htmlspecialchars($room['price_per_night']); ?>" required>

        <label for="max_adults">Max Adults</label>
        <input type="number" id="max_adults" name="max_adults" value="<?php echo htmlspecialchars($room['max_adults']); ?>" required>

        <label for="max_children">Max Children</label>
        <input type="number" id="max_children" name="max_children" value="<?php echo htmlspecialchars($room['max_children']); ?>" required>

        <label for="availability">Availability</label>
        <select name="availability">
            <option value="available" <?php if ($room['availability'] == 'available') echo 'selected'; ?>>Available</option>
            <option value="unavailable" <?php if ($room['availability'] == 'unavailable') echo 'selected'; ?>>Unavailable</option>
        </select>

        <button type="submit">Update Room</button>
    </form>
</body>
</html>
