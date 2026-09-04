<?php

session_start();

include "../config/database.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../Authorization/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Get all diary entries of the logged-in user
$sql = "SELECT * FROM entries
        WHERE user_id = ?
        ORDER BY entry_date DESC, created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();


// ==========================================
// STORE ENTRIES ACCORDING TO MOOD
// ==========================================

$mood_entries = [];

while ($entry = $result->fetch_assoc()) {

    $mood = $entry["mood"];

    if (!isset($mood_entries[$mood])) {
        $mood_entries[$mood] = [];
    }

    $mood_entries[$mood][] = $entry;
}


// ==========================================
// MOOD ICONS
// ==========================================

$mood_icons = [
    "Happy"   => "😊",
    "Sad"     => "😢",
    "Angry"   => "😡",
    "Excited" => "🤩",
    "Calm"    => "😌",
    "Loved"   => "🥰",
    "Tired"   => "😴",
    "Anxious" => "😰"
];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Mood Tracker | DIGIDIARY</title>


    <!-- MAIN DIGIDIARY CSS -->

    <link
        rel="stylesheet"
        href="../css/style.css"
    >


    <!-- MOOD TRACKER CSS -->

    <link
        rel="stylesheet"
        href="moodtracker.css"
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

    <main class="main-content mood-container">


        <!-- ==========================================
             MOOD TRACKER HEADER
             ========================================== -->

        <header class="mood-header">

            <div>

                <h1>
                     <b>Mood Tracker</b>
                </h1>
                <p class="small-text">
                    <i>Your feelings. Your moments. Your story.</i>
                </p>
            </div>

        </header>


        <!-- ==========================================
             MOOD SECTIONS
             ========================================== -->

        <?php if (empty($mood_entries)): ?>


            <!-- ======================================
                 EMPTY STATE
                 ====================================== -->

            <div class="no-entries">

                <div class="no-entry-icon">
                    📖
                </div>

                <h2>
                    No diary entries yet
                </h2>

                <p>
                    Start writing your first diary entry
                    and choose a mood.
                </p>

                <a
                    href="../entries/newentries.php"
                    class="write-btn"
                >
                    ✍️ Write an Entry
                </a>

            </div>


        <?php else: ?>


            <!-- ======================================
                 DISPLAY MOODS
                 ====================================== -->

            <?php foreach ($mood_entries as $mood => $entries): ?>


                <section class="mood-section">


                    <!-- MOOD HEADER -->

                    <div class="mood-title">


                        <div class="mood-icon">

                            <?php

                            echo isset($mood_icons[$mood])
                                ? $mood_icons[$mood]
                                : "💭";

                            ?>

                        </div>


                        <div>

                            <h2>

                                <?php

                                echo htmlspecialchars($mood);

                                ?>

                            </h2>


                            <span>

                                <?php echo count($entries); ?>

                                <?php

                                echo count($entries) == 1
                                    ? " entry"
                                    : " entries";

                                ?>

                            </span>

                        </div>


                    </div>


                    <!-- ==================================
                         ENTRIES GRID
                         ================================== -->

                    <div class="mood-entries-grid">


                        <?php foreach ($entries as $entry): ?>


                            <!-- ==================================
                                 ENTRY CARD
                                 ================================== -->

                            <article class="mood-entry-card">


                                <!-- DATE -->

                                <div class="mood-entry-date">

                                    <?php

                                    echo date(
                                        "F j, Y",
                                        strtotime(
                                            $entry["entry_date"]
                                        )
                                    );

                                    ?>

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

                                <p>

                                    <?php

                                    $content = strip_tags(
                                        $entry["content"]
                                    );


                                    if (strlen($content) > 120) {

                                        echo htmlspecialchars(
                                            substr(
                                                $content,
                                                0,
                                                120
                                            )
                                        ) . "...";

                                    } else {

                                        echo htmlspecialchars(
                                            $content
                                        );

                                    }

                                    ?>

                                </p>


                                <!-- VIEW ENTRY -->

                                <a
                                    href="../entries/viewentry.php?id=<?php echo $entry["id"]; ?>"
                                    class="view-btn"
                                >
                                    View Entry →
                                </a>


                            </article>


                        <?php endforeach; ?>


                    </div>


                </section>


            <?php endforeach; ?>


        <?php endif; ?>


    </main>


</div>


</body>

</html>


<?php

$stmt->close();

?>