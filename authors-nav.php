<?php
session_start();
require 'connection.php';

if (isset($_SESSION['authorEmail'])) {
    $authorEmail = $_SESSION['authorEmail'];
} else {
    echo "<script>alert('No valid email found in session');</script>";
    exit;
}

// Fetch the author's name and profile picture from the database based on the email
$query = "SELECT name, image_path FROM authors WHERE email = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $authorEmail);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// If the author is found, fetch the name and profile picture, otherwise, set defaults
if ($result && $row = mysqli_fetch_assoc($result)) {
    $authorName = $row['name'];
    $profile_picture = $row['image_path']; 
} else {
    $authorName = "AUTHOR"; 
    $profile_picture = "default_image.jpg"; 
}

// Perform logout logic
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authors Navigation</title>
</head>
<body>
    <div class="sidebar">
        <div class="content">
            <a href="author-profile-settings.php?newUsername=<?php echo urlencode($authorName); ?>">
                <button class="authors-btn">
                    <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
                    <label> <?php echo htmlspecialchars($authorName); ?> </label>
                </button>
            </a>
            <div class="btn">
                <a href="authors-articles.php"><button class="authors-articles"> <span> DASHBOARD </span> </button></a>
                <a href="add-articles.php"><button class="submit-articles"> <span> SUBMIT ARTICLES </span> </button></a>
                <form method="POST">
                    <button class="logout-btn" name="logout">Logout</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>