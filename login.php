<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include "../config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Find user using a prepared statement
    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM users WHERE email = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "s",
        $email
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        // Check password
        if (password_verify($password, $user["password"])) {

            // Create login session
            $_SESSION["user_id"] = $user["ID"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["user_email"] = $user["email"];

            mysqli_stmt_close($stmt);

            // Go to Dashboard
            header("Location: ../Dashboard/dashboard.php");
            exit();

        } else {

            $message = "Incorrect password.";

        }

    } else {

        $message = "User not found.";

    }

    mysqli_stmt_close($stmt);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>DIGIDIARY - Login</title>

    <link rel="stylesheet" href="../css/login.css">

</head>

<body>

    <div class="login-container">

        <div class="login-card">

            <!-- DigiDiary Branding -->

            <div class="brand">

                <h1>DIGIDIARY</h1>

                <p>by khusss</p>

            </div>


            <!-- Welcome -->

            <div class="welcome">

                <h2>Welcome Back</h2>

                <p>Your little diary is waiting for you ✨</p>

            </div>


            <!-- Error Message -->

            <?php if ($message != ""): ?>

                <div class="message">

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php endif; ?>


            <!-- Login Form -->

            <form method="POST">

                <div class="input-group">

                    <label for="email">Email</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your email"
                        required
                    >

                </div>


                <div class="input-group">

                    <label for="password">Password</label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                    >

                </div>


                <button type="submit">

                    Login

                </button>

            </form>


            <!-- Register Link -->

            <div class="register-link">

                Don't have an account?

                <a href="register.php">Create Account</a>

            </div>

        </div>

    </div>

</body>

</html>