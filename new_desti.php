<?php

include('config.php');

if (isset($_POST['add_destination'])) {
    $desti_name = $conn->real_escape_string($_POST['desti_name']);
    $desti_description = $conn->real_escape_string($_POST['desti_description']);
    $city = $conn->real_escape_string($_POST['city']);

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Validate file extension and size
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
        $maxFileSize = 5 * 1024 * 1024; // 5 MB

        if (in_array($fileExtension, $allowedExtensions) && $fileSize <= $maxFileSize) {
            $uploadFileDir = './uploads/';
            $dest_path = $uploadFileDir . $fileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $image_url = $dest_path;
            } else {
                echo "<p>There was an error uploading the file.</p>";
                exit;
            }
        } else {
            echo "<p>Invalid file extension or file too large.</p>";
            exit;
        }
    } else {
        $image_url = ''; // Default image URL or handle accordingly
    }

    $query = "INSERT INTO destinations (desti_name, desti_description, city, image_url) 
              VALUES ('$desti_name', '$desti_description', '$city', '$image_url')";
    if ($conn->query($query) === TRUE) {
        echo "<p>Destination added successfully.</p>";
        
    } else {
        echo "<p>Error: " . $conn->error . "</p>";
    }
}
?>
