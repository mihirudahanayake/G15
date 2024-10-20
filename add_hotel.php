<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hotel_name = $_POST['hotel_name'];
    // Set total_rooms to 0
    $total_rooms = 0; 
    $description = $_POST['description'];
    $admin_id = $_SESSION['user_id'];
    $city_name = $_POST['city_name'];
    $new_city_name = $_POST['new_city'] ?? ''; // New city to be added

    // Check if the hotel name already exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM hotels WHERE hotel_name = ?");
    $stmt->bind_param("s", $hotel_name);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        // Hotel name already exists, display an error message
        $_SESSION['error_message'] = "Your hotel name is already in use. Please enter another name.";
        header("Location: add_hotel.php");
        exit();
    } else {
        // Check if the city exists in the cities table
        if (!empty($new_city_name)) {
            // New city provided, add it to the cities table
            $stmt = $conn->prepare("INSERT INTO cities (city_name) VALUES (?)");
            $stmt->bind_param("s", $new_city_name);
            $stmt->execute();
            $city_id = $stmt->insert_id;
            $stmt->close();
        } else {
            // Use the existing city
            $stmt = $conn->prepare("SELECT city_id FROM cities WHERE city_name = ?");
            $stmt->bind_param("s", $city_name);
            $stmt->execute();
            $stmt->bind_result($city_id);
            $stmt->fetch();
            $stmt->close();
        }

        // Insert the new hotel into the database
        $stmt = $conn->prepare("INSERT INTO hotels (hotel_name, location, total_rooms, description, user_id, city_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisis", $hotel_name, $city_name, $total_rooms, $description, $admin_id, $city_id);
        $stmt->execute();
        $hotel_id = $stmt->insert_id;

        // Update admin's hotel_id
        $stmt = $conn->prepare("UPDATE users SET hotel_id = ? WHERE user_id = ?");
        $stmt->bind_param("ii", $hotel_id, $admin_id);
        $stmt->execute();

        $stmt->close();
        $conn->close();

        // Redirect to hotel dashboard
        header("Location: login.html");
        exit();
    }
}


// Fetch cities for the dropdown
$stmt = $conn->prepare("SELECT city_id, city_name FROM cities");
$stmt->execute();
$result = $stmt->get_result();
$cities = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Hotel</title>
    <link rel="stylesheet" href="add-hotel.css">
    <script>
        function toggleNewCityInput() {
            var citySelect = document.getElementById('city_name');
            var newCityInput = document.getElementById('new_city_input');
            var addCityButton = document.getElementById('add_city_button');
            if (citySelect.value === 'new_city') {
                newCityInput.style.display = 'block';
                document.getElementById('new_city').required = true;
                addCityButton.style.display = 'inline'; // Show Add City button
            } else {
                newCityInput.style.display = 'none';
                document.getElementById('new_city').required = false;
                addCityButton.style.display = 'none'; // Hide Add City button
            }
        }

        function addNewCity() {
            var newCityName = document.getElementById('new_city').value;
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'add_city.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert('City added successfully');
                    // Optionally, reload the page or update the city dropdown
                    window.location.reload();
                } else {
                    alert('Error adding city');
                }
            };
            xhr.send('city_name=' + encodeURIComponent(newCityName));
        }
    </script>
</head>
<body>
    <h2>Add Hotel Details</h2>
    
    <?php
    if (isset($_SESSION['error_message'])) {
        echo '<p style="color:red;">' . $_SESSION['error_message'] . '</p>';
        unset($_SESSION['error_message']);
    }
    ?>

    <form action="add_hotel.php" method="POST">
        <label for="hotel_name">Hotel Name</label>
        <input type="text" id="hotel_name" name="hotel_name" required>
        <label for="description">Description</label>
        <textarea id="description" name="description" required></textarea>
        <label for="city_name">Select City</label>
        <select id="city_name" name="city_name" required onchange="toggleNewCityInput();">
            <option value="">Select a city</option>
            <?php foreach ($cities as $city): ?>
                <option value="<?= htmlspecialchars($city['city_name']) ?>"><?= htmlspecialchars($city['city_name']) ?></option>
            <?php endforeach; ?>
            <option value="new_city">Add a new city</option>
        </select>
        <div id="new_city_input" style="display: none;">
            <label for="new_city">New City Name</label>
            <input type="text" id="new_city" name="new_city">
            <button type="button" id="add_city_button" onclick="addNewCity();" style="display: none;">
                Add City
            </button>
        </div>
        <button type="submit">Add Hotel</button>
    </form>
</body>
</html>
