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
// GET MONTH AND YEAR
// ==========================================

$month = isset($_GET["month"]) ? (int)$_GET["month"] : date("n");
$year = isset($_GET["year"]) ? (int)$_GET["year"] : date("Y");


// ==========================================
// VALIDATE MONTH
// ==========================================

if ($month < 1) {
    $month = 12;
    $year--;
}

if ($month > 12) {
    $month = 1;
    $year++;
}


// ==========================================
// CALENDAR INFORMATION
// ==========================================

$first_day = mktime(0, 0, 0, $month, 1, $year);

$days_in_month = date("t", $first_day);

$start_day = date("w", $first_day);

$month_name = date("F", $first_day);


// ==========================================
// GET USER'S DIARY ENTRIES
// ==========================================

$entries = [];

$stmt = mysqli_prepare(
    $conn,
    "SELECT ID, title, entry_date
     FROM entries
     WHERE user_id = ?
     AND MONTH(entry_date) = ?
     AND YEAR(entry_date) = ?
     ORDER BY entry_date ASC, ID ASC"
);

mysqli_stmt_bind_param(
    $stmt,
    "iii",
    $user_id,
    $month,
    $year
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);


// ==========================================
// STORE ENTRIES BY DATE
// ==========================================

while ($entry = mysqli_fetch_assoc($result)) {

    $date_key = date(
        "Y-m-d",
        strtotime($entry["entry_date"])
    );

    $entries[$date_key] = [
        "id" => $entry["ID"],
        "title" => $entry["title"]
    ];
}

mysqli_stmt_close($stmt);


// ==========================================
// PREVIOUS MONTH
// ==========================================

$previous_month = $month - 1;
$previous_year = $year;

if ($previous_month < 1) {
    $previous_month = 12;
    $previous_year--;
}


// ==========================================
// NEXT MONTH
// ==========================================

$next_month = $month + 1;
$next_year = $year;

if ($next_month > 12) {
    $next_month = 1;
    $next_year++;
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

    <title>DIGIDIARY - Calendar</title>


    <!-- SIDEBAR CSS -->

    <link
        rel="stylesheet"
        href="/DIGIDIARY/css/sidebar.css"
    >


    <!-- CALENDAR CSS -->

    <link
        rel="stylesheet"
        href="/DIGIDIARY/css/calendar.css"
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


    <div class="calendar-page">


        <!-- ==========================================
             HEADER
             ========================================== -->

        <header class="calendar-header">


        </header>


        <!-- ==========================================
             MAIN
             ========================================== -->

        <main class="calendar-main">


            <!-- ==========================================
                 INTRO
                 ========================================== -->

            <div class="calendar-intro">

                <p class="intro-small">
                    Your memories, one day at a time 🌷
                </p>

                <h2>
                    Calendar
                </h2>

                <p>
                    Revisit the little moments you've written down.
                </p>

            </div>


            <!-- ==========================================
                 CALENDAR CARD
                 ========================================== -->

            <section class="calendar-card">


                <!-- MONTH NAVIGATION -->

                <div class="calendar-navigation">

                    <a
                        href="calendar.php?month=<?php echo $previous_month; ?>&year=<?php echo $previous_year; ?>"
                        class="month-button"
                    >
                        ←
                    </a>


                    <h3>

                        <?php
                        echo $month_name . " " . $year;
                        ?>

                    </h3>


                    <a
                        href="calendar.php?month=<?php echo $next_month; ?>&year=<?php echo $next_year; ?>"
                        class="month-button"
                    >
                        →
                    </a>

                </div>


                <!-- ==========================================
                     WEEK DAYS
                     ========================================== -->

                <div class="weekdays">

                    <div>Sun</div>
                    <div>Mon</div>
                    <div>Tue</div>
                    <div>Wed</div>
                    <div>Thu</div>
                    <div>Fri</div>
                    <div>Sat</div>

                </div>


                <!-- ==========================================
                     CALENDAR GRID
                     ========================================== -->

                <div class="calendar-grid">


                    <?php

                    // Empty spaces before first day

                    for ($i = 0; $i < $start_day; $i++) {

                        echo '<div class="calendar-day empty"></div>';

                    }


                    // Days of month

                    for ($day = 1; $day <= $days_in_month; $day++) {

                        $current_date = sprintf(
                            "%04d-%02d-%02d",
                            $year,
                            $month,
                            $day
                        );


                        $today = date("Y-m-d");


                        $has_entry = isset(
                            $entries[$current_date]
                        );


                        $is_today = (
                            $current_date === $today
                        );


                        $classes = "calendar-day";


                        if ($has_entry) {
                            $classes .= " has-entry";
                        }


                        if ($is_today) {
                            $classes .= " today";
                        }

                        ?>


                        <div class="<?php echo $classes; ?>">


                            <?php if ($has_entry): ?>


                                <!-- ==================================
                                     DATE WITH DIARY ENTRY
                                     ================================== -->

                                <a
                                    href="/DIGIDIARY/entries/viewentry.php?id=<?php echo $entries[$current_date]["id"]; ?>"
                                    class="day-link"
                                    title="Read: <?php echo htmlspecialchars($entries[$current_date]["title"]); ?>"
                                >

                                    <span class="paint-highlight">

                                        <?php echo $day; ?>

                                    </span>

                                </a>


                            <?php else: ?>


                                <!-- NORMAL DATE -->

                                <span class="day-number">

                                    <?php echo $day; ?>

                                </span>


                            <?php endif; ?>


                        </div>


                        <?php

                    }

                    ?>

                </div>


                <!-- ==========================================
                     LEGEND
                     ========================================== -->

                <div class="calendar-legend">


                    <div class="legend-item">

                        <span class="legend-paint">
                            15
                        </span>

                        <span>
                            Diary entry
                        </span>

                    </div>


                    <div class="legend-item">

                        <span class="legend-today">
                            29
                        </span>

                        <span>
                            Today
                        </span>

                    </div>


                </div>


            </section>


            <!-- ==========================================
                 MONTH'S ENTRIES
                 ========================================== -->

            <?php if (count($entries) > 0): ?>


                <section class="month-entries">


                    <h3>
                        Little moments from <?php echo $month_name; ?> ✦
                    </h3>


                    <?php foreach ($entries as $date => $entry): ?>


                        <a
                            href="/DIGIDIARY/entries/viewentry.php?id=<?php echo $entry["id"]; ?>"
                            class="mini-entry"
                        >


                            <div class="mini-date">

                                <?php
                                echo date(
                                    "d",
                                    strtotime($date)
                                );
                                ?>

                            </div>


                            <div class="mini-info">

                                <strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $entry["title"]
                                    );
                                    ?>

                                </strong>


                                <span>

                                    <?php
                                    echo date(
                                        "F d, Y",
                                        strtotime($date)
                                    );
                                    ?>

                                </span>

                            </div>


                            <span class="mini-arrow">
                                →
                            </span>


                        </a>


                    <?php endforeach; ?>


                </section>


            <?php else: ?>


                <!-- ==========================================
                     NO ENTRIES
                     ========================================== -->

                <section class="no-entries">


                    <div class="no-entry-icon">
                        ✦
                    </div>


                    <h3>
                        No memories here yet
                    </h3>


                    <p>
                        This month is waiting for a little piece of you.
                    </p>


                    <a
                        href="/DIGIDIARY/entries/newentries.php"
                        class="write-button"
                    >
                        Write an Entry
                    </a>


                </section>


            <?php endif; ?>


        </main>


    </div>


</div>


</body>

</html>