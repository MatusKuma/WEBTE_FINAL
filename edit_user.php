<?php
session_start();
include "../.configFinal.php"; // Zahrnutie databázového pripojenia

// Kontrola prihlásenia a oprávnení
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: logged_in.php");
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Získanie hodnôt z formulára
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $isAdmin = isset($_POST['isAdmin']) ? 1 : 0;

    // Aktualizácia údajov v databáze
    $stmt = $db->prepare("UPDATE users SET username = ?, isAdmin = ? WHERE id = ?");
    if ($stmt->execute([$username, $isAdmin, $user_id])) {
        $_SESSION["toast_success"] = "User updated successfully";
    } else {
        $_SESSION["toast_error"] = "Failed to update user";
    }
    // Presmerovanie späť na zoznam užívateľov alebo informovanie o úspechu
    header("Location: admin.php");
    exit;
}
// Získanie user_id z URL
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : "die('ERROR: User ID not specified.')";

// Načítanie údajov užívateľa z databázy
$stmt = $db->prepare("SELECT username, isAdmin FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('ERROR: User not found.');
}

$username = $user['username'];
$isAdmin = $user['isAdmin'];

<<<<<<< HEAD
=======



>>>>>>> 8ef626f8838a4c03cda942bbe2d69551e0b9f6a8
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit User</title>
<<<<<<< HEAD
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
        margin-bottom: 35px;
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
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="add_question_admin.php">Add question</a></li>
                    <li class="nav-item"><a class="nav-link" href="add_user.php">Add user</a></li>
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
    <h2 class="mt-3 text-center">Edit User</h2>
    <form id="myForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="needs-validation">
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">

        <div class="mb-3">
            <label for="username" class="form-label">Username:</label>
            <input type="text" class="form-control" name="username" id="username" value="<?php echo htmlspecialchars($username); ?>" required>
            <div class="invalid-feedback">
                Please enter a username.
            </div>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" name="isAdmin" id="isAdmin" <?php echo $isAdmin ? 'checked' : ''; ?>>
            <label class="form-check-label" for="isAdmin">Is Admin</label>
        </div>

        <div class="mb-3">
            <button type="submit" class="btn">Save Changes</button>
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
            <a href="add_question_admin.php">Add question</a>
            <a href="add_user.php">Add User</a>
            <a href="admin.php">Home</a>
            <a href="logout.php">Log out</a>
            <h2><?php echo "Logged in: " . $_SESSION["username"]; ?></h2>
        </div>
    </div>
    <h1>Edit User</h1>
    <form id="myForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
        <div>
            <label>Username:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
        </div>
        <div>
            <label>Is Admin:</label>
            <input type="checkbox" name="isAdmin" <?php echo $isAdmin ? 'checked' : ''; ?>>
        </div>
        <div>
            <input type="submit" value="Save Changes">
        </div>
    </form>
</body>

>>>>>>> 8ef626f8838a4c03cda942bbe2d69551e0b9f6a8
</html>