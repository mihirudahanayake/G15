<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link rel="stylesheet" type="text/css" href="about.css">
</head>
<body>
    <div class="bg"></div>
    <?php include('header.php'); ?>
    <section class="travel">
        <div class="heading">
            <h1>About</h1>
        </div>
        <div class="paragraph">
        <p class="about"> To easily navigate travel destinations and
 choose places that provide related accommodation facilities according
 to your needs. Easily explore, book accommodations, and access information about various tourist attractions and services. You can search for hotels, check real-time availability, and manage your bookings through the system. And also system provide facilities to manage listings, update availability, and handle bookings.
</p></div>
<h2>Our Team</h2>
        <div class="container">
            <div class="travel-content">
                <p></p>
            </div>
            <div class="admin-image">
                <img id="adminImg" src="images/bg4.jpg" alt="admin Image" class="image-popup-trigger">
                <img id="adminImg2" src="images/bg2.jpg" alt="admin Image" class="image-popup-trigger">
                <img id="adminImg3" src="images/bg3.jpg" alt="admin Image" class="image-popup-trigger">
                <img id="adminImg3" src="images/bg3.jpg" alt="admin Image" class="image-popup-trigger">
                <img id="adminImg3" src="images/bg3.jpg" alt="admin Image" class="image-popup-trigger">
                <img id="adminImg3" src="images/bg3.jpg" alt="admin Image" class="image-popup-trigger">
            </div>

            <!-- Bullet Indicators -->
            <div class="bullet-container"></div>
        </div>
    </section>

    <!-- Image Popup Modal -->
    <div id="popupModal" class="popup-modal">
        <span class="close-popup">&times;</span>
        <img class="popup-content" id="imgPopup">
    </div>

    <?php include('footer.php'); ?>

    <script>
const modal = document.getElementById("popupModal");
const images = document.querySelectorAll('.admin-image img'); // Select all images
const modalImg = document.getElementById("imgPopup");
const closePopup = document.getElementsByClassName("close-popup")[0];
let currentImageIndex = 0;

// Array of paragraphs associated with each image
const paragraphs = [
    "Hey, I am Mihiru Dahanayake.",
    "This is the second paragraph for the second image.",
    "This is the third paragraph for the third image.",
    "This is the fourth paragraph for the fourth image.",
    "This is the fifth paragraph for the fifth image.",
    "This is the sixth paragraph for the sixth image."
];

// Select the paragraph element
const paragraphElement = document.querySelector('.travel-content p');

// Select the bullet container
const bulletContainer = document.querySelector('.bullet-container');

// Create bullets dynamically based on the number of images
images.forEach((img, index) => {
    const bullet = document.createElement('span');
    bullet.classList.add('bullet');
    if (index === 0) bullet.classList.add('active'); // Set first bullet as active initially
    bulletContainer.appendChild(bullet);

    // Bullet click event
    bullet.onclick = function() {
        currentImageIndex = index; // Update current image index
        updateImageAndParagraph(); // Show corresponding image and paragraph
        updateActiveBullet(); // Update bullet highlights
    };
});

// Function to update the image and paragraph
function updateImageAndParagraph() {
    // Hide all images and paragraphs, remove active class
    images.forEach(img => {
        img.classList.remove('show'); // Remove show class to fade out
    });
    paragraphElement.classList.remove('show'); // Remove show class to fade out

    // Show current image and add show class
    images[currentImageIndex].classList.add('show'); // Add show class to fade in
    // Update paragraph with a timeout to allow fading out before fading in
    setTimeout(() => {
        paragraphElement.textContent = paragraphs[currentImageIndex]; // Change paragraph text
        paragraphElement.classList.add('show'); // Add show class to fade in
    }, 300); // Delay to allow the image to change first
}

// Function to update the active bullet
function updateActiveBullet() {
    // Remove active class from all bullets
    const bullets = document.querySelectorAll('.bullet');
    bullets.forEach(bullet => bullet.classList.remove('active'));

    // Add active class to the current bullet
    bullets[currentImageIndex].classList.add('active');
}

// Click event to show image and paragraph in modal
images.forEach((img, index) => {
    img.onclick = function() {
        showImageInModal(index);
    }
});

// Function to show the current image and paragraph in the modal
function showImageInModal(index) {
    modal.style.display = "block";
    modalImg.src = images[index].src;
    paragraphElement.textContent = paragraphs[index]; // Update the paragraph text
}

closePopup.onclick = function() {
    modal.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

// Function to change images and paragraphs automatically
function changeImage() {
    // Update index for next image
    currentImageIndex = (currentImageIndex + 1) % images.length; // Loop back to first image

    // Update image, paragraph, and active bullet
    updateImageAndParagraph();
    updateActiveBullet();
}

// Initial call to display the first image and paragraph
updateImageAndParagraph();
// Set interval to change images and paragraphs every 3 seconds
setInterval(changeImage, 5000); // Change time (in ms) as needed

    </script>
</body>
</html>
