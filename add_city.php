<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $city_name = $_POST['city_name'];

    // Add new city to the cities table
    $stmt = $conn->prepare("INSERT INTO cities (city_name) VALUES (?)");
    $stmt->bind_param("s", $city_name);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    echo "City added successfully";
}
?>
