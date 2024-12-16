<?php
error_reporting(E_ALL);
session_start();
ini_set('display_errors', 1);
require 'connection.php';


// Functions for reusable code
function renderTable($conn, $tableName, $searchEmail = '')
{
    $query = "SELECT * FROM $tableName";
    if ($searchEmail) {
        $query .= " WHERE email LIKE ?";
    }

    $stmt = $conn->prepare($query);
    if ($searchEmail) {
        $searchParam = "%$searchEmail%";
        $stmt->bind_param("s", $searchParam);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<div class='table-responsive'>";
    echo "<table class='table table-striped table-hover'>";
    echo "<thead><tr><th>Name</th><th>Email</th><th>Blocked</th><th>Actions</th></tr></thead>";
    echo "<tbody>";
    while ($row = $result->fetch_assoc()) {
        $blockedStatus = $row['blocked'] ? 'Yes' : 'No';
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>$blockedStatus</td>";
        echo "<td><form method='POST'><input type='hidden' name='email' value='" . htmlspecialchars($row['email']) . "'>
                <input type='hidden' name='table' value='$tableName'>
                <button type='submit' class='btn btn-success' name='unblock'>Unblock</button>
                <button type='submit' class='btn btn-success' name='delete'>Delete</button></form></td>";
        echo "</tr>";
    }

    echo "</tbody></table></div>";
}

function unblockUser($conn, $email, $tableName)
{
    $stmt = $conn->prepare("UPDATE $tableName SET blocked = 0, attempts = 0 WHERE email = ?");
    $stmt->bind_param("s", $email);
    return $stmt->execute();
}

function deleteUser($conn, $email, $tableName)
{
    $stmt = $conn->prepare("DELETE FROM $tableName WHERE email = ?");
    $stmt->bind_param("s", $email);
    return $stmt->execute();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $table = $_POST['table'] ?? '';

    if (isset($_POST['unblock'])) {
        if (unblockUser($conn, $email, $table)) {
            echo "<script>alert('User unblocked successfully.');</script>";
        } else {
            echo "<script>alert('Failed to unblock user.');</script>";
        }
    }

    if (isset($_POST['delete'])) {
        if (deleteUser($conn, $email, $table)) {
            echo "<script>alert('User deleted successfully.');</script>";
        } else {
            echo "<script>alert('Failed to delete user.');</script>";
        }
    }
}

// Rendering the page
$searchEmail = $_GET['search'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>


<div class="container-fluid ">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 bg-light">
            <div class="list-group">
                <?php include 'admin-nav.php'; ?>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">MANAGE USERS</h3>
                </div>
                <div class="card-body">
                    <!-- Search Bar -->
                    <form method="GET" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search by email" value="<?php echo htmlspecialchars($searchEmail); ?>">
                            <div class="input-group-append">
                                <button class="btn btn-success" type="submit">Search</button>
                            </div>
                        </div>
                    </form>

                    <!-- User Management Tables -->
                    <section id="user-management">
                        <h3>Users</h3>
                        <?php renderTable($conn, 'users', $searchEmail); ?>
                        <h3>Authors</h3>
                        <?php renderTable($conn, 'authors', $searchEmail); ?>
                        <h3>Editors</h3>
                        <?php renderTable($conn, 'editors', $searchEmail); ?>
                        <h3>Reviewers</h3>
                        <?php renderTable($conn, 'reviewers', $searchEmail); ?>
                    </section>

                    <!-- Unblock User Form -->
                    <section id="unblock-user" class="mt-5">
                        <h3>Unblock User</h3>
                        <form method="post" action="add-account.php" class="needs-validation" novalidate>
                            <div class="form-group">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="Enter email address" required>
                                <div class="invalid-feedback">
                                    Please enter a valid email address.
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="txtadmin" class="form-label">Admin Password:</label>
                                <input type="password" id="txtadmin" name="txtadmin" class="form-control" placeholder="Enter admin password" required>
                                <div class="invalid-feedback">
                                    Please enter your admin password.
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success">Unblock User</button>
                        </form>
                    </section>

                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>