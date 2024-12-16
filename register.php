<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

require 'connection.php';

// Function to check if an email already exists in any table
function isEmailUnique($conn, $email)
{
    $sql = "
        SELECT email FROM users WHERE email = '$email'
        UNION
        SELECT email FROM authors WHERE email = '$email'
        UNION
        SELECT email FROM editors WHERE email = '$email'
        UNION
        SELECT email FROM reviewers WHERE email = '$email'
    ";

    $result = mysqli_query($conn, $sql);
    return (mysqli_num_rows($result) == 0);
}


// Check if a form parameter named "register" has been submitted via the HTTP POST method.
if (isset($_POST["register"])) {
    $role = $_POST["role"];
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $cpassword = $_POST["confirm_password"];
    $phone_number = $_POST['contact_number'];
    $address = $_POST['address'];
    $fname = $_POST['first_name'];
    $mname = $_POST['middle_name'];
    $lname = $_POST['last_name'];
    $image = $_POST['image_path'];

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    if (!isEmailUnique($conn, $email)) {
        echo "<script>alert('Email already exists. Please choose a different email.'); window.history.back();</script>";
    } else {
        // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            // Enable verbose debug output
            $mail->SMTPDebug = 0; // SMTP::DEBUG_SERVER;
            // Send using SMTP
            $mail->isSMTP();
            // Set the SMTP server to send through
            $mail->Host = 'smtp.gmail.com';
            // Enable SMTP authentication
            $mail->SMTPAuth = true;
            // SMTP username
            $mail->Username = 'petermaravilla522@gmail.com'; // email that will be host
            // SMTP password
            $mail->Password = 'dbyj cdfb evov mede'; // app name password
            // Enable TLS encryption;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
            $mail->Port = 587;
            // Sender
            $mail->setFrom('petermaravilla522@gmail.com', 'Written Journey');
            // Add a recipient
            $mail->addAddress($email, $name);
            // Set email format to HTML
            $mail->isHTML(true);
            $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
            $mail->Subject = 'Email verification';
            $mail->Body = '<p>Your verification code is: <b style="font-size: 30px;">' . $verification_code . '</b></p>';
            // send function to email
            $mail->send();

            // Encrypt the password
            $encrypted_password = password_hash($password, PASSWORD_DEFAULT);

            // Determine which table to insert data based on role
            $table = '';
            switch ($role) {
                case 'Author':
                    $table = 'authors';
                    break;
                case 'Editor':
                    $table = 'editors';
                    break;
                case 'Reviewer':
                    $table = 'reviewers';
                    break;
                case 'Reader':
                    $table = 'users';
            }

            // Insert the user into the appropriate table
            $sql = "INSERT INTO $table (name, email, password, verification_code, email_verified_at, attempts, contact_number, address, first_name, middle_name, last_name, image_path) 
                    VALUES ('$name', '$email', '$encrypted_password', '$verification_code', NULL, 0, '$phone_number', '$address', '$fname', '$mname', '$lname', '$image')";

            if (mysqli_query($conn, $sql)) {
                // Redirect to the email verification page
                header("Location:http://localhost/written-journey/email-verification.php?email=" . $email);
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REGISTER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>


</head>

<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-sm" style="width: 700px;">
            <h3 class="text-center mb-4">SIGN UP</h3>
            <form method="POST" onsubmit="return validateForm();">
                <div class="row">
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select name="role" id="role" class="form-control" required>
                                <option value="">-- Select Role --</option>
                                <option value="Reader">Reader</option>
                                <option value="Editor">Editor</option>
                                <option value="Reviewer">Reviewer</option>
                                <option value="Author">Author</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" id="first_name" name="first_name" class="form-control" placeholder="First Name" required>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="middle_name" class="form-label">Middle Name</label>
                            <input type="text" id="middle_name" name="middle_name" class="form-control" placeholder="Middle Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" id="last_name" name="last_name" class="form-control" placeholder="Last Name" required>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="tel" id="contact_number" name="contact_number" class="form-control" placeholder="Contact Number" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" id="address" name="address" class="form-control" placeholder="Address" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" id="username" name="name" class="form-control" placeholder="Username" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="info@gmail.com" required>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="word" name="word" required>
                    <label class="form-check-label" for="word">I accept the <a href="#">Terms and Conditions</a></label>
                </div>
                <div class="d-grid">
                    <button type="submit" name="register" class="btn btn-success">REGISTER</button>
                </div>
                <div class="text-center mt-3">
                    <p class="mb-0">Have an account? <a href="login.php" class="link-success">LOGIN</a></p>
                </div>
            </form>
        </div>
    </div>
    <script>
        function validateForm() {
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('confirm_password').value;
            if (password !== confirmPassword) {
                alert('Password and Confirm Password do not match.');
                return false;
            }
            return true;
        }
    </script>
</body>

</html>