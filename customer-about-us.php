<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';
include('customer-nav.php');

// Check if the user is logged in
if (!isset($_SESSION['user_name'])) {
    // Redirect to the login page or handle accordingly
    header("Location: http://localhost/written-journey/login.php");
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

    if (empty($profilePic)) {
        // Default profile picture if none is found
        $profilePic = 'default-profile.png';
    }
} else {
    // Default profile picture if none is found
    $profilePic = 'default-profile.png';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="what-is">
                    <h1 class="what-is-title display-4">WHAT IS WRITTEN JOURNEY</h1>
                    <p class="what-is-content lead">Lorem ipsum dolor sit amet consectetur adipisicing elit. Perspiciatis nemo, unde officiis cum laboriosam quis eveniet repudiandae dolore aliquam dicta delectus ipsam iure aliquid mollitia dignissimos sequi ipsa omnis fuga.</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="mission">
                    <h1 class="mission-title display-4">MISSION</h1>
                    <p class="mission-content lead">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Suscipit ea error placeat, nesciunt eum, harum voluptatem quisquam architecto doloremque, voluptas illum aspernatur cupiditate expedita nihil facere impedit adipisci in maxime!</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="vission">
                    <h1 class="vission-title display-4">VISION</h1>
                    <p class="vission-content lead">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Suscipit ea error placeat, nesciunt eum, harum voluptatem quisquam architecto doloremque, voluptas illum aspernatur cupiditate expedita nihil facere impedit adipisci in maxime!</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="scope">
                    <h1 class="scope-title display-4">WRITTEN JOURNEY SCOPE</h1>
                    <p class="scope-content lead">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Suscipit ea error placeat, nesciunt eum, harum voluptatem quisquam architecto doloremque, voluptas illum aspernatur cupiditate expedita nihil facere impedit adipisci in maxime!</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>