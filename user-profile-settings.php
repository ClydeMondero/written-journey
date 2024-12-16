<?php
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
    $_SESSION['image_path'] = (isset($row['image_path']) && !empty($row['image_path'])) ? $row['image_path'] : 'img/default-profile.png';
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
    <title>User Profile Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body style="background-color: #f8f9fa;">

    <script>
        function updateProfileImage(newImagePath) {
            document.getElementById('profileImage').src = newImagePath;
        }
    </script>

    <div class="container mt-5">
        <div class="settings card p-3">
            <h1 class="h3 mb-4 font-weight-normal">ACCOUNT SETTINGS</h1>
            <form action="update-profile.php" class="forms" method="post" enctype="multipart/form-data">
                <div class="profile-con my-3">
                    <img id="profileImage" class="profile-image rounded-circle" src="<?php echo isset($_SESSION['image_path']) ? $_SESSION['image_path'] . '?' . time() : ''; ?>" alt="Profile Picture" style="width: 100px; height: 100px;">
                    <label for="profile_picture" class="profile-txt">Profile Picture</label>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="form-control-file" onchange="previewImage(event)">

                </div>

                <div class="forms-content my-3">
                    <!-- Display username -->
                    <div class="form-group">
                        <label for="new_username">Username:</label>
                        <input type="text" id="new_username" name="new_username" value="<?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''; ?>" required class="form-control" />
                    </div>

                    <!-- First Name -->
                    <div class="form-group">
                        <label for="first_name">First Name:</label>
                        <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" value="<?php echo isset($_SESSION['first_name']) ? htmlspecialchars($_SESSION['first_name']) : ''; ?>" required class="form-control" />
                    </div>

                    <!-- Middle Name -->
                    <div class="form-group">
                        <label for="middle_name">Middle Name:</label>
                        <input type="text" id="middle_name" name="middle_name" placeholder="Enter your middle name" value="<?php echo isset($_SESSION['middle_name']) ? htmlspecialchars($_SESSION['middle_name']) : ''; ?>" required class="form-control" />
                    </div>

                    <!-- Last Name -->
                    <div class="form-group">
                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" value="<?php echo isset($_SESSION['last_name']) ? htmlspecialchars($_SESSION['last_name']) : ''; ?>" required class="form-control" />
                    </div>

                    <!-- Address -->
                    <div class="form-group">
                        <label for="address">Address:</label>
                        <input type="text" id="address" name="address" placeholder="Enter your address" value="<?php echo isset($_SESSION['address']) ? htmlspecialchars($_SESSION['address']) : ''; ?>" required class="form-control" />
                    </div>

                    <!-- Contact Number -->
                    <div class="form-group">
                        <label for="contact_number">Contact Number:</label>
                        <input type="text" id="contact_number" name="contact_number" pattern="[0-9]{11}" value="<?php echo $_SESSION['contact_number']; ?>" class="form-control">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="save-btn btn btn-success">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const profileImage = document.getElementById('profileImage');
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profileImage.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    </script>

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