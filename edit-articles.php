<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';

if (isset($_GET['id'])) {
    $articleId = intval($_GET['id']);

    // Fetch the article details from the database
    $query = "SELECT * FROM articles WHERE id = $articleId";
    $result = mysqli_query($conn, $query);
    $article = mysqli_fetch_assoc($result);

    if (!$article) {
        echo "<script>alert('Article not found');</script>";
        exit;
    }
} else {
    echo "<script>alert('No article ID specified');</script>";
    exit;
}

// Handle form submission to update the article
if (isset($_POST['submit'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $abstract = mysqli_real_escape_string($conn, $_POST['abstract']);
    $reference = mysqli_real_escape_string($conn, $_POST['reference']);
    $citation = mysqli_real_escape_string($conn, $_POST['citation']);
    $comments = mysqli_real_escape_string($conn, $_POST['comments']);
    $selectedIssue = mysqli_real_escape_string($conn, $_POST['category']);

    // Handle PDF upload (optional: if a new PDF is uploaded, update it)
    $pdfPath = $article['pdf']; // Keep the old PDF by default
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

    // Prepare the update query (DOI should not be updated)
    $updateQuery = "UPDATE articles SET 
                    title = '$title', 
                    author = '$author', 
                    email = '$email', 
                    abstract = '$abstract', 
                    reference = '$reference', 
                    citation = '$citation', 
                    comments = '$comments', 
                    issues = '$selectedIssue', 
                    pdf = '$pdfPath' 
                    WHERE id = $articleId";

    if (mysqli_query($conn, $updateQuery)) {
        echo "<script>alert('Article updated successfully');</script>";
        // Redirect to the articles page (optional)
        echo "<script>window.location.href = 'add-articles.php';</script>";
    } else {
        echo "<script>alert('Failed to update article');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>Edit Article</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="text-center mb-4">EDIT ARTICLE</h2>
                <form action="" method="post" autocomplete="off" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title:</label>
                        <input type="text" name="title" id="title" class="form-control" value="<?php echo $article['title']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="author" class="form-label">Author:</label>
                        <input type="text" name="author" id="author" class="form-control" value="<?php echo $article['author']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?php echo $article['email']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="abstract" class="form-label">Abstract:</label>
                        <textarea name="abstract" id="abstract" class="form-control" rows="3" required><?php echo $article['abstract']; ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label">Select Issue:</label>
                        <select name="category" id="category" class="form-select" required>
                            <?php
                            $issuesQuery = "SELECT * FROM issues";
                            $issuesResult = mysqli_query($conn, $issuesQuery);

                            while ($issue = mysqli_fetch_assoc($issuesResult)) {
                                $selected = ($issue['title'] == $article['issues']) ? 'selected' : '';
                                echo "<option value='" . $issue['title'] . "' $selected>" . $issue['title'] . " (Vol. " . $issue['vol_no'] . ", " . $issue['publication_date'] . ")</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="reference" class="form-label">References:</label>
                        <textarea name="reference" id="reference" class="form-control" rows="2" required><?php echo $article['reference']; ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="citation" class="form-label">Citation:</label>
                        <textarea name="citation" id="citation" class="form-control" rows="2" required><?php echo $article['citation']; ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="comments" class="form-label">Comments:</label>
                        <textarea name="comments" id="comments" class="form-control" rows="2" required><?php echo $article['comments']; ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="pdf" class="form-label">Current PDF:</label>
                        <a href="<?php echo $article['pdf']; ?>" target="_blank" class="form-text">View PDF</a>
                    </div>
                    <div class="mb-3">
                        <label for="pdf" class="form-label">Upload New PDF (Optional):</label>
                        <input type="file" name="pdf" id="pdf" class="form-control" accept=".pdf">
                    </div>

                    <div class="mb-3">
                        <label for="doi" class="form-label">DOI:</label>
                        <input type="text" name="doi" id="doi" class="form-control" value="<?php echo $article['doi']; ?>" readonly>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" onclick="window.history.back();">Back</button>
                        <button type="submit" name="submit" class="btn btn-success">Update Article</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>

<?php mysqli_close($conn); ?>