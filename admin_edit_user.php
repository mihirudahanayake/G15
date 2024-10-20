<?php

include('config.php');

// Check if the admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Get the user ID from the query parameter
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Fetch user details from the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "User not found.";
        exit();
    }

    $stmt->close();
} else {
    header("Location: admin_panel.php");
    exit();
}

// Handle form submission for updating user details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $user_type = $_POST['user_type'];

    // Update user details in the database
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, user_type = ? WHERE user_id = ?");
    $stmt->bind_param("sssi", $username, $email, $user_type, $user_id);

    if ($stmt->execute()) {
        header("Location: admin_panel.php");
        exit();
    } else {
        echo "Error updating user details.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-container">
        <h2>Edit User Details</h2>
        <form action="admin_edit_user.php?id=<?php echo $user_id; ?>" method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="user_type">User Type</label>
            <select id="user_type" name="user_type">
                <option value="user" <?php echo $user['user_type'] === 'user' ? 'selected' : ''; ?>>User</option>
                <option value="hotel_admin" <?php echo $user['user_type'] === 'hotel_admin' ? 'selected' : ''; ?>>Hotel Admin</option>
                <!-- You can add more user types here if needed -->
            </select>

            <button type="submit">Update User</button>
        </form>
    </div>
</body>
</html>
