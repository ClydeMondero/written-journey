<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';
include('customer-nav.php');

// Check if the user is logged in
if (!isset($_SESSION['user_name'])) {
    header("Location: http://localhost/written-journey/login.php");
    exit;
}

$userName = $_SESSION['user_name'];

// Handle logout
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    session_unset();
    session_destroy();
    header("Location: http://localhost/written-journey/login.php");
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <title>Reader Dashboard</title>
</head>

<body>
    <div class="container mt-4">
        <!-- Latest Issues Section -->
        <div class="latest-issues mb-4">
            <h2 class="mb-3">Latest Issues</h2>
            <?php if ($resultLatestIssues->num_rows > 0): ?>
                <div class="row">
                    <?php while ($issue = $resultLatestIssues->fetch_assoc()): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <img class="card-img-top" src="img/<?= htmlspecialchars($issue['image'] ?? 'default-image.png'); ?>" alt="Issue Image" style="height: 150px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($issue['title']); ?></h5>
                                    <p class="card-text">Vol. <?= htmlspecialchars($issue['vol_no']); ?>, <?= htmlspecialchars($issue['publication_date']); ?></p>
                                    <a href="customer-archives-articles.php?issues=<?= htmlspecialchars($issue['id']); ?>" class="btn btn-success">View Articles</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="text-muted">No recent issues available.</p>
            <?php endif; ?>
        </div>

        <hr>

        <!-- Most Downloaded Articles Section -->
        <div class="most-downloaded mb-4">
            <h2 class="mb-3">Most Downloaded Articles</h2>
            <?php if ($resultMostDownloaded->num_rows > 0): ?>
                <div class="list-group">
                    <?php while ($article = $resultMostDownloaded->fetch_assoc()): ?>
                        <div class="list-group-item">
                            <h5 class="mb-1"><?= htmlspecialchars($article['title']); ?></h5>
                            <p class="mb-1">by <?= htmlspecialchars($article['author']); ?></p>
                            <p><strong>Abstract:</strong> <?= htmlspecialchars($article['abstract']); ?></p>
                            <p><strong>DOI:</strong> <?= htmlspecialchars($article['doi']); ?></p>
                            <p><strong>Reference:</strong> <?= htmlspecialchars($article['reference']); ?></p>
                            <p><strong>Citation:</strong> <?= htmlspecialchars($article['citation']); ?></p>
                            <small class="text-muted">Downloads: <?= htmlspecialchars($article['download_count']); ?></small>
                            <a href="customer-articles.php?download=<?= htmlspecialchars($article['id']); ?>" class="btn btn-success btn-sm">Download PDF</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="text-muted">No articles with sufficient downloads.</p>
            <?php endif; ?>
        </div>

        <hr>

        <!-- Journal Introduction -->
        <div class="journal-intro">
            <h1 class="display-4">WRITTEN JOURNEY</h1>
            <p class="lead">
                <strong>Written Journey</strong> is a scholarly journal by Bulacan State University students, focusing on contemporary issues.
                It is published annually and covers a broad spectrum of emerging topics.
                The journal features content appealing to diverse audiences, including research papers, reviews, policy discussions, creative pieces, and translations.
            </p>
            <p class="lead">
                This publication serves as a unique forum for sharing insights on subjects such as pedagogy, curriculum development, technology integration, and educational leadership.
                It addresses teacher education across different stages, including early childhood, basic, tertiary, technical, and vocational education, as well as community and lifelong learning.
            </p>
            <p class="lead">
                <strong>Written Journey</strong> invites submissions of original research, book reviews, and scholarly articles.
                Contributions undergo review by field experts and are published based on quality standards.
            </p>
            <p class="lead">
                The journal aims to foster innovation and collaboration among researchers, educators, and students, promoting a deeper understanding of the world.
            </p>
        </div>
    </div>
</body>

</html>