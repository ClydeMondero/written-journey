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
    <title>Authors Information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="display-4 text-center">INFORMATION FOR AUTHORS</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <p class="text-dark lead">
                    Interested in submitting to this journal?
                    We recommend that you review the <a href="customer-about-us.php">about</a> page for the journal&apos;s section policies,
                    as well as the <a href="customer-submission-guidelines.php">authors guidelines.</a>
                    Authors need to <a href="register.php">register</a> with the journal prior to submitting or,
                    if already registered, can simply <a href="login.php">login</a> and begin the submission process.
                </p>
            </div>
        </div>
        <div class="row">
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