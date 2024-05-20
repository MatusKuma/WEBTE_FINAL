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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <style>
        body {
            background-image: url('design/background4.jpg'); 
            background-size: cover; 
            background-position: center center; 
            background-attachment: fixed;
            color: #B0A8B9; 
            font-family: "Chakra Petch", sans-serif; 
            font-weight: 600; 
        }
        .navbar {
            background-color: #320636;
            padding: 10px 15px;
            font-size: 18px; 
            font-weight: 700; 
            text-transform: uppercase; 
            border-bottom: 3px solid #564366; 
        }
        a,.btn {
                color: #D8BFD8; 
                transition: color 0.3s; 
            }
        a:hover {
            color: #ffffff;
        }
        .btn {
            border-color: #C996CC;
            background-color: transparent;
            padding: 8px 12px;
            transition: color 0.3s ease-in-out, background-color 0.3s ease-in-out, border-color 0.3s ease-in-out; /* Add transition effects */

        }
        .btn:hover{
            color: #ffffff;
            border-color: #C996CC;
            background-color: #2a052e;
        }
            
        .container {
            border: 3px solid #564366;
            border-radius: 30px; 
            padding: 35px; 
            margin-top: 25px; 
            background-color: #320636;
            box-shadow: 0 16px 32px rgba(0,0,0,0.5); 
            max-width: 600px;

        }
        h2 {
            border-bottom: 5px solid #564366; 
            border-top: 5px solid #564366; 
            color: #e0cee0;
            border-radius: 25px;
            padding-top: 5px;
            padding-bottom: 5px;
            margin-bottom: 50px;
        }
        .error-msg {
            color: red;
        }

        #error span {
            color: red;
        }

        .error,.text-danger{
            text-align: center;
        }

        .input-group {
            display: flex;
            flex-direction: column; 
            margin-bottom: 15px; 
        }

        .input-group label {
            margin-bottom: 5px; 
            color: #D8BFD8; 
        }


        .form-control {
            width: 100%; 
            padding: 8px; 
            margin-top: 5px; 
            border: 1px solid #564366; 
            background-color: transparent; 
            color: #D8BFD8; 
        }

        .form-control:focus {
            border-color: #C996CC; 
            outline: none; 
        }

        .btn {
            width: 100%; 
            padding: 10px;
            margin-top: 20px; 
            background-color: #564366; 
            color: #ffffff; 
            border: none; 
        }

        .btn:hover {
            background-color: #6A418F; 
        }
        .input-group .form-control {
            width: 100%;
            flex: none; 
        }
        #toast-container > .toast-success {
        background-image: none !important;
        background-color: #28a745 !important; 
        }

        #toast-container > .toast-error {
            background-image: none !important;
            background-color: #dc3545 !important; 
        }


</style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">WEBTE FINAL</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container mt-3">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="login-form" onclick="validateForm()">
                <h2 class="mb-3 text-center">Register</h2>
                <div class="input-group mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required pattern="[a-zA-Z0-9_-]{5,32}" onblur="validateInput('Please enter Username!', 'Username cannot contain special characters (min-5, max-32)', 'username', 'error-username')">
                    <div id="error-username" class="form-text text-danger">
                        <?php if (isset($error_username)) { echo $error_username; } ?>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required pattern="^.{5,100}$" oninput="validateInput('Please enter Password!', 'Password must be 5-100 characters long', 'password', 'error-password')">
                    <div id="error-password" class="form-text text-danger">
                        <?php if (isset($error_password)) { echo $error_password; } ?>
                    </div>
                </div>
                <div class="error mb-3 text-danger">
                    <span id="userexist"><?php if (isset($errmsg)) { echo $errmsg; } ?></span>
                </div>
                <button type="submit" class="btn">Register</button>
            </form>
        </div>

    <script src="script.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        toastr.options = {
            "positionClass": "toast-bottom-right", // tu sa meni pozicia toastr
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