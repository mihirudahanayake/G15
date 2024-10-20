<?php
include('config.php'); // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email, name, telephone, profile_picture, user_type FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle form submission for updating user data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $name = $_POST['name'];
    $telephone = $_POST['telephone'];
    $password = $_POST['password'];
    $error_messages = [];

    // Validate telephone number (must be 10 digits)
    if (!preg_match('/^\d{10}$/', $telephone)) {
        $error_messages[] = "Telephone number must be exactly 10 digits.";
    }

    // Validate password criteria (if a new password is provided)
    if (!empty($password) && !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,12}$/', $password)) {
        $error_messages[] = "Password must be 8 to 12 characters long, and include at least one uppercase letter, one lowercase letter, and one number.";
    }

    // Check for file upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_name = basename($_FILES['profile_picture']['name']);
        $file_path = 'uploads/' . $file_name; // Adjust the path as needed

        // Move the uploaded file to the specified directory
        if (move_uploaded_file($file_tmp, $file_path)) {
            // Update the profile_picture path in the database
            $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE user_id = ?");
            $stmt->bind_param("si", $file_name, $user_id);
            $stmt->execute();
        } else {
            $error_messages[] = "Error uploading the file.";
        }
    }

    // Validate and update other user info
    if (!empty($password)) {
        // Hash the new password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, name = ?, telephone = ?, password = ? WHERE user_id = ?");
        $stmt->bind_param("sssssi", $username, $email, $name, $telephone, $hashed_password, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, name = ?, telephone = ? WHERE user_id = ?");
        $stmt->bind_param("ssssi", $username, $email, $name, $telephone, $user_id);
    }

    // Check for any errors and execute the statement
    if (empty($error_messages)) {
        if ($stmt->execute()) {
            echo "<script>alert('Profile updated successfully!'); window.location.href = 'profile.php';</script>";
            exit;
        } else {
            $error_messages[] = "Error updating profile: " . $stmt->error;
        }
    }

    // If there are any error messages, show them in a JavaScript alert
    if (!empty($error_messages)) {
        echo "<script>alert('" . implode("\\n", $error_messages) . "'); window.location.href = 'profile.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <div class="bg"></div>
    <div class="back-button-container">
        <?php if ($user['user_type'] == 'user'): ?>
            <button onclick="location.href='index.php'" class="back-button">Back to Home</button>
        <?php elseif ($user['user_type'] == 'hotel_admin'): ?>
            <button onclick="location.href='hotel_dashboard.php'" class="back-button">Back to Dashboard</button>
        <?php elseif ($user['user_type'] == 'admin'): ?>
            <button onclick="location.href='admin.php'" class="back-button">Back to Dashboard</button>
        <?php endif; ?>
    </div>
    <div class="profile-container">

        <h2>Your Profile</h2>
        
        <div class="profile-picture">
            <?php if ($user['profile_picture']): ?>
                <img src="uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" width="150">
            <?php else: ?>
                <img src="default.png" alt="Default Profile Picture" width="150"> <!-- Default image if no profile picture -->
            <?php endif; ?>
        </div>

        <form action="profile.php" method="POST" enctype="multipart/form-data">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

            <label for="telephone">Telephone</label>
            <input type="tel" id="telephone" name="telephone" value="<?php echo htmlspecialchars($user['telephone']); ?>" required>

            <label for="password">New Password (leave blank to keep current password)</label>
            <input type="password" id="password" name="password">

            <label for="profile_picture">Profile Picture</label>
            <input type="file" id="profile_picture" name="profile_picture" accept="image/*">

            <button type="submit">Update Profile</button>
        </form>
        <div class="action-buttons">
            <?php if ($user['user_type'] == 'user'): ?>
                <button onclick="location.href='view_booking.php'" class="view-bookings-button">View Bookings</button>
            <?php endif; ?>
            <button onclick="location.href='logout.php'" class="logout">Log out</button>
        </div>
    </div>
</body>
</html>
