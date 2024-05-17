<?php
include "../.configFinal.php";
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header("Location: index.php");
    exit;
}




$id = $_GET['id'];
$stmt = $db->prepare("SELECT * FROM questions_open WHERE id = ?");
$stmt->execute([$id]);
$question = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $isActive = isset($_POST['isActive']) ? 1 : 0;

    $stmt = $db->prepare("UPDATE questions_open SET title = ?, isActive = ? WHERE id = ?");
    $stmt->execute([$title, $isActive, $id]);
    $_SESSION["toast_success"] = "Question has been updated successfully";
    session_write_close();
    header("Location: view_questions_user.php?user_id=" . $_SESSION['user_id']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit Open Question</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="navigation_bar">
        <div class="navbar">
            <a href=<?php echo "view_questions_user.php?user_id=" . $_SESSION["user_id"] ?>>My questions</a>
            <a href="logged_in.php">Home</a>
            <a href="logout.php">Log out</a>
            <h2><?php echo "Logged in: " . $_SESSION["username"]; ?></h2>
        </div>
    </div>

    <h2>Edit Open Question</h2>
    <form action="edit_question_open.php?id=<?php echo $id; ?>" method="post">
        <div>
            <label>Question Title:</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($question['title']); ?>" required>
        </div>
        <div>
            <label>Active:</label>
            <input type="checkbox" name="isActive" <?php echo $question['isActive'] ? 'checked' : ''; ?>>
        </div>
        <div>
            <input type="submit" value="Update Question">
        </div>
    </form>
</body>

</html>