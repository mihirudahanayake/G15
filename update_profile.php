<?php

include('config.php'); // Include your database connection file


// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $name = $_POST['name'];
    $telephone = $_POST['telephone'];
    $password = $_POST['password'];
    $user_id = $_SESSION['user_id'];

    // Prepare the SQL statement
    if (!empty($password)) {
        // Hash the new password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, name = ?, telephone = ?, password = ? WHERE user_id = ?");
        $stmt->bind_param("sssssi", $username, $email, $name, $telephone, $hashed_password, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, name = ?, telephone = ? WHERE user_id = ?");
        $stmt->bind_param("ssssi", $username, $email, $name, $telephone, $user_id);
    }

    // Execute the update
    if ($stmt->execute()) {
        echo "Profile updated successfully!";
        header("Location: profile.php");
        exit;
    } else {
        echo "Error updating profile: " . $stmt->error;
    }
}
?>
