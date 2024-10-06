<?php
// Start the session to manage any messages or errors
session_start();

// Initialize variables for error messages and success messages
$errorMessage = '';
$successMessage = '';

// Connect to the database
$conn = new mysqli('localhost:3390', 'root', '', 'capturemoments');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission when the request is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $description = $_POST['description'];

    // Server-side validation
    if (empty($name) || empty($email) || empty($contact) || empty($description)) {
        $errorMessage = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Invalid email format.";
    } elseif (!is_numeric($contact) || strlen($contact) < 10) {
        $errorMessage = "Please enter a valid 10-digit contact number.";
    } else {
        // Insert data into the database
        $stmt = $conn->prepare("INSERT INTO message (name, email, contact_number, description) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $contact, $description);

        if ($stmt->execute()) {
            $successMessage = "Message sent successfully!";
        } else {
            $errorMessage = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capture Moments - Contact Us</title>
    <link rel="stylesheet" href="contactus.css">
    <script>
        function validateForm() {
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const contact = document.getElementById('contact').value;
            const description = document.getElementById('description').value;
            const errorMessage = document.getElementById('error-message');
            
            // Regular Expression for email validation
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (name === '') {
                errorMessage.textContent = 'Name is required.';
                return false;
            }

            if (email === '' || !emailPattern.test(email)) {
                errorMessage.textContent = 'Please enter a valid email address.';
                return false;
            }

            if (contact === '' || isNaN(contact) || contact.length < 10) {
                errorMessage.textContent = 'Please enter a valid 10-digit contact number.';
                return false;
            }

            if (description === '') {
                errorMessage.textContent = 'Please enter a description.';
                return false;
            }

            errorMessage.textContent = '';
            return true;
        }

        function openNav() {
            document.getElementById("mySidenav").style.width = "220px";
            document.getElementById("menuIcon").classList.add("hidden");
        }

        function closeNav() {
            document.getElementById("mySidenav").style.width = "0";
            document.getElementById("menuIcon").classList.remove("hidden");
        }
    </script>
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
    <div class="container">
        <div class="contact-form">
            <h2>Contact Us</h2>
            <!-- Form submits to the same PHP file -->
            <form id="contactForm" method="POST" onsubmit="return validateForm()">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" placeholder="Your Name" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Your Email" required>

                <label for="contact">Contact Number:</label>
                <input type="tel" id="contact" name="contact" placeholder="Your Contact Number" required>

                <label for="description">Description:</label>
                <textarea id="description" name="description" placeholder="Enter which photographer you wish to hire and for what task" required></textarea>

                <button type="submit">Submit</button>
            </form>

            <!-- Display error or success messages -->
            <p id="error-message"><?php echo $errorMessage; ?></p>
            <p id="success-message"><?php echo $successMessage; ?></p>
        </div>
    </div>
</main>
<div class="footer">
    <p>&copy; 2024 Capture Moments. All rights reserved.</p>
</div>
</body>
</html>
