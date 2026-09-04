<?php

session_start();

include "../config/database.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../Authorization/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Check if entry ID is provided
if (!isset($_GET["id"])) {
    header("Location: ../Dashboard/dashboard.php");
    exit();
}

$entry_id = $_GET["id"];

// Delete only the entry belonging to the logged-in user
$stmt = mysqli_prepare(
    $conn,
    "DELETE FROM entries WHERE id = ? AND user_id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $entry_id,
    $user_id
);

if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);

    header("Location: ../Dashboard/dashboard.php");
    exit();

} else {

    echo "Error deleting entry: " . mysqli_stmt_error($stmt);

    mysqli_stmt_close($stmt);
}

?>