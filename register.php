<?php
session_start();
include "../.configFinal.php";

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    if (isset($_SESSION["admin"]) && $_SESSION["admin"] === true) {
        header("Location: admin.php");
        exit;
    } else {
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

function checkUsername($username)
{
    if (!preg_match("/^[a-zA-Z0-9_]+$/", trim($username))) {
        return false;
    }
    return true;
}

function checkSpecialChars($field)
{
    if (preg_match("/[0-9!@#$%^&*(),.?\":{}|<>]/", trim($field))) {
        return false; // 
    }
    return true;
}

function userExist($db, $username)
{
    $exist = false;
    $param_username = trim($username);
    $sql = "SELECT id FROM users WHERE username=:username";
    $stmt = $db->prepare($sql);
    $stmt->bindParam("username", $param_username, PDO::PARAM_STR);

    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $exist = true;
    }
    unset($stmt);

    return $exist;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {


    $valid = true;

    if (checkEmpty($_POST['username']) === true) {
        $error_username .= "<p>Enter Username!</p> <br>";
        $valid = false;
    } elseif (checkLength($_POST['username'], 5, 32) === false) {
        $error_username .= "<p>Username must have between 5-32 chars</p><br>";
        $valid = false;
    } elseif (checkUsername($_POST['username']) === false) {
        $error_username .= "<p>Username cannot not contain special characters</p><br>";
        $valid = false;
    }

    if (checkEmpty($_POST['password']) === true) {
        $error_password .= "<p>Enter Password!</p> <br>";
        $valid = false;
    } elseif (checkLength($_POST['password'], 5, 100) === false) {
        $error_password .= "<p>Password must have between 5-100 chars</p><br>";
        $valid = false;
    }


    if (userExist($db, $_POST['username']) === true) {
        $errmsg .= "User with this Username already exist<br></p>";
        $valid = false;
    }




    if (empty($errmsg) && $valid) {
        $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";

        $username = $_POST['username'];
        $hashed_password = password_hash($_POST['password'], PASSWORD_ARGON2ID);

        $stmt = $db->prepare($sql);

        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->bindParam(":password", $hashed_password, PDO::PARAM_STR);
        if (!$stmt->execute()) {
            $_SESSION["toast_error"] = "Sorry, something went wrong.";
        } else {
            unset($stmt);
            $_SESSION["toast_success"] = "You have been successfully registered";
            header("Location: login.php");
        }
    }
    unset($db);
}

?>
<!doctype html>
<html lang="sk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register Form</title>
    <link rel="stylesheet" href="formsheet.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
</head>

<body>
    <div class="navigation_bar">
        <h2>Register Form</h2>
        <div class="navbar">
            <a href="login.php">Login</a>
            <a href="index.php">Home</a>
        </div>
    </div>
    <div class="login-container">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="login-form" onclick="validateForm()">
            <h2>Register</h2>
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required pattern="[a-zA-Z0-9_-]{5,32}" onblur="validateInput('Please enter Username!', 'Username cannot contain special characters (min-5, max-32)', 'username', 'error-username')">
                <span class="error-msg" id="error-username"><?php if (isset($error_username)) {
                                                                echo $error_username;
                                                            } ?></span>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" pattern="^.{5,100}$" oninput="validateInput('Please enter Password!', 'min-5, max-100', 'password', 'error-password')" required>
                <span class="error-msg" id="error-password"><?php if (isset($error_password)) {
                                                                echo $error_password;
                                                            } ?></span>
            </div>
            <span class="error-msg" id="userexist"><?php if (isset($errmsg)) {
                                                        echo $errmsg;
                                                    } ?></span>
            <button type="submit">Register</button>
        </form>
    </div>
    <script src="script.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
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