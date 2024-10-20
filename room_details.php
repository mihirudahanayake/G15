<?php
include('config.php');

// Get the room ID from the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $room_id = $conn->real_escape_string($_GET['id']);

    // Fetch room details
    $stmt = $conn->prepare("SELECT rooms.*, hotels.hotel_name AS hotel_name, hotels.location
                            FROM rooms
                            JOIN hotels ON rooms.hotel_id = hotels.hotel_id
                            WHERE rooms.room_id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $room = $result->fetch_assoc();
    } else {
        echo "<p>Room not found.</p>";
        exit;
    }

    // Fetch room images
    $stmt = $conn->prepare("SELECT image_path FROM room_images WHERE room_id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $imgResult = $stmt->get_result();

    $images = [];
    if ($imgResult->num_rows > 0) {
        while ($row = $imgResult->fetch_assoc()) {
            $images[] = $row['image_path'];
        }
    }

} else {
    echo "<p>No room specified.</p>";
    exit;
}

if (isset($_POST['check_availability'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    if ($start_date && $end_date && $start_date <= $end_date) {

        $stmt = $conn->prepare("SELECT * FROM bookings
                                WHERE room_id = ?
                                  AND (start_date <= ? AND end_date >= ?)");
        $stmt->bind_param("iss", $room_id, $end_date, $start_date);
        $stmt->execute();
        $result = $stmt->get_result();

        $is_available = ($result->num_rows == 0);
    } else {
        echo "<p>Invalid date range.</p>";
        $is_available = false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Details</title>
    <link rel="stylesheet" href="room_details.css">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
</head>

<body>
    <div class="background"></div>
    <?php include 'header.php'; ?>
    <h1>Room no : <?php echo htmlspecialchars($room['room_number']); ?></h1>
    <div class="container">
        <!-- Swiper Image Gallery -->
        <section class="gallery" id="gallery">
            <div class="swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($images as $image): ?>
                            <img src="<?php echo htmlspecialchars($image); ?>" class="swiper-slide" alt="Room Image">
                    <?php endforeach; ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </section>

        <section class="room-details">
            <p><strong>Hotel:</strong> <?php echo htmlspecialchars($room['hotel_name']); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($room['location']); ?></p>
            <p><strong>Price per Night: </strong> LKR. <?php echo htmlspecialchars($room['price_per_night']); ?></p>
            <p><strong>Max Adults:</strong> <?php echo htmlspecialchars($room['max_adults']); ?></p>
            <p><strong>Max Children:</strong> <?php echo htmlspecialchars($room['max_children']); ?></p>
            
            <p><strong>Facilities:</strong></p>
            <ul>
                <?php 
                $facilities = explode("\n", $room['facilities']); // Split facilities by line breaks
                foreach ($facilities as $facility): ?>
                    <li><?php echo htmlspecialchars($facility); ?></li> <!-- Display each facility in a list -->
                <?php endforeach; ?>
            </ul>
        </section>


        <!-- availability -->
        <section class="availability" id="availability">
            <h2>Check Availability</h2>
            <form action="room_details.php?id=<?php echo htmlspecialchars($room_id); ?>" method="POST">
                <label for="start_date">Start Date</label>
                <input type="date" id="start_date" name="start_date" required>

                <label for="end_date">End Date</label>
                <input type="date" id="end_date" name="end_date" required>

                <button type="submit" name="check_availability" id="check-availability">Check Availability</button>
            </form>

            <?php if (isset($is_available)): ?>
                <?php if ($is_available): ?>
                    <p>The room is available for the selected dates.</p>
                    <form action="book_room.php" method="POST">
                        <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room_id); ?>">
                        <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                        <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                        <button type="submit" name="book_now" id="book-now-btn">Book Now</button>
                    </form>
                <?php else: ?>
                    <p>Sorry, the room is not available for the selected dates.</p>
                <?php endif; ?>
            <?php endif; ?>
        </section>
        
    </div>
    <div class="foot">
    <?php include 'footer.php'; ?>
    </div>
    <script>
        document.getElementById("book-now-btn").addEventListener("click", function(event) {
            // Check if the user is logged in (adjust the condition as needed)
            var userLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
            
            if (!userLoggedIn) {
                alert("You must be logged in to book a room.");
                event.preventDefault(); // Prevent the form from submitting
            }
        });
        
    document.getElementById("book-now-btn").addEventListener("click", function(event) {
        // Check if the user is logged in (adjust the condition as needed)
        var userLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
        
        if (!userLoggedIn) {
            alert("You must be logged in to book a room.");
            event.preventDefault(); // Prevent the form from submitting
            return;
        }

        // Confirm booking action
        var confirmation = confirm("Are you sure you want to book this room for the selected dates?");
        if (!confirmation) {
            event.preventDefault(); // Prevent the form from submitting if user cancels
        }
    });
</script>

    <script src="room_details.js"></script>
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
</body>
</html>
