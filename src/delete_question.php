<?php
include "./.configFinal.php";
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
            $stmt = $db->prepare("DELETE FROM questions_open WHERE id = ?");
        } else if ($type === 'option') {
            $stmt = $db->prepare("DELETE FROM questions_options WHERE id = ?");
        } else {
            echo "Invalid question type";
            exit;
        }

        if ($stmt->execute([$id])) {
            $_SESSION["toast_success"] = "Question deleted successfully";
        } else {
            $_SESSION["toast_error"] = "Error deleting question";
        }
    } else {
        $_SESSION["toast_error"] = "Invalid request";
    }
} else {
    $_SESSION["toast_error"] = "Invalid request method";
}