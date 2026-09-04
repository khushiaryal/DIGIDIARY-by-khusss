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
$sql = "SELECT ID, title, content, mood, entry_date
        FROM entries
        WHERE user_id = ?
        ORDER BY entry_date DESC, ID DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();


// Group entries by month and year
$memories = [];

while ($entry = $result->fetch_assoc()) {

    $monthYear = date("F Y", strtotime($entry["entry_date"]));

    $memories[$monthYear][] = $entry;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Memories | DIGIDIARY</title>

    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/memories.css">

</head>

<body>

    <?php include "../includes/sidebar.php"; ?>


    <div class="main-content">

        <!-- PAGE HEADER -->

        <div class="memories-header">

            <div>
                <h1>Your Memories</h1>

                <p>
                    Little moments worth remembering. 🌷
                </p>
            </div>

        </div>


        <?php if (empty($memories)): ?>

            <!-- EMPTY STATE -->

            <div class="empty-memories">

                <div class="empty-icon">
                    🌸
                </div>

                <h2>No memories yet</h2>

                <p>
                    Start writing your diary and your beautiful moments
                    will appear here.
                </p>

                <a href="/DIGIDIARY/entries/newentries.php" class="create-memory-btn">
                    ✍️ Write Your First Memory
                </a>

            </div>


        <?php else: ?>


            <!-- MEMORY MONTHS -->

            <div class="memory-sections">

                <?php foreach ($memories as $month => $entries): ?>

                    <section class="memory-month">

                        <div class="month-heading">

                            <span class="month-icon">
                                🌿
                            </span>

                            <h2>
                                Memories from <?php echo htmlspecialchars($month); ?>
                            </h2>

                        </div>


                        <div class="memory-grid">

                            <?php foreach ($entries as $entry): ?>

                                <?php

                                $content = strip_tags($entry["content"]);

                                $preview = mb_substr($content, 0, 150);

                                if (mb_strlen($content) > 150) {
                                    $preview .= "...";
                                }

                                $formattedDate = date(
                                    "d M Y",
                                    strtotime($entry["entry_date"])
                                );

                                ?>

                                <div class="memory-card">


                                    <!-- DATE -->

                                    <div class="memory-date">

                                        📅

                                        <?php echo htmlspecialchars($formattedDate); ?>

                                    </div>


                                    <!-- TITLE -->

                                    <h3 class="memory-title">

                                        <?php
                                        echo htmlspecialchars($entry["title"]);
                                        ?>

                                    </h3>


                                    <!-- MOOD -->

                                    <?php if (!empty($entry["mood"])): ?>

                                        <div class="memory-mood">

                                            <?php
                                            echo htmlspecialchars($entry["mood"]);
                                            ?>

                                        </div>

                                    <?php endif; ?>


                                    <!-- CONTENT -->

                                    <p class="memory-preview">

                                        <?php
                                        echo htmlspecialchars($preview);
                                        ?>

                                    </p>


                                    <!-- FOOTER -->

                                    <div class="memory-footer">

                                        <span>
                                            A little moment 💗
                                        </span>

                                        <a
                                            href="../entries/viewentry.php?id=<?php echo $entry["ID"]; ?>"
                                            class="read-memory"
                                        >
                                            Read memory →
                                        </a>

                                    </div>


                                </div>

                            <?php endforeach; ?>

                        </div>

                    </section>

                <?php endforeach; ?>

            </div>


        <?php endif; ?>

    </div>


</body>

</html>