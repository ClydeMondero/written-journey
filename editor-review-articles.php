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

function safeOutput($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Article Review</title>
</head>
<body>
    <h1>ARTICLE REVIEW</h1>
    <div>
        <h2><?php echo safeOutput($article['title']); ?></h2>
        <p><strong>Author:</strong> <?php echo safeOutput($article['author']); ?></p>
        <p><strong>Email:</strong> <?php echo safeOutput($article['email']); ?></p>
        <p><strong>Abstract:</strong> <?php echo safeOutput($article['abstract']); ?></p>
        <p><strong>Reference:</strong> <?php echo safeOutput($article['reference']); ?></p>
        <p><strong>Citation:</strong> <?php echo safeOutput($article['citation']); ?></p>
        <p><strong>Comments:</strong> <?php echo safeOutput($article['comments']); ?></p>
        <p><strong>Issue:</strong> <?php echo safeOutput($article['issues']); ?></p>
        <p><strong>Status:</strong> <?php echo safeOutput($article['status']); ?></p>
        <p><strong>PDF:</strong> <a href="<?php echo htmlspecialchars($article['pdf']); ?>">Download</a></p>
    </div>

    <div>
        <h3>Submit Review</h3>
        <form action="" method="post">
            <input type="hidden" name="article_id" value="<?php echo safeOutput($article['id']); ?>">
            <label for="new_status">Status:</label>
            <select name="new_status" id="new_status">
                <option value="Accepted by Editor" <?php echo $article['status'] == 'Accepted by Editor' ? 'selected' : ''; ?>>Accepted by Editor</option>
                <option value="Rejected by Editor" <?php echo $article['status'] == 'Rejected by Editor' ? 'selected' : ''; ?>>Rejected by Editor</option>
            </select>
            <br><br>
            <label for="editor_comment">Comment:</label>
            <textarea name="editor_comment" id="editor_comment" rows="4" cols="50"><?php echo safeOutput($article['editor_comment']); ?></textarea>
            <br><br>
            <button type="submit" name="submit_review">Submit Review</button>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
