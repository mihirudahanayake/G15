<?php
include ('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare statement to fetch user details
    $stmt = $conn->prepare("SELECT user_id, password, user_type, hotel_id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($id, $hashed_password, $user_type, $hotel_id);
    $stmt->fetch();
    $stmt->close();

    // Verify password
    if (password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['user_type'] = $user_type;
        $_SESSION['hotel_id'] = $hotel_id;  // Store hotel_id in session if available

        // Check if the user is a hotel admin
        if ($user_type === 'hotel_admin') {
            if ($hotel_id) {
                // Hotel admin has already added hotel details
                header("Location: hotel_dashboard.php");
            } else {
                // Hotel admin needs to add hotel details
                header("Location: add_hotel.php");
            }
        } else if ($user_type === 'admin'){
            // Regular user
            header("Location: admin.php");
        } else{
            // Regular user
            header("Location: index.php");
        }
        exit();
    } else {
        echo "Invalid login credentials!";
    }

    $conn->close();
}
?>
