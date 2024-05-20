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
        header("location: logged_in.php");
        exit;
    }
}
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : 0;
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Change Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        display: block; 
        text-align: center;

    }

    p{

        text-align: center;
    }

    #error span {
        color: red;
        text-align: center;
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
    .nav-link i {
        margin-right: 5px; 
        font-size: 1.5rem; 
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
            <a class="navbar-brand" href="admin.php">WEBTE FINAL</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto"> 
                    <li class="nav-item">
                        <a class="nav-link">
                            <i class="fa fa-user"></i> <?php echo $_SESSION["username"]; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php" aria-label="Log out">
                            <i class="fa-solid fa-right-from-bracket"></i> 
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>  



    <div class="container mt-3">
    <form id="myForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
        
        <h2 class="mb-3 text-center">Change Password</h2>

        <div class="mb-3">
            <label for="current_password" class="form-label">Current Password:</label>
            <input type="password" id="current_password" name="current_password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="new_password" class="form-label">New Password:</label>
            <input type="password" id="new_password" name="new_password" class="form-control" required pattern="^.{5,100}$" oninput="validatePasswords()">
            <span class="error-msg text-danger" id="error-newPassword"><?php if (isset($error_newPassword)) { echo $error_newPassword; } ?></span>
        </div>

        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required oninput="validatePasswords()">
            <span class="error-msg text-danger" id="error-confirmPassword"><?php if (isset($error_confirmPassword)) { echo $error_confirmPassword; } ?></span>
            <p id="message" class="form-text" style="color: red"><?php if (isset($error_message) && !empty($error_message)) {
                                                    echo $error_message;
                                                } ?></p>
        </div>

        <div class="d-grid gap-2">
            <input type="submit" id="submit_btn" class="btn" value="Change Password" disabled>
        </div>
    </form>
</div>

    
    <script>
        function validatePasswords() {
            var newPassword = document.getElementById('new_password');
            var confirmPassword = document.getElementById('confirm_password');
            var currentPassword = document.getElementById('current_password');
            var submitBtn = document.getElementById('submit_btn');
            var message = document.getElementById('message');




            if (newPassword.value === currentPassword.value && newPassword.value !== '') {
                message.textContent = 'Your new password cannot match with your current password';
                message.style.color = 'red';
                newPassword.style.borderColor = 'red';
                submitBtn.disabled = true;
                return;
            } else if (newPassword.value !== confirmPassword.value && (newPassword.value !== '' && confirmPassword.value !== '')) {
                message.textContent = 'Passwords dont match';
                message.style.color = 'red';
                newPassword.style.borderColor = 'red';
                confirmPassword.style.borderColor = 'red';
                submitBtn.disabled = true;
                return;
            } else if (newPassword.value === confirmPassword.value && newPassword.value !== '' && confirmPassword !== '') {
                message.textContent = 'Passwords match';
                message.style.color = 'green';
                confirmPassword.style.borderColor = 'green';
                newPassword.style.borderColor = 'green';
                if (currentPassword.value === '' || !validateInput('Please enter new Password!', 'min-5, max-100', 'new_password', 'error-newPassword')) {
                    submitBtn.disabled = true;
                    message.textContent = '';
                } else {
                    submitBtn.disabled = false;
                }
            } else {
                message.textContent = '';
                message.style.color = '#564366';
                newPassword.style.borderColor = '#564366';
                confirmPassword.style.borderColor = '#564366';
                currentPassword.style.borderColor = '#564366';
                submitBtn.disabled = true;
                return;
            }
        }
    </script>
    <script src="script.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
 
</body>

</html>