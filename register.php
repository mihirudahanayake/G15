<?php
include('config.php'); // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $name = $_POST['name'];
    $telephone = $_POST['telephone'];

    // Validate telephone number (must be 10 digits)
    if (!preg_match('/^\d{10}$/', $telephone)) {
        echo "<script>alert('Telephone number must be exactly 10 digits.'); window.location.href='register.php';</script>";
        exit;
    }

    // Validate passwords
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.'); window.location.href='register.php';</script>";
        exit;
    }

    // Validate password criteria
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,12}$/', $password)) {
        echo "<script>alert('Password must be 8 to 12 characters long, and include at least one uppercase letter, one lowercase letter, and one number.'); window.location.href='register.php';</script>";
        exit;
    }

    // Check if the username or email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Username or email already taken.'); window.location.href='register.php';</script>";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Set default user type
    $user_type = 'user';

    // Insert the new user into the database
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, user_type, name, telephone) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $username, $email, $hashed_password, $user_type, $name, $telephone);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href='login.html';</script>";
        exit;
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href='register.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <div class="signup-container"></div>
        <div class="signup-box">
            <h2>Sign Up</h2>
            <form action="register.php" method="POST">
                <label for="name">Name</label>
                <div class="textbox">
                    <input type="text" id="name" name="name" required>
                </div>

                <label for="telephone">Telephone</label>
                <div class="textbox">
                    <input type="tel" id="telephone" name="telephone" required>
                </div>

                <label for="username">Username</label>
                <div class="textbox">
                    <input type="text" id="username" name="username" required>
                </div>

                <label for="email">Email</label>
                <div class="textbox">
                    <input type="email" id="email" name="email" required>
                </div>

                <label for="password">Password</label>
                <div class="textbox">
                    <input type="password" id="password" name="password" required>
                </div>

                <label for="confirm_password">Confirm Password</label>
                <div class="textbox">
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
            
                <button type="submit" class="btn">Sign Up</button>
            </form>
            <div class="signup-link">
                <p>Already have an account? <a href="login.html">Login here</a>.</p>
            </div>
        </div>   
</body>
</html>
