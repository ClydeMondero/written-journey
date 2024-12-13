<?php
require 'connection.php';

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
    header("Location: login.php");
    exit();
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Navigation</title>
</head>

<body>
    <div class="sidebar">
        <div class="content">
            <a href="admin-account.php?newUsername=<?php echo urlencode($newAdminName); ?>">
                <button class="admin-btn">
                    <img src="<?php echo $profile_picture; ?>" alt="">
                    <label> <?php echo $newAdminName; ?> </label>
                </button>
            </a>
            <div class="btn">
                <a href="admin-dashboard.php"><button class="dashboard"> <span> DASHBOARD </span> </button></a>
                <a href="add-journal-issues.php"><button class="dashboard"> <span> JOURNAL ISSUES </span> </button></a>
                <a href="admin-articles.php"><button class="dashboard"> <span> ARTICLES </span> </button></a>
                <a href="add-account.php"><button class="dashboard"> <span> ACCOUNTS </span> </button></a>
                <a href="login.php"><button class="logout"> <span> LOG OUT</span></button></a>
            </div>
        </div>
    </div>
</body>
</html>