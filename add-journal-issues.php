<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('admin-nav.php');
require 'connection.php';

$fileUploadedSuccessfully = false;

if (isset($_POST["submit"])) {
    $title = $_POST["title"];
    $vol_no = $_POST["vol_no"];
    $publication_date = $_POST["publication_date"];

    if (isset($_POST["submit"])) {
        $title = $_POST["title"];
        $vol_no = $_POST["vol_no"];
        $publication_date = date('Y-m-d');
    
        if (isset($_FILES["image"])) {
            if ($_FILES["image"]["error"] === UPLOAD_ERR_OK) {
                $fileName = $_FILES["image"]["name"];
                $fileSize = $_FILES["image"]["size"];
                $tmpName = $_FILES["image"]["tmp_name"];
    
                $validImageExtension = ['jpg', 'jpeg', 'png', 'webp', 'avif'];
                $imageExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
                if (in_array($imageExtension, $validImageExtension) && $fileSize <= 1000000) {
                    $newImageName = uniqid() . '.' . $imageExtension;
                    $res = move_uploaded_file($tmpName, 'img/' . $newImageName);
                    if ($res) {
                        $query = "INSERT INTO issues (title, vol_no, publication_date, image) 
                                  VALUES ('$title', '$vol_no', '$publication_date', '$newImageName')";
                        if (mysqli_query($conn, $query)) {
                            echo "<script>alert('Successfully Added');</script>";
                        } else {
                            echo "<script>alert('Failed to Add Issue');</script>";
                        }
                    } else {
                        echo "<script>alert('Failed to Upload Image');</script>";
                    }
                } else {
                    echo "<script>alert('Invalid Image Extension or Image Size Is Too Large');</script>";
                }
            } else {
                echo "<script>alert('Image Upload Error');</script>";
            }
        }
    }    
}

// Delete Selected Issues
if (isset($_POST["delete_selected"])) {
    if (isset($_POST["selected_issues"]) && is_array($_POST["selected_issues"])) {
        foreach ($_POST["selected_issues"] as $selectedIssueId) {
            $deleteQuery = "DELETE FROM issues WHERE id = $selectedIssueId";
            mysqli_query($conn, $deleteQuery);
        }
        echo "<script>alert('Selected Issues Deleted Successfully');</script>";
    } else {
        echo "<script>alert('No issues selected for deletion');</script>";
    }
}

// Search Issues
$searchTerm = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$searchQuery = "SELECT * FROM issues WHERE title LIKE '%$searchTerm%'";
$searchResult = mysqli_query($conn, $searchQuery);
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>JOURNAL ISSUES</title>
</head>
<body>
    <h1 class="text1">JOURNAL ISSUES</h1>
    <div class="all">
        <div class="add">
            <h2 class="text2">ADD JOURNAL ISSUES</h2><br>
            <form action="" method="post" autocomplete="off" enctype="multipart/form-data">
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" required placeholder="Enter title"><br><br>
                <label for="vol_no">Volume No.:</label>
                <input type="text" name="vol_no" id="vol_no" required placeholder="Enter volume number"><br><br>
                <label for="publication_date">Publication Date:</label>
                <input type="text" name="publication_date" id="publication_date" value="<?php echo date('Y-m-d'); ?>" readonly><br><br>
                <label for="image">Image:</label>
                <input type="file" name="image" id="image" accept=".jpg, .jpeg, .png, .webp, .avif" onchange="previewImage(this);"><br><br>
                <button type="submit" name="submit" class="btnSubmit">Submit</button>
            </form>
        </div>

        <!-- Image Preview -->
        <div class="imageProd">
            <img src="no-image.webp" id="imagePreview" alt="Image Preview">
        </div>

        <!-- Issues List -->
        <div class="view">
            <h1 class="text4">JOURNAL ISSUES LIST</h1>

            <!-- Search Form -->
            <form action="" method="get">
                <label for="search" class="text5">Search Issue:</label>
                <input type="text" name="search" class="searchtxt" id="search" placeholder="Enter issue title" required>
                <button type="submit" class="btnSearch">Search</button>
            </form><br>

            <form action="" method="POST">
                <table border="1" cellspacing="0" cellpadding="10" class="viewTable">
                    <tr class="thView">
                        <th>ID</th>
                        <th>Title</th>
                        <th>Volume No.</th>
                        <th>Publication Date</th>
                        <th>Image</th>
                        <th>Select</th>
                    </tr>
                    <?php while ($row = mysqli_fetch_assoc($searchResult)): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['title']; ?></td>
                            <td><?php echo $row['vol_no']; ?></td>
                            <td><?php echo $row['publication_date']; ?></td>
                            <td><img src="img/<?php echo $row['image']; ?>" alt="Issue Image" height="100"></td>
                            <td>
                                <a href="edit-journal-issues.php?id=<?php echo $row['id']; ?>" class="editbtn">Edit</a>
                            </td>
                            <td>
                                <input type="checkbox" name="selected_issues[]" value="<?php echo $row['id']; ?>">
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
                <button type="submit" name="delete_selected" class="deletebtn">Delete</button>
            </form>
        </div>
    </div>

    <script>
        function previewImage(input) {
            var preview = document.getElementById('imagePreview');
            var file = input.files[0];
            var reader = new FileReader();

            reader.onload = function (e) {
                preview.src = e.target.result;
            };

            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = "no-image.webp";
            }
        }
    </script>
</body>
</html>