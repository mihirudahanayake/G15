<?php
include('config.php');

if (isset($_POST['location']) && !empty($_POST['location'])) {
    $location = $conn->real_escape_string($_POST['location']);

    // Fetch cities for the selected location
    $query = "SELECT cities.city_name 
              FROM hotels 
              JOIN cities ON hotels.city_id = cities.city_id
              WHERE hotels.location = '$location'
              GROUP BY cities.city_name";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . $row['city_name'] . '">' . $row['city_name'] . '</option>';
        }
    } else {
        echo '<option value="">No cities available</option>';
    }
}

$conn->close();
?>
