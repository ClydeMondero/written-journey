<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('editors-nav.php');
require 'connection.php';

function safeOutput($value)
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// Handle comment update, and optional PDF upload
if (isset($_POST['submit_review'])) {
    $articleId = intval($_POST['article_id']);
    $editorComment = mysqli_real_escape_string($conn, $_POST['editor_comment']);

    // Fetch the current article details to retain the old PDF if not replaced
    $articleQuery = "SELECT pdf FROM articles WHERE id = $articleId";
    $articleResult = mysqli_query($conn, $articleQuery);
    $article = mysqli_fetch_assoc($articleResult);
    $pdfPath = $article['pdf']; // Default to existing PDF

    // Handle PDF upload
    if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
        $fileName = $_FILES['pdf']['name'];
        $tmpName = $_FILES['pdf']['tmp_name'];
        $fileSize = $_FILES['pdf']['size'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($fileExtension === 'pdf' && $fileSize <= 5000000) {
            $newFileName = uniqid() . '.pdf';
            $pdfPath = 'uploads/' . $newFileName;
            move_uploaded_file($tmpName, $pdfPath);
        } else {
            echo "<script>alert('Invalid PDF file or size exceeds limit');</script>";
        }
    }

    // Update the database with the new details
    $updateQuery = "
        UPDATE articles 
        SET editor_comment = '$editorComment', 
            pdf = '$pdfPath' 
        WHERE id = $articleId";

    if (mysqli_query($conn, $updateQuery)) {
        echo "<script>alert('Edit submitted successfully'); window.location.href = 'status-editor-articles.php';</script>";
    } else {
        echo "Error updating article: " . mysqli_error($conn);
    }
}

// Fetch article details
$articleId = intval($_GET['id']);
$articleQuery = "SELECT * FROM articles WHERE id = $articleId";
$articleResult = mysqli_query($conn, $articleQuery);
$article = mysqli_fetch_assoc($articleResult);
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
        <h1 class="text-center">ARTICLE EDIT</h1>
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
            <h3 class="card-header">Submit Edit</h3>
            <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="article_id" value="<?php echo safeOutput($article['id']); ?>">
                    <div class="mb-3">
                        <label for="pdf" class="form-label">Upload New PDF:</label>
                        <input type="file" name="pdf" id="pdf" class="form-control" accept=".pdf">
                    </div>
                    <div class="mb-3">
                        <label for="editor_comment" class="form-label">Comment:</label>
                        <textarea name="editor_comment" id="editor_comment" rows="4" cols="50" class="form-control"><?php echo safeOutput($article['editor_comment']); ?></textarea>
                    </div>
                    <button type="submit" name="submit_review" class="btn btn-success">Submit Edit</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>

<?php
$conn->close();
?>