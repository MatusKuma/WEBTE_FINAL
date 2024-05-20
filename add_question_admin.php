<?php
session_start();
include "../.configFinal.php"; // Predpokladá sa, že tento súbor obsahuje pripojenie k databáze.

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: logged_in.php");
    exit;
}




$error = ''; // Inicializujte premennú chyby.

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $subject = $_POST['subject'];
    $option = $_POST['option'];
    $creator_id = $_POST["userSelect"];
    $eval_type = $_POST['eval_type_select'];



    $timestamp = date("Y-m-d H:i:s");

    do {
        // vygenerovanie unikatneho 5 miestneho kodu
        $code = randString();

        // overujeme, ze ci kod sa uz nenachadza v databaze
        $stmt1 = $db->prepare("SELECT COUNT(*) FROM questions_open WHERE code = ?");
        $stmt1->execute([$code]);
        $count1 = $stmt1->fetchColumn();

        $stmt2 = $db->prepare("SELECT COUNT(*) FROM questions_options WHERE code = ?");
        $stmt2->execute([$code]);
        $count2 = $stmt2->fetchColumn();

        // ak sa kod nachadza v nasich DB pre otazky, tak musime ho pregenerovat
    } while ($count1 > 0 || $count2 > 0);

    // Check which option is selected
    if ($_POST['option'] == 'option1') {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        // Validácia checkboxov
        $correctCount = 0;
        for ($i = 1; $i <= 4; $i++) {
            if (isset($_POST["correct$i"]) && $_POST["correct$i"] == '1') {
                $correctCount++;
            }
        }

        if ($correctCount === 0) {
            $error = 'Please check at least one correct';
        } else if ($correctCount === 4) {
            $error = 'You cannot check all options as correct';
        } else {
            // Convert the array of answers to a string
            // Insert data into your database
            // Process data for Option 1
            $answers = array();
            $correct_answer = '';
            for ($i = 1; $i <= 4; $i++) {
                $answer = $_POST['answer' . $i];
                $correct = isset($_POST['correct' . $i]) ? '1' : '0';
                $answers[] = $answer;
                if ($correct === '1') {
                    $correct_answer .= $i; // Concatenate the correct answer indices
                }
            }

            try {
                $stmt = $db->prepare("INSERT INTO questions_options (title,correct_answer, option_1, option_2, option_3, option_4, subject, timestamp, creator_id, isActive, code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?)");
                $stmt->execute([$title, $correct_answer, $answers[0], $answers[1], $answers[2], $answers[3], $subject, $timestamp, $creator_id, $code]);
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }
    } else if ($_POST['option'] == 'option2') {

        // Insert data into your database
        try {
            $stmt = $db->prepare("INSERT INTO questions_open (creator_id, timestamp, isActive, title, subject, code, type) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$creator_id, $timestamp, '1', $title, $subject, $code, $eval_type]);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

function randString()
{
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    $charLength = strlen($chars);
    $randomStr = '';
    for ($i = 0; $i < 5; $i++) {
        $randomStr .= $chars[rand(0, $charLength - 1)];
    }
    return $randomStr;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>WEBTE FINAL</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
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

    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="admin.php">WEBTE FINAL</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                 </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link">
                                <i class="fa fa-user"></i> <?php echo $_SESSION["username"]; ?>
                            </a>
                        </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </a>
                    </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container mt-3">
    <form id="myForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="needs-validation" onsubmit="validateForm();">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="option" id="option1" value="option1" <?php if (!isset($_POST['option']) || $_POST['option'] === 'option1') echo 'checked'; ?>>
            <label class="form-check-label" for="option1">Otázka s výberom</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="option" id="option2" value="option2" <?php if (isset($_POST['option']) && $_POST['option'] === 'option2') echo 'checked'; ?>>
            <label class="form-check-label" for="option2">Otvorená otázka</label>
        </div>

        <div class="mb-3">
            <label for="userSelect" class="form-label">User</label>
            <select class="form-select" name="userSelect">
                <?php
                $stmt = $db->prepare("SELECT * FROM users");
                $stmt->execute();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['username']) . "</option>";
                }
                ?>
            </select>
        </div>

        <div id="eval_type" class="<?php if (!isset($_POST['option']) || $_POST['option'] === 'option1') echo 'hidden'; ?> mb-3">
            <label for="eval_type_select" class="form-label">Evaluation Type:</label>
            <select class="form-select" name="eval_type_select" id="eval_type_select">
                <option value="wordcloud">Word Cloud</option>
                <option value="list">Unordered List</option>
            </select>
        </div>

        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" class="form-control" required minlength="5" maxlength="100">
        </div>
        
        <div id="answers" class="form-group <?php if (!isset($_POST['option']) || $_POST['option'] === 'option2') echo 'hidden'; ?>">
            <label>Answers:</label>
            <div class="row mb-2">
                <div class="col-md-8">
                    <input type="text" name="answer1" class="form-control" placeholder="Answer 1" maxlength="100">
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" value="1" name="correct1" id="correct1">
                        <label class="form-check-label" for="correct1">Correct</label>
                    </div>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-8">
                    <input type="text" name="answer2" class="form-control" placeholder="Answer 2" maxlength="100">
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" value="1" name="correct2" id="correct2">
                        <label class="form-check-label" for="correct2">Correct</label>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-8">
                    <input type="text" name="answer3" class="form-control" placeholder="Answer 3" maxlength="100">
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" value="1" name="correct3" id="correct3">
                        <label class="form-check-label" for="correct3">Correct</label>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-8">
                    <input type="text" name="answer4" class="form-control" placeholder="Answer 4" maxlength="100">
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" value="1" name="correct4" id="correct4">
                        <label class="form-check-label" for="correct4">Correct</label>
                    </div>
                </div>
            </div> 
        </div>

        <div class="mb-3">
            <label for="subject" class="form-label">Subject:</label>
            <input type="text" class="form-control" id="subject" name="subject" required minlength="3" maxlength="50">
        </div>

        <div class="mb-3">
            <input type="submit" class="btn btn-primary" value="Submit">
        </div>

        <div id="error-message" class="text-danger"><?php echo $error; ?></div>
    </form>
</div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            updateFormVisibility();

            document.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox) {
                checkbox.addEventListener('change', validateCheckboxes);
            });

            document.getElementById('option1').addEventListener('change', updateFormVisibility);
            document.getElementById('option2').addEventListener('change', updateFormVisibility);
        });

        function updateFormVisibility() {
            let option1 = document.getElementById('option1').checked;
            let answersDiv = document.getElementById('answers');
            let evalType = document.getElementById("eval_type");
            evalType.style.display = option1 ? "none" : "block";
            answersDiv.style.display = option1 ? 'block' : 'none';
        }

        function validateCheckboxes() {
            if (document.getElementById('option1').checked) {
                let checkboxes = document.querySelectorAll('#answers input[type="checkbox"]');
                let checkedCount = 0;
                checkboxes.forEach(function(box) {
                    if (box.checked) checkedCount++;
                });

                const errorMessage = document.getElementById('error-message');
                if (checkedCount === 0) {
                    errorMessage.textContent = 'Please check at least one correct.';
                } else if (checkedCount === 4) {
                    errorMessage.textContent = 'You cannot check all options as correct.';
                } else {
                    errorMessage.textContent = '';
                }
            }
        }


        function validateForm() {
            if (document.getElementById('option1').checked) {
                validateCheckboxes();
                return document.getElementById('error-message').textContent === '';
            }
            return true;
        }

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
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
 
</body>

</html>