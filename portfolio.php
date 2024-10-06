<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capture Moments</title>
    <link rel="stylesheet" href="portfolio_design.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</head>
<body>
    <header>
    <div class="nav">
        <span id="menuIcon" class="menu-icon" onclick="openNav()">☰</span>
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
        <div class="Photographers">
            <div class="filter">
                <div class="Photographers-text">
                    <h1>OUR <span>PHOTOGRAPHERS</span></h1>
                </div>
            </div>
        </div>
        <div class="profile-container">
        <?php
session_start();
// Database connection
$conn = new mysqli('localhost:3390', 'root', '', 'capturemoments');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Skill to category mapping
$skillToCategory = [
    'Wedding' => 1,
    'Nature' => 2,
    'Miniature' => 3,
    'Night life' => 4,
    'Others' => 5,
];

// Fetch all photographers
$photographers = $conn->query("SELECT * FROM photographers");

// Display each photographer in the specified profile card structure
while ($row = $photographers->fetch_assoc()) {
    // Get category based on the photographer's skills
    $category = isset($skillToCategory[$row['skills']]) ? $skillToCategory[$row['skills']] : 5; // Default to 'Others' if no match
    
    echo "<div class='profile-container'></div>
          <div class='profile-card'>
              <div class='image-container'>
                  <img src='uploads/{$row['photographer_image']}' alt='Profile Image'>
                  <span>
                      <div class='profile-info' style='width: 100%;'>
                          <h2 class='name'>{$row['name']}</h2>
                          <p class='skills'>Skills: {$row['skills']}</p>
                          <p class='experience'>Experience: {$row['experience']} years</p>
                          <p class='description'>{$row['description']}</p>
                          <p class='work'>
                              <a href='landing.php?category={$category}'>see my work</a>
                          </p>
                      </div>
                  </span>
              </div>
          </div>";
}

$conn->close();
?>

        </div>
    </main>
    <script>
        function openNav() {
            document.getElementById("mySidenav").style.width = "220px";
            document.getElementById("menuIcon").classList.add("hidden");
        }
    
        function closeNav() {
            document.getElementById("mySidenav").style.width = "0";
            document.getElementById("menuIcon").classList.remove("hidden");
        }
    </script>
    <div class="footer">
        <p>&copy; 2024 Capture Moments. All rights reserved.</p>
    </div>
</body>
</html>
