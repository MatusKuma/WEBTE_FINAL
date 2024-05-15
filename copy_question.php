<?php
include "../.configFinal.php";
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header("Location: index.php");
    exit;
} 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id']) && isset($_POST['type'])) {
        $id = $_POST['id'];
        $type = $_POST['type'];

        if ($type === 'open') {
            $stmt = $db->prepare("SELECT * FROM questions_open WHERE id = ?");
            $stmt->execute([$id]);
            $question = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($question) {
                $newCode = generateUniqueCode('questions_open');
                $stmt = $db->prepare("INSERT INTO questions_open (title, subject, timestamp, creator_id, isActive, code, type) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$question['title'], $question['subject'], date('Y-m-d H:i:s'), $question['creator_id'], $question['isActive'], $newCode, $question["type"]]);
                echo "Question copied successfully";
            } else {
                echo "Question not found";
            }
        } else if ($type === 'option') {
            $stmt = $db->prepare("SELECT * FROM questions_options WHERE id = ?");
            $stmt->execute([$id]);
            $question = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($question) {
                $newCode = generateUniqueCode('questions_options');
                $stmt = $db->prepare("INSERT INTO questions_options (title, option_1, option_2, option_3, option_4, correct_answer, subject, timestamp, creator_id, isActive, code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$question['title'], $question['option_1'], $question['option_2'], $question['option_3'], $question['option_4'], $question['correct_answer'], $question['subject'], date('Y-m-d H:i:s'), $question['creator_id'], $question['isActive'], $newCode]);
                echo "Question copied successfully";
            } else {
                echo "Question not found";
            }
        } else {
            echo "Invalid question type";
        }
    } else {
        echo "Invalid request";
    }
} else {
    echo "Invalid request method";
}

function generateUniqueCode($table) {
    global $db;
    do {
        $code = randString();
        $stmt = $db->prepare("SELECT COUNT(*) FROM $table WHERE code = ?");
        $stmt->execute([$code]);
        $count = $stmt->fetchColumn();
    } while ($count > 0);
    return $code;
}

function randString() {
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    $charLength = strlen($chars);
    $randomStr = '';
    for ($i = 0; $i < 5; $i++) {
        $randomStr .= $chars[rand(0, $charLength - 1)];
    }
    return $randomStr;
}
?>
