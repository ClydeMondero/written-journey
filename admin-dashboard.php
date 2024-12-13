<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('admin-nav.php');
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
</head>
<body>
    <div class="content">
        <h1>Admin Dashboard</h1>
        
        <div class="stats">
            <h3>General Statistics</h3>
            <p>Total Users: <?= htmlspecialchars($userCount); ?></p>
            <p>Total Authors: <?= htmlspecialchars($authorCount); ?></p>
            <p>Total Reviewers: <?= htmlspecialchars($reviewerCount); ?></p>
            <p>Total Editors: <?= htmlspecialchars($editorCount); ?></p>

            <hr>
            
            <h3>Submission Statistics</h3>
            <p>Total Submitted Articles: <?= htmlspecialchars($submissionCount); ?></p>

            <hr>

            <h3>Review Timelines</h3>
            <?php if (!empty($statusCounts)): ?>
                <table border="1" cellspacing="0" cellpadding="5">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($statusCounts as $status => $count): ?>
                            <tr>
                                <td><?= htmlspecialchars(ucfirst($status)); ?></td>
                                <td><?= htmlspecialchars($count); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No review timelines available.</p>
            <?php endif; ?>

            <hr>

            <h3>Journal Impact</h3>
            <p>Total Downloads: <?= htmlspecialchars($totalDownloads); ?></p>
            <p>Average Downloads per Article: <?= htmlspecialchars($averageDownloads); ?></p>

            <hr>

            <form method="GET">
                <button type="submit" name="generate_report">Generate Report</button>
            </form>
        </div>
    </div>
</body>
</html>