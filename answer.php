<?php
session_start();
include "../.configFinal.php"; // Zabezpečte správne pripojenie k databáze

// Ochrana pred neautorizovaným prístupom
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: index.php");
    exit;
}
if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    header("Location: admin.php");
    exit;
}

// Získanie parametra kódu otázky z URL
$code = $_GET['code'] ?? '';
$questionType = $_GET['question_type'] ?? '';

// Načítanie otázky na základe kódu a typu
$query = $questionType === 'open' ? "SELECT * FROM questions_open WHERE code = ?" : "SELECT * FROM questions_options WHERE code = ?";
$stmt = $db->prepare($query);
$stmt->execute([$code]);
$question = $stmt->fetch();

if (!$question) {
    die("Otázka nenájdená.");
}

$questionId = $question['id'];
$questionTitle = $question['title'];
$questionCode = $question['code'];


// Spracovanie odpovede
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['answer'])) {
    $timestamp = date("Y-m-d H:i:s");

    if ($questionType === 'open') {
        $answer = $_POST['answer'];
        $insertStmt = $db->prepare("INSERT INTO answers_open (question_id, answer, timestamp) VALUES (?, ?, ?)");
        $insertStmt->execute([$questionId, $answer, $timestamp]);
    } else {
        $answers = $_POST['answer'] ?? [];
        $answersString = implode('', $answers);
        $insertStmt = $db->prepare("INSERT INTO answers_options (question_id, answer, timestamp) VALUES (?, ?, ?)");
        $insertStmt->execute([$questionId, $answersString, $timestamp]);
    }
    echo "<p>Odpoveď bola úspešne uložená.</p>";
}

// Načítanie možností pre otázky s výberom
$options = [];
if ($questionType === 'options') {
    $options = [$question['option_1'], $question['option_2'], $question['option_3'], $question['option_4']];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Answer</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
<div class="navigation_bar">
        <h2>HOME</h2>
        <div class="navbar">
            <a href="find_question.php">Find question</a>
            <a href="add_question_user.php">Add question</a>
            <a href="logout.php">Log out</a>
            <h2><?php echo "Logged in: " . $_SESSION["username"]; ?></h2> 
        </div>
    </div>
    <h1><?php echo "Question Code: " . htmlspecialchars($questionCode); ?></h1>
    <h1><?php echo htmlspecialchars($questionTitle); ?></h1>
    <form
        action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . "?code=" . $code . "&question_type=" . $questionType); ?>"
        method="post">
        <?php if ($questionType === 'open'): ?>
            <label for="answer">Your answer</label>
            <input type="text" id="answer" name="answer" required>
        <?php else: ?>
            <div>
                <?php foreach ($options as $index => $option): ?>
                    <input type="checkbox" id="option<?php echo $index + 1; ?>" name="answer[]"
                        value="<?php echo $index + 1; ?>">
                    <label for="option<?php echo $index + 1; ?>"><?php echo htmlspecialchars($option); ?></label><br>
                <?php endforeach; ?>

            </div>
        <?php endif; ?>
        <input type="submit" value="Send answer">
    </form>
</body>

</html>