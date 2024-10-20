<?php
include 'config.php';

// Check if a destination ID is passed
if (isset($_GET['id'])) {
    $destination_id = intval($_GET['id']);

    // Fetch destination details from the database
    $result = $conn->query("SELECT * FROM destinations WHERE destination_id = $destination_id");
    $destination = $result->fetch_assoc();
}

// Check if form is submitted to update the destination
if (isset($_POST['update_destination'])) {
    $desti_name = $conn->real_escape_string($_POST['desti_name']);
    $desti_description = $conn->real_escape_string($_POST['desti_description']);
    $new_city = $conn->real_escape_string($_POST['new_city']);

    // If a new city is added, insert it into the cities table
    if (!empty($new_city)) {
        $city_query = "INSERT INTO cities (city_name) VALUES ('$new_city')";
        if ($conn->query($city_query) === TRUE) {
            $city = $new_city;
        } else {
            echo "<p>Error adding city: " . $conn->error . "</p>";
        }
    } else {
        $city = $conn->real_escape_string($_POST['city']);
    }

    // Update the destination in the database
    $updateQuery = "UPDATE destinations SET desti_name='$desti_name', desti_description='$desti_description', city='$city' WHERE destination_id=$destination_id";
    if ($conn->query($updateQuery) === TRUE) {
        // Redirect to edit_destination.php after successful update
        header("Location: edit_destination.php?id=$destination_id");
        exit();
    } else {
        echo "<p>Error updating destination: " . $conn->error . "</p>";
    }
}

// Handle image upload
if (isset($_POST['upload_image'])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if the file is an actual image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
        // Check file size (limit to 5MB)
        if ($_FILES["image"]["size"] <= 5000000) {
            // Allow only certain file formats
            if (in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
                // Move file to the target directory
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    // Insert the image into the database
                    $insertImageQuery = "INSERT INTO destination_images (destination_id, image_url) VALUES ($destination_id, '$target_file')";
                    if ($conn->query($insertImageQuery) === TRUE) {
                        // Redirect to edit_destination.php after successful image upload
                        header("Location: edit_destination.php?id=$destination_id");
                        exit();
                    } else {
                        echo "<p>Error uploading image: " . $conn->error . "</p>";
                    }
                } else {
                    echo "<p>Sorry, there was an error uploading your file.</p>";
                }
            } else {
                echo "<p>Only JPG, JPEG, PNG, and GIF files are allowed.</p>";
            }
        } else {
            echo "<p>Sorry, your file is too large.</p>";
        }
    } else {
        echo "<p>File is not an image.</p>";
    }
}


// Fetch images for this destination
$imagesResult = $conn->query("SELECT * FROM destination_images WHERE destination_id = $destination_id");
if (isset($_GET['delete_image'])) {
    $image_id = intval($_GET['delete_image']);
    
    // Fetch the image URL from the database before deleting
    $getImageQuery = "SELECT image_url FROM destination_images WHERE id = $image_id";
    $imageResult = $conn->query($getImageQuery);
    $image = $imageResult->fetch_assoc();

    if ($image) {
        // Delete the file from the server
        $image_path = $image['image_url'];
        if (file_exists($image_path)) {
            unlink($image_path); // Deletes the file from the server
        }

        // Now delete the image record from the database
        $deleteImageQuery = "DELETE FROM destination_images WHERE id = $image_id";
        if ($conn->query($deleteImageQuery) === TRUE) {
            // Redirect to the same page after deletion
            header("Location: edit_destination.php?id=$destination_id");
            exit();
        } else {
            echo "<p>Error deleting image: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>Image not found.</p>";
    }
}


$cities = $conn->query("SELECT * FROM cities ORDER BY city_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Destination</title>
    <link rel="stylesheet" href="edit_destination.css">
    <script>
        function toggleCityInput() {
            var citySelect = document.getElementById('city');
            var newCityInput = document.getElementById('new_city_input');

            if (citySelect.value === "add_new_city") {
                newCityInput.style.display = 'block';
            } else {
                newCityInput.style.display = 'none';
            }
        }
    </script>
</head>
<body>
<?php include 'header.php'; ?>
    <h1>Edit Destination</h1>

    <!-- Edit destination form -->
    <form action="edit_destination.php?id=<?php echo $destination_id; ?>" method="POST">
        <label for="desti_name">Destination Name</label>
        <input type="text" id="desti_name" name="desti_name" value="<?php echo htmlspecialchars($destination['desti_name']); ?>" required>

        <label for="desti_description">Description</label>
        <textarea id="desti_description" name="desti_description" required><?php echo htmlspecialchars($destination['desti_description']); ?></textarea>

        <label for="city">City</label>
        <select id="city" name="city" onchange="toggleCityInput()">
            <option value="">Select city</option>
            <?php while ($city = $cities->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($city['city_name']); ?>" <?php echo ($destination['city'] === $city['city_name']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($city['city_name']); ?>
                </option>
            <?php endwhile; ?>
            <option value="add_new_city">Add New City</option>
        </select>

        <div id="new_city_input" style="display:none;">
            <label for="new_city">New City Name</label>
            <input type="text" id="new_city" name="new_city">
        </div>

        <button type="submit" name="update_destination">Update Destination</button>
    </form>

    <!-- Image upload form -->
    <h2>Upload New Image</h2>
    <form action="edit_destination.php?id=<?php echo $destination_id; ?>" method="POST" enctype="multipart/form-data">
        <input type="file" name="image" accept="image/*" required>
        <button type="submit" name="upload_image">Upload Image</button>
    </form>

    <!-- Show images and delete option -->
    <h2>Images for this Destination</h2>
    <?php if ($imagesResult->num_rows > 0): ?>
        <div>
            <?php while ($image = $imagesResult->fetch_assoc()): ?>
                <div style="display: inline-block; margin: 10px;">
                    <img src="<?php echo htmlspecialchars($image['image_url']); ?>" alt="Image" style="width: 150px; height: auto;">
                    <br>
                    <a href="edit_destination.php?id=<?php echo $destination_id; ?>&delete_image=<?php echo $image['id']; ?>" onclick="return confirm('Are you sure you want to delete this image?');">Delete</a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No images found for this destination.</p>
    <?php endif; ?>
</body>
</html>
