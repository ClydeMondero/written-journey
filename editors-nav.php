<?php
require 'connection.php';
session_start();

if (isset($_SESSION['editorEmail'])) {
    $editorEmail = $_SESSION['editorEmail'];
} else {
    echo "<script>alert('No valid email found in session');</script>";
    exit;
}

// Fetch the editor's name and profile picture from the database based on the email
$query = "SELECT name, image_path FROM editors WHERE email = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $editorEmail);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// If the editor is found, fetch the name and profile picture, otherwise, set defaults
if ($result && $row = mysqli_fetch_assoc($result)) {
    $editorName = $row['name'];
    $profile_picture = $row['image_path'];
} else {
    $editorName = "EDITOR";
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
    <title>Editors Navigation</title>
</head>
<body>
    <div class="sidebar">
        <div class="content">
            <a href="editor-profile-settings.php?newUsername=<?php echo urlencode($editorName); ?>">
                <button class="editors-btn">
                    <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
                    <label> <?php echo htmlspecialchars($editorName); ?> </label>
                </button>
            </a>
            <div class="btn">
                <a href="status-editor-articles.php"><button class="dashboard"> <span> ARTICLES </span> </button></a>
                <form method="POST" action="">
                    <button class="logout" type="submit" name="logout">LOGOUT</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
