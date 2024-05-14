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
$userId = $question['creator_id'];

// Spracovanie odpovede
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['answer'])) {
    $timestamp = date("Y-m-d H:i:s");

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    if ($questionType === 'open') {

        $stmt = $db->prepare("SELECT COUNT(*) FROM answers_open WHERE question_id = ? AND user_id = ?");
        $stmt->execute([$questionId, $userId]);
        $count = $stmt->fetchColumn();
        
        if($count < 1){
            $answer = $_POST['answer'];
            $insertStmt = $db->prepare("INSERT INTO answers_open (question_id, answer, timestamp, user_id) VALUES (?, ?, ?, ?)");
            $insertStmt->execute([$questionId, $answer, $timestamp, $userId]);
            $_SESSION["toast_success"] = "Your answer has been submitted.";
        }else { 
            $_SESSION["toast_error"] = "Sorry, you have already submitted your answer for this question.";
        }

    } else {

        $stmt = $db->prepare("SELECT COUNT(*) FROM answers_options WHERE question_id = ? AND user_id = ?");
        $stmt->execute([$questionId, $userId]);
        $count = $stmt->fetchColumn();

        if($count < 1){
            $answers = $_POST['answer'] ?? [];
            $answersString = implode('', $answers);
            $insertStmt = $db->prepare("INSERT INTO answers_options (question_id, answer, timestamp, user_id) VALUES (?, ?, ?, ?)");
            $insertStmt->execute([$questionId, $answersString, $timestamp, $userId]);

            $_SESSION["toast_success"] = "Your answer has been submitted.";
        }else {
            $_SESSION["toast_error"] = "Sorry, you have already submitted your answer for this question.";
        }
    }
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
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

<div class="main-wrapper">
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
                <!-- TODO -> zobrazovanie spravneho poctu checkboxov -->
                <?php foreach ($options as $index => $option): ?>
                    <input type="checkbox" id="option<?php echo $index + 1; ?>" name="answer[]"
                        value="<?php echo $index + 1; ?>">
                    <label for="option<?php echo $index + 1; ?>"><?php echo htmlspecialchars($option); ?></label><br>
                <?php endforeach; ?>

            </div>
        <?php endif; ?>
        <input type="submit" value="Send answer">
    </form>
</div>
</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    toastr.options = {
        "positionClass": "toast-top-right",     // tu sa meni pozicia toastr
    };

    <?php if(isset($_SESSION["toast_success"])): ?>
        toastr.success('<?php echo $_SESSION["toast_success"]; ?>');

        <?php unset($_SESSION["toast_success"]); ?>
    <?php endif; ?>

    <?php if(isset($_SESSION["toast_error"])): ?>
        toastr.error('<?php echo $_SESSION["toast_error"]; ?>');

        <?php unset($_SESSION["toast_error"]); ?>
    <?php endif; ?>
</script>
</html>