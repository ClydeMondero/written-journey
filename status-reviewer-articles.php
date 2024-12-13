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
    <style>
        .btn-accept {
            background-color: green;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn-reject {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn-accept:hover {
            background-color: darkgreen;
        }
        .btn-reject:hover {
            background-color: darkred;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>ARTICLES FOR REVIEW</h1>
    <div>
        <table>
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
                        <td><a href="<?php echo $row['pdf']; ?>" target="_blank">PDF</a></td>
                        <td>
                            <a href="?action=accept&article_id=<?php echo $row['id']; ?>" class="btn-accept">Accept</a>
                            <a href="?action=reject&article_id=<?php echo $row['id']; ?>" class="btn-reject">Reject</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
