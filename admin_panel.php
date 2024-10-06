<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost:3390', 'root', '', 'capturemoments');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle image deletion
if (isset($_GET['delete_image'])) {
    $image_id = (int)$_GET['delete_image']; // Cast to integer for safety

    // Fetch the image name before deletion for file handling
    $image_stmt = $conn->prepare("SELECT image_name FROM image_info WHERE id = ?");
    $image_stmt->bind_param("i", $image_id);
    $image_stmt->execute();
    $image_row = $image_stmt->get_result()->fetch_assoc();

    // Check if the image exists
    if ($image_row) {
        $image_name = $image_row['image_name'];

        // Delete the image record from the database
        $delete_stmt = $conn->prepare("DELETE FROM image_info WHERE id = ?");
        $delete_stmt->bind_param("i", $image_id);

        if ($delete_stmt->execute()) {
            // Delete the physical image file from the server
            if (file_exists("uploads/" . $image_name)) {
                unlink("uploads/" . $image_name);
            }
            echo "Image deleted successfully!";
        } else {
            echo "An error occurred while deleting the image.";
        }
    } else {
        echo "Image not found.";
    }
}

// Fetch images from the database
$gallery_result = $conn->query("SELECT image_info.*, category.img_category FROM image_info INNER JOIN category ON image_info.img_category_id = category.id");

// Fetch photographers from the database
$photographers_result = $conn->query("SELECT photographers.*, category.img_category FROM photographers INNER JOIN category ON photographers.img_category_id = category.id");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Arial:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Arial', sans-serif; 
            margin: 0; 
            padding: 0; 
            background-color: #19191a; 
            color: white; /* Dark text */
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
            transition: background-color 0.3s ease; /* Transition effect */
        }
        nav a:hover { 
            background-color: white; /* Darker green on hover */
            color: black;
        }
        .container { 
            padding: 20px; 
            max-width: 1200px; 
            margin: auto; 
        }
        .section { 
            margin-bottom: 30px; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
            background-color: #282829; 
            padding: 20px; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }
        .btn { 
            padding: 10px 20px; 
            background-color: black; 
            color: white; 
            border: none; 
            cursor: pointer; 
            font-weight: bold; 
            border-radius: 5px; 
            text-decoration: none; 
            transition: background-color 0.3s ease; /* Transition effect */
        }
        .btn:hover { 
            background-color: white; /* Darker green on hover */
            color: black;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 20px 0; 
        }
        table, th, td { 
            border: 1px solid #ddd; 
        }
        th, td { 
            padding: 12px; 
            text-align: left; 
        }
        th { 
            background-color: black; 
            color: #d4d5d9; 
        }
        img { 
            max-width: 100px; 
            max-height: 100px; 
            border-radius: 5px; 
        }
        h3 { 
            color: white; 
            margin-bottom: 10px; 
        }
        .footer { 
            text-align: center; 
            padding: 20px; 
            background-color: black; 
            position: relative; 
            bottom: 0; 
            width: 100%; 
            color: white; /* White footer text */
        }
    </style>
</head>
<body>
    <nav>
        <h2>Welcome, Admin</h2>
        <div class="button-container">
            <a href="landing.php">Go to Home Page</a>
            <a href="login.php">Logout</a>
        </div>
    </nav>
    <div class="container">
                <!-- View Messages Section -->
                <div class="section">
            <h3>View Messages</h3>
            <table>
                <tr><th>Name</th><th>Email</th><th>Contact Number</th><th>Description</th></tr>
                <?php
                // Fetch messages from the database
                $messages_result = $conn->query("SELECT * FROM message");

                while ($row = $messages_result->fetch_assoc()) {
                    echo "<tr>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td>" . htmlspecialchars($row['contact_number']) . "</td>
                        <td>" . htmlspecialchars($row['description']) . "</td>
                    </tr>";
                }
                ?>
            </table>
        </div>
        <!-- Manage Gallery Section -->
        <div class="section">
            <h3>Manage Gallery</h3>
            <table>
                <tr><th>Image</th><th>Category</th><th>Actions</th></tr>
                <?php while ($row = $gallery_result->fetch_assoc()) { ?>
                    <tr>
                        <td><img src="uploads/<?php echo htmlspecialchars($row['image_name']); ?>" alt="Image Preview"></td>
                        <td><?php echo htmlspecialchars($row['img_category']); ?></td>
                        <td> 
                            <a href="admin_panel.php?delete_image=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this image?');" style="color: red;">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
            <a href="add_image.php" class="btn">Add Image</a>
            <a href="manage_img_category.php" class="btn">Re-arrange Category</a>
        </div>
        <!-- Manage Photographers Section -->
        <div class="section">
            <h3>Manage Photographers</h3>
            <!-- Display Existing Photographers -->
            <table>
                <tr>
                    <th>Name</th>
                    <th>Skills</th>
                    <th>Experience</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Photo</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $photographers_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['skills']); ?></td>
                        <td><?php echo htmlspecialchars($row['experience']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo htmlspecialchars($row['img_category']); ?></td>
                        <td><img src="uploads/<?php echo htmlspecialchars($row['photographer_image']); ?>" alt="Photographer Image" style="width:100px;"></td>
                        <td>
                            <a href="manage_photographers.php?edit=<?php echo $row['id']; ?>" style="color: white;">Edit</a> |
                            <a href="manage_photographers.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this photographer?');" style="color: red;">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
            <a href="manage_photographers.php"class="btn">Add Photographer</a>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2024 Capture Moments. All rights reserved.</p>
    </div>
</body>
</html>

