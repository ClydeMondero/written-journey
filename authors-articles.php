<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start(); // Ensure session is started
require 'connection.php';

// Fetch the author's name and profile picture
$query = "SELECT name, image_path FROM authors WHERE email = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $authorEmail);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && $row = mysqli_fetch_assoc($result)) {
    $authorName = $row['name'];
    $profile_picture = $row['image_path'] ?: "img/default-profile.png";
} else {
    $authorName = "AUTHOR";
    $profile_picture = "img/default-profile.png";
}

// Fetch all articles
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <style>
        .articles-container {
            max-height: calc(100vh - 100px);
            /* Adjust this value if you have headers/footers */
            overflow-y: auto;
            overflow-x: hidden;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Navigation -->
            <div class="col-md-3 bg-light vh-100 p-0">
                <?php include('authors-nav.php'); ?>
            </div>

            <!-- Articles Section -->
            <div class="col-md-9 p-4">
                <h2>Articles Dashboard</h2>
                <div class="articles-container">
                    <div class="row">
                        <?php if ($articlesResult && mysqli_num_rows($articlesResult) > 0): ?>
                            <?php while ($article = mysqli_fetch_assoc($articlesResult)): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card shadow-sm">
                                        <div class="card-body">
                                            <h3 class="card-title"><?php echo htmlspecialchars($article['title']); ?></h3>
                                            <p><strong>DOI:</strong> <?php echo htmlspecialchars($article['doi']); ?></p>
                                            <p><strong>Author:</strong> <?php echo htmlspecialchars($article['author']); ?></p>
                                            <p><strong>Email:</strong> <?php echo htmlspecialchars($article['email']); ?></p>
                                            <p><strong>Abstract:</strong> <?php echo htmlspecialchars($article['abstract']); ?></p>
                                            <p><strong>Issues:</strong> <?php echo htmlspecialchars($article['issues']); ?></p>
                                            <p><strong>PDF:</strong> <a href="<?php echo htmlspecialchars($article['pdf']); ?>" class="btn btn-success btn-sm" target="_blank">Download</a></p>
                                            <p><strong>Reference:</strong> <?php echo htmlspecialchars($article['reference']); ?></p>
                                            <p><strong>Citation:</strong> <?php echo htmlspecialchars($article['citation']); ?></p>
                                            <p><strong>Status:</strong> <?php echo htmlspecialchars($article['status']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-muted">No articles found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>