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

// Initialize the query for fetching articles
$search = '';
$sqlGetArticles = "SELECT id, title, author, email, abstract, doi, reference, citation, pdf, download_count 
                   FROM articles 
                   WHERE status = 'accepted'";

// Add search filter if provided
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = trim($_GET['search']);
    $sqlGetArticles .= " AND (title LIKE ? OR author LIKE ?)";
}

// Prepare and execute the query
$stmtArticles = $conn->prepare($sqlGetArticles);

if (!empty($search)) {
    $searchTerm = "%$search%";
    $stmtArticles->bind_param("ss", $searchTerm, $searchTerm);
}

$stmtArticles->execute();
$resultArticles = $stmtArticles->get_result();

// Handle file download
if (isset($_GET['download']) && is_numeric($_GET['download'])) {
    $articleId = $_GET['download'];

    // Increment the download count
    $sqlUpdateDownload = "UPDATE articles SET download_count = download_count + 1 WHERE id = ?";
    $stmtUpdate = $conn->prepare($sqlUpdateDownload);
    $stmtUpdate->bind_param("i", $articleId);
    $stmtUpdate->execute();

    // Fetch the file path
    $sqlGetFile = "SELECT pdf FROM articles WHERE id = ?";
    $stmtGetFile = $conn->prepare($sqlGetFile);
    $stmtGetFile->bind_param("i", $articleId);
    $stmtGetFile->execute();
    $resultFile = $stmtGetFile->get_result();

    if ($resultFile->num_rows > 0) {
        $article = $resultFile->fetch_assoc();
        $filePath = "uploads/" . basename($article['pdf']);

        if (file_exists($filePath)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
            readfile($filePath);
            exit;
        } else {
            echo "File not found.";
        }
    } else {
        echo "Invalid article ID.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Articles</title>
</head>
<body>
    <div class="content">
        <h1>Search Articles</h1>
        <form method="GET" action="">
            <input type="text" name="search" value="<?= htmlspecialchars($search); ?>" placeholder="Search by title or author">
            <button type="submit">Search</button>
        </form>
        <hr>
        <?php if ($resultArticles->num_rows > 0): ?>
            <h2>Search Results</h2>
            <?php while ($article = $resultArticles->fetch_assoc()): ?>
                <div class="article-card">
                    <h3><?= htmlspecialchars($article['title']); ?></h3>
                    <p><strong>Author:</strong> <?= htmlspecialchars($article['author']); ?></p>
                    <p><strong>Abstract:</strong> <?= htmlspecialchars($article['abstract']); ?></p>
                    <p><strong>DOI:</strong> <?= htmlspecialchars($article['doi']); ?></p>
                    <p><strong>Reference:</strong> <?= htmlspecialchars($article['reference']); ?></p>
                    <p><strong>Citation:</strong> <?= htmlspecialchars($article['citation']); ?></p>
                    <p><strong>Download Count:</strong> <?= htmlspecialchars($article['download_count']); ?></p>
                    <a href="?download=<?= htmlspecialchars($article['id']); ?>">Download PDF</a>
                </div>
                <hr>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No articles found for the search term.</p>
        <?php endif; ?>
    </div>
</body>
</html>