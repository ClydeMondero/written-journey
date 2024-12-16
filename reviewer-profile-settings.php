<?php
error_reporting(E_ALL & ~E_NOTICE);
session_start();
include('reviewers-nav.php');
require 'connection.php';

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the user is logged in
if (isset($_SESSION['user_name'])) {
    $userName = $_SESSION['user_name'];
}

// Retrieve user information from the database
$query = "SELECT image_path, address, first_name, middle_name, last_name, contact_number FROM reviewers WHERE name='$userName'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    // Save the user information in session variables
    $_SESSION['image_path'] = empty($row['image_path']) ? 'img/default-profile.png' : $row['image_path'];
    $_SESSION['address'] = $row['address'];
    $_SESSION['first_name'] = $row['first_name'];
    $_SESSION['middle_name'] = $row['middle_name'];
    $_SESSION['last_name'] = $row['last_name'];
    $_SESSION['contact_number'] = $row['contact_number'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviewers Profile Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body>

    <script>
        function updateProfileImage(newImagePath) {
            document.getElementById('profileImage').src = newImagePath;
        }
    </script>

    <div class="container mt-5">
        <a href="javascript:history.back()" class="btn btn-secondary">Back</a>
        <h1 class="text-center mb-4">ACCOUNT SETTINGS</h1>
        <form action="reviewers-update-profile.php" method="post" enctype="multipart/form-data" class="row g-3">
            <div class="col-md-12 text-center">
                <div id="profilePicturePreviewContainer" class="mb-3"></div>
                <img id="profileImage" class="img-thumbnail" src="<?php echo isset($_SESSION['image_path']) ? $_SESSION['image_path'] . '?' . time() : ''; ?>" alt="Profile Picture" style="width: 150px; height: 150px;">
                <label for="profile_picture" class="form-label mt-3">Profile Picture</label>
                <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
            </div>

            <div class="col-md-12">
                <label for="new_username" class="form-label">Username:</label>
                <input type="text" class="form-control" id="new_username" name="new_username" value="<?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''; ?>" required>
            </div>

            <div class="col-md-4">
                <label for="first_name" class="form-label">First Name:</label>
                <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter your first name" value="<?php echo isset($_SESSION['first_name']) ? htmlspecialchars($_SESSION['first_name']) : ''; ?>" required>
            </div>

            <div class="col-md-4">
                <label for="middle_name" class="form-label">Middle Name:</label>
                <input type="text" class="form-control" id="middle_name" name="middle_name" placeholder="Enter your middle name" value="<?php echo isset($_SESSION['middle_name']) ? htmlspecialchars($_SESSION['middle_name']) : ''; ?>" required>
            </div>

            <div class="col-md-4">
                <label for="last_name" class="form-label">Last Name:</label>
                <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter your last name" value="<?php echo isset($_SESSION['last_name']) ? htmlspecialchars($_SESSION['last_name']) : ''; ?>" required>
            </div>

            <div class="col-md-12">
                <label for="address" class="form-label">Address:</label>
                <input type="text" class="form-control" id="address" name="address" placeholder="Enter your address" value="<?php echo isset($_SESSION['address']) ? htmlspecialchars($_SESSION['address']) : ''; ?>" required>
            </div>

            <div class="col-md-12">
                <label for="contact_number" class="form-label">Contact Number:</label>
                <input type="text" class="form-control" id="contact_number" name="contact_number" pattern="[0-9]{11}" value="<?php echo $_SESSION['contact_number']; ?>">
            </div>

            <div class="col-md-12">
                <button type="submit" class="btn btn-success">Save Changes</button>
            </div>
        </form>
    </div>

    <?php include('reviewers-change-password.php'); ?>

    <script>
        function validateForm() {
            var contactNumber = document.getElementById("contact_number").value;
            var pattern = /^[0-9]{11}$/;
            if (!pattern.test(contactNumber)) {
                alert("Please enter a valid 11-digit contact number.");
                return false;
            }
            return true;
        }
    </script>

</body>

</html>