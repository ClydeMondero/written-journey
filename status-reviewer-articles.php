<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('reviewers-nav.php');
require 'connection.php';

// Handle reviewer decision via buttons
if (isset($_GET['action']) && isset($_GET['article_id'])) {
    $articleId = intval($_GET['article_id']);
    $action = $_GET['action'];
    $newStatus = $action === 'accept' ? 'Accepted' : 'Rejected';

    $updateQuery = "UPDATE articles SET status = '$newStatus' WHERE id = $articleId";
    if (mysqli_query($conn, $updateQuery)) {
        echo "<script>alert('Article status updated to $newStatus'); window.location.href = 'status-reviewer-articles.php';</script>";
    } else {
        echo "Error updating article: " . mysqli_error($conn);
    }
}

// Fetch articles accepted by the editor
$articlesQuery = "SELECT * FROM articles WHERE status = 'Accepted by Editor'";
$articlesResult = mysqli_query($conn, $articlesQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviewer Article Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">ARTICLES FOR REVIEW</h1>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Email</th>
                        <th>Abstract</th>
                        <th>DOI</th>
                        <th>Reference</th>
                        <th>Citation</th>
                        <th>Comments</th>
                        <th>Issue</th>
                        <th>PDF</th>
                        <th>Reviewer Decision</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($articlesResult)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['author']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['abstract']); ?></td>
                            <td><?php echo htmlspecialchars($row['doi']); ?></td>
                            <td><?php echo htmlspecialchars($row['reference']); ?></td>
                            <td><?php echo htmlspecialchars($row['citation']); ?></td>
                            <td><?php echo htmlspecialchars($row['comments']); ?></td>
                            <td><?php echo htmlspecialchars($row['issues']); ?></td>
                            <td><a href="<?php echo $row['pdf']; ?>" target="_blank" class="btn btn-outline-primary btn-sm">PDF</a></td>
                            <td>
                                <a href="?action=accept&article_id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">Accept</a>
                                <a href="?action=reject&article_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Reject</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>

<?php
$conn->close();
?>