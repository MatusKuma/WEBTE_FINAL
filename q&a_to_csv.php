<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Includovanie konfiguračného súboru pre databázu
require_once("../.configFinal.php");

// Funkcia na export otázok do CSV súboru
function exportQuestionsToCSV($db, $filename)
{
    try {
        // Overenie prítomnosti záznamov v tabuľke questions_options
        $stmtOptions = $db->prepare("SELECT COUNT(*) FROM questions_options WHERE isActive = 1");
        $stmtOptions->execute();
        $countOptions = $stmtOptions->fetchColumn();

        // Overenie prítomnosti záznamov v tabuľke questions_open
        $stmtOpen = $db->prepare("SELECT COUNT(*) FROM questions_open WHERE isActive = 1");
        $stmtOpen->execute();
        $countOpen = $stmtOpen->fetchColumn();

        if ($countOptions > 0 || $countOpen > 0) {
            // Otvorenie súboru pre zápis
            $fp = fopen($filename, 'w');

            // Napísanie hlavičky CSV súboru
            fputcsv($fp, array('Title', 'Correct Answer', 'Option 1', 'Option 2', 'Option 3', 'Option 4', 'Subject'));

            // Export otázok z tabuľky questions_options
            if ($countOptions > 0) {
                $stmt = $db->prepare("SELECT * FROM questions_options WHERE isActive = 1");
                $stmt->execute();
                $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($questions as $question) {
                    fputcsv($fp, array(
                        $question['title'],
                        $question['correct_answer'],
                        $question['option_1'],
                        $question['option_2'],
                        $question['option_3'],
                        $question['option_4'],
                        $question['subject']
                    ));
                }
            }

            // Export otázok z tabuľky questions_open
            if ($countOpen > 0) {
                $stmt = $db->prepare("SELECT * FROM questions_open WHERE isActive = 1");
                $stmt->execute();
                $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($questions as $question) {
                    fputcsv($fp, array(
                        $question['title'],
                        'N/A', // Pre otázky s otvorenou odpoveďou neexistuje správna odpoveď
                        'N/A', // Pre otázky s otvorenou odpoveďou neexistujú možnosti
                        'N/A', // Pre otázky s otvorenou odpoveďou neexistujú možnosti
                        'N/A', // Pre otázky s otvorenou odpoveďou neexistujú možnosti
                        'N/A', // Pre otázky s otvorenou odpoveďou neexistujú možnosti
                        $question['subject']
                    ));
                }
            }

            // Zatvorenie súboru
            fclose($fp);

            // URL, na ktorú chcete zprávu odkázať
            $linkUrl = 'questions.csv'; // Nahraďte vašou cieľovou URL tu

            // Text odkazu
            $linkText = "Otázky boli úspešne exportované do CSV súboru: $filename";

            // Výpis s odkazom a bielou farbou textu
            echo '<a href="' . $linkUrl . '" style="color: white;">' . $linkText . '</a><br>';
        } else {
            $_SESSION["toast_error"] = "Žiadne aktívne otázky na export.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Funkcia na export odpovedí do CSV súboru
function exportAnswersToCSV($db, $filename)
{
    try {
        // Overenie prítomnosti záznamov v tabuľkách answers_options a answers_open
        $stmtOptions = $db->prepare("SELECT COUNT(*) FROM answers_options");
        $stmtOptions->execute();
        $countOptions = $stmtOptions->fetchColumn();

        $stmtOpen = $db->prepare("SELECT COUNT(*) FROM answers_open");
        $stmtOpen->execute();
        $countOpen = $stmtOpen->fetchColumn();

        if ($countOptions > 0 || $countOpen > 0) {
            // Otvorenie súboru pre zápis
            $fp = fopen($filename, 'w');

            // Napísanie hlavičky CSV súboru
            fputcsv($fp, array('Question ID', 'Answer', 'Timestamp', 'User ID', 'Type'));

            // Export odpovedí z tabuľky answers_options
            if ($countOptions > 0) {
                $stmt = $db->prepare("SELECT * FROM answers_options");
                $stmt->execute();
                $answers = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($answers as $answer) {
                    fputcsv($fp, array(
                        $answer['question_id'],
                        $answer['answer'],
                        $answer['timestamp'],
                        $answer['user_id'],
                        'options'
                    ));
                }
            }

            // Export odpovedí z tabuľky answers_open
            if ($countOpen > 0) {
                $stmt = $db->prepare("SELECT * FROM answers_open");
                $stmt->execute();
                $answers = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($answers as $answer) {
                    fputcsv($fp, array(
                        $answer['question_id'],
                        $answer['answer'],
                        $answer['timestamp'],
                        $answer['user_id'],
                        'open'
                    ));
                }
            }

            // Zatvorenie súboru
            fclose($fp);

            // URL, na ktorú chcete zprávu odkázať
            $linkUrl = 'answers.csv'; // Nahraďte vašou cieľovou URL tu

            // Text odkazu
            $linkText = "Odpovede boli úspešne exportované do CSV súboru: $filename";

            // Výpis s odkazom a bielou farbou textu
            echo '<a href="' . $linkUrl . '" style="color: white;">' . $linkText . '</a><br>';
        } else {
            $_SESSION["toast_error"] = "Žiadne odpovede na export.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Použitie existujúceho pripojenia k databáze z konfiguračného súboru
try {
    // Použitie existujúcej premennej $db z konfiguračného súboru
    exportQuestionsToCSV($db, 'questions.csv');
    exportAnswersToCSV($db, 'answers.csv');
    header("Location: logged_in.php");
    exit;
} catch (PDOException $e) {
    echo "Connection error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>WEBTE FINAL</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
</head>

<body>

    <script src="script.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
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
    </script>
</body>

</html>