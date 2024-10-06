<?php
// Connect to the database
$conn = new mysqli('localhost:3390', 'root', '', 'capturemoments');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get selected category (if any)
$category = isset($_GET['category']) ? $_GET['category'] : 'all';

// Prepare the query to fetch images
if ($category !== 'all') {
    $stmt = $conn->prepare("SELECT * FROM image_info WHERE img_category_id = ?");
    $stmt->bind_param("s", $category);
} else {
    $stmt = $conn->prepare("SELECT * FROM image_info");
}

$stmt->execute();
$images = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capture Moments</title>
    <link rel="stylesheet" href="landing.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</head>
<body>
<header>
    <div class="nav">
        <span id="menuIcon" class="menu-icon" onclick="openNav()">☰</span>
    </div>
    <div class="social-icons">
        <a href="https://twitter.com/" target="_blank" aria-label="Twitter">
            <i class="fa fa-twitter social-icon" aria-hidden="true"></i>
        </a>
        <a href="https://www.instagram.com/" target="_blank" aria-label="Instagram">
            <i class="fa fa-instagram social-icon" aria-hidden="true"></i>
        </a>
    </div>
</header>



    <div id="mySidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <a href="landing.php">Home</a>
        <a href="portfolio.php">Portfolios</a>
        <a href="contactus.php">Contact Us</a>
        <a href="login.php">Admin</a>
    </div>

    <main>
        <div class="hero">
            <div class="overlay"></div>
            <div class="hero-text">
                <h1>CAPTURE <span>MOMENTS</span></h1>
                <p>We capture life’s most beautiful moments through stunning photography, from nature to weddings, nightlife, and more. 
                    Explore our gallery!</p>
            </div>
        </div>
    </main>
    
    <nav class="categories">
        <ul>
            <li><a href="landing.php?category=all">All</a></li>
            <li><a href="landing.php?category=1">Wedding</a></li>
            <li><a href="landing.php?category=2">Nature</a></li>
            <li><a href="landing.php?category=3">Miniature</a></li>
            <li><a href="landing.php?category=4">Night life</a></li>
            <li><a href="landing.php?category=5">Others</a></li>
        </ul>
    </nav>

    <main1>
        <div class="gallery-container">
            <div class="gallery">
                <?php while ($row = $images->fetch_assoc()) { ?>
                    <div class="gallery-item">
                        <img src="uploads/<?php echo $row['image_name']; ?>" alt="<?php echo $row['image_name']; ?>">
                    </div>
                <?php } ?>
            </div>
        </div>
    </main1>

    <script>
        function openNav() {
            document.getElementById("mySidenav").style.width = "220px";
            document.getElementById("menuIcon").classList.add("hidden");  // Hide menu icon when the panel is open
        }

        function closeNav() {
            document.getElementById("mySidenav").style.width = "0";
            document.getElementById("menuIcon").classList.remove("hidden");  // Show menu icon when the panel is closed
        }
    </script>
</body>
<div class="footer">
    <p>&copy; 2024 Capture Moments. All rights reserved.</p>
</div>
</html>

<?php $conn->close(); ?>
