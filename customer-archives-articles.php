<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';
include('customer-nav.php');

// Check if the user is logged in
if (!isset($_SESSION['user_name'])) {
    header("Location: http://localhost/journal/login.php");
    exit;
}

$userName = $_SESSION['user_name'];

// Handle logout
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    session_unset();
    session_destroy();
    header("Location: http://localhost/journal/login.php");
    exit;
}

// Check database connection
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
    $profilePic = 'default-profile.png';
}

// Query to fetch all accepted articles
$sqlGetArticles = "SELECT title, author, email, abstract, doi, reference, citation, pdf 
                   FROM articles 
                   WHERE status = 'accepted'";
$resultArticles = $conn->query($sqlGetArticles);

if (!$resultArticles) {
    die("Error fetching articles: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Articles</title>
</head>
<body>
    <div class="content">
        <?php if ($resultArticles->num_rows > 0): ?>
            <h2>All Accepted Articles</h2>
            <?php while ($article = $resultArticles->fetch_assoc()): ?>
                <div class="article-card">
                    <h3><?= htmlspecialchars($article['title']); ?></h3>
                    <p><strong>Author:</strong> <?= htmlspecialchars($article['author']); ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($article['email']); ?></p>
                    <p><strong>Abstract:</strong> <?= htmlspecialchars($article['abstract']); ?></p>
                    <p><strong>DOI:</strong> <?= htmlspecialchars($article['doi']); ?></p>
                    <p><strong>Reference:</strong> <?= htmlspecialchars($article['reference']); ?></p>
                    <p><strong>Citation:</strong> <?= htmlspecialchars($article['citation']); ?></p>
                    <a href="uploads/<?= htmlspecialchars(basename($article['pdf'])); ?>" target="_blank">Download PDF</a>
                </div>
                <hr>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No articles found.</p>
        <?php endif; ?>
    </div>
</body>
</html>