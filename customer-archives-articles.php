<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';
include('customer-nav.php');


// Fetch user profile picture
$sqlGetUser = "SELECT image_path FROM users WHERE name = ?";
$stmt = $conn->prepare($sqlGetUser);
$stmt->bind_param("s", $userName);
$stmt->execute();
$resultUser = $stmt->get_result();

if ($resultUser->num_rows > 0) {
    $user = $resultUser->fetch_assoc();
    $profilePic = $user['image_path'];

    if (empty($profilePic)) {
        $profilePic = 'default-profile.png';
    }
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</head>

<body>
    <div class="container">
        <?php if ($resultArticles->num_rows > 0): ?>
            <h2 class="text-center">All Accepted Articles</h2>
            <div class="row">
                <?php while ($article = $resultArticles->fetch_assoc()): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <h3 class="card-title"><?= htmlspecialchars($article['title']); ?></h3>
                            <div class="card-body">
                                <p><strong>Author:</strong> <?= htmlspecialchars($article['author']); ?></p>
                                <p><strong>Email:</strong> <?= htmlspecialchars($article['email']); ?></p>
                                <p><strong>Abstract:</strong> <?= htmlspecialchars($article['abstract']); ?></p>
                                <p><strong>DOI:</strong> <?= htmlspecialchars($article['doi']); ?></p>
                                <p><strong>Reference:</strong> <?= htmlspecialchars($article['reference']); ?></p>
                                <p><strong>Citation:</strong> <?= htmlspecialchars($article['citation']); ?></p>
                                <a href="uploads/<?= htmlspecialchars(basename($article['pdf'])); ?>" target="_blank" class="btn btn-success">Download PDF</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-center">No articles found.</p>
        <?php endif; ?>
    </div>
</body>

</html>