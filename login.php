<?php
     session_start();
     if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
        if(isset($_SESSION["admin"]) && $_SESSION["admin"] === true){
            header("Location: admin.php");
            exit;
        }else{
            header("Location: logged_in.php");
            exit;
        }
    }
    
    include "../.configFinal.php";

    function checkEmpty($field){
        if(empty(trim($field))){
            return true;
        }
        return false;
      
    }

    if($_SERVER["REQUEST_METHOD"] == "POST"){
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
        if($stmt->execute()){
            if($stmt->rowCount() == 1 ){
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $hashed_password = $row["password"];

                if(password_verify($_POST["password"], $hashed_password)){

    
                        $_SESSION["loggedin"] = true;
                        $_SESSION["username"] = $row['username'];
                        $_SESSION["admin"] = ($row['isAdmin'] == 1);
                        $_SESSION["user_id"] = $row["id"];
                        header("location: logged_in.php");
                        exit;
                }
                else{
                    $error .= "Wrong username or password<br>";
                }
            }
            else{
                $error .= "Wrong username or password<br>";
            }
        }
        else{
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
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
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
                <span id="error" style="color: red;"><?php if(isset($error)){
                    echo $error;
                } ?></span>
            </div>
            <h2>Login</h2>
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required pattern="[a-zA-Z0-9_]{5,32}" onblur="validateIsEmpty('Please enter Username!', 'username', 'error-username')" >
                <span id="error-username"></span>
                <span id="error" style="color: red;"><?php if(isset($error_username)){
                    echo $error_username;
                } ?></span>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required onblur="validateIsEmpty('Please enter password', 'password', 'error-password')">
                <span class="error-msg" id="error-password"></span>
                <span id="error" style="color: red;"><?php if(isset($error_password)){
                    echo $error_password;
                } ?></span>
            </div>
            
            
            <button type="submit">Login</button>
        </form>
</div>

    
<script src="script.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
      toastr.options = {
          "positionClass": "toast-top-right",     // tu sa meni pozicia toastr
      };

      <?php if(isset($_SESSION["toast_success"])): ?>
         toastr.success('<?php echo $_SESSION["toast_success"]; ?>');

         <?php unset($_SESSION["toast_success"]); ?>
      <?php endif; ?>

      <?php if(isset($_SESSION["toast_error"])): ?>
        toastr.error('<?php echo $_SESSION["toast_error"]; ?>');

         <?php unset($_SESSION["toast_error"]); ?>
     <?php endif; ?>
</script>
</body>
</html>