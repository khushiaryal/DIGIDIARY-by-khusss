<?php

session_start();

include "../config/database.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../Authorization/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Check if entry ID exists
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: mydiary.php");
    exit();
}

$entry_id = intval($_GET["id"]);

$message = "";


/* ==========================================
   GET EXISTING ENTRY
   ========================================== */

$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM entries
     WHERE ID = ? AND user_id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $entry_id,
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) != 1) {

    mysqli_stmt_close($stmt);

    header("Location: mydiary.php");
    exit();
}

$entry = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


/* ==========================================
   UPDATE ENTRY
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

        // Update using prepared statement

        $update_stmt = mysqli_prepare(
            $conn,
            "UPDATE entries
             SET title = ?,
                 content = ?,
                 mood = ?,
                 entry_date = ?
             WHERE ID = ? AND user_id = ?"
        );

        mysqli_stmt_bind_param(
            $update_stmt,
            "ssssii",
            $title,
            $content,
            $mood,
            $entry_date,
            $entry_id,
            $user_id
        );


        if (mysqli_stmt_execute($update_stmt)) {

            mysqli_stmt_close($update_stmt);

            // Go back to My Diary

            header("Location: mydiary.php");
            exit();

        } else {

            $message =
                "Error updating entry: "
                . mysqli_stmt_error($update_stmt);

            mysqli_stmt_close($update_stmt);
        }
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

    <title>DIGIDIARY - Edit Entry</title>

    <link
        rel="stylesheet"
        href="../css/newentry.css"
    >

</head>


<body>

<div class="new-entry-page">


    <!-- ==========================================
         HEADER
         ========================================== -->

    <header class="entry-header">


        <a
            href="mydiary.php"
            class="back-button"
        >
            ← My Diary
        </a>


        <div class="brand">

            <h1>
                DIGIDIARY
            </h1>

            <p>
                by khusss
            </p>

        </div>


    </header>



    <!-- ==========================================
         MAIN
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
                Edit Your Story
            </h2>

            <p>
                <i>
                    Make changes, add thoughts,
                    and keep your memory just right.
                </i>
            </p>

        </div>



        <!-- ==========================================
             EDIT CARD
             ========================================== -->

        <section class="entry-card">


            <!-- ERROR MESSAGE -->

            <?php if ($message != "") { ?>

                <div class="error-message">

                    <?php
                    echo htmlspecialchars($message);
                    ?>

                </div>

            <?php } ?>



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
                        value="<?php
                        echo htmlspecialchars(
                            $entry["title"]
                        );
                        ?>"
                        placeholder="Give your memory a title..."
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
                        value="<?php
                        echo htmlspecialchars(
                            $entry["entry_date"]
                        );
                        ?>"
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

                        <option
                            value="Happy"
                            <?php
                            if ($entry["mood"] == "Happy") {
                                echo "selected";
                            }
                            ?>
                        >
                            😊 Happy
                        </option>

                        <option
                            value="Excited"
                            <?php
                            if ($entry["mood"] == "Excited") {
                                echo "selected";
                            }
                            ?>
                        >
                            🤩 Excited
                        </option>

                        <option
                            value="Calm"
                            <?php
                            if ($entry["mood"] == "Calm") {
                                echo "selected";
                            }
                            ?>
                        >
                            😌 Calm
                        </option>

                        <option
                            value="Sad"
                            <?php
                            if ($entry["mood"] == "Sad") {
                                echo "selected";
                            }
                            ?>
                        >
                            😔 Sad
                        </option>

                        <option
                            value="Angry"
                            <?php
                            if ($entry["mood"] == "Angry") {
                                echo "selected";
                            }
                            ?>
                        >
                            😡 Angry
                        </option>

                        <option
                            value="Anxious"
                            <?php
                            if ($entry["mood"] == "Anxious") {
                                echo "selected";
                            }
                            ?>
                        >
                            😰 Anxious
                        </option>

                        <option
                            value="Loved"
                            <?php
                            if ($entry["mood"] == "Loved") {
                                echo "selected";
                            }
                            ?>
                        >
                            🥰 Loved
                        </option>

                        <option
                            value="Tired"
                            <?php
                            if ($entry["mood"] == "Tired") {
                                echo "selected";
                            }
                            ?>
                        >
                            😴 Tired
                        </option>

                    </select>

                </div>



                <!-- CONTENT -->

                <div class="form-group">

                    <label for="content">
                        Your Thoughts
                    </label>

                    <textarea
                        id="content"
                        name="content"
                        rows="10"
                        placeholder="Dear diary..."
                        required
                    ><?php
                    echo htmlspecialchars(
                        $entry["content"]
                    );
                    ?></textarea>

                </div>



                <!-- BUTTONS -->

                <div class="form-actions">


                    <a
                        href="mydiary.php"
                        class="cancel-button"
                    >
                        Cancel
                    </a>


                    <button
                        type="submit"
                        class="save-button"
                    >
                        Save Changes 💫
                    </button>


                </div>


            </form>


        </section>



        <!-- BOTTOM NOTE -->

        <p class="bottom-note">

            Your memories are worth keeping 🌷

        </p>


    </main>


</div>

</body>

</html>