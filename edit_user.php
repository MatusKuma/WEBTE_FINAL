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


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Získanie hodnôt z formulára
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $isAdmin = isset($_POST['isAdmin']) ? 1 : 0;

    // Aktualizácia údajov v databáze
    $stmt = $db->prepare("UPDATE users SET username = ?, isAdmin = ? WHERE id = ?");
    if ($stmt->execute([$username, $isAdmin, $user_id])) {
        $_SESSION["toast_success"] = "User updated successfully";
    } else {
        $_SESSION["toast_error"] = "Failed to update user";
    }
    // Presmerovanie späť na zoznam užívateľov alebo informovanie o úspechu
    header("Location: admin.php");
    exit;
}
// Získanie user_id z URL
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : "die('ERROR: User ID not specified.')";

// Načítanie údajov užívateľa z databázy
$stmt = $db->prepare("SELECT username, isAdmin FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('ERROR: User not found.');
}

$username = $user['username'];
$isAdmin = $user['isAdmin'];




?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit User</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="navigation_bar">
        <div class="navbar">
            <a href="add_question_admin.php">Add question</a>
            <a href="add_user.php">Add User</a>
            <a href="admin.php">Home</a>
            <a href="logout.php">Log out</a>
            <h2><?php echo "Logged in: " . $_SESSION["username"]; ?></h2>
        </div>
    </div>
    <h1>Edit User</h1>
    <form id="myForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
        <div>
            <label>Username:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
        </div>
        <div>
            <label>Is Admin:</label>
            <input type="checkbox" name="isAdmin" <?php echo $isAdmin ? 'checked' : ''; ?>>
        </div>
        <div>
            <input type="submit" value="Save Changes">
        </div>
    </form>
</body>

</html>