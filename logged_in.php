<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header("Location: index.php");
    exit;
} else {
    if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
        header("Location: admin.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>WEBTE FINAL</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="navigation_bar">
        <div class="navbar">
            <a href="find_question.php">Find question</a>
            <a href="add_question_user.php">Add question</a>
            <a href=<?php echo "view_questions_user.php?user_id=" . $_SESSION["user_id"]?>>My questions</a>
            <a href="logged_in.php">Home</a>
            <a href="logout.php">Log out</a>
            <h2><?php echo "Logged in: " . $_SESSION["username"]; ?></h2>
        </div>
    </div>
    <script src="script.js"></script>
</body>

</html>