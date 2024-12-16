<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';
include('customer-nav.php');


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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container my-5">
        <h1 class="text-center mb-4">Search Articles</h1>
        <form method="GET" action="" class="d-flex justify-content-center mb-3">
            <input type="text" name="search" value="<?= htmlspecialchars($search); ?>" placeholder="Search by title or author" class="form-control me-2">
            <button type="submit" class="btn btn-success">Search</button>
        </form>
        <hr>
        <?php if ($resultArticles->num_rows > 0): ?>
            <h2 class="text-center mt-4">Search Results</h2>
            <div class="row">
                <?php while ($article = $resultArticles->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h3 class="card-title"><?= htmlspecialchars($article['title']); ?></h3>
                                <p class="card-text"><strong>Author:</strong> <?= htmlspecialchars($article['author']); ?></p>
                                <p class="card-text"><strong>Abstract:</strong> <?= htmlspecialchars($article['abstract']); ?></p>
                                <p class="card-text"><strong>DOI:</strong> <?= htmlspecialchars($article['doi']); ?></p>
                                <p class="card-text"><strong>Reference:</strong> <?= htmlspecialchars($article['reference']); ?></p>
                                <p class="card-text"><strong>Citation:</strong> <?= htmlspecialchars($article['citation']); ?></p>
                                <p class="card-text"><strong>Download Count:</strong> <?= htmlspecialchars($article['download_count']); ?></p>
                                <a href="?download=<?= htmlspecialchars($article['id']); ?>" class="btn btn-success">Download PDF</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-muted">No articles found for the search term.</p>
        <?php endif; ?>
    </div>
</body>

</html>