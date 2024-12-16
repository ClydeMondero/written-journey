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
            $mail->Username = 'petermaravilla522@gmail.com';
            $mail->Password = 'dbyj cdfb evov mede';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('petermaravilla522@gmail.com', 'Written Journey');
            $mail->addAddress($email, $email);
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset';
            $mail->Body    = '<p>Your verification code is: <b style="font-size: 30px;">' . $verification_code . '</b></p>';
            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: " . $mail->ErrorInfo;
        }

        // Redirect to the verification page with the role parameter
        header("Location:http://localhost/written-journey/email-verification.php?email=" . $email . "&type=password&role=$role");
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>


</head>

<body>
    <div class="container-content p-5">
        <p class="h2 forgot-label">Forgot Password?</p>
        <p class="lead email-label">Enter the email address </p>
        <form method="POST">
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="info@gmail.com" required />
            </div>
            <div class="d-grid">
                <input type="submit" name="next" class="btn btn-success" value="Next">
            </div>
        </form>
        <a href="login.php" class="btn btn-secondary mt-3">Back</a>
    </div>
</body>

</html>