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
    <title>Reviewers Information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <h1 class="display-4 text-center">INFORMATION FOR REVIEWERS</h1>
                <p class="lead text-center">
                    Interested in becoming a reviewer for our journal?
                    We encourage you to review the <a href="customer-about-us.php" class="text-primary">about us</a> page to understand our review policies and the role of our reviewers.
                    Reviewers are essential for maintaining the quality and integrity of our journal by providing unbiased and constructive feedback on submitted manuscripts.
                    To join our team, please <a href="register.php" class="text-primary">register</a> with the journal and submit an application detailing your expertise and experience.
                    Existing reviewers can <a href="login.php" class="text-primary">login</a> to access their assignments and provide their reviews.
                    Thank you for contributing to the excellence of our journal.
                </p>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="info-role">
                    <h4 class="info-content text-secondary">INFORMATION</h4>
                    <div class="list-group">
                        <a href="customer-for-readers.php" class="list-group-item list-group-item-action">For Readers</a>
                        <a href="customer-for-authors.php" class="list-group-item list-group-item-action">For Authors</a>
                        <a href="customer-for-reviewers.php" class="list-group-item list-group-item-action">For Reviewers</a>
                        <a href="customer-for-editors.php" class="list-group-item list-group-item-action">For Editors</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>