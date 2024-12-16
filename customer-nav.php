<?php
session_start();
error_reporting(E_ALL & ~E_WARNING);
ini_set('display_errors', 1);
require 'connection.php';

// Check if the user is logged in
if (isset($_SESSION['user_type'])) {
    // Redirect to the  handle accordingly
    if ($_SESSION['user_type'] == 'admin') {
        // Redirect to admin dashboard
        header("Location: http://localhost/written-journey/admin-dashboard.php");
    } else if ($_SESSION['user_type'] == 'authors') {
        header("Location: http://localhost/written-journey/authors-articles.php");
    } else if ($_SESSION['user_type'] == 'editors') {
        header("Location: http://localhost/written-journey/status-editor-articles.php");
    } elseif ($_SESSION['user_type'] == 'reviewers') {
        header("Location: http://localhost/written-journey/status-reviewer-articles.php");
    }
    exit;
}

$profilePic = 'default-profile.png';
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
    <header class="bg-light py-3 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <nav class="navbar">
                <a class="navbar-brand d-flex align-items-center" href="customer-dashboard.php">
                    <img src="img/logo.png" width="75" height="75" class="me-2" alt="">
                    Written Journey
                </a>
            </nav>
            <nav>
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link active text-success" href="customer-dashboard.php">HOME</a></li>
                    <li class="nav-item"><a class="nav-link text-success" href="customer-archives.php">ARCHIVE</a></li>
                    <li class="nav-item"><a class="nav-link text-success" href="customer-articles.php">ARTICLES</a></li>
                    <li class="nav-item"><a class="nav-link text-success" href="customer-editorial-details.php">EDITORIAL</a></li>
                    <li class="nav-item"><a class="nav-link text-success" href="customer-submission-guidelines.php">SUBMISSIONS</a></li>
                    <li class="nav-item"><a class="nav-link text-success" href="customer-about-us.php">ABOUT US</a></li>
                </ul>
            </nav>
            <div class="dropdown">
                <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="img/<?php echo basename($profilePic); ?>" alt="Profile Picture" class="rounded-circle" width="40" height="40">
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                    <li><a class="dropdown-item" href="login.php">Sign In</a></li>
                </ul>
            </div>
        </div>
    </header>
    <!-- Bootstrap Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-PLQPUUQ2MySFL4tFCghlAf+J5EtWULf0NWaHWT8llPm8M3T/O9lR7qEPXunUu12f" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ppkY3IhaG1EzOzTUSY/ZIN/JX7kBB5ViR2eRhsFjVOJd8Cn8S5bkI+WdFDR3D82x" crossorigin="anonymous"></script>

</body>

</html>