<?php

include('config.php');

// Check if the admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Handle form submission for updating user details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $user_type = $_POST['user_type'];

    // Update user details in the database
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, user_type = ? WHERE user_id = ?");
    $stmt->bind_param("sssi", $username, $email, $user_type, $user_id);

    if ($stmt->execute()) {
        // Redirect to the same page to see changes
        header("Location: admin_panel.php?update=success");
        exit();
    } else {
        echo "Error updating user details.";
    }

    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_hotel'])) {
    $hotel_id = $_POST['hotel_id'];
    $hotel_name = $_POST['hotel_name'];
    $location = $_POST['location'];

    // Check if the admin added a new city
    if ($location === 'add_new_city' && !empty($_POST['new_city'])) {
        $new_city = $_POST['new_city'];

        // Insert the new city into the cities table
        $stmt = $conn->prepare("INSERT INTO cities (city_name) VALUES (?)");
        $stmt->bind_param("s", $new_city);
        if ($stmt->execute()) {
            $location = $new_city;  // Set the location to the new city
        } else {
            echo "Error adding new city.";
            exit();
        }
        $stmt->close();
    }

    // Update hotel details with the selected or new city
    $stmt = $conn->prepare("UPDATE hotels SET hotel_name = ?, location = ? WHERE hotel_id = ?");
    $stmt->bind_param("ssi", $hotel_name, $location, $hotel_id);

    if ($stmt->execute()) {
        // Redirect to the same page to see changes
        header("Location: admin_panel.php?update=success");
        exit();
    } else {
        echo "Error updating hotel details.";
    }

    $stmt->close();
}


// Fetch all users excluding admin users
$users_stmt = $conn->prepare("SELECT * FROM users WHERE user_type != 'admin'");
$users_stmt->execute();
$users = $users_stmt->get_result();
$users_stmt->close();

// Fetch all hotels
$hotels_stmt = $conn->prepare("SELECT * FROM hotels");
$hotels_stmt->execute();
$hotels = $hotels_stmt->get_result();
$hotels_stmt->close();

// Fetch all cities
$cities_stmt = $conn->prepare("SELECT city_name FROM cities");
$cities_stmt->execute();
$cities = $cities_stmt->get_result();
$cities_stmt->close();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="admin_panel.css">
    <script>
        function editUser(userId) {
            document.getElementById('edit-form-' + userId).style.display = 'table-row';
            document.getElementById('view-row-' + userId).style.display = 'none';
        }

        function cancelEditUser(userId) {
            document.getElementById('edit-form-' + userId).style.display = 'none';
            document.getElementById('view-row-' + userId).style.display = 'table-row';
        }

        function editHotel(hotelId) {
            document.getElementById('edit-hotel-form-' + hotelId).style.display = 'table-row';
            document.getElementById('view-hotel-row-' + hotelId).style.display = 'none';
        }

        function cancelEditHotel(hotelId) {
            document.getElementById('edit-hotel-form-' + hotelId).style.display = 'none';
            document.getElementById('view-hotel-row-' + hotelId).style.display = 'table-row';
        }
    </script>
</head>
<body>

    <div class="admin-container">
        <h2>Admin Panel</h2>
        <button><a href="admin.php">Back to Dashboard</a></button>
        <h3>Manage Users</h3>
        <table>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>User Type</th>
                <th>Actions</th>
            </tr>
            <?php while ($user = $users->fetch_assoc()): ?>
            <tr id="view-row-<?php echo $user['user_id']; ?>">
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['user_type']); ?></td>
                <td>
                    <button onclick="editUser(<?php echo $user['user_id']; ?>)">Edit</button>
                    <a href="admin_delete_user.php?id=<?php echo $user['user_id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    <?php if ($user['user_type'] === 'user'): ?>
                        <a href="admin_view_user_bookings.php?user_id=<?php echo $user['user_id']; ?>" class="view-booking-button">View Booking Details</a>
                    <?php endif; ?>
                </td>
            </tr>
            <tr id="edit-form-<?php echo $user['user_id']; ?>" style="display:none;">
                <form action="admin_panel.php" method="POST">
                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                    <td><input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required></td>
                    <td><input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required></td>
                    <td>
                        <select name="user_type">
                            <option value="user" <?php echo $user['user_type'] === 'user' ? 'selected' : ''; ?>>User</option>
                            <option value="hotel_admin" <?php echo $user['user_type'] === 'hotel_admin' ? 'selected' : ''; ?>>Hotel Admin</option>
                        </select>
                    </td>
                    <td>
                        <button type="submit" name="update_user">Update</button>
                        <button type="button" onclick="cancelEditUser(<?php echo $user['user_id']; ?>)">Cancel</button>
                    </td>
                </form>
            </tr>
            <?php endwhile; ?>
        </table>
        
        <h3>Manage Hotels</h3>
        <table>
            <tr>
                <th>Hotel Name</th>
                <th>Location</th>
                <th>Actions</th>
            </tr>
            <?php while ($hotel = $hotels->fetch_assoc()): ?>
            <tr id="view-hotel-row-<?php echo $hotel['hotel_id']; ?>">
                <td><?php echo htmlspecialchars($hotel['hotel_name']); ?></td>
                <td><?php echo htmlspecialchars($hotel['location']); ?></td>
                <td>
                    <button onclick="editHotel(<?php echo $hotel['hotel_id']; ?>)">Edit</button>
                    <a href="admin_delete_hotel.php?id=<?php echo $hotel['hotel_id']; ?>" onclick="return confirm('Are you sure you want to delete this hotel?');">Delete</a>
                    <a href="admin_hotel_details.php?id=<?php echo $hotel['hotel_id']; ?>" class="view-hotel-button">View Hotel Details</a>
                </td>
            </tr>
            <tr id="edit-hotel-form-<?php echo $hotel['hotel_id']; ?>" style="display:none;">
                <form action="admin_panel.php" method="POST">
                    <input type="hidden" name="hotel_id" value="<?php echo $hotel['hotel_id']; ?>">
                    <td><input type="text" name="hotel_name" value="<?php echo htmlspecialchars($hotel['hotel_name']); ?>" required></td>
                    <td>
                        <select name="location" id="location-select-<?php echo $hotel['hotel_id']; ?>" onchange="toggleNewCityInput(<?php echo $hotel['hotel_id']; ?>)">
                            <option value="">Select a city</option>
                            <?php
                            // Reset the cities result set for each hotel
                            $cities_stmt = $conn->prepare("SELECT city_name FROM cities");
                            $cities_stmt->execute();
                            $cities = $cities_stmt->get_result();
                            while ($city = $cities->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($city['city_name']); ?>" <?php echo $hotel['location'] === $city['city_name'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($city['city_name']); ?>
                                </option>
                            <?php endwhile; ?>
                            <option value="add_new_city">Add New City</option>
                        </select>
                        <input type="text" name="new_city" placeholder="New City Name" style="display:none;" id="new-city-input-<?php echo $hotel['hotel_id']; ?>">
                    </td>
                    <td>
                        <button type="submit" name="update_hotel">Update</button>
                        <button type="button" onclick="cancelEditHotel(<?php echo $hotel['hotel_id']; ?>)">Cancel</button>
                    </td>
                </form>
            </tr>
            <?php endwhile; ?>
        </table>


    </div>

    <script>
        function toggleNewCityInput(hotelId) {
            const selectElement = document.getElementById('location-select-' + hotelId);
            const newCityInput = document.getElementById('new-city-input-' + hotelId);
            
            if (selectElement.value === 'add_new_city') {
                newCityInput.style.display = 'inline-block';
            } else {
                newCityInput.style.display = 'none';
            }
        }
    </script>


</body>
</html>

<?php
$conn->close();
?>
