<?php 
    include "../.configFinal.php";
    
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
        
?>
<!DOCTYPE html>
<html>
<head>
    <title>WEBTE FINAL</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <div class="navigation_bar">
        <h2>HOME</h2>
        <div class="navbar">
            <a href="login.php">Login</a>
            <a href="register.php">Register</a> 
        </div>
    </div>
    
    

<script src="script.js"></script>
</body>
</html>