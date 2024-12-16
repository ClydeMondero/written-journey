<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';
require_once 'tcpdf/tcpdf.php'; // Include TCPDF library

// Initialize counts
$userCount = $authorCount = $reviewerCount = $editorCount = $submissionCount = 0;
$totalDownloads = $averageDownloads = 0;

// Fetch count of all users
$sqlUsers = "SELECT COUNT(*) AS user_count FROM users";
$resultUsers = $conn->query($sqlUsers);
if ($resultUsers && $resultUsers->num_rows > 0) {
    $userRow = $resultUsers->fetch_assoc();
    $userCount = $userRow['user_count'];
}

// Fetch count of all authors
$sqlAuthors = "SELECT COUNT(DISTINCT author) AS author_count FROM articles";
$resultAuthors = $conn->query($sqlAuthors);
if ($resultAuthors && $resultAuthors->num_rows > 0) {
    $authorRow = $resultAuthors->fetch_assoc();
    $authorCount = $authorRow['author_count'];
}

// Fetch count of all reviewers
$sqlReviewers = "SELECT COUNT(*) AS reviewer_count FROM reviewers";
$resultReviewers = $conn->query($sqlReviewers);
if ($resultReviewers && $resultReviewers->num_rows > 0) {
    $reviewerRow = $resultReviewers->fetch_assoc();
    $reviewerCount = $reviewerRow['reviewer_count'];
}

// Fetch count of all editors
$sqlEditors = "SELECT COUNT(*) AS editor_count FROM editors";
$resultEditors = $conn->query($sqlEditors);
if ($resultEditors && $resultEditors->num_rows > 0) {
    $editorRow = $resultEditors->fetch_assoc();
    $editorCount = $editorRow['editor_count'];
}

// Fetch all submission statistics
$sqlSubmissionStats = "SELECT COUNT(*) AS submission_count FROM articles";
$resultSubmissionStats = $conn->query($sqlSubmissionStats);
if ($resultSubmissionStats && $resultSubmissionStats->num_rows > 0) {
    $submissionRow = $resultSubmissionStats->fetch_assoc();
    $submissionCount = $submissionRow['submission_count'];
}

// Fetch status counts for review timelines
$statusCounts = [];
$sqlStatusCounts = "SELECT status, COUNT(*) AS count FROM articles GROUP BY status";
$resultStatusCounts = $conn->query($sqlStatusCounts);
if ($resultStatusCounts && $resultStatusCounts->num_rows > 0) {
    while ($row = $resultStatusCounts->fetch_assoc()) {
        $statusCounts[$row['status']] = $row['count'];
    }
}

// Calculate download statistics
$sqlDownloadStats = "SELECT SUM(download_count) AS total_downloads, AVG(download_count) AS avg_downloads FROM articles";
$resultDownloadStats = $conn->query($sqlDownloadStats);
if ($resultDownloadStats && $resultDownloadStats->num_rows > 0) {
    $downloadStatsRow = $resultDownloadStats->fetch_assoc();
    $totalDownloads = $downloadStatsRow['total_downloads'] ?? 0;
    $averageDownloads = round($downloadStatsRow['avg_downloads'], 2);
}

// Generate Report if the button is clicked
if (isset($_GET['generate_report'])) {
    // Avoid any output before this point to prevent issues
    ob_clean(); // Clean any existing output

    // Create a new PDF document
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Admin');
    $pdf->SetTitle('Admin Dashboard Report');
    $pdf->SetHeaderData('', 0, 'Admin Dashboard Report', 'Generated on: ' . date('Y-m-d H:i:s'));
    $pdf->setHeaderFont(['helvetica', '', 12]);
    $pdf->setFooterFont(['helvetica', '', 10]);
    $pdf->SetMargins(15, 27, 15);
    $pdf->SetAutoPageBreak(TRUE, 25);
    $pdf->AddPage();

    $html = "
        <h1>Admin Dashboard Report</h1>
        <h3>General Statistics</h3>
        <p>Total Users: $userCount</p>
        <p>Total Authors: $authorCount</p>
        <p>Total Reviewers: $reviewerCount</p>
        <p>Total Editors: $editorCount</p>
        <hr>
        <h3>Submission Statistics</h3>
        <p>Total Submitted Articles: $submissionCount</p>
        <hr>
        <h3>Review Timelines</h3>
        <table border=\"1\" cellspacing=\"0\" cellpadding=\"5\">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>";
    foreach ($statusCounts as $status => $count) {
        $html .= "
                <tr>
                    <td>" . htmlspecialchars(ucfirst($status)) . "</td>
                    <td>" . htmlspecialchars($count) . "</td>
                </tr>";
    }
    $html .= "
            </tbody>
        </table>
        <hr>
        <h3>Journal Impact</h3>
        <p>Total Downloads: $totalDownloads</p>
        <p>Average Downloads per Article: $averageDownloads</p>
    ";

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('Admin_Dashboard_Report.pdf', 'D'); // Forces download
    exit; // End the script after the PDF output
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 bg-light p-3">
                <?php include 'admin-nav.php'; ?>
            </div>
            <div class="col-md-9">
                <div class="content p-4">
                    <h2 class="mb-4">Admin Dashboard</h2>

                    <div class="stats mb-4">
                        <h3 class="mb-3">General Statistics</h3>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="p-3 border bg-light">
                                    <h5>Total Users</h5>
                                    <p><?= htmlspecialchars($userCount); ?></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 border bg-light">
                                    <h5>Total Authors</h5>
                                    <p><?= htmlspecialchars($authorCount); ?></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 border bg-light">
                                    <h5>Total Reviewers</h5>
                                    <p><?= htmlspecialchars($reviewerCount); ?></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 border bg-light">
                                    <h5>Total Editors</h5>
                                    <p><?= htmlspecialchars($editorCount); ?></p>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <h3 class="mb-3">Submission Statistics</h3>
                        <div class="p-3 border bg-light">
                            <h5>Total Submitted Articles</h5>
                            <p><?= htmlspecialchars($submissionCount); ?></p>
                        </div>

                        <hr>

                        <h3 class="mb-3">Review Timelines</h3>
                        <?php if (!empty($statusCounts)): ?>
                            <div class="row">
                                <?php foreach ($statusCounts as $status => $count): ?>
                                    <div class="col-md-3 mb-3">
                                        <div class="p-3 border bg-light">
                                            <h5 class="mb-1"><?= htmlspecialchars(ucfirst($status)); ?></h5>
                                            <p class="mb-0">Count: <?= htmlspecialchars($count); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p>No review timelines available.</p>
                        <?php endif; ?>
                    </div>

                    <hr>

                    <h3 class="mb-3">Journal Impact</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="p-3 border bg-light">
                                <h5>Total Downloads</h5>
                                <p><?= htmlspecialchars($totalDownloads); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border bg-light">
                                <h5>Average Downloads per Article</h5>
                                <p><?= htmlspecialchars($averageDownloads); ?></p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <form method="GET">
                        <button type="submit" name="generate_report" class="btn btn-success">Generate Report</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>