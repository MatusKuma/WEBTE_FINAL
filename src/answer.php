<?php
session_start();
include "./.configFinal.php";
// Získanie parametra kódu otázky z URL
$code = $_GET['code'] ?? '';
$questionType = $_GET['question_type'] ?? '';
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Načítanie otázky na základe kódu a typu
$query = $questionType === 'open' ? "SELECT * FROM questions_open WHERE code = ?" : "SELECT * FROM questions_options WHERE code = ?";
$stmt = $db->prepare($query);
$stmt->execute([$code]);
$question = $stmt->fetch();

if (!$question) {
    $_SESSION["toast_error"] = "Question does not exist.";
    header("Location: find_question.php");
    exit;
}

// Kontrola, či je otázka aktívna
if ($question['isActive'] == 0) {
    $_SESSION["toast_error"] = "This question is not active.";
    header("Location: find_question.php");
    exit;
}

$questionId = $question['id'];
$questionTitle = $question['title'];
$questionCode = $question['code'];

// Skontrolovať, či už používateľ odpovedal na otázku

if ($questionType === "open") {
    $stmt = $db->prepare("SELECT COUNT(*) FROM answers_open WHERE question_id = ? AND user_id = ?");
    $stmt->execute([$questionId, $userId]);
    $count = $stmt->fetchColumn();
} else {
    $stmt = $db->prepare("SELECT COUNT(*) FROM answers_options WHERE question_id = ? AND user_id = ?");
    $stmt->execute([$questionId, $userId]);
    $count = $stmt->fetchColumn();
}


if ($count > 0 && isset($_SESSION["user_id"])) {
    $_SESSION["toast_error"] = "Sorry, you have already submitted your answer for this question.";
    header("Location: evaluate_question.php?code=" . $code . "&question_type=" . $questionType);
    exit;
}

// Spracovanie odpovede
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $timestamp = date("Y-m-d H:i:s");

    if ($questionType === 'open') {
        $answers = $_POST['answer'] ?? [];
        if (empty($answers)) {
            $_SESSION["toast_error"] = "You answer is empty";
            header("Location: answer.php?code=" . $code . "&question_type=" . $questionType);
            exit;
        }

        $insertStmt = $db->prepare("INSERT INTO answers_open (question_id, answer, timestamp, user_id) VALUES (?, ?, ?, ?)");
        $insertStmt->execute([$questionId, $answer, $timestamp, $userId]);
        $inserted_answer_id = $db->lastInsertId();
        $_SESSION["toast_success"] = "Your answer has been submitted.";
    } else {
        $answers = $_POST['answer'] ?? [];
        $answerCount = count($answers);
        // Backendová validácia počtu zaškrtnutých možností
        if ($answerCount === 0) {
            $_SESSION["toast_error"] = "Please select at least one option";
            header("Location: answer.php?code=" . $code . "&question_type=" . $questionType);
            exit;
        } else if ($answerCount === 4) {
            $_SESSION["toast_error"] = "You cannot select all options";
            header("Location: answer.php?code=" . $code . "&question_type=" . $questionType);
            exit;
        }
        $answer = implode('', $answers);
        $insertStmt = $db->prepare("INSERT INTO answers_options (question_id, answer, timestamp, user_id) VALUES (?, ?, ?, ?)");
        $insertStmt->execute([$questionId, $answer, $timestamp, $userId]);
        $inserted_answer_id = $db->lastInsertId();
        $_SESSION["toast_success"] = "Your answer has been submitted.";
    }
    if ($userId === 0) {
        header("Location: evaluate_question.php?code=" . $code . "&question_type=" . $questionType . "&answer_id=" . $inserted_answer_id);
        exit;
    }
    header("Location: evaluate_question.php?code=" . $code . "&question_type=" . $questionType);
    exit;
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
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
</head>

<body>
    <div class="navigation_bar">
        <div class="navbar">
            <a href="find_question.php">Find question</a>
            <a href="logged_in.php">Home</a>
            <?php if (isset($_SESSION["username"])) : ?>
            <a href="logout.php">Log out</a>
            <h2><?php echo "Logged in: " . $_SESSION["username"]; ?></h2>
            <?php endif; ?>
        </div>
    </div>

    <div class="main-wrapper">
        <h1><?php echo "Question Code: " . htmlspecialchars($questionCode); ?></h1>
        <h1><?php echo htmlspecialchars($questionTitle); ?></h1>
        <form
            action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . "?code=" . $code . "&question_type=" . $questionType); ?>"
            method="post">
            <?php if ($questionType === 'open') : ?>
            <label for="answer">Your answer</label>
            <input type="text" id="answer" name="answer" required>
            <?php else : ?>
            <div>
                <?php foreach ($options as $index => $option) : ?>
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

</html>