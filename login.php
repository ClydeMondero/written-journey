<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

require 'connection.php';

session_start();

// Check if the user is logged in
if (isset($_SESSION['user_name'])) {
    header("Location: http://localhost/written-journey/customer-dashboard.php?user=" . $_SESSION['user_name']);
    exit;
}

// Check if a form parameter named "login" has been submitted via the HTTP POST method.
if (isset($_POST["login"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Define the default admin credentials
    $default_admin_email = "admin@gmail.com";
    $default_admin_password = "admin";

    // Check if the email exists in the admin table
    $stmt_admin = mysqli_prepare($conn, "SELECT * FROM admin WHERE email = ?");
    mysqli_stmt_bind_param($stmt_admin, "s", $email);
    mysqli_stmt_execute($stmt_admin);
    $result_admin = mysqli_stmt_get_result($stmt_admin);

    if (mysqli_num_rows($result_admin) > 0) {
        // Admin login
        $user = mysqli_fetch_object($result_admin);

        // Check if the password is correct
        if (!password_verify($password, $user->password)) {
            echo "<script>alert('Incorrect password for admin.'); window.history.back();</script>";
            exit;
        }

        // Set session variables for the logged-in admin
        $_SESSION['user_type'] = 'admin';
        $_SESSION['user_email'] = $email;
        $_SESSION['admin_username'] = $user->username;
        $_SESSION['admin_fullname'] = $user->fullname;

        // Redirect to admin dashboard
        header("Location: http://localhost/written-journey/admin-dashboard.php");
        exit;
    } else {
        $roles = ['users', 'authors', 'editors', 'reviewers'];
        $userFound = false;
        foreach ($roles as $role) {
            $stmt = mysqli_prepare($conn, "SELECT * FROM $role WHERE email = ?");
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_object($result);

                // Check if the password is correct
                if (!password_verify($password, $user->password)) {
                    recordLoginAttempt($conn, $email, $role);
                    $loginAttempts = getLoginAttempts($conn, $email, $role);

                    // Check if the user has reached the maximum attempts
                    $maxAttempts = 3;
                    if ($loginAttempts >= $maxAttempts) {
                        $blockSql = "UPDATE $role SET blocked = 1 WHERE email = '$email'";
                        mysqli_query($conn, $blockSql);

                        echo "<script>alert('Your account is blocked. Contact the admin for assistance.'); window.history.back();</script>";
                        exit;
                    }

                    echo "<script>alert('Incorrect password.'); window.history.back();</script>";
                    exit;
                }

                // Check if the user is blocked
                if ($user->blocked == 1) {
                    echo "<script>alert('Your account is blocked. Contact the admin for assistance.'); window.history.back();</script>";
                    exit;
                }

                // Check email verification for users, authors, reviewers, editors
                if ($user->email_verified_at == null && strtolower($email) !== $default_admin_email) {
                    echo "<script>alert('Please verify your email.'); window.history.back();</script>";
                    exit;
                }

                $_SESSION['user_type'] = $role;
                $_SESSION['user_name'] = $user->name;
                $_SESSION['user_email'] = $email;
                $_SESSION['role'] = $role;

                // Set session for authors
                if ($role === 'authors') {
                    $_SESSION['authorEmail'] = $email;
                    header("Location: http://localhost/written-journey/authors-articles.php");
                    exit;
                }

                if ($role === 'editors') {
                    $_SESSION['editorEmail'] = $email;
                    header("Location: http://localhost/written-journey/status-editor-articles.php");
                    exit;
                }

                if ($role === 'reviewers') {
                    $_SESSION['reviewerEmail'] = $email;
                    header("Location: http://localhost/written-journey/status-reviewer-articles.php");
                    exit;
                }

                // Redirect to the respective dashboard
                if ($role === 'users') {
                    header("Location: http://localhost/written-journey/customer-dashboard.php");
                }
                exit;
            }
        }

        echo "<script>alert('Email not found.'); window.history.back();</script>";
        exit;
    }
}

function recordLoginAttempt($conn, $email, $role)
{
    $sql = "SELECT * FROM $role WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        echo "<script>alert('SQL error: " . mysqli_error($conn) . "');</script>";
    }
    if (mysqli_num_rows($result) == 0) {
        $insertSql = "INSERT INTO $role (email, attempts) VALUES ('$email', 1)";
        mysqli_query($conn, $insertSql);
    } else {
        $updateSql = "UPDATE $role SET attempts = attempts + 1 WHERE email = '$email'";
        mysqli_query($conn, $updateSql);
    }
}

function getLoginAttempts($conn, $email, $role)
{
    $sql = "SELECT attempts FROM $role WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        echo "<script>alert('SQL error: " . mysqli_error($conn) . "');</script>";
    }

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['attempts'];
    } else {
        return 0;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <title>LOGIN</title>
</head>

<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-sm" style="width: 400px;">
            <img src="img/logo.png" class="d-block mx-auto mb-4" alt="Written Journey Logo" width="120" height="120">
            <h3 class="text-center mb-4">LOGIN</h3>
            <form method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                </div>
                <div class="mb-3 text-end">
                    <a href="forgotpassword.php" class="link-secondary">Forgot Password?</a>
                </div>
                <div class="d-grid">
                    <button type="submit" name="login" class="btn btn-success">Login</button>
                </div>
                <div class="text-center mt-3">
                    <p class="mb-0">New? <a href="register.php" class="link-success">SIGN UP</a></p>
                </div>
            </form>
        </div>
    </div>
</body>

</html>