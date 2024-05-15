<?php
include "../.configFinal.php";
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header("Location: index.php");
    exit;
}

if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    header("Location: admin.php");
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id']) && isset($_POST['isActive']) && isset($_POST['table'])) {
        $id = $_POST['id'];
        $isActive = $_POST['isActive'];
        $table = $_POST['table'];

        if ($table === 'questionTableOpen') {
            $stmt = $db->prepare("UPDATE questions_open SET isActive = ? WHERE id = ?");
        } else if ($table === 'questionTableOption') {
            $stmt = $db->prepare("UPDATE questions_options SET isActive = ? WHERE id = ?");
        } else {
            echo "Invalid table name";
            exit;
        }

        if ($stmt->execute([$isActive, $id])) {
            echo "Status updated successfully";
        } else {
            echo "Error updating status";
        }
    } else {
        echo "Invalid request";
    }
} else {
    echo "Invalid request method";
}
