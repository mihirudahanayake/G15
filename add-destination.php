<?php
// Include the database configuration
include 'config.php';

// Handle form submission to add a new destination
if (isset($_POST['add_destination'])) {
    $desti_name = $conn->real_escape_string($_POST['desti_name']);
    $desti_description = $conn->real_escape_string($_POST['desti_description']);
    
    // Check if a new city is added or an existing one is selected
    $new_city = $conn->real_escape_string($_POST['new_city']);
    if (!empty($new_city)) {
        // Insert the new city into the cities table
        $city_query = "INSERT INTO cities (city_name) VALUES ('$new_city')";
        if ($conn->query($city_query) === TRUE) {
            $city = $new_city;
        } else {
            echo "<p>Error adding city: " . $conn->error . "</p>";
        }
    } else {
        $city = $conn->real_escape_string($_POST['city']);
    }

    // Insert the destination into the destinations table
    $query = "INSERT INTO destinations (desti_name, desti_description, city) 
              VALUES ('$desti_name', '$desti_description', '$city')";
    if ($conn->query($query) === TRUE) {
        $destination_id = $conn->insert_id;

        // Handle file uploads
        if (isset($_FILES['images']) && $_FILES['images']['error'][0] === UPLOAD_ERR_OK) {
            $uploadFileDir = './uploads/';
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $fileTmpPath = $_FILES['images']['tmp_name'][$key];
                $fileName = $_FILES['images']['name'][$key];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                $maxFileSize = 5 * 1024 * 1024; // 5MB

                if (in_array($fileExtension, $allowedExtensions) && $_FILES['images']['size'][$key] <= $maxFileSize) {
                    $dest_path = $uploadFileDir . $fileName;
                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $conn->query("INSERT INTO destination_images (destination_id, image_url) VALUES ('$destination_id', '$dest_path')");
                    } else {
                        echo "<p>Error uploading file.</p>";
                    }
                } else {
                    echo "<p>Invalid file or file too large.</p>";
                }
            }
        }
        echo "<p>Destination added successfully.</p>";
    } else {
        echo "<p>Error: " . $conn->error . "</p>";
    }
}

$cities = $conn->query("SELECT * FROM cities ORDER BY city_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Destination</title>
    <link rel="stylesheet" href="add-destination.css">
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
<h1>Add Destination</h1>

<form action="add-destination.php" method="POST" enctype="multipart/form-data">
    <label for="desti_name">Destination Name</label>
    <input type="text" id="desti_name" name="desti_name" required>

    <label for="desti_description">Description</label>
    <textarea id="desti_description" name="desti_description" required></textarea>

    <label for="city">City</label>
    <select id="city" name="city" onchange="toggleCityInput()">
        <option value="">Select city</option>
        <?php while ($city = $cities->fetch_assoc()): ?>
            <option value="<?php echo htmlspecialchars($city['city_name']); ?>">
                <?php echo htmlspecialchars($city['city_name']); ?>
            </option>
        <?php endwhile; ?>
        <option value="add_new_city">Add New City</option>
    </select>

    <div id="new_city_input" style="display:none;">
        <label for="new_city">New City Name</label>
        <input type="text" id="new_city" name="new_city">
    </div>

    <label for="images">Images</label>
    <input type="file" id="images" name="images[]" multiple required>

    <button type="submit" name="add_destination">Add Destination</button>
</form>

</body>
</html>
