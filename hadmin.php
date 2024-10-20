<?php

include('config.php');

// Check if the user is logged in and is a hotel admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'hotel_admin') {
    header("Location: login.php");
    exit;
}

$admin_id = $_SESSION['user_id'];

// Fetch hotels managed by this admin
$stmt = $conn->prepare("SELECT * FROM hotels WHERE admin_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="admin-container">
        <h2>Hotel Admin Dashboard</h2>

        <section>
            <h3>Add New Hotel</h3>
            <form action="add_hotel.php" method="POST">
                <label for="hotel_name">Hotel Name</label>
                <input type="text" id="hotel_name" name="hotel_name" required>

                <label for="location">Location</label>
                <input type="text" id="location" name="location" required>

                <label for="description">Description</label>
                <textarea id="description" name="description" required></textarea>

                <button type="submit">Add Hotel</button>
            </form>
        </section>

        <section>
            <h3>Your Hotels</h3>
            <div class="hotels-list">
                <?php
                while($hotel = $result->fetch_assoc()) {
                    echo "<div class='hotel'>";
                    echo "<h4>" . $hotel['name'] . "</h4>";
                    echo "<p>Location: " . $hotel['location'] . "</p>";
                    echo "<p>Total Rooms: " . $hotel['total_rooms'] . "</p>";
                    echo "<a href='manage_rooms.php?hotel_id=" . $hotel['id'] . "' class='manage-link'>Manage Rooms</a>";
                    echo "</div>";
                }
                ?>
            </div>
        </section>
    </div>
</body>
</html>
