<?php

session_start();

include "../config/database.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../Authorization/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];


// ==========================================
// CHECK ENTRY ID
// ==========================================

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: mydiary.php");
    exit();
}

$entry_id = intval($_GET["id"]);


// ==========================================
// GET ENTRY
// ==========================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT ID, title, content, mood, entry_date
     FROM entries
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


// ==========================================
// CHECK IF ENTRY EXISTS
// ==========================================

if (mysqli_num_rows($result) !== 1) {

    mysqli_stmt_close($stmt);

    header("Location: mydiary.php");
    exit();
}

$entry = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?php echo htmlspecialchars($entry["title"]); ?> - DIGIDIARY
    </title>


    <!-- SIDEBAR CSS -->

    <link
        rel="stylesheet"
        href="/DIGIDIARY/css/sidebar.css"
    >


    <!-- VIEW ENTRY CSS -->

    <link
        rel="stylesheet"
        href="/DIGIDIARY/css/viewentry.css"
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


    <div class="view-page">


        <!-- ==========================================
             HEADER
             ========================================== -->

        <header class="view-header">


            <a
                href="mydiary.php"
                class="back-button"
            >
                ← My Diary
            </a>


            <a
                href="/DIGIDIARY/entries/newentries.php"
                class="new-entry-button"
            >
                + New Entry
            </a>


        </header>


        <!-- ==========================================
             ENTRY
             ========================================== -->

        <main class="view-main">


            <article class="entry-view-card">


                <!-- ==========================================
                     ENTRY HEADER
                     ========================================== -->

                <div class="entry-heading">


                    <p class="entry-date">

                        <?php

                        echo date(
                            "F d, Y",
                            strtotime($entry["entry_date"])
                        );

                        ?>

                    </p>


                    <h1>

                        <?php

                        echo htmlspecialchars(
                            $entry["title"]
                        );

                        ?>

                    </h1>


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

                    echo nl2br(
                        htmlspecialchars(
                            $entry["content"]
                        )
                    );

                    ?>

                </div>


                <!-- ==========================================
                     ACTIONS
                     ========================================== -->

                <div class="entry-actions">


                    <a
                        href="editentries.php?id=<?php echo $entry["ID"]; ?>"
                        class="edit-button"
                    >
                        Edit Entry
                    </a>


                    <a
                        href="deleteentries.php?id=<?php echo $entry["ID"]; ?>"
                        class="delete-button"
                        onclick="return confirm('Are you sure you want to delete this entry?');"
                    >
                        Delete Entry
                    </a>


                </div>


            </article>


        </main>


    </div>


</div>


</body>

</html>