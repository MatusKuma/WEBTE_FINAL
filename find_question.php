<?php
session_start();

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
        if ($question) {
            header("Location: answer.php?code=" . $code . "&question_type=options");
            exit;
        } else {
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
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Zadaj Kód</title>
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
        a, .btn {
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
            margin-top: 75px; 
            background-color: #320636;
            box-shadow: 0 16px 32px rgba(0,0,0,0.5); 
            max-width: 500px;
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
        <a class="navbar-brand" href="logged_in.php">WEBTE FINAL</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION["username"])) : ?>
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
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>




    <div class="container form-wrapper">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <div class="mb-3">
                <label for="code" class="form-label">Question Code</label>
                <input type="text" class="form-control" id="code" name="code" required>
            </div>
            <div class="mb-3 ">
                <input type="submit" class="btn center" value="Find question">
            </div>
            <!-- Optional error message display -->
            <?php if (isset($error) && $error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
        </form>
=======
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
</head>

<body>
    <div class="navigation_bar">
        <div class="navbar">
            <a href="logged_in.php">Home</a>
            <?php if (isset($_SESSION["username"])) : ?>
                <a href="logout.php">Log out</a>
                <h2><?php echo "Logged in: " . $_SESSION["username"]; ?></h2>
            <?php endif; ?>
        </div>
    </div>
    <div class="form-wrapper">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <label for="code">Question Code</label>
            <input type="text" id="code" name="code" required>
            <input type="submit" value="Find question">
        </form>
        <!-- <?php if ($error) : ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?> -->
>>>>>>> 8ef626f8838a4c03cda942bbe2d69551e0b9f6a8
    </div>


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