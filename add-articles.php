<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Navigation -->
            <div class="col-md-3 bg-light p-0 dvh-100">
                <?php include('authors-nav.php'); ?>
            </div>

            <!-- Main Content -->
            <div class="col-md-9">
                <div class="container py-4">
                    <h2 class="mb-4">Add Articles</h2>

                    <!-- Submission Form -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4>Online Submission Form</h4>
                        </div>
                        <div class="card-body">
                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Paper Title</label>
                                    <input type="text" name="title" id="title" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="author" class="form-label">Author Name</label>
                                    <input type="text" name="author" id="author" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Author Email</label>
                                    <input type="email" name="email" id="email" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="abstract" class="form-label">Abstract</label>
                                    <textarea name="abstract" id="abstract" class="form-control" rows="4" required></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="issues" class="form-label">Select Issue</label>
                                    <select name="category" id="category" class="form-select" required>
                                        <?php while ($issue = mysqli_fetch_assoc($issuesResult)): ?>
                                            <option value="<?php echo $issue['title']; ?>">
                                                <?php echo $issue['title']; ?> (Vol. <?php echo $issue['vol_no']; ?>, <?php echo $issue['publication_date']; ?>)
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="pdf" class="form-label">Upload Paper (PDF Format)</label>
                                    <input type="file" name="pdf" id="pdf" class="form-control" accept=".pdf" required>
                                </div>

                                <div class="mb-3">
                                    <label for="reference" class="form-label">References</label>
                                    <textarea name="reference" id="reference" class="form-control" rows="2" required></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="citation" class="form-label">Citation</label>
                                    <textarea name="citation" id="citation" class="form-control" rows="2" required></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="comments" class="form-label">Comments</label>
                                    <textarea name="comments" id="comments" class="form-control" rows="2" required></textarea>
                                </div>

                                <button type="submit" name="submit" class="btn btn-success">Submit</button>
                            </form>
                        </div>
                    </div>

                    <!-- Articles List -->
                    <div class="card">
                        <div class="card-header">
                            <h4>Articles List</h4>
                            <form action="" method="get" class="d-flex mt-2">
                                <input type="text" name="search" class="form-control me-2" placeholder="Search by title" required>
                                <button type="submit" class="btn btn-outline-success">Search</button>
                            </form>
                        </div>
                        <div class="card-body table-responsive">
                            <form action="" method="post">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-dark">
                                        <tr>
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
                                            <th>Select</th>
                                        </tr>
                                    </thead>
                                    <tbody>
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
                                                <td><a href="edit-articles.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success">Edit</a></td>
                                                <td><input type="checkbox" name="selected_issues[]" value="<?php echo $row['id']; ?>"></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                                <button type="submit" name="delete_selected" class="btn btn-success">Delete Selected</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>