<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost:3390', 'root', '', 'capturemoments');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Delete Operation
if (isset($_GET['delete'])) {
    $photographer_id = $_GET['delete'];
    // Delete the photographer from the database
    $query = "DELETE FROM photographers WHERE id = '$photographer_id'";
    if ($conn->query($query) === TRUE) {
        echo "<script>alert('Photographer deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting photographer: " . $conn->error . "');</script>";
    }
}

// Handle Add/Edit Form Submission
if (isset($_POST['save'])) {
    $name = $_POST['name'];
    $skills = $_POST['skills'];
    $experience = $_POST['experience'];
    $description = $_POST['description'];
    $img_category_id = $_POST['img_category_id'];

    // Handle file upload for photographer's image
    if (isset($_FILES['photo']) && $_FILES['photo']['size'] > 0) {
        $photo = basename($_FILES['photo']['name']);
        $target_file = "uploads/" . $photo;

        // Check if upload is successful
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
            $photo = $photo; // Save the photo name for DB
        } else {
            echo "<script>alert('Error uploading photo.');</script>";
            $photo = ''; // No photo uploaded
        }
    } else {
        $photo = $_POST['existing_photo'] ?? ''; // Keep the existing photo if no new photo is uploaded
    }

    if (isset($_POST['photographer_id']) && !empty($_POST['photographer_id'])) {
        // Update existing photographer
        $photographer_id = $_POST['photographer_id'];
        $query = "UPDATE photographers SET 
                    name = '$name', 
                    skills = '$skills', 
                    experience = '$experience', 
                    description = '$description', 
                    img_category_id = '$img_category_id', 
                    photographer_image = '$photo'
                  WHERE id = '$photographer_id'";
        if ($conn->query($query) === TRUE) {
            echo "<script>alert('Photographer updated successfully!');</script>";
        } else {
            echo "<script>alert('Error updating photographer: " . $conn->error . "');</script>";
        }
    } else {
        // Add new photographer
        $query = "INSERT INTO photographers (name, skills, experience, description, img_category_id, photographer_image) 
                  VALUES ('$name', '$skills', '$experience', '$description', '$img_category_id', '$photo')";
        if ($conn->query($query) === TRUE) {
            echo "<script>alert('Photographer added successfully!');</script>";
        } else {
            echo "<script>alert('Error adding photographer: " . $conn->error . "');</script>";
        }
    }
}

// Fetch all photographers
$photographers = $conn->query("SELECT photographers.*, category.img_category 
                               FROM photographers 
                               INNER JOIN category ON photographers.img_category_id = category.id");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Photographers</title>
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
            background-color: white;
            color:black;
        }
        .container { 
            padding: 20px; 
            max-width: 800px; 
            margin: auto; 
        }
        h2 { 
            text-align: center; 
            color: white; /* Blue for headings */
        }
        h3 { 
            text-align: center; 
            color: White; /* Blue for headings */
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
            background-color: white; /* White background for the table */
            border-radius: 5px; 
            overflow: hidden; /* Prevents overflow */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 10px; 
            text-align: left; 
        }
        th { 
            background-color: #f2f2f2; 
        }
        input[type="text"], textarea, select { 
            width: 100%; 
            padding: 8px; 
            margin-bottom: 10px; 
            border-radius: 5px; /* Rounded corners */
            border: 1px solid #ddd; /* Subtle border */
        }
        input[type="submit"], input[type="button"] { 
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
        img { 
            max-width: 100px; 
            max-height: 100px; 
            border-radius: 5px; /* Rounded image corners */
        }
    </style>
</head>
<body>
    <nav>
        <h2>Manage Photographers</h2>
        <div class="button-container">
            <a href="admin_panel.php">Go to Admin Panel</a>
            <a href="login.php">Logout</a>
        </div>
    </nav>
    <div class="container">
        <?php
        // Fetch photographer details if in edit mode
        $edit = false;
        $photographer = null;
        if (isset($_GET['edit'])) {
            $edit = true;
            $photographer_id = $_GET['edit'];
            $photographer = $conn->query("SELECT * FROM photographers WHERE id = '$photographer_id'")->fetch_assoc();
        }
        ?>

        <!-- Add/Edit Photographer Form -->
        <h3><?php echo $edit ? 'Edit Photographer' : 'Add Photographer'; ?></h3>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="photographer_id" value="<?php echo $edit ? htmlspecialchars($photographer['id']) : ''; ?>">
            
            <label for="name">Name:</label>
            <input type="text" name="name" value="<?php echo $edit ? htmlspecialchars($photographer['name']) : ''; ?>" required>

            <label for="skills">Skills:</label>
            <input type="text" name="skills" value="<?php echo $edit ? htmlspecialchars($photographer['skills']) : ''; ?>" required>

            <label for="experience">Experience (in years):</label>
            <input type="text" name="experience" value="<?php echo $edit ? htmlspecialchars($photographer['experience']) : ''; ?>" required>

            <label for="description">Description:</label>
            <textarea name="description" required><?php echo $edit ? htmlspecialchars($photographer['description']) : ''; ?></textarea>

            <label for="img_category_id">Category:</label>
            <select name="img_category_id" required>
                <option value="">Select Category</option>
                <?php
                // Fetch categories for the dropdown
                $categories = $conn->query("SELECT * FROM category");
                while ($cat = $categories->fetch_assoc()) {
                    $selected = ($edit && $photographer['img_category_id'] == $cat['id']) ? 'selected' : '';
                    echo "<option value='{$cat['id']}' $selected>{$cat['img_category']}</option>";
                }
                ?>
            </select>

            <label for="photo">Photographer Photo:</label>
            <input type="file" name="photo">
            <?php if ($edit && $photographer['photographer_image']) { ?>
                <input type="hidden" name="existing_photo" value="<?php echo htmlspecialchars($photographer['photographer_image']); ?>">
                <img src="uploads/<?php echo htmlspecialchars($photographer['photographer_image']); ?>" alt="Current Photo"><br>
                <small>Leave empty to keep the current photo</small>
            <?php } ?>

            <input type="submit" name="save" value="<?php echo $edit ? 'Update Photographer' : 'Add Photographer'; ?>">
        </form>
    </div>
</body>
</html>

<?php $conn->close(); ?>
