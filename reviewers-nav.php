<?php
require 'connection.php';
session_start();

if (!isset($_SESSION['user_type'])) {
    header("Location: customer-dashboard.php");
} else {
    if ($_SESSION['user_type'] != 'reviewers') {
        header("Location: customer-dashboard.php");
    }
}

// Fetch the reviewer's name and profile picture from the database based on the email
$query = "SELECT name, image_path FROM reviewers WHERE email = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $editorEmail);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// If the reviewer is found, fetch the name and profile picture, otherwise, set defaults
if ($result && $row = mysqli_fetch_assoc($result)) {
    $editorName = $row['name'];
    $profile_picture = $row['image_path'];

    if (empty($profile_picture)) {
        $profile_picture = "img/default-profile.png";
    }
} else {
    $editorName = "REVIEWER";
    $profile_picture = "img/default-profile.png";
}

// Perform logout logic
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: customer-dashboard.php");
    exit();
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviewers Navigation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="reviewer-profile-settings.php?newUsername=<?php echo urlencode($editorName); ?>">
                <img src="<?php echo htmlspecialchars($profile_picture); ?>" width="50" height="50" class="rounded-circle" style="border-radius: 50%;" alt="Profile Picture">
                <span class="ms-2"><?php echo htmlspecialchars($editorName); ?></span>
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <form method="POST" action="" class="d-inline">
                            <button class="btn btn-success" type="submit" name="logout">LOGOUT</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</body>

</html>