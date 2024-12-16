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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <h1 class="text-center mb-4">LIST OF ARTICLES</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th class="text-center">Title</th>
                        <th class="text-center">Author</th>
                        <th class="text-center">Email</th>
                        <th class="text-center">Abstract</th>
                        <th class="text-center">DOI</th>
                        <th class="text-center">Reference</th>
                        <th class="text-center">Citation</th>
                        <th class="text-center">Comments</th>
                        <th class="text-center">Issue</th>
                        <th class="text-center">PDF</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Edit Article</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($articlesResult)): ?>
                        <tr>
                            <td class="text-center"><?php echo htmlspecialchars($row['title']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($row['author']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($row['abstract']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($row['doi']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($row['reference']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($row['citation']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($row['comments']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($row['issues']); ?></td>
                            <td class="text-center"><a href="<?php echo $row['pdf']; ?>" target="_blank">View PDF</a></td>
                            <td class="text-center"><?php echo htmlspecialchars($row['status']); ?></td>
                            <td class="text-center">
                                <?php if ($row['status'] != 'Accepted by Reviewer'): ?>
                                    <a href="editor-edit-article.php?id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">Edit</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>

<?php
$conn->close();
?>