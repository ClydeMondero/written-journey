<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('authors-nav.php');
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

// Fetch all articles from the database
$articlesQuery = "SELECT doi, title, author, email, abstract, issues, pdf, reference, citation, status FROM articles";
$articlesResult = mysqli_query($conn, $articlesQuery);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authors Dashboard</title>
</head>
<body>
    <div class="content">        
        <h2>All Articles</h2>
        <?php if ($articlesResult && mysqli_num_rows($articlesResult) > 0): ?>
            <?php while ($article = mysqli_fetch_assoc($articlesResult)): ?>
                <div class="article">
                    <h3><?php echo htmlspecialchars($article['title']); ?></h3>
                    <p><strong>DOI:</strong> <?php echo htmlspecialchars($article['doi']); ?></p>
                    <p><strong>Author:</strong> <?php echo htmlspecialchars($article['author']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($article['email']); ?></p>
                    <p><strong>Abstract:</strong> <?php echo htmlspecialchars($article['abstract']); ?></p>
                    <p><strong>Issues:</strong> <?php echo htmlspecialchars($article['issues']); ?></p>
                    <p><strong>PDF:</strong> <a href="<?php echo htmlspecialchars($article['pdf']); ?>">Download</a></p>
                    <p><strong>Reference:</strong> <?php echo htmlspecialchars($article['reference']); ?></p>
                    <p><strong>Citation:</strong> <?php echo htmlspecialchars($article['citation']); ?></p>
                    <p><strong>Status: </strong><?php echo htmlspecialchars($article['status']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No articles found.</p>
        <?php endif; ?>
    </div>
</body>
</html>