<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';
require 'connection.php';

if (isset($_POST["next"])) {
    $email = $_POST["email"];

    // Check for the email in all relevant tables
    $roles = ['users', 'authors', 'editors', 'reviewers'];
    $roleFound = false;
    foreach ($roles as $role) {
        $sql = "SELECT * FROM $role WHERE email = '$email'";
        $result = mysqli_query($conn, $sql); 
        if (mysqli_num_rows($result) > 0) {
            $roleFound = true;
            break;
        }
    }

    if (!$roleFound) {
        echo "<script>alert('Email not found.'); window.history.back();</script>";
    } else {
        // Generate a unique reset token (you can use random_bytes or any other method)
        $reset_token = bin2hex(random_bytes(16));
        $expiration_time = date("Y-m-d H:i:s", strtotime("+1 hour"));
        $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);

        // Store the reset token, expiration time, and verification code in the appropriate table
        $update_sql = "UPDATE $role SET reset_token = '$reset_token', reset_token_expiration = '$expiration_time', verification_code = '$verification_code' WHERE email = '$email'";
        mysqli_query($conn, $update_sql);

        // Send the verification code to the user's email
        $mail = new PHPMailer(true);
        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'jeremderoxas@gmail.com';
            $mail->Password = 'ljtjopuicvwcwjwe';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('jeremderoxas@gmail.com', 'AndromedaArchive');
            $mail->addAddress($email, $email);
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset';
            $mail->Body    = '<p>Your verification code is: <b style="font-size: 30px;">' . $verification_code . '</b></p>';
            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: " . $mail->ErrorInfo;
        }

        // Redirect to the verification page with the role parameter
        header("Location:http://localhost/journal/email-verification.php?email=".$email."&type=password&role=$role");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FORGET PASSWORD</title>
</head>
<body>
    <div class="container-content">
        <p class="forgot-label">Forgot Password?</p>
        <p class="email-label">Enter the email address </p>
        <form method="POST" >
            <input type="email" name="email" placeholder="  info@gmail.com" required /><br>
            <input type="submit" name="next" value="Next">
        </form>
        <a href="login.php"><button class="back"> Back</button></a>
    </div>
</body>
</html>