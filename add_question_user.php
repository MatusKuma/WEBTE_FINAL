<?php
session_start();
include "../.configFinal.php"; // Predpokladá sa, že tento súbor obsahuje pripojenie k databáze.

require_once 'vendor/autoload.php';

use Endroid\QrCode\QrCode;

error_reporting(E_ALL);
ini_set('display_errors', 1);

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;

use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;

$writer = new PngWriter();



if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header("Location: index.php");
    exit;
}
if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    header("Location: admin.php");
    exit;
}



$username = $_SESSION["username"];
$stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

$creator_id = $user ? $user['id'] : null;

$error = ''; // Inicializujte premennú chyby.

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $subject = $_POST['subject'];
    $option = $_POST['option'];
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



        //$qrCode = new QrCode('https://node75.webte.fei.stuba.sk/zadanieFINAL/answer.php?code=' . $code . '&question_type=options');
        $qrCode = QrCode::create('https://node75.webte.fei.stuba.sk/zadanieFINAL/answer.php?code=' . $code . '&question_type=options')
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
            ->setSize(300)
            ->setMargin(20)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        // Generate the QR code image
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Generate a data URI to include image data inline (i.e. inside an <img> tag)
        $dataUri = $result->getDataUri();

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
                $_SESSION["toast_success"] = "Your question has been created.";
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }
    } else if ($_POST['option'] == 'option2') {

        //$qrCode = new QrCode('https://node75.webte.fei.stuba.sk/zadanieFINAL/answer.php?code=' . $code . '&question_type=open');
        // Create QR code
        $qrCode = QrCode::create('https://node75.webte.fei.stuba.sk/zadanieFINAL/answer.php?code=' . $code . '&question_type=open')
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
            ->setSize(300)
            ->setMargin(20)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        // Generate the QR code image
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Generate a data URI to include image data inline (i.e. inside an <img> tag)
        $dataUri = $result->getDataUri();

        // Insert data into your database
        try {
            $stmt = $db->prepare("INSERT INTO questions_open (creator_id, timestamp, isActive, title, subject, code, type) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$creator_id, $timestamp, '1', $title, $subject, $code, $eval_type]);
            $_SESSION["toast_success"] = "Your question has been created.";
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
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <style>
        .hidden {
            display: none;
        }

        .form-wrapper {
            display: flex;
            justify-content: center;
            margin: 10px;
            flex-direction: column;
        }

        input {
            margin-top: 5px;
        }

        #error-message {
            color: red;
        }
    </style>
</head>

<body>
    <div class="navigation_bar">
        <h2>HOME</h2>
        <div class="navbar">
            <a href="find_question.php">Find question</a>
            <a href="logged_in.php">Home</a>
            <h2><?php echo "Logged in: " . $_SESSION["username"]; ?></h2>
        </div>
    </div>

    <div class="form-wrapper">
        <form id="myForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" onsubmit="validateForm();">
            <input type="radio" name="option" id="option1" value="option1" <?php if (!isset($_POST['option']) || $_POST['option'] === 'option1')
                                                                                echo 'checked'; ?>>
            <label for="option1">Otázka s výberom</label>
            <input type="radio" name="option" id="option2" value="option2" <?php if (isset($_POST['option']) && $_POST['option'] === 'option2')
                                                                                echo 'checked'; ?>>
            <label for="option2">Otvorená otázka</label><br><br>
            <div id="eval_type" class="<?php if (!isset($_POST['option']) || $_POST['option'] === 'option1')
                                            echo 'hidden'; ?>">
                <label>Evaluation Type:</label><br>
                <select name="eval_type_select" id="eval_type_select">
                    <option value="wordcloud">Word Cloud</option>
                    <option value="list">Unordered List</option>
                </select>

            </div>
            <label for="title">Title:</label><br>
            <input type="text" id="title" name="title" required minlength="5" maxlength="100"><br>
            <div id="answers" class="<?php if (!isset($_POST['option']) || $_POST['option'] === 'option2')
                                            echo 'hidden'; ?>">
                <label>Answers:</label><br>
                <input type="text" name="answer1" placeholder="Answer 1" maxlength="100">
                <input type="checkbox" value="1" name="correct1"> Correct<br>
                <input type="text" name="answer2" placeholder="Answer 2" maxlength="100">
                <input type="checkbox" value="1" name="correct2"> Correct<br>
                <input type="text" name="answer3" placeholder="Answer 3" maxlength="100">
                <input type="checkbox" value="1" name="correct3"> Correct<br>
                <input type="text" name="answer4" placeholder="Answer 4" maxlength="100">
                <input type="checkbox" value="1" name="correct4"> Correct<br><br>
            </div>
            <label for="subject">Subject:</label><br>
            <input type="text" id="subject" name="subject" required minlength="3" maxlength="50"><br><br>
            <input type="submit" value="Submit">
            <div id="error-message"><?php echo $error; ?></div>
        </form>
        <?php if (isset($code) && isset($dataUri)) : ?>
            <div class="qr-code-block">
                <label for="code">CODE:</label>
                <input type="text" id="code" value="<?php echo $code; ?>" readonly>
                <img src="<?php echo $dataUri ?>" alt="QR Code">
            </div>
        <?php endif; ?>

    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
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
                console.log("checkujem moznosti");
                validateCheckboxes();
                return document.getElementById('error-message').textContent === '';
            }
            return true;
        }


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
</body>

</html>