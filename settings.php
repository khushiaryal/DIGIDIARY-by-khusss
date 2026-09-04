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
// GET USER INFORMATION
// ==========================================

$sql = "SELECT name, email, created_at FROM users WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

$stmt->close();


// ==========================================
// DEFAULT VALUES
// ==========================================

$name = $user["name"] ?? $_SESSION["user_name"] ?? "User";
$email = $user["email"] ?? $_SESSION["user_email"] ?? "";
$created_at = $user["created_at"] ?? "";

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Settings | DIGIDIARY</title>


    <!-- MAIN DIGIDIARY CSS -->

    <link
        rel="stylesheet"
        href="../css/style.css"
    >


    <!-- SETTINGS CSS -->

    <link
        rel="stylesheet"
        href="settings.css"
    >

</head>


<body>


<div class="dashboard">


    <!-- ==========================================
         SIDEBAR
         ========================================== -->

    <?php include "../includes/sidebar.php"; ?>


    <!-- ==========================================
         MAIN CONTENT
         ========================================== -->

    <main class="main-content settings-container">


        <!-- ==========================================
             HEADER
             ========================================== -->

        <header class="settings-header">

            <p class="small-text">
                Manage your DIGIDIARY space.
            </p>

            <h1>
                ⚙️ Settings
            </h1>

            <p>
                Customize your account and manage your diary.
            </p>

        </header>


        <!-- ==========================================
             PROFILE SECTION
             ========================================== -->

        <section class="settings-card">


            <div class="section-heading">

                <div class="section-icon">
                    👤
                </div>

                <div>

                    <h2>
                        Profile
                    </h2>

                    <p>
                        Your DIGIDIARY account information.
                    </p>

                </div>

            </div>


            <div class="profile-info">


                <!-- NAME -->

                <div class="info-row">

                    <div class="info-label">
                        <span>👤</span>
                        Name
                    </div>

                    <div class="info-value">
                        <?php echo htmlspecialchars($name); ?>
                    </div>

                </div>


                <!-- EMAIL -->

                <div class="info-row">

                    <div class="info-label">
                        <span>📧</span>
                        Email
                    </div>

                    <div class="info-value">
                        <?php echo htmlspecialchars($email); ?>
                    </div>

                </div>


                <!-- JOINED DATE -->

                <?php if (!empty($created_at)): ?>

                    <div class="info-row">

                        <div class="info-label">
                            <span>📅</span>
                            Joined
                        </div>

                        <div class="info-value">

                            <?php

                            echo date(
                                "F j, Y",
                                strtotime($created_at)
                            );

                            ?>

                        </div>

                    </div>

                <?php endif; ?>


            </div>


        </section>


        <!-- ==========================================
             ACCOUNT ACTIONS
             ========================================== -->

        <section class="settings-card">


            <div class="section-heading">

                <div class="section-icon">
                    🔐
                </div>

                <div>

                    <h2>
                        Account
                    </h2>

                    <p>
                        Manage your account security.
                    </p>

                </div>

            </div>


            <div class="settings-actions">


                <!-- CHANGE PASSWORD -->

                <a
                    href="#"
                    class="settings-action"
                    onclick="alert('Change password feature coming soon!'); return false;"
                >

                    <div class="action-left">

                        <div class="action-icon">
                            🔑
                        </div>

                        <div>

                            <h3>
                                Change Password
                            </h3>

                            <p>
                                Update your account password.
                            </p>

                        </div>

                    </div>


                    <span class="arrow">
                        →
                    </span>

                </a>


            </div>


        </section>


        <!-- ==========================================
             APPEARANCE
             ========================================== -->

        <section class="settings-card">


            <div class="section-heading">

                <div class="section-icon">
                    🎨
                </div>

                <div>

                    <h2>
                        Appearance
                    </h2>

                    <p>
                        Choose how your diary looks.
                    </p>

                </div>

            </div>


            <div class="appearance-option">


                <div class="action-left">

                    <div class="action-icon">
                        🌿
                    </div>

                    <div>

                        <h3>
                            DIGIDIARY Theme
                        </h3>

                        <p>
                            Soft sage and cream
                        </p>

                    </div>

                </div>


                <span class="theme-status">
                    Active
                </span>


            </div>


        </section>


        <!-- ==========================================
             ABOUT DIGIDIARY
             ========================================== -->

        <section class="settings-card about-card">


            <div class="section-heading">

                <div class="section-icon">
                    💌
                </div>

                <div>

                    <h2>
                        About DIGIDIARY
                    </h2>

                    <p>
                        A little corner for your thoughts and memories.
                    </p>

                </div>

            </div>


            <div class="about-content">

                <h3>
                    DIGIDIARY
                </h3>

                <p class="by-line">
                    by khusss
                </p>

                <p>
                    Your personal digital diary where you can
                    write, remember, reflect, and keep your
                    favorite moments close.
                </p>

                <span class="version">
                    Version 1.0
                </span>

            </div>


        </section>


        <!-- ==========================================
             DANGER ZONE
             ========================================== -->

        <section class="settings-card danger-card">


            <div class="section-heading">

                <div class="section-icon danger-icon">
                    ⚠️
                </div>

                <div>

                    <h2>
                        Danger Zone
                    </h2>

                    <p>
                        Actions that permanently affect your account.
                    </p>

                </div>

            </div>


            <div class="danger-action">


                <div>

                    <h3>
                        Delete Account
                    </h3>

                    <p>
                        Permanently delete your DIGIDIARY account
                        and diary entries.
                    </p>

                </div>


                <button
                    type="button"
                    class="delete-btn"
                    onclick="confirmDelete()"
                >
                    Delete Account
                </button>


            </div>


        </section>


        <!-- ==========================================
             FOOTER
             ========================================== -->

        <footer class="settings-footer">

            <p>
                Made with 🤍 for your little moments.
            </p>

        </footer>


    </main>


</div>


<script>

function confirmDelete() {

    const confirmation = confirm(
        "Are you sure you want to delete your account? This action cannot be undone."
    );

    if (confirmation) {

        alert(
            "Account deletion feature coming soon!"
        );

    }

}

</script>


</body>

</html>