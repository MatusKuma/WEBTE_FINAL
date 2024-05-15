<?php
include "../.configFinal.php";
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header("Location: index.php");
    exit;
} else {
    if (!isset($_SESSION['admin']) && $_SESSION['admin'] === false) {
        header("Location: logged_in.php");
        exit;
    }
}
function checkEmpty($field)
{
    if (empty(trim($field))) {
        return true;
    }
    return false;
}
function checkLength($field, $min, $max)
{
    $string = trim($field);
    $length = strlen($string);
    if ($length < $min || $length > $max) {
        return false;
    }
    return true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST["user_id"];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $valid = true;
    $error_message = "";

    if (checkEmpty($_POST['current_password']) === true) {
        $error_message = "Enter Current Password!<br>";
        $valid = false;
    }
    if (checkEmpty($_POST['new_password']) === true) {
        $error_message = "Enter New Password!<br>";
        $valid = false;
    } elseif (checkLength($_POST['new_password'], 5, 100) === false) {
        $error_message = "New Password must have between 5-100 chars<br>";
        $valid = false;
    }



    if ($new_password != $confirm_password) {
        $error_message = "New passwords do not match."; //toaster by bolo treba
        $valid = false;
    }


    $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($current_password, $user['password'])) {
        $error_message = "Your current password is wrong";
        $valid = false;
    }
    if ($valid) {
        $new_password_hash = password_hash($new_password, PASSWORD_ARGON2ID);
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$new_password_hash, $user_id]);
        $_SESSION["toast_success"] = "Your password has been changed successfully";
        header("location: admin.php");
        exit;
    }
}
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : 0;
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <link rel="stylesheet" href="style.css">

</head>

<body>
    <div class="navigation_bar">
        <h2>Change Password</h2>
        <div class="navbar">
            <a href="admin.php">Home</a>
            <h2><?php echo "Logged in: " . $_SESSION["username"]; ?></h2>
        </div>
    </div>


    <form id="myForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
        <div>
            <label>Current Password:</label>
            <input type="password" id="current_password" name="current_password" required>
        </div>
        <div>
            <label>New Password:</label>
            <input type="password" id="new_password" name="new_password" required pattern="^.{5,100}$" oninput="validateInput('Please enter new Password!', 'min-5, max-100', 'new_password', 'error-newPassword')">
            <span class="error-msg" id="error-newPassword"><?php if (isset($error_newPassword)) {
                                                                echo $error_newPassword;
                                                            } ?></span>
        </div>
        <div>
            <label>Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required oninput="validatePasswords();">
            <span class="error-msg" id="error-confirmPassword"><?php if (isset($error_confirmPassword)) {
                                                                    echo $error_confirmPassword;
                                                                } ?></span>
            <p id="message" style="color: red"><?php if (isset($error_message) && !empty($error_message)) {
                                                    echo $error_message;
                                                } ?></p>
        </div>
        <div>
            <input type="submit" id="submit_btn" value="Change Password" disabled>
        </div>
    </form>
    <script>
        function validatePasswords() {
            var newPassword = document.getElementById('new_password');
            var confirmPassword = document.getElementById('confirm_password');
            var currentPassword = document.getElementById('current_password');
            var submitBtn = document.getElementById('submit_btn');
            var message = document.getElementById('message');

            console.log("aktualne heslo: " + currentPassword.value);
            console.log("nove heslo: " + newPassword.value);
            console.log("potvrdzujuce heslo: " + confirmPassword.value);



            if (newPassword.value === currentPassword.value && newPassword.value !== '') {
                message.textContent = 'Your new password cannot match with your current password';
                message.style.color = 'red';
                newPassword.style.borderColor = 'red';
                submitBtn.disabled = true;
                console.log("1");
                return;
            } else if (newPassword.value !== confirmPassword.value && (newPassword.value !== '' && confirmPassword.value !== '')) {
                message.textContent = 'Passwords dont match';
                message.style.color = 'red';
                newPassword.style.borderColor = 'red';
                confirmPassword.style.borderColor = 'red';
                submitBtn.disabled = true;
                console.log("2");
                return;
            } else if (newPassword.value === confirmPassword.value && newPassword.value !== '' && confirmPassword !== '') {
                message.textContent = 'Passwords match';
                message.style.color = 'green';
                confirmPassword.style.borderColor = 'green';
                newPassword.style.borderColor = 'green';
                console.log("3");
                if (currentPassword.value === '' || !validateInput('Please enter new Password!', 'min-5, max-100', 'new_password', 'error-newPassword')) {
                    submitBtn.disabled = true;
                } else {
                    submitBtn.disabled = false;
                }
            } else {
                message.textContent = '';
                message.style.color = 'black';
                newPassword.style.borderColor = 'black';
                confirmPassword.style.borderColor = 'black';
                currentPassword.style.borderColor = 'black';
                submitBtn.disabled = true;
                return;
            }
        }
    </script>
    <script src="script.js"></script>
</body>

</html>