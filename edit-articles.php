<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('authors-nav.php');
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
</head>
<body>
    <h1 class="text1">EDIT ARTICLE</h1>
    <div class="all">
        <div class="edit">
            <h2 class="text2">EDIT ARTICLE</h2><br>
            <form action="" method="post" autocomplete="off" enctype="multipart/form-data">
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" value="<?php echo $article['title']; ?>" required><br><br>
                
                <label for="author">Author:</label>
                <input type="text" name="author" id="author" value="<?php echo $article['author']; ?>" required><br><br>
                
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo $article['email']; ?>" required><br><br>
                
                <label for="abstract">Abstract:</label>
                <textarea name="abstract" id="abstract" required><?php echo $article['abstract']; ?></textarea><br><br>

                <label for="category">Select Issue:</label><br>
                <select name="category" id="category" required>
                    <?php
                    // Fetch all issues for the dropdown
                    $issuesQuery = "SELECT * FROM issues";
                    $issuesResult = mysqli_query($conn, $issuesQuery);

                    while ($issue = mysqli_fetch_assoc($issuesResult)) {
                        $selected = ($issue['title'] == $article['issues']) ? 'selected' : '';
                        echo "<option value='" . $issue['title'] . "' $selected>" . $issue['title'] . " (Vol. " . $issue['vol_no'] . ", " . $issue['publication_date'] . ")</option>";
                    }
                    ?>
                </select><br><br>

                <label for="reference">References:</label>
                <textarea name="reference" id="reference" required><?php echo $article['reference']; ?></textarea><br><br>
                
                <label for="citation">Citation:</label>
                <textarea name="citation" id="citation" required><?php echo $article['citation']; ?></textarea><br><br>

                <label for="comments">Comments:</label>
                <textarea name="comments" id="comments" required><?php echo $article['comments']; ?></textarea><br><br>

                <!-- Show the current PDF but allow a new one to be uploaded -->
                <label for="pdf">Current PDF:</label>
                <a href="<?php echo $article['pdf']; ?>" target="_blank">View PDF</a><br><br>
                <label for="pdf">Upload New PDF (Optional):</label>
                <input type="file" name="pdf" id="pdf" accept=".pdf"><br><br>

                <!-- DOI should not be editable -->
                <label for="doi">DOI:</label>
                <input type="text" name="doi" id="doi" value="<?php echo $article['doi']; ?>" readonly><br><br>

                <button type="submit" name="submit" class="btnSubmit">Update Article</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php mysqli_close($conn); ?>