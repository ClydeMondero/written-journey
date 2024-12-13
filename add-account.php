<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('admin-nav.php');
require 'connection.php';

// Function to unblock a user
function unBlock($conn, $email, $table)
{
    try {
        $resetSql = "UPDATE $table SET blocked = 0, attempts = 0 WHERE email = '$email'";
        return mysqli_query($conn, $resetSql);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

// Check if the search form is submitted
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["searchEmail"])) {
    $searchEmail = mysqli_real_escape_string($conn, $_GET["searchEmail"]);
    $sql = "SELECT * FROM users WHERE email LIKE '%$searchEmail%'";
} else {
    // If the search form is not submitted, retrieve all users
    $sql = "SELECT * FROM users";
}

$result = mysqli_query($conn, $sql);

echo "<div class='view'>";

echo "<h2 class='text2'> READERS LIST</h2>";

// Search Form
echo "<form method='get' action='add-account.php'>";
echo "<label for='searchEmail' class='text5'>Search Email: </label>";
echo "<input type='email' name='searchEmail' class='searchtxt' id='searchEmail' placeholder='Enter email address' required>";
echo "<button type='submit' class='btnSearch'>Search</button>";
echo "</form>";

echo "<form method='POST'>";
echo "<table border=1 cellspacing=0 cellpadding=10 class='viewTable'>";
echo "<tr class='thView'>";
echo "<th>Username</th>";
echo "<th>Email</th>";
echo "<th>Attempts</th>";
echo "<th>Blocked</th>";
echo "<th>Action</th>";
echo "</tr>";

while ($row = mysqli_fetch_array($result)) {
    $temp0 = $row['name'];
    $temp1 = $row['email'];
    $temp2 = $row['attempts'];
    $temp3 = $row['blocked'];

    echo "<tr><td>$temp0</td>";
    echo "<td>$temp1</td>";
    echo "<td>$temp2</td>";
    echo "<td>$temp3</td>";
    echo "<td> <input type='checkbox' name='selectedEmails[]' value='$temp1'></td></tr>";
}

echo "</table>";
echo "<button type='submit' name='delete_selected' class='deletebtn'>Delete</button>";
echo "</form>";

// Handle user unblocking
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"]) && isset($_POST["txtadmin"])) {
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $adminPassword = $_POST["txtadmin"];

    // Check if the email exists in the database
    $checkEmailSql = "SELECT COUNT(*) AS count FROM users WHERE email = '$email'";
    $checkEmailResult = mysqli_query($conn, $checkEmailSql);
    $emailCount = mysqli_fetch_assoc($checkEmailResult)['count'];

    if ($emailCount == 0) {
        //echo '<script>alert("Email does not exist. Cannot unblock.");</script>';
    } else {
        // Proceed with unblocking process
        // Check if the email is admin's email
        $adminEmail = "admin@gmail.com";
        if ($email === $adminEmail) {
            echo '<script>alert("Cannot unblock the admin account.");</script>';
        } else {
            // Fetch the hashed admin password from the database
            $fetchAdminPasswordSql = "SELECT password FROM admin WHERE email = '$adminEmail'";
            $fetchAdminPasswordResult = mysqli_query($conn, $fetchAdminPasswordSql);

            if ($fetchAdminPasswordResult) {
                $adminData = mysqli_fetch_assoc($fetchAdminPasswordResult);
                $hashedAdminPassword = $adminData['password'];

                // Check if the entered admin password is correct
                if (password_verify($adminPassword, $hashedAdminPassword)) {
                    // Check if the user is already unblocked
                    $checkUnblockSql = "SELECT blocked FROM users WHERE email = ?";
                    $stmt = mysqli_prepare($conn, $checkUnblockSql);
                    mysqli_stmt_bind_param($stmt, "s", $email);
                    mysqli_stmt_execute($stmt);

                    $checkUnblockResult = mysqli_stmt_get_result($stmt);

                    $userData = mysqli_fetch_assoc($checkUnblockResult);
                    $isBlocked = $userData['blocked'];

                    if ($isBlocked == 0) {
                        echo '<script>alert("User with email ' . $email . ' is already unblocked.");</script>';
                    } else {
                        // User is not admin and not already unblocked, proceed with unblocking
                        $success = unBlock($conn, $email, 'users');

                        if ($success) {
                            //echo "<script>alert('User with email ' . $email . ' has been unblocked successfully.'); document.location.href = 'add-account.php';</script>";

                        } else {
                            echo '<script>alert("Failed to unblock the user.");</script>';
                        }
                    }
                } else {
                    echo '<script>alert("Incorrect admin password.");</script>';
                }
            } else {
                echo '<script>alert("Error fetching admin password.");</script>';
            }
        }
    }
}

// Handle multiple deletion
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_selected"])) {
    if (isset($_POST["selectedEmails"]) && is_array($_POST["selectedEmails"])) {
        foreach ($_POST["selectedEmails"] as $selectedEmail) {
            $deleteSql = "DELETE FROM users WHERE email = '$selectedEmail'";
            mysqli_query($conn, $deleteSql);
        }
        echo "<script>alert('Selected Users Deleted Successfully'); document.location.href = 'add-account.php';</script>";
    } else {
        echo "<script>alert('No users selected for deletion');</script>";
    }
}

echo "</form>";
echo "</div>";

// Function to unblock an author
function aunBlock($conn, $email)
{
    try {
        $resetSql = "UPDATE authors SET blocked = 0, attempts = 0 WHERE email = '$email'";
        return mysqli_query($conn, $resetSql);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

// Check if the search form is submitted
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["searchEmail"])) {
    $searchEmail = mysqli_real_escape_string($conn, $_GET["searchEmail"]);
    $sql = "SELECT * FROM authors WHERE email LIKE '%$searchEmail%'";
} else {
    // If the search form is not submitted, retrieve all authors
    $sql = "SELECT * FROM authors";
}

$result = mysqli_query($conn, $sql);

echo "<div class='view'>";

echo "<h2 class='text2'> AUTHORS LIST </h2>";

// Search Form
echo "<form method='get' action='add-account.php'>";
echo "<label for='searchEmail' class='text5'>Search Email: </label>";
echo "<input type='email' name='searchEmail' class='searchtxt' id='searchEmail' placeholder='Enter email address' required>";
echo "<button type='submit' class='btnSearch'>Search</button>";
echo "</form>";

echo "<form method='POST'>";
echo "<table border=1 cellspacing=0 cellpadding=10 class='viewTable'>";
echo "<tr class='thView'>";
echo "<th>Username</th>";
echo "<th>Email</th>";
echo "<th>Attempts</th>";
echo "<th>Blocked</th>";
echo "<th>Action</th>";
echo "</tr>";

while ($row = mysqli_fetch_array($result)) {
    $temp0 = $row['name'];
    $temp1 = $row['email'];
    $temp2 = $row['attempts'];
    $temp3 = $row['blocked'];

    echo "<tr><td>$temp0</td>";
    echo "<td>$temp1</td>";
    echo "<td>$temp2</td>";
    echo "<td>$temp3</td>";
    echo "<td> <input type='checkbox' name='selectedEmails[]' value='$temp1'></td></tr>";
}

echo "</table>";
echo "<button type='submit' name='delete_selected' class='deletebtn'>Delete</button>";
echo "</form>";

// Handle author unblocking
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"]) && isset($_POST["txtadmin"])) {
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $adminPassword = $_POST["txtadmin"];

    // Check if the email exists in the database
    $checkEmailSql = "SELECT COUNT(*) AS count FROM authors WHERE email = '$email'";
    $checkEmailResult = mysqli_query($conn, $checkEmailSql);
    $emailCount = mysqli_fetch_assoc($checkEmailResult)['count'];

    if ($emailCount == 0) {
        //echo '<script>alert("Email does not exist. Cannot unblock.");</script>';
    } else {
        // Proceed with unblocking process
        // Check if the email is admin's email
        $adminEmail = "admin@gmail.com";
        if ($email === $adminEmail) {
            echo '<script>alert("Cannot unblock the admin account.");</script>';
        } else {
            // Fetch the hashed admin password from the database
            $fetchAdminPasswordSql = "SELECT password FROM admin WHERE email = '$adminEmail'";
            $fetchAdminPasswordResult = mysqli_query($conn, $fetchAdminPasswordSql);

            if ($fetchAdminPasswordResult) {
                $adminData = mysqli_fetch_assoc($fetchAdminPasswordResult);
                $hashedAdminPassword = $adminData['password'];

                // Check if the entered admin password is correct
                if (password_verify($adminPassword, $hashedAdminPassword)) {
                    // Check if the author is already unblocked
                    $checkUnblockSql = "SELECT blocked FROM authors WHERE email = ?";
                    $stmt = mysqli_prepare($conn, $checkUnblockSql);
                    mysqli_stmt_bind_param($stmt, "s", $email);
                    mysqli_stmt_execute($stmt);

                    $checkUnblockResult = mysqli_stmt_get_result($stmt);

                    $userData = mysqli_fetch_assoc($checkUnblockResult);
                    $isBlocked = $userData['blocked'];

                    if ($isBlocked == 0) {
                        echo '<script>alert("Author with email ' . $email . ' is already unblocked.");</script>';
                    } else {
                        // Author is not admin and not already unblocked, proceed with unblocking
                        $success = aunBlock($conn, $email);

                        if ($success) {
                            echo "<script>alert('Author with email ' . $email . ' has been unblocked successfully.'); document.location.href = 'add-account.php';</script>";
                        } else {
                            echo '<script>alert("Failed to unblock the author.");</script>';
                        }
                    }
                } else {
                    echo '<script>alert("Incorrect admin password.");</script>';
                }
            } else {
                echo '<script>alert("Error fetching admin password.");</script>';
            }
        }
    }
}

// Handle multiple deletion
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_selected"])) {
    if (isset($_POST["selectedEmails"]) && is_array($_POST["selectedEmails"])) {
        foreach ($_POST["selectedEmails"] as $selectedEmail) {
            $deleteSql = "DELETE FROM authors WHERE email = '$selectedEmail'";
            mysqli_query($conn, $deleteSql);
        }
        echo "<script>alert('Selected Authors Deleted Successfully'); document.location.href = 'add-account.php';</script>";
    } else {
        echo "<script>alert('No authors selected for deletion');</script>";
    }
}

echo "</form>";
echo "</div>";

// Similar logic for editors and reviewers can be implemented below
function eUnBlock($conn, $email)
{
    try {
        $resetSql = "UPDATE editors SET blocked = 0, attempts = 0 WHERE email = '$email'";
        return mysqli_query($conn, $resetSql);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

// Check if the search form is submitted
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["searchEmail"])) {
    $searchEmail = mysqli_real_escape_string($conn, $_GET["searchEmail"]);
    $sql = "SELECT * FROM editors WHERE email LIKE '%$searchEmail%'";
} else {
    // If the search form is not submitted, retrieve all editors
    $sql = "SELECT * FROM editors";
}

$result = mysqli_query($conn, $sql);

echo "<div class='view'>";

echo "<h2 class='text2'> EDITORS LIST </h2>";

// Search Form
echo "<form method='get' action='add-account.php'>";
echo "<label for='searchEmail' class='text5'>Search Email: </label>";
echo "<input type='email' name='searchEmail' class='searchtxt' id='searchEmail' placeholder='Enter email address' required>";
echo "<button type='submit' class='btnSearch'>Search</button>";
echo "</form>";

echo "<form method='POST'>";
echo "<table border=1 cellspacing=0 cellpadding=10 class='viewTable'>";
echo "<tr class='thView'>";
echo "<th>Username</th>";
echo "<th>Email</th>";
echo "<th>Attempts</th>";
echo "<th>Blocked</th>";
echo "<th>Action</th>";
echo "</tr>";

while ($row = mysqli_fetch_array($result)) {
    $temp0 = $row['name'];
    $temp1 = $row['email'];
    $temp2 = $row['attempts'];
    $temp3 = $row['blocked'];

    echo "<tr><td>$temp0</td>";
    echo "<td>$temp1</td>";
    echo "<td>$temp2</td>";
    echo "<td>$temp3</td>";
    echo "<td> <input type='checkbox' name='selectedEmails[]' value='$temp1'></td></tr>";
}

echo "</table>";
echo "<button type='submit' name='delete_selected' class='deletebtn'>Delete</button>";
echo "</form>";

// Handle editor unblocking
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"]) && isset($_POST["txtadmin"])) {
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $adminPassword = $_POST["txtadmin"];

    // Check if the email exists in the database
    $checkEmailSql = "SELECT COUNT(*) AS count FROM editors WHERE email = '$email'";
    $checkEmailResult = mysqli_query($conn, $checkEmailSql);
    $emailCount = mysqli_fetch_assoc($checkEmailResult)['count'];

    if ($emailCount == 0) {
        //echo '<script>alert("Email does not exist. Cannot unblock.");</script>';
    } else {
        // Proceed with unblocking process
        // Check if the email is admin's email
        $adminEmail = "admin@gmail.com";
        if ($email === $adminEmail) {
            echo '<script>alert("Cannot unblock the admin account.");</script>';
        } else {
            // Fetch the hashed admin password from the database
            $fetchAdminPasswordSql = "SELECT password FROM admin WHERE email = '$adminEmail'";
            $fetchAdminPasswordResult = mysqli_query($conn, $fetchAdminPasswordSql);

            if ($fetchAdminPasswordResult) {
                $adminData = mysqli_fetch_assoc($fetchAdminPasswordResult);
                $hashedAdminPassword = $adminData['password'];

                // Check if the entered admin password is correct
                if (password_verify($adminPassword, $hashedAdminPassword)) {
                    // Check if the editor is already unblocked
                    $checkUnblockSql = "SELECT blocked FROM editors WHERE email = ?";
                    $stmt = mysqli_prepare($conn, $checkUnblockSql);
                    mysqli_stmt_bind_param($stmt, "s", $email);
                    mysqli_stmt_execute($stmt);

                    $checkUnblockResult = mysqli_stmt_get_result($stmt);

                    $userData = mysqli_fetch_assoc($checkUnblockResult);
                    $isBlocked = $userData['blocked'];

                    if ($isBlocked == 0) {
                        echo '<script>alert("Editor with email ' . $email . ' is already unblocked.");</script>';
                    } else {
                        // Editor is not admin and not already unblocked, proceed with unblocking
                        $success = eUnBlock($conn, $email);

                        if ($success) {
                            echo "<script>alert('Editor with email ' . $email . ' has been unblocked successfully.'); document.location.href = 'add-account.php';</script>";
                        } else {
                            echo '<script>alert("Failed to unblock the editor.");</script>';
                        }
                    }
                } else {
                    echo '<script>alert("Incorrect admin password.");</script>';
                }
            } else {
                echo '<script>alert("Error fetching admin password.");</script>';
            }
        }
    }
}

// Handle multiple deletion
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_selected"])) {
    if (isset($_POST["selectedEmails"]) && is_array($_POST["selectedEmails"])) {
        foreach ($_POST["selectedEmails"] as $selectedEmail) {
            $deleteSql = "DELETE FROM editors WHERE email = '$selectedEmail'";
            mysqli_query($conn, $deleteSql);
        }
        echo "<script>alert('Selected Editors Deleted Successfully'); document.location.href = 'add-account.php';</script>";
    } else {
        echo "<script>alert('No editors selected for deletion');</script>";
    }
}

echo "</form>";
echo "</div>";

// Similar logic for reviewers can be implemented below
function rUnBlock($conn, $email)
{
    try {
        $resetSql = "UPDATE reviewers SET blocked = 0, attempts = 0 WHERE email = '$email'";
        return mysqli_query($conn, $resetSql);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

// Check if the search form is submitted
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["searchEmail"])) {
    $searchEmail = mysqli_real_escape_string($conn, $_GET["searchEmail"]);
    $sql = "SELECT * FROM reviewers WHERE email LIKE '%$searchEmail%'";
} else {
    // If the search form is not submitted, retrieve all reviewers
    $sql = "SELECT * FROM reviewers";
}

$result = mysqli_query($conn, $sql);

echo "<div class='view'>";

echo "<h2 class='text2'> REVIEWERS LIST </h2>";

// Search Form
echo "<form method='get' action='add-account.php'>";
echo "<label for='searchEmail' class='text5'>Search Email: </label>";
echo "<input type='email' name='searchEmail' class='searchtxt' id='searchEmail' placeholder='Enter email address' required>";
echo "<button type='submit' class='btnSearch'>Search</button>";
echo "</form>";

echo "<form method='POST'>";
echo "<table border=1 cellspacing=0 cellpadding=10 class='viewTable'>";
echo "<tr class='thView'>";
echo "<th>Username</th>";
echo "<th>Email</th>";
echo "<th>Attempts</th>";
echo "<th>Blocked</th>";
echo "<th>Action</th>";
echo "</tr>";

while ($row = mysqli_fetch_array($result)) {
    $temp0 = $row['name'];
    $temp1 = $row['email'];
    $temp2 = $row['attempts'];
    $temp3 = $row['blocked'];

    echo "<tr><td>$temp0</td>";
    echo "<td>$temp1</td>";
    echo "<td>$temp2</td>";
    echo "<td>$temp3</td>";
    echo "<td> <input type='checkbox' name='selectedEmails[]' value='$temp1'></td></tr>";
}

echo "</table>";
echo "<button type='submit' name='delete_selected' class='deletebtn'>Delete</button>";
echo "</form>";

// Handle reviewer unblocking
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"]) && isset($_POST["txtadmin"])) {
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $adminPassword = $_POST["txtadmin"];

    // Check if the email exists in the database
    $checkEmailSql = "SELECT COUNT(*) AS count FROM reviewers WHERE email = '$email'";
    $checkEmailResult = mysqli_query($conn, $checkEmailSql);
    $emailCount = mysqli_fetch_assoc($checkEmailResult)['count'];

    if ($emailCount == 0) {
        //echo '<script>alert("Email does not exist. Cannot unblock.");</script>';
    } else {
        // Proceed with unblocking process
        // Check if the email is admin's email
        $adminEmail = "admin@gmail.com";
        if ($email === $adminEmail) {
            echo '<script>alert("Cannot unblock the admin account.");</script>';
        } else {
            // Fetch the hashed admin password from the database
            $fetchAdminPasswordSql = "SELECT password FROM admin WHERE email = '$adminEmail'";
            $fetchAdminPasswordResult = mysqli_query($conn, $fetchAdminPasswordSql);

            if ($fetchAdminPasswordResult) {
                $adminData = mysqli_fetch_assoc($fetchAdminPasswordResult);
                $hashedAdminPassword = $adminData['password'];

                // Check if the entered admin password is correct
                if (password_verify($adminPassword, $hashedAdminPassword)) {
                    // Check if the reviewer is already unblocked
                    $checkUnblockSql = "SELECT blocked FROM reviewers WHERE email = ?";
                    $stmt = mysqli_prepare($conn, $checkUnblockSql);
                    mysqli_stmt_bind_param($stmt, "s", $email);
                    mysqli_stmt_execute($stmt);

                    $checkUnblockResult = mysqli_stmt_get_result($stmt);

                    $userData = mysqli_fetch_assoc($checkUnblockResult);
                    $isBlocked = $userData['blocked'];

                    if ($isBlocked == 0) {
                        echo '<script>alert("Reviewer with email ' . $email . ' is already unblocked.");</script>';
                    } else {
                        // Reviewer is not admin and not already unblocked, proceed with unblocking
                        $success = rUnBlock($conn, $email);

                        if ($success) {
                            echo "<script>alert('Reviewer with email ' . $email . ' has been unblocked successfully.'); document.location.href = 'add-account.php';</script>";
                        } else {
                            echo '<script>alert("Failed to unblock the reviewer.");</script>';
                        }
                    }
                } else {
                    echo '<script>alert("Incorrect admin password.");</script>';
                }
            } else {
                echo '<script>alert("Error fetching admin password.");</script>';
            }
        }
    }
}

// Handle multiple deletion
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_selected"])) {
    if (isset($_POST["selectedEmails"]) && is_array($_POST["selectedEmails"])) {
        foreach ($_POST["selectedEmails"] as $selectedEmail) {
            $deleteSql = "DELETE FROM reviewers WHERE email = '$selectedEmail'";
            mysqli_query($conn, $deleteSql);
        }
        echo "<script>alert('Selected Reviewers Deleted Successfully'); document.location.href = 'add-account.php';</script>";
    } else {
        echo "<script>alert('No reviewers selected for deletion');</script>";
    }
}

echo "</form>";
echo "</div>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE, edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ACCOUNT MANAGEMENT</title>
</head>
<body>
    <div class="block">
    <h2>UNBLOCK USER</h2>

    <!-- Unblock Form -->
    <form method="post" action="add-account.php">
        <label for="email">Email: </label>
        <input type="email" id="email" name="email"  placeholder='Enter email address' required>

        <label for="txtadmin">Admin Password: </label>
        <input type="password" id="txtadmin" name="txtadmin" placeholder='Enter admin password' required>

        <button type="submit" >Unblock User</button>
    </form>
    </div>
    <script>
        function confirmDelete(email) {
            return confirm("Do you want to delete the email: " + email + "?");
        }
    </script>
</body>
</html>
