<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 bg-light p-3">
                <?php include 'admin-nav.php'; ?>
            </div>

            <!-- Main Content -->
            <div class="card col-md-9">

                <div class="card-header">
                    <h3 class="card-title my-4">JOURNAL ISSUES</h3>
                </div>
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>ADD JOURNAL ISSUES</h4>
                    </div>
                    <div class="card-body">
                        <form action="" method="post" autocomplete="off" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title:</label>
                                <input type="text" name="title" id="title" class="form-control" required placeholder="Enter title">
                            </div>
                            <div class="mb-3">
                                <label for="vol_no" class="form-label">Volume No.:</label>
                                <input type="text" name="vol_no" id="vol_no" class="form-control" required placeholder="Enter volume number">
                            </div>
                            <div class="mb-3">
                                <label for="publication_date" class="form-label">Publication Date:</label>
                                <input type="text" name="publication_date" id="publication_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Image:</label>
                                <input type="file" name="image" id="image" class="form-control" accept=".jpg, .jpeg, .png, .webp, .avif" onchange="previewImage(this);">
                            </div>
                            <button type="submit" name="submit" class="btn btn-success">Submit</button>
                        </form>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h4>JOURNAL ISSUES LIST</h4>
                    </div>
                    <div class="card-body">
                        <form action="" method="get" class="mb-4">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" id="search" placeholder="Enter issue title" required>
                                <button type="submit" class="btn btn-outline-secondary">Search</button>
                            </div>
                        </form>

                        <form action="" method="POST">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Volume No.</th>
                                        <th>Publication Date</th>
                                        <th>Image</th>
                                        <th>Edit</th>
                                        <th>Select</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($searchResult)): ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td><?php echo $row['title']; ?></td>
                                            <td><?php echo $row['vol_no']; ?></td>
                                            <td><?php echo $row['publication_date']; ?></td>
                                            <td><img src="img/<?php echo $row['image']; ?>" alt="Issue Image" height="100"></td>
                                            <td>
                                                <a href="edit-journal-issues.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success">Edit</a>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="selected_issues[]" value="<?php echo $row['id']; ?>">
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                            <button type="submit" name="delete_selected" class="btn btn-success">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            var preview = document.getElementById('imagePreview');
            var file = input.files[0];
            var reader = new FileReader();

            reader.onload = function(e) {
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