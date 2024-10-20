<?php

include('config.php');

// Check if the user is logged in and is a hotel admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] == 'user') {
    header("Location: login.html");
    exit();
}

// Fetch room details
$room_id = isset($_GET['room_id']) ? $_GET['room_id'] : 0;
$stmt = $conn->prepare("SELECT * FROM rooms WHERE room_id = ?");
$stmt->bind_param("i", $room_id);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handle room update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_room'])) {
    $room_id = $_POST['room_id']; // Assuming you're passing the room_id to identify the room being updated
    $hotel_id = $_SESSION['hotel_id']; // Assuming the admin's ID is linked to hotel ID
    $room_number = $_POST['room_number'];
    $room_name = $_POST['room_name'];
    $availability = $_POST['availability'];
    $capacity_adults = $_POST['max_adults'];
    $capacity_children = $_POST['max_children'];
    $facilities = $_POST['facilities'];
    $price_per_night = $_POST['price_per_night'];

    // Prepare the SQL statement for updating the room details
    $stmt = $conn->prepare("
        UPDATE rooms 
        SET 
            room_number = ?, 
            room_name = ?, 
            max_adults = ?, 
            max_children = ?, 
            facilities = ?, 
            price_per_night = ?, 
            availability = ?
        WHERE room_id = ? AND hotel_id = ?
    ");

    // Bind the parameters to the statement
    $stmt->bind_param("ssiisssii", $room_number, $room_name, $capacity_adults, $capacity_children, $facilities, $price_per_night, $availability, $room_id, $hotel_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Room updated successfully!');</script>";
    header("Location: edit_room.php?room_id=$room_id");
    exit();
}

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_image'])) {
    $room_id = $_POST['room_id']; // Ensure room_id is present

    // Check if the room_id exists in the rooms table and get the room number
    $stmt = $conn->prepare("SELECT room_number FROM rooms WHERE room_id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $roomData = $result->fetch_assoc();
        $room_number = $roomData['room_number'];
    } else {
        echo "<script>alert('Invalid room ID.');</script>";
        exit();
    }
    $stmt->close();

    // Count how many images have been uploaded for this room
    $stmt = $conn->prepare("SELECT COUNT(*) as image_count FROM room_images WHERE room_id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $imageCountResult = $stmt->get_result();
    $imageData = $imageCountResult->fetch_assoc();
    $image_count = $imageData['image_count'] + 1; // Increment for new image
    $stmt->close();

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";

        // Set the image name as room_$room_number_image$image_count
        $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $target_file = $target_dir . "room_" . $room_number . "_image" . $image_count . "." . $imageFileType;

        $uploadOk = 1;

        // Check if image file is a real image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            echo "<script>alert('File is not an image.');</script>";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["image"]["size"] > 500000) {
            echo "<script>alert('Sorry, your file is too large.');</script>";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');</script>";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "<script>alert('Sorry, your file was not uploaded.');</script>";
        } else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // Insert image info into database
                $stmt = $conn->prepare("INSERT INTO room_images (room_id, image_path) VALUES (?, ?)");
                $stmt->bind_param("is", $room_id, $target_file);
                $stmt->execute();
                $stmt->close();
                echo "<script>alert('The file " . htmlspecialchars(basename($target_file)) . " has been uploaded.');</script>";
                header("Location: edit_room.php?room_id=$room_id");
            } else {
                echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
            }
        }
    } else {
        echo "<script>alert('No file uploaded or file upload error.');</script>";
    }
}

// Handle image deletion
if (isset($_GET['delete_image_id'])) {
    $image_id = $_GET['delete_image_id'];
    // Fetch the image path to delete the file from the server
    $stmt = $conn->prepare("SELECT image_path FROM room_images WHERE image_id = ?");
    $stmt->bind_param("i", $image_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $image = $result->fetch_assoc();
    $stmt->close();

    if ($image && !empty($image['image_path'])) {
        // Delete the image file from the server
        if (file_exists($image['image_path'])) {
            unlink($image['image_path']);
        }
        // Delete the image record from the database
        $stmt = $conn->prepare("DELETE FROM room_images WHERE image_id = ?");
        $stmt->bind_param("i", $image_id);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('Image deleted successfully!');</script>";
        header("Location: edit_room.php?room_id=$room_id");
        exit();
    } else {
        echo "<script>alert('Image not found.');</script>";
    }
}

// Fetch room images
$imageStmt = $conn->prepare("SELECT * FROM room_images WHERE room_id = ?");
$imageStmt->bind_param("i", $room_id);
$imageStmt->execute();
$images = $imageStmt->get_result();
$imageStmt->close();
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
    <div class="bg"></div>
    <?php include('header.php');?>
    <div class="div1">
        
        <h2>Booking Details</h2>
        <p><b><?php
            // Fetch all booking date ranges for this room
            $stmt = $conn->prepare("SELECT booking_id, start_date, end_date FROM bookings WHERE room_id = ?");
            $stmt->bind_param("i", $room['room_id']);
            $stmt->execute();
            $booking_result = $stmt->get_result();
            $stmt->close();

            if ($booking_result->num_rows > 0) {
                while ($booking = $booking_result->fetch_assoc()) {
                    // Create a clickable link for each booking date range
                    echo "<a href='booking_details.php?booking_id=" . htmlspecialchars($booking['booking_id']) . "'>";
                    echo "Booking ID ".htmlspecialchars($booking['booking_id']).": Booked from " . htmlspecialchars($booking['start_date']) . " to " . htmlspecialchars($booking['end_date']) . "</a><br>";
                }
            } else {
                echo "No bookings";
            }
        ?></b></p>
        <h2>Edit Room</h2>
        <form method="POST" action="edit_room.php">
            <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room['room_id'] ?? ''); ?>">
            <label for="room_number">Room Number:</label>
            <input type="text" name="room_number" value="<?php echo htmlspecialchars($room['room_number'] ?? ''); ?>">

            <label for="room_name">Room Name:</label>
            <input type="text" name="room_name" value="<?php echo htmlspecialchars($room['room_name'] ?? ''); ?>">

            <label for="max_adults">Maximum Adults:</label>
            <input type="number" name="max_adults" value="<?php echo htmlspecialchars($room['max_adults'] ?? ''); ?>">

            <label for="max_children">Maximum Children:</label>
            <input type="number" name="max_children" value="<?php echo htmlspecialchars($room['max_children'] ?? ''); ?>">

            <label for="facilities">Facilities:</label>
            <textarea name="facilities"><?php echo htmlspecialchars($room['facilities'] ?? ''); ?></textarea>

            <label for="price_per_night">Price Per Night:</label>
            <input type="text" name="price_per_night" value="<?php echo htmlspecialchars($room['price_per_night'] ?? ''); ?>">

            <label for="availability">Availability:</label>
            <select name="availability">
                <option value="available" <?php if (($room['availability'] ?? '') == 'available') echo 'selected'; ?>>Available</option>
                <option value="unavailable" <?php if (($room['availability'] ?? '') == 'unavailable') echo 'selected'; ?>>Unavailable</option>
            </select>

            <button type="submit" name="update_room">Update Room</button>
        </form>
    </div>

    

    <h2>Room Images</h2>


    <div class="image-gallery">
        <?php if ($images && $images->num_rows > 0) {
            while ($image = $images->fetch_assoc()) { ?>
                <div class="image-item">
                    <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="Room Image" style="width: 100px; height: auto;">
                    <a href="edit_room.php?room_id=<?php echo htmlspecialchars($room['room_id']); ?>&delete_image_id=<?php echo htmlspecialchars($image['image_id']); ?>" onclick="return confirm('Are you sure you want to delete this image?');">Delete</a>
                </div>
            <?php }
        } else {
            echo "No images found for this room.";
        } ?>
    </div>
    <form method="POST" action="edit_room.php" enctype="multipart/form-data">
        <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room['room_id'] ?? ''); ?>">
        <label for="image">Upload New Image:</label>
        <input type="file" name="image" id="image" accept="image/*">
        <button type="submit" name="upload_image">Upload Image</button>
    </form>

</body>
</html>
