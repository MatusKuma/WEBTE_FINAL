<?php
include "../.configFinal.php";
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header("Location: index.php");
    exit;
} else {
    if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
        header("Location: admin.php");
        exit;
    }
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
            echo "Question deleted successfully";
        } else {
            echo "Error deleting question";
        }
    } else {
        echo "Invalid request";
    }
} else {
    echo "Invalid request method";
}
