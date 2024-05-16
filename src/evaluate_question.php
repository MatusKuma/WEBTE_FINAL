<?php
session_start();
include "./.configFinal.php";

$code = $_GET['code'] ?? '';
$questionType = $_GET['question_type'] ?? '';

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

if ($questionType === "open") {
    $questionEvalType = $question['type'];
}

$questionId = $question['id'];
$questionTitle = $question['title'];
$questionCode = $question['code'];
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
if ($userId === 0) {
    $answer_id = $_GET["answer_id"];
}
// Načítanie odpovedí používateľa
$userAnswer = '';
if ($userId !== 0) {
    if ($questionType === 'open') {
        $stmt = $db->prepare("SELECT answer FROM answers_open WHERE question_id = ? AND user_id = ?");
        $stmt->execute([$questionId, $userId]);
        $userAnswer = $stmt->fetchColumn();
    } else {
        $stmt = $db->prepare("SELECT answer FROM answers_options WHERE question_id = ? AND user_id = ?");
        $stmt->execute([$questionId, $userId]);
        $userAnswer = $stmt->fetchColumn();
        $userAnswerArray = str_split($userAnswer);
    }
} else {
    if ($questionType === 'open') {
        $stmt = $db->prepare("SELECT answer FROM answers_open WHERE id = ?");
        $stmt->execute([$answer_id]);
        $userAnswer = $stmt->fetchColumn();
    } else {
        $stmt = $db->prepare("SELECT answer FROM answers_options WHERE id = ?");
        $stmt->execute([$answer_id]);
        $userAnswer = $stmt->fetchColumn();
        $userAnswerArray = str_split($userAnswer);
    }
}

// Načítanie a inkrementácia odpovedí pre jednotlivé možnosti
$optionCounts = array(0, 0, 0, 0); // Inicializácia poľa s 4 nulami
if ($questionType === 'options') {
    $stmt = $db->prepare("SELECT answer FROM answers_options WHERE question_id = ?");
    $stmt->execute([$questionId]);
    $answers = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Prechod všetkých odpovedí a spočítanie hlasov pre každú možnosť
    foreach ($answers as $answer) {
        $answerArray = str_split($answer);
        foreach ($answerArray as $selectedOption) {
            $index = $selectedOption - 1; // Index je o 1 menší ako číslo možnosti
            if (isset($optionCounts[$index])) {
                $optionCounts[$index]++;
            }
        }
    }
} else {
    // Načítanie unikátnych odpovedí a ich počtov
    $stmt = $db->prepare("SELECT answer, COUNT(answer) as count FROM answers_open WHERE question_id = ? GROUP BY answer");
    $stmt->execute([$questionId]);
    $answerCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Získanie správnych odpovedí
$correctAnswers = [];
if ($questionType === 'options') {
    $correctAnswers = str_split($question['correct_answer']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Question Evaluation</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <style>
    .correct-answer {
        color: green;
    }

    .incorrect-answer {
        color: red;
    }

    .user-answer {
        font-weight: bold;
    }

    .word-cloud span {
        display: inline-block;
        margin: 5px;
        white-space: nowrap;
    }
    </style>
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

        <?php if ($questionType === 'open') : ?>
        <?php if ($questionEvalType === 'list') : ?>
        <h2>Answers Statistics</h2>
        <table id="answerTable" class="display">
            <thead>
                <tr>
                    <th>Answer</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($answerCounts as $row) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['answer']); ?></td>
                    <td><?php echo $row['count']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else : ?>
        <div id="wordcloud" class="word-cloud"></div>
        <script>
        var answers = <?php echo json_encode($answerCounts); ?>;
        var container = document.getElementById('wordcloud');
        var maxCount = Math.max.apply(Math, answers.map(function(o) {
            return o.count;
        }));

        answers.forEach(function(answer) {
            var span = document.createElement('span');
            span.textContent = answer.answer;
            var size = (answer.count / maxCount) * 40 + 10; // Scale font size based on max count
            span.style.fontSize = size + 'px';
            container.appendChild(span);
        });
        </script>
        <?php endif; ?>
        <h2>Your Answer</h2>
        <p class="user-answer"><?php echo htmlspecialchars($userAnswer); ?></p>
        <?php else : ?>
        <h2>Answer Statistics</h2>
        <table>
            <thead>
                <tr>
                    <th>Option</th>
                    <th>Answer</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i = 1; $i <= 4; $i++) : ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td class="<?php echo in_array($i, $correctAnswers) ? 'correct-answer' : 'incorrect-answer'; ?>">
                        <?php echo htmlspecialchars($question["option_$i"]); ?>
                    </td>
                    <td><?php echo $optionCounts[$i - 1] ?? 0; ?></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
        <h2>Your Answer</h2>
        <ul class="user-answer">
            <?php foreach ($userAnswerArray as $answer) : ?>
            <li class="<?php echo in_array($answer, $correctAnswers) ? 'correct-answer' : 'incorrect-answer'; ?>">
                <?php echo htmlspecialchars($question["option_$answer"]); ?>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>
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
    $(document).ready(function() {
        $('#answerTable').DataTable();
    });
    </script>
</body>

</html>