<?php
session_start();
include "../.configFinal.php"; // Zabezpečte správne pripojenie k databáze

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
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Question Evaluation</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        .hidden {
            display: none;
        }

        #error-message {
            color: red;
        }

        body {
        background-image: url('design/background4.jpg'); 
        background-size: cover; 
        background-position: center center; 
        background-attachment: fixed;
        color: #B0A8B9; 
        font-family: "Chakra Petch", sans-serif; 
        font-weight: 600; 
        }
        .navbar {
            background-color: #320636;
            padding: 10px 15px;
            font-size: 18px; 
            font-weight: 700; 
            text-transform: uppercase; 
            border-bottom: 3px solid #564366; 
        }
        a,.btn {
                color: #D8BFD8; 
                transition: color 0.3s; 
            }
        a:hover {
            color: #ffffff;
        }
        .btn {
            border-color: #C996CC;
            background-color: transparent;
            padding: 8px 12px;
            transition: color 0.3s ease-in-out, background-color 0.3s ease-in-out, border-color 0.3s ease-in-out;

        }
        .btn:hover{
            color: #ffffff;
            border-color: #C996CC;
            background-color: #2a052e;
        }
            
        .container {
            border: 3px solid #564366;
            border-radius: 30px; 
            padding: 35px; 
            margin-top: 25px; 
            background-color: #320636;
            box-shadow: 0 16px 32px rgba(0,0,0,0.5); 
            max-width: 600px;

        }
        h2 {
            border-bottom: 5px solid #564366; 
            border-top: 5px solid #564366; 
            color: #e0cee0;
            border-radius: 25px;
            padding-top: 5px;
            padding-bottom: 5px;
            margin-bottom: 40px;
        }
        .error-msg {
            color: red;
        }

        #error span {
            color: red;
        }

        .error,.text-danger{
            text-align: center;
        }

        .input-group {
            display: flex;
            flex-direction: column; 
            margin-bottom: 15px; 
        }

        .input-group label {
            margin-bottom: 5px; 
            color: #D8BFD8; 
        }


        .form-control {
            width: 100%; 
            padding: 8px; 
            margin-top: 5px; 
            border: 1px solid #564366; 
            background-color: transparent; 
            color: #D8BFD8;
            border-radius: 8px;
        }

        .form-control:focus {
            border-color: #C996CC;
            outline: none; 
        }

        .btn {
            width: 100%; 
            padding: 10px;
            margin-top: 20px; 
            background-color: #564366; 
            color: #ffffff; 
            border: none; 
        }

        .btn:hover {
            background-color: #6A418F;
        }
        .input-group .form-control {
            width: 100%;
            flex: none;
        }
        #toast-container > .toast-success {
            background-image: none !important;
            background-color: #28a745 !important; 
        }

        #toast-container > .toast-error {
            background-image: none !important;
            background-color: #dc3545 !important; 
        }

        .nav-link i {
            margin-right: 5px; 
            font-size: 1.5rem; 
        }
        #toast-container > .toast-success {
        background-image: none !important;
        background-color: #28a745 !important; 
        }

        #toast-container > .toast-error {
            background-image: none !important;
            background-color: #dc3545 !important; 
        }

        .form-control::placeholder { 
            color: #D8BFD8;
            opacity: 1; 
        }

        .form-control:-ms-input-placeholder { 
            color: #D8BFD8;
        }

        .form-control::-ms-input-placeholder { 
            color: #D8BFD8;
        }
        .img-fluid{
            margin-top: 3%;
        }
        .form-check,.form-check-input{
            margin-top: 0.45rem;
        }
        .form-check-label{
            margin-top: 0.2rem;
        }
        h1{
            color: white;
            margin-bottom: 10px;
        }
        .table-primary {
            --bs-table-bg: #e0cee0;
        }

    </style>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">WEBTE FINAL</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="find_question.php">Find question</a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION["username"])) : ?>
                    <li class="nav-item">
                        <a class="nav-link">
                            <i class="fa fa-user"></i> <?php echo $_SESSION["username"]; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php" aria-label="Log out">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-3 main-wrapper">
    <h2 class="mt-3 text-center">Question Code: <?php echo htmlspecialchars($questionCode); ?></h1>
    <h1 class="mt-3 text-center"><?php echo htmlspecialchars($questionTitle); ?></h1>

    <?php if ($questionType === 'open') : ?>
        <?php if ($questionEvalType === 'list') : ?>
            <h2>Answers Statistics</h2>
            <table id="answerTable" class="table table-hover text-center">
                <thead class="table-dark">
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
        <h2 class="mt-3 text-center">Answer Statistics</h2>
        <table class="table table-striped text-center">
            <thead class="table-primary">
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
                        <td class="<?php echo in_array($i, $correctAnswers) ? 'table-success' : 'table-danger'; ?>">
                            <?php echo htmlspecialchars($question["option_$i"]); ?>
                        </td>
                        <td><?php echo $optionCounts[$i - 1] ?? 0; ?></td>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>
        <h2 class="mt-3 text-center">Your Answer</h2>
        <ul class="list-group">
            <?php foreach ($userAnswerArray as $answer) : ?>
                <li class="list-group-item text-center <?php echo in_array($answer, $correctAnswers) ? 'list-group-item-success' : 'list-group-item-danger'; ?>">
                    <?php echo htmlspecialchars($question["option_$answer"]); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        // toastr nastavenia
        toastr.options = {
            "positionClass": "toast-bottom-right", // tu sa meni pozicia toastr
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