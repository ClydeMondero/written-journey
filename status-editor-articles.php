<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('editors-nav.php');
require 'connection.php';

// Fetch articles
$articlesQuery = "SELECT * FROM articles";
$articlesResult = mysqli_query($conn, $articlesQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Article Status</title>
</head>
<body>
    <h1>LIST OF ARTICLES</h1>
    <div>
        <table border="1" cellspacing="0" cellpadding="10" class="viewTable">
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
                    <th>Status</th>
                    <th>Review Article</th>
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
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                            <a href="editor-review-articles.php?id=<?php echo $row['id']; ?>">Review</a>
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
