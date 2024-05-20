<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header("Location: index.php");
    exit;
}

// Získanie úrovne oprávnení používateľa
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;

// Includovanie konfiguračného súboru pre databázu
require_once("../.configFinal.php");

// Funkcia na export otázok do CSV súboru
function exportQuestionsToCSV($db, $filename, $userId, $isAdmin)
{
    try {
        // Overenie prítomnosti záznamov v tabuľke questions_options
        $stmtOptions = $db->prepare("SELECT COUNT(*) FROM questions_options WHERE isActive = 1 AND (creator_id = :userId OR :isAdmin)");
        $stmtOptions->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmtOptions->bindParam(':isAdmin', $isAdmin, PDO::PARAM_BOOL);
        $stmtOptions->execute();
        $countOptions = $stmtOptions->fetchColumn();

        // Overenie prítomnosti záznamov v tabuľke questions_open
        $stmtOpen = $db->prepare("SELECT COUNT(*) FROM questions_open WHERE isActive = 1 AND (creator_id = :userId OR :isAdmin)");
        $stmtOpen->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmtOpen->bindParam(':isAdmin', $isAdmin, PDO::PARAM_BOOL);
        $stmtOpen->execute();
        $countOpen = $stmtOpen->fetchColumn();

        if ($countOptions > 0 || $countOpen > 0) {
            // Otvorenie súboru pre zápis
            $fp = fopen($filename, 'w');

            // Napísanie hlavičky CSV súboru
            fputcsv($fp, array('Title', 'Correct Answer', 'Option 1', 'Option 2', 'Option 3', 'Option 4', 'Subject'));

            // Export otázok z tabuľky questions_options
            if ($countOptions > 0) {
                $stmt = $db->prepare("SELECT * FROM questions_options WHERE isActive = 1 AND (creator_id = :userId OR :isAdmin)");
                $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                $stmt->bindParam(':isAdmin', $isAdmin, PDO::PARAM_BOOL);
                $stmt->execute();
                $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($questions as $question) {
                    // Check if there are multiple correct answers
                    $correctAnswerString = $question['correct_answer'];
                    $correctAnswers = str_split($correctAnswerString);
            
                    // Join correct answers with space
                    $correctAnswersString = implode(' ', $correctAnswers);
            
                    fputcsv($fp, array(
                        $question['title'],
                        $correctAnswersString, // Correct answer with space between multiple correct answers
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
                $stmt = $db->prepare("SELECT * FROM questions_open WHERE isActive = 1 AND (creator_id = :userId OR :isAdmin)");
                $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                $stmt->bindParam(':isAdmin', $isAdmin, PDO::PARAM_BOOL);
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

            return true;
        } else {
            $_SESSION["toast_error"] = "Žiadne aktívne otázky na export.";
            return false;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

// Funkcia na export odpovedí do CSV súboru
function exportAnswersToCSV($db, $filename, $userId, $isAdmin)
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
                $stmt = $db->prepare("SELECT ao.*, qo.option_1, qo.option_2, qo.option_3, qo.option_4 
                                      FROM answers_options ao
                                      JOIN questions_options qo ON ao.question_id = qo.id");
                $stmt->execute();
                $answers = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($answers as $answer) {
                    $answerStr = '';
                    $answerArr = str_split($answer['answer']);
                    foreach ($answerArr as $ans) {
                        $answerStr .= $answer['option_' . $ans] . ' ';
                    }
                    fputcsv($fp, array(
                        $answer['question_id'],
                        trim($answerStr),
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

            return true;
        } else {
            $_SESSION["toast_error"] = "Žiadne odpovede na export.";
            return false;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

try {
    // Získanie ID používateľa a úrovne oprávnení
    $userId = $_SESSION["user_id"];
    $isAdmin = $_SESSION['admin'];

    // Použitie existujúcej premennej $db z konfiguračného súboru
    $exportQuestionsSuccess = exportQuestionsToCSV($db, 'questions.csv', $userId, $isAdmin);
    $exportAnswersSuccess = exportAnswersToCSV($db, 'answers.csv', $userId, $isAdmin);

    if ($exportQuestionsSuccess && $exportAnswersSuccess) {
        // Both exports were successful, so concatenate the files
        $questionsContent = file_get_contents('questions.csv');
        $answersContent = file_get_contents('answers.csv');

        // Concatenate the contents
        $combinedContent = $questionsContent . $answersContent;

        // Send headers for file download
        header("Content-Disposition: attachment; filename=\"questions_answers.csv\"");
        header("Content-Type: application/csv");

        // Output the concatenated content
        echo $combinedContent;
    } else {
        // At least one export failed
        header("Location: logged_in.php");
        exit;
    }
} catch (PDOException $e) {
    echo "Connection error: " . $e->getMessage();
}
