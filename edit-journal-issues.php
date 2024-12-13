<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('admin-nav.php');
require 'connection.php';

if (isset($_GET['id'])) {
    $issueId = $_GET['id'];
    // Fetch the issue data from the database using the provided ID
    $query = "SELECT * FROM issues WHERE id = $issueId";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $issueData = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Issue not found'); window.location.href = 'add-journal-issues.php';</script>";
        exit();
    }
}

// Update the journal issue
if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $vol_no = $_POST['vol_no'];
    $publication_date = $_POST['publication_date'];

    $updateQuery = "UPDATE issues SET title = '$title', vol_no = '$vol_no', publication_date = '$publication_date' WHERE id = $issueId";

    if (mysqli_query($conn, $updateQuery)) {
        echo "<script>alert('Issue Updated Successfully'); window.location.href = 'add-journal-issues.php';</script>";
    } else {
        echo "<script>alert('Failed to Update Issue');</script>";
    }

    // Handle image update if a new image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileName = $_FILES["image"]["name"];
        $fileSize = $_FILES["image"]["size"];
        $tmpName = $_FILES["image"]["tmp_name"];

        $validImageExtension = ['jpg', 'jpeg', 'png', 'webp', 'avif'];
        $imageExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($imageExtension, $validImageExtension) && $fileSize <= 1000000) {
            $newImageName = uniqid() . '.' . $imageExtension;
            $res = move_uploaded_file($tmpName, 'img/' . $newImageName);
            if ($res) {
                // Update the image in the database
                $imageUpdateQuery = "UPDATE issues SET image = '$newImageName' WHERE id = $issueId";
                mysqli_query($conn, $imageUpdateQuery);
            }
        } else {
            echo "<script>alert('Invalid Image Extension or Image Size Is Too Large');</script>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Edit Journal Issue</title>
</head>
<body>
    <h1>Edit Journal Issue</h1>

    <div class="edit">
        <form action="" method="post" autocomplete="off" enctype="multipart/form-data">
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" value="<?php echo $issueData['title']; ?>" required placeholder="Enter title"><br><br>
            
            <label for="vol_no">Volume No.:</label>
            <input type="text" name="vol_no" id="vol_no" value="<?php echo $issueData['vol_no']; ?>" required placeholder="Enter volume number"><br><br>
            
            <label for="publication_date">Publication Date:</label>
            <input type="text" name="publication_date" id="publication_date" value="<?php echo $issueData['publication_date']; ?>" readonly><br><br>

            <label for="image">Image:</label>
            <input type="file" name="image" id="image" accept=".jpg, .jpeg, .png, .webp, .avif" onchange="previewImage(this);"><br><br>

            <button type="submit" name="submit" class="btnSubmit">Update</button>
        </form>
    </div>

    <div class="imageProd">
        <img src="img/<?php echo $issueData['image']; ?>" id="imagePreview" alt="Image Preview" height="100">
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
