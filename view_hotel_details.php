<?php

include('config.php');

// Check if the admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Ensure a hotel ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid hotel ID.";
    exit();
}

$hotel_id = intval($_GET['id']);

// Fetch hotel details
$hotel_stmt = $conn->prepare("SELECT * FROM hotels WHERE hotel_id = ?");
$hotel_stmt->bind_param("i", $hotel_id);
$hotel_stmt->execute();
$hotel = $hotel_stmt->get_result()->fetch_assoc();
$hotel_stmt->close();

if (!$hotel) {
    echo "Hotel not found.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Details</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="details-container">
        <h2>Hotel Details</h2>

        <h3>Hotel Information</h3>
        <p><strong>Hotel Name:</strong> <?php echo htmlspecialchars($hotel['hotel_name']); ?></p>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($hotel['location']); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($hotel['description']); ?></p>
        <a href="admin_panel.php">Back to Admin Panel</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>
