```php
<?php

session_start();

include "../config/database.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../Authorization/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$user_name = $_SESSION["user_name"];

$message = "";


/* ==========================================
   SAVE NEW DIARY ENTRY
   ========================================== */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);
    $mood = trim($_POST["mood"]);
    $entry_date = $_POST["entry_date"];


    // Basic validation

    if (
        empty($title) ||
        empty($content) ||
        empty($mood) ||
        empty($entry_date)
    ) {

        $message = "Please fill in all fields.";

    } else {

        // Secure INSERT using prepared statement

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO entries
            (user_id, title, content, mood, entry_date)
            VALUES (?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "issss",
            $user_id,
            $title,
            $content,
            $mood,
            $entry_date
        );

        if (mysqli_stmt_execute($stmt)) {

            $message = "Diary entry saved successfully! 💚";

        } else {

            $message =
                "Error saving entry: "
                . mysqli_stmt_error($stmt);

        }

        mysqli_stmt_close($stmt);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>DIGIDIARY - New Entry</title>

    <!-- SIDEBAR CSS -->
    <link
        rel="stylesheet"
        href="../css/sidebar.css"
    >

    <!-- NEW ENTRY CSS -->
    <link
        rel="stylesheet"
        href="../css/newentry.css"
    >

</head>


<body>


<!-- ==========================================
     FIXED SIDEBAR
     ========================================== -->

<?php include "../includes/sidebar.php"; ?>


<!-- ==========================================
     MAIN CONTENT
     ========================================== -->

<div class="main-content">


    <div class="new-entry-page">


        <!-- ==========================================
             HEADER
             ========================================== -->

        <header class="entry-header">



        </header>



        <!-- ==========================================
             MAIN CONTENT
             ========================================== -->

        <main class="entry-main">


            <!-- INTRO -->

            <div class="entry-intro">

                <p class="intro-small">
                    <i>
                        A little space for you 👼
                    </i>
                </p>


                <h2>
                    Write Your Story
                </h2>


                <p>
                    <i>
                        Take a moment, breathe, and put your thoughts
                        into words.
                    </i>
                </p>

            </div>



            <!-- ==========================================
                 ENTRY CARD
                 ========================================== -->

            <section class="entry-card">


                <!-- MESSAGE -->

                <?php if ($message != "") { ?>

                    <div class="success-message">

                        <?php
                        echo htmlspecialchars($message);
                        ?>

                    </div>

                <?php } ?>



                <!-- FORM -->

                <form method="POST">


                    <!-- TITLE -->

                    <div class="form-group">

                        <label for="title">
                            Title
                        </label>


                        <input
                            type="text"
                            id="title"
                            name="title"
                            placeholder="Give today's memory a title..."
                            required
                        >

                    </div>



                    <!-- DATE -->

                    <div class="form-group">

                        <label for="entry_date">
                            Date
                        </label>


                        <input
                            type="date"
                            id="entry_date"
                            name="entry_date"
                            value="<?php echo date('Y-m-d'); ?>"
                            required
                        >

                    </div>



                    <!-- MOOD -->

                    <div class="form-group">

                        <label for="mood">
                            How are you feeling?
                        </label>


                        <select
                            id="mood"
                            name="mood"
                            required
                        >

                            <option value="">
                                Select your mood
                            </option>


                            <option value="Happy">
                                😊 Happy
                            </option>


                            <option value="Excited">
                                🤩 Excited
                            </option>


                            <option value="Calm">
                                😌 Calm
                            </option>


                            <option value="Sad">
                                😔 Sad
                            </option>


                            <option value="Angry">
                                😡 Angry
                            </option>


                            <option value="Anxious">
                                😰 Anxious
                            </option>


                            <option value="Loved">
                                🥰 Loved
                            </option>


                            <option value="Tired">
                                😴 Tired
                            </option>


                        </select>

                    </div>



                    <!-- THOUGHTS -->

                    <div class="form-group">

                        <label for="content">
                            Your Thoughts
                        </label>


                        <textarea
                            id="content"
                            name="content"
                            rows="10"
                            placeholder="Dear diary, today..."
                            required
                        ></textarea>

                    </div>



                    <!-- BUTTONS -->

                    <div class="form-actions">


                        <!-- CANCEL -->

                        <a
                            href="/DIGIDIARY/dashboard.php"
                            class="cancel-button"
                        >
                            Cancel
                        </a>


                        <!-- SAVE -->

                        <button
                            type="submit"
                            class="save-button"
                        >
                            Save Entry 💫
                        </button>


                    </div>


                </form>


            </section>



            <!-- BOTTOM NOTE -->

            <p class="bottom-note">
                Your thoughts deserve a safe little place 🌷
            </p>


        </main>


    </div>


</div>


</body>

</html>
