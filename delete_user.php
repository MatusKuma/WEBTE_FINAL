
<?php
session_start();
include "../.configFinal.php"; // Zahrnutie databázového pripojenia

// Kontrola prihlásenia a oprávnení
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: index.php");
    exit;
}

// Získanie user_id z URL
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : "die('ERROR: User ID not specified.')";

// Načítanie údajov užívateľa z databázy
$stmt = $db->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

header("location: admin.php");
exit;


