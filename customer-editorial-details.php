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
    <title>Editorial Board Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <h1 class="display-4 text-center mt-4">EDITORIAL BOARD DETAILS</h1>
        <div class="row">
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Editor in Chief</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Dr. Chief</h6>
                        <p class="card-text">Email: chief@gmail.com</p>
                        <p class="card-text">Contact No.: 09123456789</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Editorial Board Members</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php
                            $sqlGetBoardMembers = "SELECT CONCAT(first_name, ' ', middle_name, ' ', last_name) AS full_name, email, contact_number FROM editors"; // Adjust table and column names as necessary
                            $resultBoardMembers = $conn->query($sqlGetBoardMembers);

                            if ($resultBoardMembers->num_rows > 0) {
                                while ($member = $resultBoardMembers->fetch_assoc()) {
                                    echo '
                            <li class="list-group-item">
                                <label for="member-name" class="member-name-label">Name</label>
                                <p class="member-name">' . htmlspecialchars($member['full_name']) . '</p>
                                <label for="member-contact" class="member-contact-label">Contact Information</label>
                                <p class="member-contact">Email: ' . htmlspecialchars($member['email']) . '</p>
                                <p class="member-contact">Contact No.: ' . htmlspecialchars($member['contact_number']) . '</p>
                            </li>';
                                }
                            } else {
                                echo '<p>No editorial board members found.</p>';
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>