<?php
session_start();
include "./.configFinal.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: logged_in.php");
    exit;
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

    $isAdmin = isset($_POST['isAdmin']) ? 1 : 0;
    $valid = true;

    if (checkEmpty($_POST['username']) === true) {
        // netreba toastr, validacia js
        $error_username .= "<p>Enter Username!</p> <br>";
        $valid = false;
    } elseif (checkLength($_POST['username'], 5, 32) === false) {
        $error_username .= "<p>Username must have between 5-32 chars</p><br>";
        $_SESSION["toast_error"] = "Username must have between 5-32 chars";
        $valid = false;
    } elseif (checkUsername($_POST['username']) === false) {
        $error_username .= "<p>Username cannot not contain special characters</p><br>";
        $_SESSION["toast_error"] = "Username cannot not contain special characters";
        $valid = false;
    }

    if (checkEmpty($_POST['password']) === true) {
        // netreba toastr, validacia js
        $error_password .= "<p>Enter Password!</p> <br>";
        $valid = false;
    } elseif (checkLength($_POST['password'], 5, 100) === false) {
        // netreba toastr, validacia js
        $error_password .= "<p>Password must have between 5-100 chars</p><br>";
        $valid = false;
    }


    if (userExist($db, $_POST['username']) === true) {
        $errmsg .= "User with this Username already exist<br></p>";
        $_SESSION["toast_error"] = "User with this Username already exists";
        $valid = false;
    }




    if (empty($errmsg) && $valid) {
        $sql = "INSERT INTO users (username, password, isAdmin) VALUES (:username, :password, :isAdmin)";

        $username = $_POST['username'];
        $hashed_password = password_hash($_POST['password'], PASSWORD_ARGON2ID);

        $stmt = $db->prepare($sql);

        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->bindParam(":password", $hashed_password, PDO::PARAM_STR);
        $stmt->bindParam(":isAdmin", $isAdmin, PDO::PARAM_INT);
        if (!$stmt->execute()) {
            $_SESSION["toast_error"] = "Sorry, something went wrong.";
        } else {
            unset($stmt);
            $_SESSION["toast_success"] = "User was successfully added";
            session_write_close();
            header("Location: admin.php");
            exit;
        }
    }
    unset($db); // toto tu mozno netreba
}

?>
<!doctype html>
<html lang="sk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add User</title>
    <link rel="stylesheet" href="formsheet.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
</head>

<body>
    <div class="navigation_bar">
        <div class="navbar">
            <a href="add_question_admin.php">Add question</a>
            <a href="admin.php">Home</a>
            <a href="logout.php">Log out</a>
            <h2><?php echo "Logged in: " . $_SESSION["username"]; ?></h2>
        </div>
    </div>
    <div class="login-container">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="login-form"
            onclick="validateForm()">
            <h2>Add User</h2>
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required pattern="[a-zA-Z0-9_-]{5,32}"
                    onblur="validateInput('Please enter Username!', 'Username cannot contain special characters (min-5, max-32)', 'username', 'error-username')">
                <span class="error-msg" id="error-username"><?php if (isset($error_username)) {
                                                                echo $error_username;
                                                            } ?></span>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" pattern="^.{5,100}$"
                    oninput="validateInput('Please enter Password!', 'min-5, max-100', 'password', 'error-password')"
                    required>
                <span class="error-msg" id="error-password"><?php if (isset($error_password)) {
                                                                echo $error_password;
                                                            } ?></span>
            </div>
            <div>
                <label>Is Admin:</label>
                <input type="checkbox" name="isAdmin">
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