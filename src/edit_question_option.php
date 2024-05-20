<?php
include "../.configFinal.php";
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header("Location: index.php");
    exit;
}




$id = $_GET['id'];
$stmt = $db->prepare("SELECT * FROM questions_options WHERE id = ?");
$stmt->execute([$id]);
$question = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $option1 = $_POST['option_1'];
    $option2 = $_POST['option_2'];
    $option3 = $_POST['option_3'];
    $option4 = $_POST['option_4'];
    $correct_answer = implode('', array_filter(array_keys($_POST['correct']), function ($val) {
        return is_numeric($val);
    }));
    $isActive = isset($_POST['isActive']) ? 1 : 0;

    $stmt = $db->prepare("UPDATE questions_options SET title = ?, option_1 = ?, option_2 = ?, option_3 = ?, option_4 = ?, correct_answer = ?, isActive = ? WHERE id = ?");
    $stmt->execute([$title, $option1, $option2, $option3, $option4, $correct_answer, $isActive, $id]);
    $_SESSION["toast_success"] = "Question has been updated successfully";
    session_write_close();
    header("Location: view_questions_user.php?user_id=" . $_SESSION['user_id']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit Question with Options</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .hidden {
            display: none;
        }

        #error-message {
            color: red;
        }

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
            margin-bottom: 60px;
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

        .form-control::placeholder { 
            color: #D8BFD8;
            opacity: 1; 
        }

        .form-control:-ms-input-placeholder { 
            color: #D8BFD8;
        }

        .form-control::-ms-input-placeholder { 
            color: #D8BFD8;
        }
        .img-fluid{
            margin-top: 3%;
        }
        .form-check,.form-check-input{
            margin-top: 0.45rem;
        }
        .form-check-label{
            margin-top: 0.2rem;
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
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href=<?php echo "view_questions_user.php?user_id=" . $_SESSION["user_id"] ?>>My questions</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link">
                            <i class="fa fa-user"></i> <?php echo $_SESSION["username"]; ?>
                        </a>
                    </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </a>
                </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-3">
    <h2 class="mb-3 text-center">Edit Question with Options</h2>
    <form action="edit_question_option.php?id=<?php echo $id; ?>" method="post">
        <div class="mb-3">
            <label for="title" class="form-label">Question Title:</label>
            <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($question['title']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="option_1" class="form-label">Option 1:</label>
            <input type="text" id="option_1" name="option_1" class="form-control" value="<?php echo htmlspecialchars($question['option_1']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="option_2" class="form-label">Option 2:</label>
            <input type="text" id="option_2" name="option_2" class="form-control" value="<?php echo htmlspecialchars($question['option_2']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="option_3" class="form-label">Option 3:</label>
            <input type="text" id="option_3" name="option_3" class="form-control" value="<?php echo htmlspecialchars($question['option_3']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="option_4" class="form-label">Option 4:</label>
            <input type="text" id="option_4" name="option_4" class="form-control" value="<?php echo htmlspecialchars($question['option_4']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Correct Options:</label>
            <div>
                <?php
                for ($i = 1; $i <= 4; $i++) {
                    echo "<div class='form-check form-check-inline'>";
                    echo "<input class='form-check-input' type='checkbox' id='correct$i' name='correct[$i]' value='$i' " . (strpos($question['correct_answer'], (string)$i) !== false ? 'checked' : '') . ">";
                    echo "<label class='form-check-label' for='correct$i'>Option $i</label>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>

        <div class="mb-3">
            <label for="isActive" class="form-label">Active:</label>
            <input type="checkbox" id="isActive" name="isActive" class="form-check-input" <?php echo $question['isActive'] ? 'checked' : ''; ?>>
        </div>

        <div class="d-grid">
            <input type="submit" value="Update Question" class="btn">
        </div>
    </form>
</div>

</body>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
</html>