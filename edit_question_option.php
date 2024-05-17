<?php
include "../.configFinal.php";
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header("Location: index.php");
    exit;
}




$id = $_GET['id'];
$stmt = $db->prepare("SELECT * FROM questions_options WHERE id = ?");
$stmt->execute([$id]);
$question = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $option1 = $_POST['option_1'];
    $option2 = $_POST['option_2'];
    $option3 = $_POST['option_3'];
    $option4 = $_POST['option_4'];
    $correct_answer = implode('', array_filter(array_keys($_POST['correct']), function ($val) {
        return is_numeric($val);
    }));
    $isActive = isset($_POST['isActive']) ? 1 : 0;

    $stmt = $db->prepare("UPDATE questions_options SET title = ?, option_1 = ?, option_2 = ?, option_3 = ?, option_4 = ?, correct_answer = ?, isActive = ? WHERE id = ?");
    $stmt->execute([$title, $option1, $option2, $option3, $option4, $correct_answer, $isActive, $id]);
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
    <title>Edit Question with Options</title>
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

    <h2>Edit Question with Options</h2>
    <form action="edit_question_option.php?id=<?php echo $id; ?>" method="post">
        <div>
            <label>Question Title:</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($question['title']); ?>" required>
        </div>
        <div>
            <label>Option 1:</label>
            <input type="text" name="option_1" value="<?php echo htmlspecialchars($question['option_1']); ?>" required>
        </div>
        <div>
            <label>Option 2:</label>
            <input type="text" name="option_2" value="<?php echo htmlspecialchars($question['option_2']); ?>" required>
        </div>
        <div>
            <label>Option 3:</label>
            <input type="text" name="option_3" value="<?php echo htmlspecialchars($question['option_3']); ?>" required>
        </div>
        <div>
            <label>Option 4:</label>
            <input type="text" name="option_4" value="<?php echo htmlspecialchars($question['option_4']); ?>" required>
        </div>
        <div>
            <label>Correct Options:</label><br>
            <?php
            for ($i = 1; $i <= 4; $i++) {
                echo "<input type='checkbox' name='correct[$i]' value='$i' " . (strpos($question['correct_answer'], (string)$i) !== false ? 'checked' : '') . "> Option $i<br>";
            }
            ?>
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