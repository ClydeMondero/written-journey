<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';

// Handle deletion of selected articles
if (isset($_POST['delete']) && isset($_POST['article_ids'])) {
    $articleIds = $_POST['article_ids'];
    $placeholders = implode(',', array_fill(0, count($articleIds), '?'));
    $deleteQuery = "DELETE FROM articles WHERE doi IN ($placeholders)";
    $stmt = mysqli_prepare($conn, $deleteQuery);

    // Bind parameters dynamically
    $types = str_repeat('s', count($articleIds));
    mysqli_stmt_bind_param($stmt, $types, ...$articleIds);
    mysqli_stmt_execute($stmt);

    // Check if deletion was successful
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo "<script>alert('Selected articles deleted successfully');</script>";
    } else {
        echo "<script>alert('Error deleting articles');</script>";
    }
}

// Fetch all articles from the database
$articlesQuery = "SELECT doi, title, author, email, abstract, issues, pdf, reference, citation FROM articles";
$articlesResult = mysqli_query($conn, $articlesQuery);

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

// Search Articles
$searchTerm = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$searchQuery = "SELECT * FROM articles WHERE title LIKE '%$searchTerm%'";
$searchResult = mysqli_query($conn, $searchQuery);

// Fetch all issues for the checkbox
$issuesQuery = "SELECT * FROM issues";
$issuesResult = mysqli_query($conn, $issuesQuery);
?>

<!DOCTYPE html>
<html lang="en" style="height: 100%">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Articles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body style="height: 100%">
    <div class="container-fluid d-flex h-100">
        <div class="row w-100">
            <!-- Sidebar -->
            <div class="col-md-3 bg-light p-3">
                <?php include 'admin-nav.php'; ?>
            </div>

            <!-- Main Content -->
            <div class="col-md-9">
                <div class="content" style="height: 100%">
                    <h2 class="text4">ARTICLES LIST</h2>

                    <!-- Search Form -->
                    <form action="" method="get" class="mb-3">
                        <label for="search" class="form-label">Search Issue:</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" id="search" placeholder="Enter article title" required>
                            <button type="submit" class="btn btn-outline-secondary">Search</button>
                        </div>
                    </form>

                    <form action="" method="post">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
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
                                    <th>Delete</th>
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
                                        <td>
                                            <input type="checkbox" name="selected_issues[]" value="<?php echo $row['id']; ?>">
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <button type="submit" name="delete_selected" class="btn btn-success">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>