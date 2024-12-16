<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('editors-nav.php');
require 'connection.php';

// Handle status and comment update
if (isset($_POST['submit_review'])) {
    $articleId = intval($_POST['article_id']);
    $newStatus = mysqli_real_escape_string($conn, $_POST['new_status']);
    $editorComment = mysqli_real_escape_string($conn, $_POST['editor_comment']);

    $updateQuery = "UPDATE articles SET status = '$newStatus', editor_comment = '$editorComment' WHERE id = $articleId";
    if (mysqli_query($conn, $updateQuery)) {
        echo "<script>alert('Review submitted successfully'); window.location.href = 'status-editor-articles.php';</script>";
    } else {
        echo "Error updating article: " . mysqli_error($conn);
    }
}

// Fetch article details
$articleId = intval($_GET['id']);
$articleQuery = "SELECT * FROM articles WHERE id = $articleId";
$articleResult = mysqli_query($conn, $articleQuery);
$article = mysqli_fetch_assoc($articleResult);

function safeOutput($value)
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Article Review</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <a href="javascript:history.back()" class="btn btn-secondary mt-3">Back</a>
        <h1 class="text-center">ARTICLE REVIEW</h1>
        <div class="card">
            <h2 class="card-header"><?php echo safeOutput($article['title']); ?></h2>
            <div class="card-body">
                <p><strong>Author:</strong> <?php echo safeOutput($article['author']); ?></p>
                <p><strong>Email:</strong> <?php echo safeOutput($article['email']); ?></p>
                <p><strong>Abstract:</strong> <?php echo safeOutput($article['abstract']); ?></p>
                <p><strong>Reference:</strong> <?php echo safeOutput($article['reference']); ?></p>
                <p><strong>Citation:</strong> <?php echo safeOutput($article['citation']); ?></p>
                <p><strong>Comments:</strong> <?php echo safeOutput($article['comments']); ?></p>
                <p><strong>Issue:</strong> <?php echo safeOutput($article['issues']); ?></p>
                <p><strong>Status:</strong> <?php echo safeOutput($article['status']); ?></p>
                <p><strong>PDF:</strong> <a href="<?php echo htmlspecialchars($article['pdf']); ?>" class="btn btn-success">Download</a></p>
            </div>
        </div>

        <div class="card mt-3">
            <h3 class="card-header">Submit Review</h3>
            <div class="card-body">
                <form action="" method="post">
                    <input type="hidden" name="article_id" value="<?php echo safeOutput($article['id']); ?>">
                    <div class="mb-3">
                        <label for="new_status" class="form-label">Status:</label>
                        <select name="new_status" id="new_status" class="form-select">
                            <option value="Accepted by Editor" <?php echo $article['status'] == 'Accepted by Editor' ? 'selected' : ''; ?>>Accepted by Editor</option>
                            <option value="Rejected by Editor" <?php echo $article['status'] == 'Rejected by Editor' ? 'selected' : ''; ?>>Rejected by Editor</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editor_comment" class="form-label">Comment:</label>
                        <textarea name="editor_comment" id="editor_comment" rows="4" cols="50" class="form-control"><?php echo safeOutput($article['editor_comment']); ?></textarea>
                    </div>
                    <button type="submit" name="submit_review" class="btn btn-success">Submit Review</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>

<?php
$conn->close();
?>