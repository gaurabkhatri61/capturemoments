<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Set the target directory for uploads
$target_dir = "uploads/";

if (isset($_POST['add'])) {
    // Get the uploaded file details
    $target_file = $target_dir . basename($_FILES["image_file"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Get the selected category ID
    $img_category_id = $_POST['img_category_id'];

    // Check if the file is an actual image
    $check = getimagesize($_FILES["image_file"]["tmp_name"]);
    if ($check === false) {
        echo "File is not an image.";
        exit();
    }

    // Allow only certain file formats
    if ($imageFileType != "png" && $imageFileType) {
        echo "Sorry, PNG files are only allowed.";
        exit();
    }

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES["image_file"]["tmp_name"], $target_file)) {
        // DB connection
        $conn = new mysqli('localhost:3390', 'root', '', 'capturemoments');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Insert the image file name and category ID into the image_info table
        $query = "INSERT INTO image_info (image_name, img_category_id) VALUES ('" . basename($target_file) . "', '$img_category_id')";
        if ($conn->query($query) === TRUE) {
            echo "Image uploaded successfully!";
            header("Location: admin_panel.php"); // Redirect to admin panel after success
        } else {
            echo "Error: " . $query . "<br>" . $conn->error;
        }
        $conn->close();
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Image</title>
    <link href="https://fonts.googleapis.com/css2?family=Arial:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Arial', sans-serif; 
            margin: 0; 
            padding: 0; 
            background-color: #19191a; 
            color: black; /* Dark text */
        }
        nav { 
            background-color: black; 
            padding: 20px; 
            text-align: center; 
            color: white; 
            position: relative; /* For positioning buttons */
        }
        nav h2 { 
            margin: 0; 
            display: inline-block; /* Align header */
        }
        nav .button-container {
            position: absolute; /* Position buttons in the corner */
            right: 20px; 
            top: 15px; 
        }
        nav a { 
            color: white; 
            padding: 10px 15px; 
            text-decoration: none; 
            font-weight: bold; 
            margin-left: 10px; /* Space between buttons */
            border-radius: 5px; /* Rounded corners */
            background-color: black; /* Green for buttons */
            transition: background-color 0.3s ease; /* Transition effect */
        }
        nav a:hover { 
            background-color: white; 
            color: black;
        }
        .container { 
            padding: 20px; 
            max-width: 600px; 
            margin: auto; 
        }
        h2 { 
            text-align: center; 
            color: white; /* Blue for headings */
        }
        form { 
            background-color: #87878a; /* White background for form */
            padding: 20px; 
            border-radius: 5px; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }
        label { 
            display: block; 
            margin: 10px 0 5px; 
        }
        input[type="text"], select, input[type="file"] { 
            width: 100%; 
            padding: 10px; 
            margin-bottom: 10px; 
            border: 1px solid #ddd; 
            border-radius: 5px; /* Rounded corners */
        }
        input[type="submit"] { 
            padding: 10px 15px; 
            background-color: #28a745; /* Green */
            color: white; 
            border: none; 
            cursor: pointer; 
            border-radius: 5px; 
            font-weight: bold; 
            transition: background-color 0.3s ease; /* Transition effect */
        }
        input[type="submit"]:hover { 
            background-color: #218838; /* Darker green on hover */
        }
    </style>
</head>
<body>
<nav>
        <h2>Add images</h2>
        <div class="button-container">
            <a href="admin_panel.php">Go to Admin Panel</a>
            <a href="login.php">Logout</a>
        </div>
    </nav>
    <div class="container">
        <form method="post" enctype="multipart/form-data" action="">
            <!-- Input for uploading image -->
            <label for="image_file">Upload Image:</label>
            <input type="file" name="image_file" id="image_file" required>

            <!-- Dropdown menu for selecting category -->
            <label for="img_category_id">Category:</label>
            <select name="img_category_id" id="img_category_id" required>
                <option value="">Select Category</option>
                <?php
                // Fetch categories from the category table
                $conn = new mysqli('localhost:3390', 'root', '', 'capturemoments');
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $result = $conn->query("SELECT * FROM category");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['img_category']}</option>";
                }
                $conn->close();
                ?>
            </select>

            <!-- Submit button -->
            <input type="submit" name="add" value="Add Image">
        </form>
    </div>
</body>
</html>
