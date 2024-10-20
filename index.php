<?php
include_once "config.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css" href="header.css">
    <link rel="stylesheet" type="text/css" href="index.css">
    
</head>
<body>
<?php include 'header.php'; ?>
<div class="bg"></div>
    <!--home section start-->
    <section class="home" id="home">
        <h1 class="mhead">Let's Create <br/>Memorable <br/>Journey</h1>
        <div class="row">
            <div class="content">
                <h1 class="head">Let's Create <br/>Memorable <br/>Journey</h1>
                <p id="p11">Escape to a world of wonder with our travel experiences tailored just for you.
                    Whether you seek the thrill of adventure, the serenity of pristine beaches, or the
                    charm of historic cities, our curated itineraries promise unforgettable memories. Dive into local culture, savor exquisite cuisines with our expert guides. Your
                    dream vacation awaits... start your journey with us today.
                </p>
            </div>
            <div class="images">
                <img class="elephant" src="Images/Elephant.jpg" alt="Elephant"/>
                <img class="sigiriya" src="Images/Sigiriya.jpeg" alt="Sigiriya"/>
                <img class="kandy" src="Images/kandy.jpeg" alt="Kandy"/>
                <img class="ella" src="Images/Ella.jpg" alt="Ella"/>
            </div>
            
        </div> 
        <p id="p2">Escape to a world of wonder with our travel experiences tailored just for you.
            Whether you seek the thrill of adventure, the serenity of pristine beaches, or the
            charm of historic cities, our curated itineraries promise unforgettable memories. Dive into local culture, savor exquisite cuisines with our expert guides. Your
            dream vacation awaits... start your journey with us today.
        </p>
    </section>

    <?php include 'footer.php'; ?>
    <script src="script.js"></script>
</body>
</html>
