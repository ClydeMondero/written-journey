<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('authors-nav.php');
require 'connection.php';

$fileUploadedSuccessfully = false;

if (isset($_POST["submit"])) {
    $title = mysqli_real_escape_string($conn, $_POST["title"]);
    $author = mysqli_real_escape_string($conn, $_POST["author"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $abstract = mysqli_real_escape_string($conn, $_POST["abstract"]);
    $reference = mysqli_real_escape_string($conn, $_POST["reference"]);
    $citation = mysqli_real_escape_string($conn, $_POST["citation"]);
    $comments = mysqli_real_escape_string($conn, $_POST["comments"]);
    $selectedIssue = mysqli_real_escape_string($conn, $_POST["category"]);

    // Generate a unique DOI
    $doi = "10." . uniqid();

    // Handle PDF upload
    if (isset($_FILES["pdf"]) && $_FILES["pdf"]["error"] === UPLOAD_ERR_OK) {
        $fileName = $_FILES["pdf"]["name"];
        $tmpName = $_FILES["pdf"]["tmp_name"];
        $fileSize = $_FILES["pdf"]["size"];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($fileExtension === 'pdf' && $fileSize <= 5000000) {
            $newFileName = uniqid() . '.pdf';
            $pdfPath = 'uploads/' . $newFileName;
            if (move_uploaded_file($tmpName, $pdfPath)) {
                $stmt = $conn->prepare("INSERT INTO articles (title, author, email, abstract, doi, pdf, reference, citation, comments, issues, status) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
                $stmt->bind_param('ssssssssss', $title, $author, $email, $abstract, $doi, $pdfPath, $reference, $citation, $comments, $selectedIssue);

                if ($stmt->execute()) {
                    echo "<script>alert('Article successfully added with DOI: $doi');</script>";
                } else {
                    echo "<script>alert('Failed to add article');</script>";
                }

                $stmt->close();
            } else {
                echo "<script>alert('Failed to upload PDF');</script>";
            }
        } else {
            echo "<script>alert('Invalid PDF file or size exceeds limit');</script>";
        }
    } else {
        echo "<script>alert('PDF file upload error');</script>";
    }
}

// Delete Selected Issues
if (isset($_POST["delete_selected"])) {
    if (isset($_POST["selected_issues"]) && is_array($_POST["selected_issues"])) {
        foreach ($_POST["selected_issues"] as $selectedIssueId) {
            $selectedIssueId = intval($selectedIssueId);
            $deleteQuery = "DELETE FROM articles WHERE id = $selectedIssueId";
            mysqli_query($conn, $deleteQuery);
        }
        echo "<script>alert('Selected Issues Deleted Successfully');</script>";
    } else {
        echo "<script>alert('No issues selected for deletion');</script>";
    }
}

// Update Article Status
if (isset($_POST["update_status"])) {
    $articleId = intval($_POST["article_id"]);
    $newStatus = mysqli_real_escape_string($conn, $_POST["new_status"]);
    $updateStatusQuery = "UPDATE articles SET status = '$newStatus' WHERE id = $articleId";
    mysqli_query($conn, $updateStatusQuery);
    echo "<script>alert('Article status updated successfully');</script>";
}

// Search Articles
$searchTerm = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$searchQuery = "SELECT * FROM articles WHERE title LIKE '%$searchTerm%'";
$searchResult = mysqli_query($conn, $searchQuery);

// Fetch all issues for the checkbox
$issuesQuery = "SELECT * FROM issues";
$issuesResult = mysqli_query($conn, $issuesQuery);
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>ARTICLES</title>
</head>
<body>
    <h1 class="text1">ARTICLES</h1>
    <div class="all">
        <div class="add">
            <h2 class="text2">Online Submission Form</h2><br>
            <form action="" method="post" autocomplete="off" enctype="multipart/form-data">
                <label for="title">Paper Title:</label>
                <input type="text" name="title" id="title" required placeholder="Enter title"><br><br>
                
                <label for="author">Author Name:</label>
                <input type="text" name="author" id="author" required placeholder="Enter author name"><br><br>
                
                <label for="email">Author Email:</label>
                <input type="email" name="email" id="email" required placeholder="Enter author email"><br><br>
                
                <label for="abstract">Abstract:</label>
                <textarea name="abstract" id="abstract" required placeholder="Enter abstract"></textarea><br><br>

                <label for="issues">Select Issues:</label><br>
                <select name="category" id="category" required>
                    <?php while ($issue = mysqli_fetch_assoc($issuesResult)): ?>
                        <option value="<?php echo $issue['title']; ?>"><?php echo $issue['title']; ?> (Vol. <?php echo $issue['vol_no']; ?>, <?php echo $issue['publication_date']; ?>)</option>
                    <?php endwhile; ?>
                </select><br><br>
                
                <label for="pdf">Upload Paper (PDF Format):</label>
                <input type="file" name="pdf" id="pdf" accept=".pdf" required><br><br>
                
                <label for="reference">References:</label>
                <textarea name="reference" id="reference" required placeholder="Enter all references"></textarea><br><br>
                
                <label for="citation">Citation:</label>
                <textarea name="citation" id="citation" required placeholder="Enter citation"></textarea><br><br>

                <label for="comments">Comments:</label>
                <textarea name="comments" id="comments" required placeholder="Enter comments for editor"></textarea><br><br>
                
                <button type="submit" name="submit" class="btnSubmit">Submit</button>
            </form>
        </div>

        <!-- Articles List -->
        <div class="view">
            <h1 class="text4">ARTICLES LIST</h1>

            <!-- Search Form -->
            <form action="" method="get">
                <label for="search" class="text5">Search Issue:</label>
                <input type="text" name="search" class="searchtxt" id="search" placeholder="Enter article title" required>
                <button type="submit" class="btnSearch">Search</button>
            </form><br>

            <form action="" method="post">
                <table border="1" cellspacing="0" cellpadding="10" class="viewTable">
                    <tr class="thView">
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Email</th>
                        <th>Abstract</th>
                        <th>DOI</th>
                        <th>Issues</th>
                        <th>PDF</th>
                        <th>References</th>
                        <th>Citations</th>
                        <th>Comments</th>
                        <th>Status</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                    <?php while ($row = mysqli_fetch_assoc($searchResult)): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['title']; ?></td>
                            <td><?php echo $row['author']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['abstract']; ?></td>
                            <td><?php echo $row['doi']; ?></td>
                            <td><?php echo $row['issues']; ?></td>
                            <td><a href="<?php echo $row['pdf']; ?>" target="_blank">View PDF</a></td>
                            <td><?php echo $row['reference']; ?></td>
                            <td><?php echo $row['citation']; ?></td>
                            <td><?php echo $row['comments']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                            <td><a href="edit_article.php?id=<?php echo $row['id']; ?>" class="edit">Edit</a></td>
                            <td><input type="checkbox" name="selected_issues[]" value="<?php echo $row['id']; ?>"></td>
                        </tr>
                    <?php endwhile; ?>
                </table><br>
                <button type="submit" name="delete_selected" class="btndel">Delete Selected</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>