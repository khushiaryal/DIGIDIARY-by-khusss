```php
<?php

session_start();

include "../config/database.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../Authorization/login.php");
    exit();
}

// Logged-in user's information
$user_id = $_SESSION["user_id"];
$user_name = $_SESSION["user_name"];
$user_email = $_SESSION["user_email"];


// ==========================================
// TIME-BASED GREETING
// ==========================================

date_default_timezone_set("Asia/Kathmandu");

$current_hour = date("H");

if ($current_hour >= 5 && $current_hour < 12) {

    $greeting = "Good morning";
    $greeting_icon = "☀️";

} elseif ($current_hour >= 12 && $current_hour < 17) {

    $greeting = "Good afternoon";
    $greeting_icon = "🌤️";

} elseif ($current_hour >= 17 && $current_hour < 19) {

    $greeting = "Good evening";
    $greeting_icon = "🌙";

} else {

    $greeting = "Good night";
    $greeting_icon = "✨";

}


// ==========================================
// GET USER'S DIARY ENTRIES
// ==========================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT *
     FROM entries
     WHERE user_id = ?
     ORDER BY entry_date DESC, id DESC"
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

    <title>DIGIDIARY - Dashboard</title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

</head>


<body>


<div class="dashboard">


    <!-- ==========================================
         SHARED SIDEBAR
         ========================================== -->

    <?php include "../includes/sidebar.php"; ?>


    <!-- ==========================================
         MAIN CONTENT
         ========================================== -->

    <main class="main-content">


        <!-- ==========================================
             TOP BAR
             ========================================== -->

        <header class="topbar">


            <div>

                <p class="small-text">

                    <i>
                        Your thoughts. Your memories. Your story.
                    </i>

                </p>


                <h2>

                    <?php echo $greeting; ?>,

                    <?php
                    echo htmlspecialchars($user_name);
                    ?>

                    <?php echo $greeting_icon; ?>

                </h2>

            </div>


            <!-- PROFILE -->

            <div class="profile">


                <div class="profile-avatar">

                    <?php

                    echo strtoupper(
                        substr($user_name, 0, 1)
                    );

                    ?>

                </div>


                <div>

                    <strong>

                        <?php

                        echo htmlspecialchars(
                            $user_name
                        );

                        ?>

                    </strong>


                    <small>

                        <?php

                        echo htmlspecialchars(
                            $user_email
                        );

                        ?>

                    </small>

                </div>


            </div>


        </header>


        <!-- ==========================================
             WELCOME CARD
             ========================================== -->

        <section class="welcome-card">


            <div>

                <p class="welcome-label">
                    Welcome back!!!
                </p>


                <h1>
                    How was your day?
                </h1>


                <p>
                    Take a moment to write down your thoughts,
                    feelings and memories.
                </p>

            </div>


            <a
                href="../entries/newentries.php"
                class="primary-button"
            >
                ✍️ Write Today's Entry
            </a>


        </section>


        <!-- ==========================================
             DASHBOARD CARDS
             ========================================== -->

        <section class="dashboard-grid">


            <!-- TODAY'S MOOD -->

            <div class="dashboard-card mood-card">


                <div class="card-icon">
                    😊
                </div>


                <div>

                    <p class="card-label">
                        Today's Mood
                    </p>


                    <h3>
                        Keep smiling
                    </h3>


                    <p>
                        Your feelings matter.
                    </p>

                </div>


            </div>


            <!-- ON THIS DAY -->

            <div class="dashboard-card memory-card">


                <div class="card-icon">
                    🌷
                </div>


                <div>

                    <p class="card-label">
                        On This Day
                    </p>


                    <h3>
                        Create a memory
                    </h3>


                    <p>
                        Your memories live here.
                    </p>

                </div>


            </div>


            <!-- TOTAL ENTRIES -->

            <div class="dashboard-card">


                <div class="card-icon">
                    📔
                </div>


                <div>

                    <p class="card-label">
                        My Entries
                    </p>


                    <h3>

                        <?php

                        echo mysqli_num_rows(
                            $result
                        );

                        ?>

                    </h3>


                    <p>
                        Diary entries so far
                    </p>

                </div>


            </div>


        </section>


        <!-- ==========================================
             RECENT ENTRIES
             ========================================== -->

        <section class="entries-section">


            <div class="section-heading">


                <div>

                    <p class="small-text">
                        Your memories
                    </p>


                    <h2>
                        Recent Entries 📔
                    </h2>

                </div>


                <a
                    href="../entries/newentries.php"
                    class="secondary-button"
                >
                    + New Entry
                </a>


            </div>


            <!-- ENTRY GRID -->

            <div class="entries-grid">


                <?php

                if (mysqli_num_rows($result) > 0) {

                    while (
                        $entry = mysqli_fetch_assoc($result)
                    ) {

                ?>


                        <!-- ENTRY CARD -->

                        <article class="diary-card">


                            <!-- ENTRY TOP -->

                            <div class="diary-card-top">


                                <span class="mood-tag">

                                    <?php

                                    echo htmlspecialchars(
                                        $entry["mood"]
                                    );

                                    ?>

                                </span>


                                <span class="entry-date">

                                    <?php

                                    echo htmlspecialchars(
                                        $entry["entry_date"]
                                    );

                                    ?>

                                </span>


                            </div>


                            <!-- TITLE -->

                            <h3>

                                <?php

                                echo htmlspecialchars(
                                    $entry["title"]
                                );

                                ?>

                            </h3>


                            <!-- CONTENT -->

                            <p class="diary-content">

                                <?php

                                echo nl2br(
                                    htmlspecialchars(
                                        $entry["content"]
                                    )
                                );

                                ?>

                            </p>


                            <!-- ACTIONS -->

                            <div class="diary-card-actions">


                                <!-- EDIT -->

                                <a
                                    href="../entries/editentries.php?id=<?php echo $entry["id"]; ?>"
                                    class="edit-button"
                                >
                                    ✏️ Edit
                                </a>


                                <!-- DELETE -->

                                <a
                                    href="../entries/deleteentries.php?id=<?php echo $entry["id"]; ?>"
                                    class="delete-button"
                                    onclick="return confirm('Are you sure you want to delete this entry?');"
                                >
                                    🗑️ Delete
                                </a>


                            </div>


                        </article>


                <?php

                    }

                } else {

                ?>


                    <!-- EMPTY STATE -->

                    <div class="empty-state">


                        <div class="empty-icon">
                            📖
                        </div>


                        <h3>
                            Your diary is waiting for its first story.
                        </h3>


                        <p>
                            Start writing and turn your thoughts
                            into beautiful memories.
                        </p>


                        <a
                            href="../entries/newentries.php"
                            class="primary-button"
                        >
                            ✍️ Write My First Entry
                        </a>


                    </div>


                <?php

                }

                ?>


            </div>


        </section>


    </main>


</div>


</body>

</html>


<?php

mysqli_stmt_close($stmt);

?>

