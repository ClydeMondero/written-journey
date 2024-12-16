<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';
include('customer-nav.php');

// Search and sort functionality
$search = '';
$sortOrder = 'DESC'; // Default sort order
$sqlGetIssues = "SELECT image, title, vol_no, publication_date, id FROM issues WHERE 1=1";

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = trim($_GET['search']);
    $sqlGetIssues .= " AND (title LIKE ? OR vol_no LIKE ? OR publication_date LIKE ?)";
}

if (isset($_GET['sort_order']) && in_array($_GET['sort_order'], ['ASC', 'DESC'])) {
    $sortOrder = $_GET['sort_order'];
}

$sqlGetIssues .= " ORDER BY publication_date $sortOrder";

$stmtIssues = $conn->prepare($sqlGetIssues);

if (!empty($search)) {
    $searchTerm = "%$search%";
    $stmtIssues->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
}

$stmtIssues->execute();
$resultIssues = $stmtIssues->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reader Archives</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <h1 class="text-center">Archives</h1>
        <form method="GET" action="" class="d-flex justify-content-center gap-3">
            <input type="text" name="search" value="<?= htmlspecialchars($search); ?>" placeholder="Search by title, volume, or date" class="form-control">
            <select name="sort_order" class="form-control">
                <option value="DESC" <?= $sortOrder === 'DESC' ? 'selected' : ''; ?>>Newest First</option>
                <option value="ASC" <?= $sortOrder === 'ASC' ? 'selected' : ''; ?>>Oldest First</option>
            </select>
            <button type="submit" class="btn btn-success">Search</button>
        </form>
        <hr>
        <?php if ($resultIssues->num_rows > 0): ?>
            <div class="row">
                <?php while ($issue = $resultIssues->fetch_assoc()): ?>
                    <?php
                    $issueId = $issue['id'] ?? '0'; // Ensure the correct key is used for the ID
                    $image = $issue['image'] ?? 'default-image.png';
                    $title = $issue['title'] ?? 'Untitled Issue';
                    $volNo = $issue['vol_no'] ?? 'N/A';
                    $publication_date = $issue['publication_date'] ?? 'N/A'; // Default value if no publication_date
                    ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <img src="img/<?php echo basename($image); ?>" class="card-img-top" alt="image">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($title); ?></h5>
                                <p class="card-text">Vol. <?= htmlspecialchars($volNo); ?>, <?= htmlspecialchars($publication_date); ?></p>
                                <a href="customer-archives-articles.php?issues=<?= htmlspecialchars($issueId); ?>" class="btn btn-success">View Articles</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-center">No issues match your search.</p>
        <?php endif; ?>
    </div>
</body>

</html>
