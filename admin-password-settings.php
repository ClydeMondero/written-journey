<?php
//password form for admin
//include('admin-nav.php');
include 'admin-change-password.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body>
    <div class="settings">
        <h1 class="text-center">PASSWORD SETTINGS</h1>
        <form method="post">
            <!-- Current Password -->
            <div class="form-group">
                <label for="current_password">OLD PASSWORD</label>
                <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Enter your old password" required />
            </div>
            <br>
            <!-- New Password -->
            <div class="form-group">
                <label for="password">NEW PASSWORD</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your new password" value="" required />
            </div>
            <br>
            <!-- Confirm New Password -->
            <div class="form-group">
                <label for="confirm_password">CONFIRM NEW PASSWORD</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm your new password" value="" required />
            </div>
            <br>
            <!-- Submit Button -->
            <div>
                <button class="btn btn-success" type="submit" name="change_password">Change Password</button>
            </div>
        </form>
    </div>
</body>

</html>