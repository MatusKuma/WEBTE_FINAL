<?php 
    session_start();
    echo $_SESSION['admin'] ? 'Admin is true' : 'Admin is false';


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
        <h2>HOME</h2>
        <div class="navbar">
            <a href="add_question_user.php">Pridaj otázku</a>
            <a href="logout.php">Log out</a>
            <h2><?php echo "Logged in: " . $_SESSION["username"]; ?></h2> 
        </div>
    </div>
<script src="script.js"></script>
</body>
</html>
