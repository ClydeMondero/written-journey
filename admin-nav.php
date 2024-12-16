<?php
error_reporting(E_ALL & ~E_NOTICE);
session_start();
require 'connection.php';

if (!isset($_SESSION['user_type'])) {
    header("Location: customer-dashboard.php");
} else {
    if ($_SESSION['user_type'] != 'admin') {
        header("Location: customer-dashboard.php");
    }
}


$currentPage = basename($_SERVER['PHP_SELF']);

if (isset($_GET["newUsername"])) {
    // Retrieve the updated username from the query parameter
    $newAdminName = $_GET["newUsername"];
} else {
    // Check if the username is stored in the session
    if (isset($_SESSION['adminUsername'])) {
        // Retrieve the username from the session
        $newAdminName = $_SESSION['adminUsername'];
    } else {
        // Fetch the actual admin username from the database
        $result = mysqli_query($conn, "SELECT username FROM admin WHERE email = 'admin@gmail.com'");

        if ($result && $row = mysqli_fetch_assoc($result)) {
            $newAdminName = $row['username'];
        } else {
            $newAdminName = "ADMIN";
        }
    }
}

// Fetch the profile picture path from the database based on the retrieved username
$result = mysqli_query($conn, "SELECT image FROM admin WHERE username = '$newAdminName'");
if ($result && $row = mysqli_fetch_assoc($result)) {
    $profile_picture = $row['image'];
} else {
    $profile_picture = "default_image.jpg";
}

// Check if the logout form is submitted
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: customer-dashboard.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Navigation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body style="height: 100dvh;">
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-light h-100">
        <a href="admin-account.php?newUsername=<?php echo urlencode($newAdminName); ?>" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none">
            <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="rounded-circle me-2" width="40" height="40">
            <span class="fs-4"><?php echo htmlspecialchars($newAdminName); ?></span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="admin-dashboard.php" class="nav-link <?php echo ($currentPage == 'admin-dashboard.php') ? 'bg-success text-white' : 'text-dark'; ?>" aria-current="page">
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="add-journal-issues.php" class="nav-link <?php echo ($currentPage == 'add-journal-issues.php') ? 'bg-success text-white' : 'text-dark'; ?>">
                    <span>Journal Issues</span>
                </a>
            </li>
            <li>
                <a href="admin-articles.php" class="nav-link <?php echo ($currentPage == 'admin-articles.php') ? 'bg-success text-white' : 'text-dark'; ?>">
                    <span>Articles</span>
                </a>
            </li>
            <li>
                <a href="add-account.php" class="nav-link <?php echo ($currentPage == 'add-account.php') ? 'bg-success text-white' : 'text-dark'; ?>">
                    <span>Accounts</span>
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