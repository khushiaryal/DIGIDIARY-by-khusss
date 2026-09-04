<?php

session_start();

// Destroy all session data
session_unset();
session_destroy();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>DIGIDIARY - Logged Out</title>

    <link rel="stylesheet" href="../css/logout.css">

</head>

<body>

    <div class="logout-container">

        <div class="logout-card">

            <div class="brand">

                <h1>DIGIDIARY</h1>

                <p>by khusss</p>

            </div>

            <div class="logout-icon">
                ✦
            </div>

            <h2>See You Soon</h2>

            <p class="goodbye">
                You've been logged out successfully.
            </p>

            <p class="diary-message">
                Your little memories will be waiting for you. 🌿
            </p>

            <a href="login.php" class="login-button">
                Login Again
            </a>

        </div>

    </div>

</body>

</html>