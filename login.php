<?php
session_start();
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    if (isset($_SESSION["admin"]) && $_SESSION["admin"] === true) {
        header("Location: admin.php");
        exit;
    } else {
        header("Location: logged_in.php");
        exit;
    }
}

include "../.configFinal.php";

function checkEmpty($field)
{
    if (empty(trim($field))) {
        return true;
    }
    return false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $valid = true;

    if (checkEmpty($_POST['username']) === true) {
        $error_username = "<p>Enter Username!</p> <br>";
        $valid = false;
    }
    if (checkEmpty($_POST['password']) === true) {
        $error_password = "<p>Enter Password !</p> <br>";
        $valid = false;
    }
    if ($valid) {
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(":username", $_POST["username"], PDO::PARAM_STR);
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $hashed_password = $row["password"];

                if (password_verify($_POST["password"], $hashed_password)) {


                    $_SESSION["loggedin"] = true;
                    $_SESSION["username"] = $row['username'];
                    $_SESSION["admin"] = ($row['isAdmin'] == 1);
                    $_SESSION["user_id"] = $row["id"];
                    header("location: logged_in.php");
                    exit;
                } else {
                    $error .= "Wrong username or password<br>";
                }
            } else {
                $error .= "Wrong username or password<br>";
            }
        } else {
            $error .= "Something went wrong!<br>";
        }
        unset($stmt);
        unset($db);
    }
}
?>

<!doctype html>
<html lang="sk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
<<<<<<< HEAD
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
        transition: color 0.3s ease-in-out, background-color 0.3s ease-in-out, border-color 0.3s ease-in-out;

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
        border-radius: 8px;
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
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="login-form">
                    <h2 class="mb-3 text-center">Login</h2>
                    <div class="input-group mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" name="username" class="form-control" required pattern="[a-zA-Z0-9_]{5,32}" onblur="validateIsEmpty('Please enter Username!', 'username', 'error-username')">
                        <div id="error-username" class="form-text text-danger"></div>
                        <div class="form-text text-danger"><?php if (isset($error_username)) { echo $error_username; } ?></div>
                    </div>
                    <div class="input-group mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required onblur="validateIsEmpty('Please enter Password!', 'password', 'error-password')">
                        <div id="error-password" class="form-text text-danger"></div>
                        <div class="form-text text-danger"><?php if (isset($error_password)) { echo $error_password; } ?></div>
                    </div>
                    <div class="error mb-3" style="color: red;">
                        <span id="error"><?php if (isset($error)) { echo $error; } ?></span>
                    </div>
                    <button type="submit" class="btn">Login</button>
                </form>
        </div>


=======
    <link rel="stylesheet" href="formsheet.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
</head>

<body>
    <div class="navigation_bar">
        <h2>LOGIN</h2>
        <div class="navbar">
            <a href="register.php">Register</a>
            <a href="index.php">Home</a>
        </div>
    </div>
    <div class="login-container">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="login-form">
            <div class="error" style="margin-bottom: 10px;">
                <span id="error" style="color: red;"><?php if (isset($error)) {
                                                            echo $error;
                                                        } ?></span>
            </div>
            <h2>Login</h2>
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required pattern="[a-zA-Z0-9_]{5,32}" onblur="validateIsEmpty('Please enter Username!', 'username', 'error-username')">
                <span id="error-username"></span>
                <span id="error" style="color: red;"><?php if (isset($error_username)) {
                                                            echo $error_username;
                                                        } ?></span>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required onblur="validateIsEmpty('Please enter password', 'password', 'error-password')">
                <span class="error-msg" id="error-password"></span>
                <span id="error" style="color: red;"><?php if (isset($error_password)) {
                                                            echo $error_password;
                                                        } ?></span>
            </div>


            <button type="submit">Login</button>
        </form>
    </div>

>>>>>>> 8ef626f8838a4c03cda942bbe2d69551e0b9f6a8

    <script src="script.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<<<<<<< HEAD
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        toastr.options = {
            "positionClass": "toast-bottom-right", // tu sa meni pozicia toastr
=======
    <script>
        toastr.options = {
            "positionClass": "toast-top-right", // tu sa meni pozicia toastr
>>>>>>> 8ef626f8838a4c03cda942bbe2d69551e0b9f6a8
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