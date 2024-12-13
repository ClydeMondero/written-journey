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
    <title>Document</title>
</head>
<body>
    <div class="content">
        <h1 class="submission-title">SUBMISSION GUIDELINES</h1>
        <h2 class="author-title">AUTHOR</h2>
        <p class="submission-content">
            Authors are encouraged to submit their work to this journal.
            To proceed with a submission, you must log in as an author. 
            If you don&apos;t have an account yet, please <a href="register.php">register</a> here. If you already have an account, you can <a href="register.php">login</a> here. 
            Note that only authors have access to the submission process.
            All submissions will be assessed by an editor to determine whether they meet the aims and scope of this journal.
            Those considered to be good fit will be sent for peer review before determining whether they will be accepted or rejected.
        </p>
        <h2 class="submission-checklist">SUBMISSION PREPARATION CHECKLIST</h2>
        <p class="label-checklist">Before submitting your paper, please ensure it adheres to the following guidelines:</p>
        <ul class="checklist">
            <li>Use the specific formatting.</li>
            <li>Strictly follows referencing and citation guidelines (APA 7th  Edition).</li>
            <li>Perform a plagiarism check before submission.</li>
            <li>Include all necessary metadata such as title, author name, and abstract.</li>
            <li>Submit your paper in a single PDF file.</li>
            <li>Follows research ethics and includes any necessary conflict of interest, acknowledgement, AI declarations, and funding declaration.</li>
        </ul>
        <div class="info-role">
            <h4 class="info-content">INFORMATION</h4>
            <a href="customer-for-readers.php">For Readers</a> <br>
            <a href="customer-for-authors.php">For Authors</a> <br>
            <a href="customer-for-reviewers.php">For Reviewers</a> <br>
            <a href="customer-for-editors.php">For Editors</a>
        </div>
    </div>
</body>
</html>