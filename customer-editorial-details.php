<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';
include('customer-nav.php');

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editorial Board Details</title>
    <style>
        label{
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="content">
        <h1 class="editor-title">EDITORIAL BOARD DETAILS</h1>
        <div class="editor-details">
            <div class="editor-info">
                <label for="editor-name" class="editor-name-label">Editor Name</label> <!-- bold this on your css -->
                <p class="editor-name">Dr. Excelsa Tongson</p>
                <label for="editor-position" class="editor-postion-label">Position</label> <!-- bold this on your css -->
                <p class="editor-position">Editor in Chief</p>
                <label for="editor-contact" class="editor-contact-label">Contact Information</label> <!-- bold this on your css -->
                <p class="editor-contact">Email: chief@gmail.com</p>
                <p class="editor-contact">Contact No.: 09123456789</p>
            </div>
        </div>
        <div class="editor-board">
            <h3 class="board-title">EDITORIAL BOARD MEMBERS</h3>
            <div class="board-members">
            <?php
            $sqlGetBoardMembers = "SELECT CONCAT(first_name, ' ', middle_name, ' ', last_name) AS full_name, email, contact_number FROM editors"; // Adjust table and column names as necessary
            $resultBoardMembers = $conn->query($sqlGetBoardMembers);

            if ($resultBoardMembers->num_rows > 0) {
                while ($member = $resultBoardMembers->fetch_assoc()) {
                    echo '
                    <div class="board-member">
                        <label for="member-name" class="member-name-label">Name</label>
                        <p class="member-name">' . htmlspecialchars($member['full_name']) . '</p>
                        <label for="member-contact" class="member-contact-label">Contact Information</label>
                        <p class="member-contact">Email: ' . htmlspecialchars($member['email']) . '</p>
                        <p class="member-contact">Contact No.: ' . htmlspecialchars($member['contact_number']) . '</p>
                    </div>
                    <hr>';
                }
            } else {
                echo '<p>No editorial board members found.</p>';
            }
            ?>
            </div>
        </div>
    </div>
</body>
</html>