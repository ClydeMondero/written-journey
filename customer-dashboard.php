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

// Fetch the latest issues (limit to 5 for brevity)
$sqlLatestIssues = "SELECT id, title, vol_no, publication_date, image FROM issues ORDER BY publication_date DESC LIMIT 3";
$resultLatestIssues = $conn->query($sqlLatestIssues);

// Fetch the most downloaded articles (limit to 5 for brevity)
$sqlMostDownloaded = "SELECT id, title, author, abstract, citation, reference, doi,download_count FROM articles WHERE download_count >= 3 ORDER BY download_count DESC LIMIT 5";
$resultMostDownloaded = $conn->query($sqlMostDownloaded);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reader Dashboard</title>
</head>
<body>
    <div class="content">
        <!-- Latest Issues Section -->
        <div class="latest-issues">
            <h2>Latest Issues</h2>
            <?php if ($resultLatestIssues->num_rows > 0): ?>
                <?php while ($issue = $resultLatestIssues->fetch_assoc()): ?>
                    <div class="issue">
                        <img src="img/<?= htmlspecialchars($issue['image'] ?? 'default-image.png'); ?>" alt="Issue Image" style="width: 50px; height: 50px;">
                        <strong><?= htmlspecialchars($issue['title']); ?></strong> (Vol. <?= htmlspecialchars($issue['vol_no']); ?>, <?= htmlspecialchars($issue['publication_date']); ?>)
                        <a href="customer-archives-articles.php?issues=<?= htmlspecialchars($issue['id']); ?>">View Articles</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No recent issues available.</p>
            <?php endif; ?>
        </div>

        <hr>

        <!-- Most Downloaded Articles Section -->
        <div class="most-downloaded">
            <h2>Most Downloaded Articles</h2>
            <?php if ($resultMostDownloaded->num_rows > 0): ?>
                <?php while ($article = $resultMostDownloaded->fetch_assoc()): ?>
                    <div class="article">
                        <strong><?= htmlspecialchars($article['title']); ?></strong> by <?= htmlspecialchars($article['author']); ?>
                        <p><strong>Abstract:</strong> <?= htmlspecialchars($article['abstract']); ?></p>
                        <p><strong>DOI:</strong> <?= htmlspecialchars($article['doi']); ?></p>
                        <p><strong>Reference:</strong> <?= htmlspecialchars($article['reference']); ?></p>
                        <p><strong>Citation:</strong> <?= htmlspecialchars($article['citation']); ?></p>
                        Downloads: <?= htmlspecialchars($article['download_count']); ?>
                        <a href="customer-articles.php?download=<?= htmlspecialchars($article['id']); ?>">Download PDF</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No articles with sufficient downloads.</p>
            <?php endif; ?>
        </div>

        <hr>

        <!-- Journal Introduction -->
        <div class="journal-introduction">
            <h1 class="journal-title">ANDROMEDA ARCHIVE</h1>
            <p class="journal-description"> 
                <b>AndromedaArchive</b> is a peer-reviewed journal created by the students of Bulacan State University that publishes articles with its emergent issues. 
                The journal is an annual publication that covers an extensive array of different emerging issues. 
                It publishes diverse content that will be of interest to a wide range of readership.
                Sample of this is research articles, article and book reviews, policy beliefs, creative works, and translations.
            </p>
            <p class="journal-description">
                <b>AndromedaArchive</b> provides a unique platform for knowledge sharing regarding, among other topics, pedagogy, curriculum, technology integration, teaching and learning, 
                assessment and evaluation, education leadership, teacher education across the lifespan (early childhood care and development, basic education, tertiary, technical and vocational education, family and community, 
                non-formal, informal and lifelong learning).
            </p>
            <p class="journal-description">
                <b>AndromedaArchive</b> encourages the submission of original research articles, book reviews, and other forms of scholarly work.
                Submissions are reviewed by a panel of experts in the field and are published in AndromedaArchive if they meet the quality standards.
            </p>
            <p class="journal-description">
                <b>AndromedaArchive</b> aims to create a platform that encourages innovation and fosters collaboration among researchers, educators, and students, and to promote a deeper understanding of the world around us.
            </p>
        </div>
    </div>
</body>
</html>