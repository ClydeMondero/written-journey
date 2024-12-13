<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_name'])) {
    // Redirect to the login page or handle accordingly
    header("Location: http://localhost/journal/login.php");
    exit;
}

$userName = $_SESSION['user_name'];

// If you want to log out, you can add a condition to check for a logout action
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    // Clear all session variables
    session_unset();
    // Destroy the session
    session_destroy();
    // Redirect to the login page or handle accordingly
    header("Location: http://localhost/journal/login.php");
    exit;
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user profile picture
$sqlGetUser = "SELECT image_path FROM users WHERE name = ?";
$stmt = $conn->prepare($sqlGetUser);
$stmt->bind_param("s", $userName);
$stmt->execute();
$resultUser = $stmt->get_result();

if ($resultUser->num_rows > 0) {
    $user = $resultUser->fetch_assoc();
    $profilePic = $user['image_path'];
} else {
    // Default profile picture if none is found
    $profilePic = 'default-profile.png';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reader Navigation</title>
</head>
<body>
<div class="header">
    <div class="info-container">
        <nav class="nav-left">
            <p class="shopName">AndromedaArchive</p>
        </nav>
        <nav class="nav-middle">
            <div class="middle-btn">
                <a href="customer-dashboard.php">HOME</a>
                <a href="customer-archives.php">ARCHIVE</a>
                <a href="customer-articles.php">ARTICLES</a>
                <a href="customer-editorial-details.php">EDITORIAL</a>
                <a href="customer-submission-guidelines.php">SUBMISSIONS</a>
                <a href="customer-about-us.php">ABOUT US</a>
            </div>
        </nav>
        <nav class="nav-right">
            <div class="dropdown">
                <button class="dropbtn">
                    <img src="img/<?php echo basename($profilePic); ?>" alt="Profile Picture" class="profile-pic">
                </button>
                <div class="dropdown-content">
                    <a href="user-profile-settings.php">Profile Settings</a>
                    <a href="users-change-password.php">Password</a>
                    <a href="?logout=1">Logout</a>
                </div>
            </div>
        </nav>
    </div>
</div>
</body>
</html>