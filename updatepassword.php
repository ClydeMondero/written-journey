<?php
// Change password for forgotten password
require 'connection.php';

$email = $_GET['email']; // Get email from URL

// Get the role dynamically based on the email
$roles = ['users', 'authors', 'editors', 'reviewers'];
$role = null;

foreach ($roles as $r) {
    $sql = "SELECT * FROM $r WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $role = $r;
        break; // Stop searching once the role is found
    }
}

if ($role === null) {
    echo "<script>alert('Email not found in any role.'); window.location.href = 'login.php';</script>";
    exit; // Stop if no role is found for this email
}

if (isset($_POST['updatepass'])) {
    $newpassword = md5($_POST['newpassword']);
    $confirmpassword = md5($_POST['confirmpassword']);

    if ($newpassword == $confirmpassword) {
        // Hash the new password
        $newpassword_hashed = password_hash($_POST['newpassword'], PASSWORD_DEFAULT);

        // Update password for the correct role
        $querychange = "UPDATE $role SET password='" . $newpassword_hashed . "' WHERE email='" . $email . "'";
        $change_result = mysqli_query($conn, $querychange);

        if ($change_result) {
            echo "<script>alert('Your password has been changed'); window.location.href = 'login.php';</script>";
        } else {
            echo "<script>alert('Failed to update password. Please try again later.');</script>";
        }
    } else {
        echo "<script>alert('New password doesn\'t match!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPDATE FORGOT PASSWORD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>


</head>

<body>
    <div class="container mt-5">
        <div class="card p-4">
            <h2 class="text-center mb-4">UPDATE FORGOT PASSWORD</h2>
            <form method="POST">
                <div class="mb-3">
                    <label for="newpassword" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="newpassword" name="newpassword" placeholder="************" required>
                </div>
                <div class="mb-3">
                    <label for="confirmpassword" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirmpassword" name="confirmpassword" placeholder="************" required>
                </div>
                <div class="d-grid">
                    <button type="submit" name="updatepass" class="btn btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>