<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header("Location: index.php");
    exit;
} else {
    if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
        header("Location: admin.php");
        exit;
    }
}

include "../.configFinal.php";

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['code'])) {
    $code = trim($_POST['code']);
    $stmt = $db->prepare("SELECT id FROM questions_open WHERE code = ?");
    $stmt->execute([$code]);
    $question = $stmt->fetch();

    if ($question) {
        header("Location: answer.php?code=" . $code . "&question_type=open");
        exit;
    } else {
        $stmt = $db->prepare("SELECT id FROM questions_options WHERE code = ?");
        $stmt->execute([$code]);
        $question = $stmt->fetch();
        if($question){
            header("Location: answer.php?code=" . $code . "&question_type=options");
            exit;
        }
        else{
            // $error = "Kód nebol nájdený.";
            $_SESSION["toast_error"] = "The code was not found";
        }
        
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Zadaj Kód</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
</head>
<body>
<div class="navigation_bar">
        <h2>HOME</h2>
        <div class="navbar">
            <a href="find_question.php">Find question</a>
            <a href="add_question_user.php">Add question</a>
            <a href="logout.php">Log out</a>
            <h2><?php echo "Logged in: " . $_SESSION["username"]; ?></h2> 
        </div>
    </div>
    <div class="form-wrapper">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <label for="code">Question Code</label>
            <input type="text" id="code" name="code" required>
            <input type="submit" value="Find question">
        </form>
        <!-- <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?> -->
    </div>


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
