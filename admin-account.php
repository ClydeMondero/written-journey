<?php 
include('admin-nav.php');
include('admin-password-settings.php');

// Initialize variables with default values
$existingFullname = "";
$existingUsername = "";
$existingAddress = "";
$existingPhonenumber = "";
$existingImage = "no-image.webp"; // Default image path

require 'connection.php';

if ($conn) {
    // Fetch existing admin data
    $selectInfoSql = "SELECT * FROM admin WHERE email = 'admin@gmail.com'";
    $result = mysqli_query($conn, $selectInfoSql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);

        if ($row) {
            $existingFullname = $row['fullname'];
            $existingUsername = $row['username'];
            $existingAddress = $row['address'];
            $existingPhonenumber = $row['phone_number'];
            $existingImage = !empty($row['image']) ? $row['image'] : $existingImage;
        }

        mysqli_free_result($result);
    } else {
        echo "<script>alert('Error fetching admin information: " . mysqli_error($conn) . "');</script>";
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["changeInfo"])) {
        $full_name = htmlspecialchars($_POST["newFullname"]);
        $username = htmlspecialchars($_POST["newUsername"]);
        $address = htmlspecialchars($_POST["newAddress"]);
        $phone_number = htmlspecialchars($_POST["newPhonenumber"]);

        $profile_picture = $existingImage; // Default to existing image

        // Handle image upload
        if (isset($_FILES["newImg"]) && $_FILES["newImg"]["error"] == 0) {
            $uploadDir = "img/";
            $imageFileType = strtolower(pathinfo($_FILES["newImg"]["name"], PATHINFO_EXTENSION));
            $allowedFormats = ["jpg", "jpeg", "png", "gif"];

            // Validate file type
            if (in_array($imageFileType, $allowedFormats)) {
                $profile_picture = $uploadDir . uniqid() . '.' . $imageFileType;

                if (!move_uploaded_file($_FILES["newImg"]["tmp_name"], $profile_picture)) {
                    echo "<script>alert('Failed to upload profile picture.');</script>";
                    $profile_picture = $existingImage;
                }
            } else {
                echo "<script>alert('Invalid file format. Allowed formats: JPG, JPEG, PNG, GIF.');</script>";
            }
        }

        // Update admin data
        $updateInfoSql = "UPDATE admin SET fullname = ?, username = ?, address = ?, phone_number = ?, image = ? WHERE email = 'admin@gmail.com'";
        $stmt = mysqli_prepare($conn, $updateInfoSql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssss", $full_name, $username, $address, $phone_number, $profile_picture);

            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Admin information updated successfully.'); window.location.href = 'admin-account.php';</script>";
            } else {
                echo "<script>alert('Error updating admin information: " . mysqli_stmt_error($stmt) . "');</script>";
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "<script>alert('Error preparing statement: " . mysqli_error($conn) . "');</script>";
        }
    }

    mysqli_close($conn);
} else {
    die("Connection failed: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN ACCOUNT SETTINGS</title>
</head>
<body>
    <div id="account-tab">
        <div class="account-form">
            <h1>ACCOUNT SETTINGS</h1>
            <p>Manage and protect your account</p> <br><br>

            <form method="POST" enctype="multipart/form-data">
                <label class="tflabel">FULL NAME</label> <br>
                <input type="text" name="newFullname" placeholder="Full Name" value="<?= htmlspecialchars($existingFullname); ?>" required /><br><br>
                
                <label class="tflabel">USERNAME</label> <br>
                <input type="text" name="newUsername" placeholder="Username" value="<?= htmlspecialchars($existingUsername); ?>" required /><br><br>
                
                <label class="tflabel">ADDRESS</label> <br>
                <input type="text" name="newAddress" placeholder="Address" value="<?= htmlspecialchars($existingAddress); ?>" required /><br><br>
                
                <label class="tflabel">PHONE NUMBER</label> <br>
                <input type="text" name="newPhonenumber" placeholder="Phone Number" value="<?= htmlspecialchars($existingPhonenumber); ?>" required /><br><br>

                <div class="imageProd">
                    <img src="<?= htmlspecialchars($existingImage); ?>" id="imagePreview" alt="Image Preview" style="width:100px; height:100px;">
                    <span class="profile">PROFILE PICTURE</span>
                </div>

                <input type="file" id="newImg" name="newImg" onchange="previewImage(this);"><br><br>
                <input class="save" type="submit" name="changeInfo" value="Save"><br><br>
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
