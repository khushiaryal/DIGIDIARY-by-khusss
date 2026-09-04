<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Basic validation
    if (empty($name) || empty($email) || empty($password)) {

        $message = "Please fill in all fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";

    } elseif (strlen($password) < 6) {

        $message = "Password must be at least 6 characters long.";

    } else {

        // Check if email already exists
        $check_stmt = mysqli_prepare(
            $conn,
            "SELECT ID FROM users WHERE email = ?"
        );

        mysqli_stmt_bind_param(
            $check_stmt,
            "s",
            $email
        );

        mysqli_stmt_execute($check_stmt);

        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) > 0) {

            $message = "This email is already registered.";

            mysqli_stmt_close($check_stmt);

        } else {

            mysqli_stmt_close($check_stmt);

            // Hash password
            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Insert user
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO users (name, email, password)
                 VALUES (?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sss",
                $name,
                $email,
                $hashed_password
            );

            if (mysqli_stmt_execute($stmt)) {

                $message = "Registration successful! 🎉";

            } else {

                $message = "Registration failed: " . mysqli_stmt_error($stmt);

            }

            mysqli_stmt_close($stmt);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>DIGIDIARY - Register</title>

    <link rel="stylesheet" href="../css/register.css">

</head>

<body>

    <div class="register-container">

        <div class="register-card">

            <!-- DigiDiary Branding -->

            <div class="brand">

                <h1>DIGIDIARY</h1>

                <p>by khusss</p>

            </div>


            <!-- Welcome -->

            <div class="welcome">

                <h2>Create Your Account</h2>

                <p>Your little corner of the world awaits ✨</p>

            </div>


            <!-- Message -->

            <?php if ($message != ""): ?>

                <div class="message">

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php endif; ?>


            <!-- Registration Form -->

            <form method="POST">

                <div class="input-group">

                    <label for="name">Name</label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="Enter your name"
                        required
                    >

                </div>


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
                        placeholder="Create a password"
                        required
                    >

                </div>


                <button type="submit">

                    Create Account

                </button>

            </form>


            <!-- Login Link -->

            <div class="login-link">

                Already have an account?

                <a href="login.php">Login</a>

            </div>

        </div>

    </div>

</body>

</html>