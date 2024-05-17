
<?php
session_start();
include "../.configFinal.php"; // Zahrnutie databázového pripojenia

// Kontrola prihlásenia a oprávnení
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: logged_in.php");
    exit;
}

// najdeme ID admina, kvoli kontrole vymazania sameho seba
$adminName = $_SESSION["username"];
$stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$adminName]);
$adminId = $stmt->fetch();

if (!isset($_GET['user_id'])) {
    $_SESSION["toast_error"] = "The user was not found";
} else {
    $user_id = $_GET['user_id'];

    if ($adminId['id'] == $user_id) {
        $_SESSION["toast_error"] = "You cannot remove yourself";
        header("location: admin.php");
        exit;
    }
    
    $stmt = $db->prepare("DELETE FROM questions_open WHERE creator_id = ?");
    $stmt->execute([$user_id]);
    $stmt = $db->prepare("DELETE FROM questions_options WHERE creator_id = ?");
    $stmt->execute([$user_id]);
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);

    if ($stmt->rowCount() === 0) {
        $_SESSION["toast_error"] = "User with ID $user_id not found";
    } else {
        $_SESSION["toast_success"] = "User deleted successfully";
    }
}

header("location: admin.php");
exit;
