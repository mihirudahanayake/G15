<?php
// Include the database connection
include 'config.php';

// Handle form submission to add or update a city
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $city_name = $_POST['city_name'];
    $city_id = isset($_POST['city_id']) ? $_POST['city_id'] : null;

    if ($city_id) {
        // Update the existing city
        $sql = "UPDATE cities SET city_name = ? WHERE city_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $city_name, $city_id);
    } else {
        // Add a new city
        $sql = "INSERT INTO cities (city_name) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $city_name);
    }

    if ($stmt->execute()) {
        echo "City saved successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}


// Handle city deletion
if (isset($_GET['delete'])) {
    $city_id = $_GET['delete'];
    $sql = "DELETE FROM cities WHERE city_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $city_id);

    if ($stmt->execute()) {
        echo '<script>alert("City Deleted Successfully")</script>';
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Fetch all cities for display
$sql = "SELECT * FROM cities";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Cities</title>
    <link rel="stylesheet" href="manage-city.css">
</head>
<body>
<?php include 'header.php'; ?>

<h1>Manage Cities</h1>

<!-- Form for adding/updating a city -->
<form method="post" action="">
    <label for="city_name">City Name:</label>
    <input type="text" id="city_name" name="city_name" required>
    <input type="hidden" name="city_id" id="city_id">
    <button type="submit">Save City</button>
</form>

<!-- List of cities -->
<h2>City List</h2>
<table border="1">
    <tr>
        <th>City ID</th>
        <th>City Name</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['city_id']; ?></td>
            <td><?php echo $row['city_name']; ?></td>
            <td>
                <a href="?edit=<?php echo $row['city_id']; ?>">Edit</a>
                <a href="?delete=<?php echo $row['city_id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
    <?php } ?>
</table>

<!-- JavaScript to handle editing -->
<script>
    // When "Edit" is clicked, fill the form with the city details
    document.querySelectorAll('a[href^="?edit="]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const cityId = this.getAttribute('href').split('=')[1];
            const row = this.closest('tr');
            const cityName = row.querySelector('td:nth-child(2)').textContent;

            document.getElementById('city_name').value = cityName;
            document.getElementById('city_id').value = cityId;
        });
    });
</script>

</body>
</html>
