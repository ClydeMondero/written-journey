<?php
error_reporting(E_ALL & ~E_NOTICE);
session_start();
require 'connection.php';

$currentPage = basename($_SERVER['PHP_SELF']);

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

    if (empty($profile_picture)) {
        $profile_picture = "img/default-profile.png";
    }
} else {
    $authorName = "AUTHOR";
    $profile_picture = "img/default-profile.png";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</head>

<body style="min-height: 100vh;">
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-light h-100">
        <a href="author-profile-settings.php?newUsername=<?php echo urlencode($authorName); ?>" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none">
            <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="rounded-circle me-2" width="40" height="40">
            <span class="fs-4"><?php echo htmlspecialchars($authorName); ?></span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="authors-articles.php" class="nav-link <?php echo ($currentPage == 'authors-articles.php') ? 'bg-success text-white' : 'text-dark'; ?>" aria-current="page">
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="add-articles.php" class="nav-link <?php echo ($currentPage == 'add-articles.php') ? 'bg-success text-white' : 'text-dark'; ?>">
                    <span>Add Articles</span>
                </a>
            </li>
        </ul>
        <hr>
        <form method="POST">
            <button class="btn btn-success w-100" name="logout">Logout</button>
        </form>
    </div>
</body>

</html>