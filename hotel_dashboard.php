<?php

    include('config.php');

    // Check if the user is logged in and is a hotel admin
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'hotel_admin') {
        header("Location: login.html");
        exit();
    }

    // Fetch hotel_id from session
    $hotel_id = $_SESSION['hotel_id'];

    // Fetch the hotel details for the current user
    $hotel_stmt = $conn->prepare("SELECT hotel_id FROM hotels WHERE hotel_id = ? AND user_id = ?");
    $hotel_stmt->bind_param("ii", $hotel_id, $_SESSION['user_id']);
    $hotel_stmt->execute();
    $hotel_result = $hotel_stmt->get_result();
    $hotel_stmt->close();

    if ($hotel_result->num_rows === 0) {
        // If the hotel does not belong to the logged-in user, redirect to an error page
        $_SESSION['error_message'] = "Add your hotel.";
        header("Location: add_hotel.php"); // or another appropriate page
        exit();
    }

    $h_stmt = $conn->prepare("SELECT hotel_name FROM hotels WHERE hotel_id = ?");
    $h_stmt->bind_param("i", $hotel_id);  // Bind the parameters
    
    $h_stmt->execute();
    $result = $h_stmt->get_result();  // Get the result of the query
    
    if ($result->num_rows > 0) {
        $h_name = $result->fetch_assoc();
        $hotel_name = $h_name['hotel_name'];
    } else {
        $hotel_name = "No hotel found";  // Handle no result found
    }
    
    $h_stmt->close();  // Close the statement
    

    // Initialize variables
    $cities = [];
    $destinations = [];
    $message = "";

    // Fetch cities from the cities table
    $cities_stmt = $conn->prepare("SELECT * FROM cities");
    $cities_stmt->execute();
    $cities_result = $cities_stmt->get_result();
    while ($city = $cities_result->fetch_assoc()) {
        $cities[] = $city;
    }
    $cities_stmt->close();

    // Fetch destinations for the selected city
    if (isset($_POST['city'])) {
        $city = $_POST['city'];

        $destinations_stmt = $conn->prepare("SELECT * FROM destinations WHERE city = ?");
        $destinations_stmt->bind_param("s", $city);
        $destinations_stmt->execute();
        $destinations_result = $destinations_stmt->get_result();
        while ($destination = $destinations_result->fetch_assoc()) {
            $destinations[] = $destination;
        }
        $destinations_stmt->close();
    }

    // Handle adding a new city
    if (isset($_POST['add_city'])) {
        $new_city = $_POST['new_city'];

        $check_city_stmt = $conn->prepare("SELECT * FROM cities WHERE city_name = ?");
        $check_city_stmt->bind_param("s", $new_city);
        $check_city_stmt->execute();
        $check_city_result = $check_city_stmt->get_result();
        $check_city_stmt->close();

        if ($check_city_result->num_rows > 0) {
            $message = "City already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO cities (city_name) VALUES (?)");
            $stmt->bind_param("s", $new_city);
            $stmt->execute();
            $stmt->close();
            $message = "City added successfully.";
            
            // Refresh cities list
            $cities_stmt = $conn->prepare("SELECT * FROM cities");
            $cities_stmt->execute();
            $cities_result = $cities_stmt->get_result();
            $cities = $cities_result->fetch_all(MYSQLI_ASSOC);
            $cities_stmt->close();
        }
    }

    // Handle adding a new destination
    if (isset($_POST['add_destination'])) {
        $desti_name = $_POST['desti_name'];
        $desti_description = $_POST['desti_description'];
        $city = $_POST['city'];

        $stmt = $conn->prepare("INSERT INTO destinations (desti_name, desti_description, city) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $desti_name, $desti_description, $city);
        $stmt->execute();
        $stmt->close();
        $message = "Destination added successfully.";
    }

    // Handle assigning destination to the hotel
    if (isset($_POST['assign_destination'])) {
        $destination_id = $_POST['destination_id'];

        // Check if the destination is already assigned to this hotel
        $check_stmt = $conn->prepare("SELECT * FROM hotel_destinations WHERE hotel_id = ? AND destination_id = ?");
        $check_stmt->bind_param("ii", $hotel_id, $destination_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        $check_stmt->close();

        if ($result->num_rows > 0) {
            $message = "The destination is already assigned to your hotel.";
        } else {
            $stmt = $conn->prepare("INSERT INTO hotel_destinations (hotel_id, destination_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $hotel_id, $destination_id);
            $stmt->execute();
            $stmt->close();
            $message = "Destination successfully assigned to your hotel.";
        }
    }

    // Fetch already assigned destinations
    $assigned_destinations_stmt = $conn->prepare("
        SELECT d.desti_name, d.desti_description, d.city 
        FROM hotel_destinations hd 
        JOIN destinations d ON hd.destination_id = d.destination_id 
        WHERE hd.hotel_id = ?
    ");
    $assigned_destinations_stmt->bind_param("i", $hotel_id);
    $assigned_destinations_stmt->execute();
    $assigned_destinations_result = $assigned_destinations_stmt->get_result();
    $assigned_destinations = $assigned_destinations_result->fetch_all(MYSQLI_ASSOC);
    $assigned_destinations_stmt->close();

    // Fetch existing images for the hotel
    $hotel_images_stmt = $conn->prepare("SELECT * FROM hotel_images WHERE hotel_id = ?");
    $hotel_images_stmt->bind_param("i", $hotel_id);
    $hotel_images_stmt->execute();
    $hotel_images_result = $hotel_images_stmt->get_result();
    $hotel_images = $hotel_images_result->fetch_all(MYSQLI_ASSOC);
    $hotel_images_stmt->close();




// Handle image upload
if (isset($_POST['upload_image'])) {
    // File upload path
    $target_dir = "uploads/";

    // Check if the directory exists, create if it doesn't
    if (!is_dir($target_dir)) {
        if (!mkdir($target_dir, 0777, true)) {
            die("Failed to create directory: $target_dir");
        }
    }

    // Check how many images the hotel already has
    $stmt = $conn->prepare("SELECT COUNT(*) as image_count FROM hotel_images WHERE hotel_id = ?");
    $stmt->bind_param("i", $hotel_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $image_count = $row['image_count'];
    $stmt->close();

    // Check if the hotel already has 5 images
    if ($image_count >= 5) {
        $message = "You cannot upload more than 5 images for this hotel.";
        echo "<script>alert('$message');</script>"; // Display alert
        return; // Exit the script if the image limit is reached
    }

    // Proceed with file upload if limit is not reached
    $target_file = $target_dir . basename($_FILES["hotel_image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if the file is an image
    $check = getimagesize($_FILES["hotel_image"]["tmp_name"]);
    if ($check !== false) {
        // Check for upload errors
        if ($_FILES["hotel_image"]["error"] !== UPLOAD_ERR_OK) {
            $message = "File upload error. Code: " . $_FILES["hotel_image"]["error"];
            echo "<script>alert('$message');</script>"; // Display alert
            return; // Exit the script if there’s an upload error
        }

        // Allowed file types
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowed_types)) {
            $message = "Sorry, only JPG, JPEG, PNG, & GIF files are allowed.";
            echo "<script>alert('$message');</script>"; // Display alert
            return; // Exit the script if the file type is not allowed
        }

        // Avoid overwriting existing files
        $fileName = pathinfo($target_file, PATHINFO_FILENAME);
        $extension = pathinfo($target_file, PATHINFO_EXTENSION);
        $counter = 1;

        while (file_exists($target_file)) {
            $target_file = $target_dir . $fileName . '_' . $counter . '.' . $extension;
            $counter++;
        }

        // Move file to target directory
        if (move_uploaded_file($_FILES["hotel_image"]["tmp_name"], $target_file)) {
            // Insert file path into the database
            $stmt = $conn->prepare("INSERT INTO hotel_images (hotel_id, image_path) VALUES (?, ?)");
            $stmt->bind_param("is", $hotel_id, $target_file);
            if ($stmt->execute()) {
                // Successful insert
                header("Location: hotel_dashboard.php?hotel_id=$hotel_id");
                exit; // Important: Use exit after header redirection
            } else {
                $message = "Error inserting image path into database: " . $stmt->error;
                echo "<script>alert('$message');</script>"; // Display alert
            }
            $stmt->close();
        } else {
            $message = "Error moving uploaded file. Please check folder permissions.";
            error_log("Error moving uploaded file: " . print_r($_FILES["hotel_image"], true)); // Log details for debugging
            echo "<script>alert('$message');</script>"; // Display alert
        }
    } else {
        $message = "File is not an image.";
        echo "<script>alert('$message');</script>"; // Display alert
    }
}



    // Handle image deletion
    if (isset($_POST['delete_image'])) {
        $image_id = $_POST['image_id'];

        // Fetch the image path from the database
        $image_stmt = $conn->prepare("SELECT image_path FROM hotel_images WHERE image_id = ? AND hotel_id = ?");
        $image_stmt->bind_param("ii", $image_id, $hotel_id);
        $image_stmt->execute();
        $image_result = $image_stmt->get_result();
        $image_stmt->close();

        if ($image_result->num_rows > 0) {
            $image = $image_result->fetch_assoc();
            $image_path = $image['image_path'];

            // Delete the image file from the server
            if (unlink($image_path)) {
                // Delete the record from the database
                $delete_stmt = $conn->prepare("DELETE FROM hotel_images WHERE image_id = ? AND hotel_id = ?");
                $delete_stmt->bind_param("ii", $image_id, $hotel_id);
                $delete_stmt->execute();
                $delete_stmt->close();
                header("Location: hotel_dashboard.php?hotel_id=$hotel_id");
            } else {
                $message = "Error deleting image file.";
            }
        } else {
            $message = "Image not found.";
        }
        
    }

?>

<?php
// Assuming hotel_id is stored in the session when the hotel admin logs in
$hotel_id = $_SESSION['hotel_id'];

// Fetch unread notifications for the current hotel
$stmt = $conn->prepare("SELECT * FROM notifications WHERE hotel_id = ? AND status = 'unread'");
$stmt->bind_param("i", $hotel_id);
$stmt->execute();
$notifications = $stmt->get_result();
$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Dashboard</title>
    <link rel="stylesheet" href="hotel-dashboard.css">
</head>
<body>
    <div class="bg"></div>
    <?php include('header.php');?>
    <div class="dashboard-container">
    <h1><?php echo htmlspecialchars($hotel_name); ?></h1>

        <div class="notifications">
            <?php if ($notifications->num_rows > 0): ?>
                <ul>
                    <?php while ($notification = $notifications->fetch_assoc()): ?>
                        <li class="notify">
                            <p><strong>Booking ID:</strong> <?php echo htmlspecialchars($notification['booking_id']); ?></p>
                            <p><?php echo htmlspecialchars($notification['message']); ?></p>
                            <p><small><?php echo htmlspecialchars($notification['created_at']); ?></small></p>
                            <form action="mark_read.php" method="post">
                                <input type="hidden" name="notification_id" value="<?php echo $notification['notification_id']; ?>">
                                <button type="submit" id="mark">Mark as Read</button>
                            </form>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p class="no-notify">No new notifications.</p>
            <?php endif; ?>
        </div>


        <ul>
            <?php foreach ($hotel_images as $image): ?>
                <li>
                    <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="Hotel Image" width="100">
                    <form method="POST" action="hotel_dashboard.php" style="display:inline;">
                        <input type="hidden" name="image_id" value="<?php echo $image['image_id']; ?>">
                        <button type="submit" name="delete_image" id="dlt">Delete</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
        


        <!-- Form to upload hotel images -->
        <form action="hotel_dashboard.php" method="POST" enctype="multipart/form-data">
            <label for="hotel_image">Upload Hotel Image:</label>
            <input type="file" name="hotel_image" id="hotel_image" accept="image/*" required>
            <button type="submit" name="upload_image" id="img-upload">Upload Image</button>
        </form>


        <!-- Message Display -->
        <?php if (!empty($message)): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <!-- Show Already Assigned Destinations -->
        <div class="box">
            <div class="destination-box">
                <h2>Destinations Assigned to Your Hotel</h2>
                <ul>
                    <?php if (empty($assigned_destinations)): ?>
                        <li>No destinations assigned to your hotel yet.</li>
                    <?php else: ?>
                        <?php foreach ($assigned_destinations as $destination): ?>
                            <li><strong><?php echo htmlspecialchars($destination['desti_name']); ?></strong></li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <button onclick="location.href='assign_desti.php'">Assign New Destination</button>
        </div>




        <!-- Manage Rooms Section -->
        <h3>Manage Rooms</h3>

        <div class="room-container">
            <?php
            // Fetch rooms for this hotel
            $stmt = $conn->prepare("SELECT * FROM rooms WHERE hotel_id = ?");
            $stmt->bind_param("i", $hotel_id);
            $stmt->execute();
            $rooms = $stmt->get_result();
            $stmt->close();

            while ($room = $rooms->fetch_assoc()): ?>
                <div class="room-box">
                    <h4>Room <?php echo htmlspecialchars($room['room_number']); ?></h4>
                    <p><strong>Price/Night:</strong> <?php echo htmlspecialchars($room['price_per_night']); ?></p>
                    <p><strong>Max Adults:</strong> <?php echo htmlspecialchars($room['max_adults']); ?></p>
                    <p><strong>Max Children:</strong> <?php echo htmlspecialchars($room['max_children']); ?></p>
                    <div class="actions">
                        <form action="delete_room.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this room?');">
                            <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room['room_id']); ?>">
                            <button type="submit" id="room-dlt">Delete</button>
                        </form>
                        <form action="edit_room.php" method="GET">
                            <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room['room_id']); ?>">
                            <button type="submit" id="view-edit">View & Edit</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
            <button onclick="location.href='add_room.php'" class="add-room">Add New Room</button>
        </div>
        
    </div>
    <div class="foot">
        <?php include ("footer.php") ?>
    </div>
</body>
</html>
