<?php
error_reporting(E_ALL & ~E_NOTICE);
session_start();
include('editors-nav.php');
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
$query = "SELECT image_path, address, first_name, middle_name, last_name, contact_number FROM editors WHERE name='$userName'";
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
    <title>Editors Profile Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body>
    <script>
        function updateProfileImage(newImagePath) {
            document.getElementById('profileImage').src = newImagePath;
        }
    </script>

    <div class="container">
        <a href="javascript:history.back()" class="btn btn-secondary mt-3">Back</a>
        <div class="cards p-4">
            <h1 class="text-center">ACCOUNT SETTINGS</h1>
            <div class="row">
                <div class="col-md-6">
                    <div class="profile-con">
                        <img id="profileImage" class="profile-image" style="width: 150px; height: 150px;" src="<?php echo isset($_SESSION['image_path']) ? $_SESSION['image_path'] . '?' . time() : ''; ?>" alt="Profile Picture">
                        <label for="profile_picture" class="profile-txt">Profile Picture</label>
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                    </div>

                    <!-- Display username -->
                    <div class="mb-3">
                        <label for="new_username" class="form-label">Username:</label>
                        <input type="text" id="new_username" name="new_username" class="form-control" value="<?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''; ?>" required />
                    </div>

                    <!-- First Name -->
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name:</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" placeholder="Enter your first name" value="<?php echo isset($_SESSION['first_name']) ? htmlspecialchars($_SESSION['first_name']) : ''; ?>" required />
                    </div>

                    <!-- Middle Name -->
                    <div class="mb-3">
                        <label for="middle_name" class="form-label">Middle Name:</label>
                        <input type="text" id="middle_name" name="middle_name" class="form-control" placeholder="Enter your middle name" value="<?php echo isset($_SESSION['middle_name']) ? htmlspecialchars($_SESSION['middle_name']) : ''; ?>" required />
                    </div>

                    <!-- Last Name -->
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" placeholder="Enter your last name" value="<?php echo isset($_SESSION['last_name']) ? htmlspecialchars($_SESSION['last_name']) : ''; ?>" required />
                    </div>

                    <!-- Address -->
                    <div class="mb-3">
                        <label for="address" class="form-label">Address:</label>
                        <input type="text" id="address" name="address" class="form-control" placeholder="Enter your address" value="<?php echo isset($_SESSION['address']) ? htmlspecialchars($_SESSION['address']) : ''; ?>" required />
                    </div>

                    <!-- Contact Number -->
                    <div class="mb-3">
                        <label for="contact_number" class="form-label">Contact Number:</label>
                        <input type="text" id="contact_number" name="contact_number" class="form-control" pattern="[0-9]{11}" value="<?php echo $_SESSION['contact_number']; ?>">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="save-btn btn btn-success">Save Changes</button>
                </div>
            </div>
            </form>
        </div>
    </div>

    <?php include('editors-change-password.php'); ?>

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