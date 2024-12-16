<?php
//users form for changing password
error_reporting(E_ALL & ~E_NOTICE);
include 'change-password.php';
require 'connection.php';
include('customer-nav.php');

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the user is logged in
if (isset($_SESSION['user_name'])) {
    $userName = $_SESSION['user_name'];
}
// Retrieve user information from the database
$query = "SELECT image_path, address, first_name, middle_name, last_name, contact_number FROM users WHERE name='$userName'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    // Save the user information in session variables
    $_SESSION['image_path'] = $row['image_path'];
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
    <title>Change User Password</title>
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
        <div class="card p-4">
            <h1 class="h3 mb-4 font-weight-normal">PASSWORD SETTINGS</h1>
            <form method="post">
                <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password:</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Enter your old password" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">New Password:</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your new password" required>
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password:</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Re-enter your new password" required>
                </div>

                <button type="submit" name="change_password" class="btn btn-success">Change Password</button>
            </form>
        </div>
    </div>
</body>

</html>