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
    header("Location: http://localhost/journal/customer-dashboard.php?user=" . $_SESSION['user_name']);
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
        header("Location: http://localhost/journal/admin-dashboard.php");
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
                    header("Location: http://localhost/journal/authors-articles.php");
                    exit;
                }

                if ($role === 'editors') {
                    $_SESSION['editorEmail'] = $email;
                    header("Location: http://localhost/journal/status-editor-articles.php");
                    exit;
                }

                if ($role === 'reviewers') {
                    $_SESSION['reviewerEmail'] = $email;
                    header("Location: http://localhost/journal/status-reviewer-articles.php");
                    exit;
                }
                
                // Redirect to the respective dashboard
                if ($role === 'users') {
                    header("Location: http://localhost/journal/customer-dashboard.php");
                }
                exit;
            }
        }

        echo "<script>alert('Email not found.'); window.history.back();</script>";
        exit;
    }
}

function recordLoginAttempt($conn, $email, $role) {
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

function getLoginAttempts($conn, $email, $role) {
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
    <title>LOGIN</title>
</head>
<body>
    <div class="gray">
        <h3>LOGIN</h3>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required /><br>
            <input type="password" name="password" placeholder="Password" required /><br>
            <p class="forget"><a href="forgotpassword.php">Forgot Password?</a></p>
            <input type="submit" name="login" value="Login">
            <p class="signup">New? <a href="register.php"> SIGN UP </a></p>
        </form>
    </div>
</body>
</html>