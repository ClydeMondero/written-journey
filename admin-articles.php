<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('admin-nav.php');
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Articles</title>
</head>
<body>
    <div class="content">
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
                <td>
                    <input type="checkbox" name="selected_issues[]" value="<?php echo $row['id']; ?>">
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
    <br>
    <button type="submit" name="delete_selected" class="btndelete">Delete</button>
</form>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>
