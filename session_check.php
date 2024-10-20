<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if the user is not logged in
    header('Location: index.html');
    exit();
}

// Optional: Check user roles (if needed)
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] !== 'user') {
    echo "<p>You do not have permission to access this page.</p>";
    exit();
}
?>
