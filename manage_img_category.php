<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// DB connection
$conn = new mysqli('localhost:3390', 'root', '', 'capturemoments');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If form is submitted, update the category of the image
if (isset($_POST['update_category'])) {
    $image_id = $_POST['image_id'];
    $new_category_id = $_POST['new_category_id'];

    $query = "UPDATE image_info SET img_category_id = '$new_category_id' WHERE id = '$image_id'";
    if ($conn->query($query) === TRUE) {
        echo "Category updated successfully!";
        header("Location: manage_image_category.php"); // Redirect after success to refresh the page
        exit(); // Ensure no further code is executed after redirection
    } else {
        echo "Error updating category: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Image Categories</title>
    <link href="https://fonts.googleapis.com/css2?family=Arial:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Arial', sans-serif; 
            margin: 0; 
            padding: 0; 
            background-color: #1e1e1e; /* Light background */
            color: #333; /* Dark text */
        }
        nav { 
            background-color: #007bff; /* Blue */
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
            background-color: #28a745; /* Green for buttons */
            transition: background-color 0.3s ease; /* Transition effect */
        }
        nav a:hover { 
            background-color: #218838; /* Darker green on hover */
        }
        .container { 
            padding: 20px; 
            max-width: 800px; 
            margin: auto; 
        }
        h2 { 
            text-align: center; 
            color: white; 
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
        img { 
            max-width: 100px; 
            max-height: 100px; 
            border-radius: 5px; /* Rounded image corners */
        }
        select { 
            padding: 5px; 
            border-radius: 5px; /* Rounded corners for select */
            border: 1px solid #ddd; /* Subtle border */
        }
        input[type="submit"] { 
            padding: 5px 10px; 
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
        <h2>Manage Image Categories</h2>
        <div class="button-container">
            <a href="admin_panel.php">Go to Admin Panel</a>
            <a href="login.php">Logout</a>
        </div>
    </nav>
    <div class="container">
        <table>
            <tr>
                <th>Image</th>
                <th>Current Category</th>
                <th>Change Category</th>
                <th>Action</th>
            </tr>
            <?php
            // Fetch all images and their current categories
            $query = "SELECT image_info.id AS image_id, image_info.image_name, category.img_category, category.id AS category_id 
                      FROM image_info 
                      INNER JOIN category ON image_info.img_category_id = category.id";
            $result = $conn->query($query);

            // Loop through each image and display it in a table row
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td><img src='uploads/{$row['image_name']}' alt='Image Preview'></td>
                    <td>{$row['img_category']}</td>
                    <td>
                        <form method='post'>
                            <input type='hidden' name='image_id' value='{$row['image_id']}'>
                            <select name='new_category_id'>";

                // Fetch all available categories for the dropdown
                $categories = $conn->query("SELECT * FROM category");
                while ($cat = $categories->fetch_assoc()) {
                    // Mark the current category as selected
                    $selected = ($row['category_id'] == $cat['id']) ? 'selected' : '';
                    echo "<option value='{$cat['id']}' $selected>{$cat['img_category']}</option>";
                }

                echo "</select>
                    </td>
                    <td><input type='submit' name='update_category' value='Update'></td>
                    </form>
                </tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
