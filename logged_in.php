<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header("Location: index.php");
    exit;
}

if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    header("Location: admin.php");
    exit;
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>WEBTE FINAL</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
</head>

<body>
    <div class="navigation_bar">
        <div class="navbar">
            <a href="find_question.php">Find question</a>
            <a href="add_question_user.php">Add question</a>
            <a href=<?php echo "view_questions_user.php?user_id=" . $_SESSION["user_id"] ?>>My questions</a>
            <a href="logout.php">Log out</a>
            <a href=<?php echo "change_password.php?user_id=" . $_SESSION["user_id"] ?>>Change Password</a>
            <h2><?php echo "Logged in: " . $_SESSION["username"]; ?></h2>
        </div>
    </div>
    <script src="script.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        // toastr nastavenia
        toastr.options = {
            "positionClass": "toast-top-right", // tu sa meni pozicia toastr
        };

        <?php if (isset($_SESSION["toast_success"])) : ?>
            toastr.success('<?php echo $_SESSION["toast_success"]; ?>');

            <?php unset($_SESSION["toast_success"]); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION["toast_error"])) : ?>
            toastr.error('<?php echo $_SESSION["toast_error"]; ?>');

            <?php unset($_SESSION["toast_error"]); ?>
        <?php endif; ?>
    </script>
</body>

</html>