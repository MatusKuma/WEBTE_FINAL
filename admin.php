<?php 
    include "../.configFinal.php";
    
    session_start();
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false){
        header("Location: index.php");
        exit;
    }else{
        if(!isset($S_SESSION['admin']) && $S_SESSION['admin'] === false){
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
            <a href="logout.php">Log out</a>
            <h2><?php echo "Logged in: " . $_SESSION["username"] ?></h2> 
        </div>
    </div>
    
    

<script src="script.js"></script>
</body>
</html>