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


// ==========================================
// GET DIARY ENTRIES
// ==========================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT ID, title, content, mood, entry_date
     FROM entries
     WHERE user_id = ?
     ORDER BY entry_date DESC, ID DESC"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>DIGIDIARY - My Diary</title>


    <!-- ==========================================
         SIDEBAR CSS
         ========================================== -->

    <link
        rel="stylesheet"
        href="/DIGIDIARY/css/sidebar.css"
    >


    <!-- ==========================================
         MY DIARY CSS
         ========================================== -->

    <link
        rel="stylesheet"
        href="/DIGIDIARY/css/mydiary.css"
    >

</head>


<body>


<!-- ==========================================
     SHARED SIDEBAR
     ========================================== -->

<?php include "../includes/sidebar.php"; ?>


<!-- ==========================================
     MAIN CONTENT
     ========================================== -->

<div class="main-content">


    <div class="diary-page">


        <!-- ==========================================
             HEADER
             ========================================== -->

        <header class="diary-header">

          

        </header>


        <!-- ==========================================
             MAIN DIARY
             ========================================== -->

        <main class="diary-main">


            <!-- ==========================================
                 INTRO
                 ========================================== -->

            <div class="diary-intro">

                <p class="intro-small">
                    A collection of your little moments 🌷
                </p>

                <h2>
                    My Diary
                </h2>

                <p>
                    Your thoughts, memories, and feelings — all in one place.
                </p>

            </div>


            <!-- ==========================================
                 ENTRIES
                 ========================================== -->

            <section class="entries-container">


                <?php if (mysqli_num_rows($result) > 0): ?>


                    <?php while ($entry = mysqli_fetch_assoc($result)): ?>


                        <article class="entry-card">


                            <!-- ==========================================
                                 ENTRY TOP
                                 ========================================== -->

                            <div class="entry-top">


                                <div>

                                    <h3>

                                        <?php
                                        echo htmlspecialchars(
                                            $entry["title"]
                                        );
                                        ?>

                                    </h3>


                                    <p class="entry-date">

                                        <?php
                                        echo date(
                                            "F d, Y",
                                            strtotime($entry["entry_date"])
                                        );
                                        ?>

                                    </p>

                                </div>


                                <!-- ==========================================
                                     MOOD
                                     ========================================== -->

                                <?php if (!empty($entry["mood"])): ?>

                                    <span class="mood">

                                        <?php
                                        echo htmlspecialchars(
                                            $entry["mood"]
                                        );
                                        ?>

                                    </span>

                                <?php endif; ?>


                            </div>


                            <!-- ==========================================
                                 ENTRY CONTENT
                                 ========================================== -->

                            <div class="entry-content">

                                <?php

                                $preview = htmlspecialchars(
                                    $entry["content"]
                                );

                                if (strlen($preview) > 250) {

                                    $preview =
                                        substr($preview, 0, 250)
                                        . "...";

                                }

                                echo nl2br($preview);

                                ?>

                            </div>


                            <!-- ==========================================
                                 ENTRY ACTIONS
                                 ========================================== -->

                            <div class="entry-actions">


                                <!-- READ -->

                                <a
                                    href="viewentry.php?id=<?php echo $entry["ID"]; ?>"
                                    class="view-button"
                                >
                                    Read Entry
                                </a>


                                <!-- EDIT -->

                                <a
                                    href="editentries.php?id=<?php echo $entry["ID"]; ?>"
                                    class="edit-button"
                                >
                                    Edit
                                </a>


                                <!-- DELETE -->

                                <a
                                    href="deleteentries.php?id=<?php echo $entry["ID"]; ?>"
                                    class="delete-button"
                                    onclick="return confirm('Are you sure you want to delete this entry?');"
                                >
                                    Delete
                                </a>


                            </div>


                        </article>


                    <?php endwhile; ?>


                <?php else: ?>


                    <!-- ==========================================
                         EMPTY DIARY
                         ========================================== -->

                    <div class="empty-diary">


                        <div class="empty-icon">
                            ✦
                        </div>


                        <h3>
                            Your diary is waiting...
                        </h3>


                        <p>
                            You haven't written anything yet.
                            Start with your first little memory. 🌿
                        </p>


                        <a
                            href="/DIGIDIARY/entries/newentries.php"
                            class="start-button"
                        >
                            Write Your First Entry
                        </a>


                    </div>


                <?php endif; ?>


            </section>


        </main>


    </div>


</div>


</body>

</html>


<?php

mysqli_stmt_close($stmt);

?>