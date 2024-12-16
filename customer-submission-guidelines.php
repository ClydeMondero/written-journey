<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';
include('customer-nav.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body class="bg-light">
    <div class="container py-5">
        <h1 class="display-4 text-center mb-4">Submission Guidelines</h1>
        <h2 class="h5 text-secondary">Author</h2>
        <p class="text-dark">
            Authors are encouraged to submit their work to this journal.
            To proceed with a submission, you must log in as an author.
            If you don't have an account yet, please <a href="register.php" class="text-primary">register</a> here. If you already have an account, you can <a href="login.php" class="text-primary">login</a> here.
            Note that only authors have access to the submission process.
            All submissions will be assessed by an editor to determine whether they meet the aims and scope of this journal.
            Those considered to be good fit will be sent for peer review before determining whether they will be accepted or rejected.
        </p>
        <h2 class="h5 text-secondary mt-4">Submission Preparation Checklist</h2>
        <p class="text-dark">Before submitting your paper, please ensure it adheres to the following guidelines:</p>
        <ul class="list-group mb-4">
            <li class="list-group-item">Use the specific formatting.</li>
            <li class="list-group-item">Strictly follows referencing and citation guidelines (APA 7th Edition).</li>
            <li class="list-group-item">Perform a plagiarism check before submission.</li>
            <li class="list-group-item">Include all necessary metadata such as title, author name, and abstract.</li>
            <li class="list-group-item">Submit your paper in a single PDF file.</li>
            <li class="list-group-item">Follows research ethics and includes any necessary conflict of interest, acknowledgement, AI declarations, and funding declaration.</li>
        </ul>
        <div class="info-role">
            <h4 class="h5 text-secondary">Information</h4>
            <div class="list-group">
                <a href="customer-for-readers.php" class="list-group-item list-group-item-action">For Readers</a>
                <a href="customer-for-authors.php" class="list-group-item list-group-item-action">For Authors</a>
                <a href="customer-for-reviewers.php" class="list-group-item list-group-item-action">For Reviewers</a>
                <a href="customer-for-editors.php" class="list-group-item list-group-item-action">For Editors</a>
            </div>
        </div>
    </div>
</body>

</html>