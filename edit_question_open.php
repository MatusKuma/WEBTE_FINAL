<?php
include "../.configFinal.php";
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header("Location: index.php");
    exit;
}




$id = $_GET['id'];
$stmt = $db->prepare("SELECT * FROM questions_open WHERE id = ?");
$stmt->execute([$id]);
$question = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $isActive = isset($_POST['isActive']) ? 1 : 0;

    $stmt = $db->prepare("UPDATE questions_open SET title = ?, isActive = ? WHERE id = ?");
    $stmt->execute([$title, $isActive, $id]);
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
    <title>Edit Open Question</title>
<<<<<<< HEAD
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>

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

        .nav-link i {
            margin-right: 5px; 
            font-size: 1.5rem; 
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
    <h2 class="mb-3 text-center">Edit Open Question</h2>
    <form action="edit_question_open.php?id=<?php echo $id; ?>" method="post" class="form-container">
        <div class="mb-3">
            <label for="title" class="form-label">Question Title:</label>
            <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($question['title']); ?>" required>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="isActive" name="isActive" <?php echo $question['isActive'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="isActive">Active</label>
        </div>
        <div class="d-grid">
            <input type="submit" value="Update Question" class="btn">
        </div>
    </form>
</div>

</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  
=======
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="navigation_bar">
        <div class="navbar">
            <a href=<?php echo "view_questions_user.php?user_id=" . $_SESSION["user_id"] ?>>My questions</a>
            <a href="logged_in.php">Home</a>
            <a href="logout.php">Log out</a>
            <h2><?php echo "Logged in: " . $_SESSION["username"]; ?></h2>
        </div>
    </div>

    <h2>Edit Open Question</h2>
    <form action="edit_question_open.php?id=<?php echo $id; ?>" method="post">
        <div>
            <label>Question Title:</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($question['title']); ?>" required>
        </div>
        <div>
            <label>Active:</label>
            <input type="checkbox" name="isActive" <?php echo $question['isActive'] ? 'checked' : ''; ?>>
        </div>
        <div>
            <input type="submit" value="Update Question">
        </div>
    </form>
</body>

>>>>>>> 8ef626f8838a4c03cda942bbe2d69551e0b9f6a8
</html>