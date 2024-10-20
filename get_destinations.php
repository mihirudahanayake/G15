<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $city_name = $_POST['city_name'] ?? '';

    if (!empty($city_name)) {
        // Get city_id from the city_name
        $stmt = $conn->prepare("SELECT city_id FROM cities WHERE city_name = ?");
        $stmt->bind_param("s", $city_name);
        $stmt->execute();
        $stmt->bind_result($city_id);
        $stmt->fetch();
        $stmt->close();

        if ($city_id) {
            // Fetch destinations for the city
            $stmt = $conn->prepare("SELECT destination_id, desti_name FROM destinations WHERE city_id = ?");
            $stmt->bind_param("i", $city_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $destinations = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            // Generate HTML for destination checkboxes
            $output = '';
            foreach ($destinations as $destination) {
                $output .= '<label><input type="checkbox" name="destination_ids[]" value="' . $destination['destination_id'] . '"> ' . htmlspecialchars($destination['desti_name']) . '</label><br>';
            }

            echo $output;
        } else {
            echo 'No destinations found for this city.';
        }
    }
    $conn->close();
}
