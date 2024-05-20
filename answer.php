<?php
session_start();
<<<<<<< HEAD

=======
>>>>>>> 8ef626f8838a4c03cda942bbe2d69551e0b9f6a8
include "../.configFinal.php"; // Zabezpečte správne pripojenie k databáze
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
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Answer</title>
<<<<<<< HEAD
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
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
            margin-bottom: 60px;
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
        <h2 class="mb-3 text-center"><?php echo "Question Code: " . htmlspecialchars($questionCode); ?></h1>
        <h1 class="mb-3 text-center"><?php echo htmlspecialchars($questionTitle); ?></h1>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . "?code=" . $code . "&question_type=" . $questionType); ?>" method="post" class="needs-validation" novalidate>
            <?php if ($questionType === 'open') : ?>
                <div class="mb-3">
                    <label for="answer" class="form-label">Your answer</label>
                    <input type="text" id="answer" name="answer" class="form-control" required>
                </div>
            <?php else : ?>
                <div>
                    <?php foreach ($options as $index => $option) : ?>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="option<?php echo $index + 1; ?>" name="answer[]" value="<?php echo $index + 1; ?>">
                            <label class="form-check-label" for="option<?php echo $index + 1; ?>"><?php echo htmlspecialchars($option); ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="d-grid gap-2">
                <input type="submit" value="Send answer" class="btn btn-primary">
            </div>
        </form>
    </div>

=======
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
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . "?code=" . $code . "&question_type=" . $questionType); ?>" method="post">
            <?php if ($questionType === 'open') : ?>
                <label for="answer">Your answer</label>
                <input type="text" id="answer" name="answer" required>
            <?php else : ?>
                <div>
                    <?php foreach ($options as $index => $option) : ?>
                        <input type="checkbox" id="option<?php echo $index + 1; ?>" name="answer[]" value="<?php echo $index + 1; ?>">
                        <label for="option<?php echo $index + 1; ?>"><?php echo htmlspecialchars($option); ?></label><br>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <input type="submit" value="Send answer">
        </form>
    </div>
>>>>>>> 8ef626f8838a4c03cda942bbe2d69551e0b9f6a8
</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<<<<<<< HEAD
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    toastr.options = {
        "positionClass": "toast-bottom-right", // tu sa meni pozicia toastr
=======
<script>
    toastr.options = {
        "positionClass": "toast-top-right", // tu sa meni pozicia toastr
>>>>>>> 8ef626f8838a4c03cda942bbe2d69551e0b9f6a8
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