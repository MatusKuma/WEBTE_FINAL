<?php
include "../.configFinal.php";

session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: logged_in.php");
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>WEBTE FINAL</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
<<<<<<< HEAD
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css" />
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
        a:hover, .btn:hover {
            color: #ffffff;
        }
        .btn {
            border-color: #C996CC;
            background-color: transparent;
            padding: 8px 12px;
        }

        .container {
            border: 3px solid #564366;
            border-radius: 30px; 
            padding: 35px; 
            margin-top: 25px; 
            background-color: #320636;
            box-shadow: 0 16px 32px rgba(0,0,0,0.5); 
        }
        h2 {
            border-bottom: 5px solid #564366; 
            border-top: 5px solid #564366; 
            color: #e0cee0;
            border-radius: 25px;
            padding-top: 5px;
            padding-bottom: 5px;
            margin-bottom: 25px;
        }

        .container li {
            font-size: 15px; 
            line-height: 1.8; 
            margin-bottom: 2px;
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

        #userTable{
            background-color: #320636;
            color: #B0A8B9;  
            border-collapse: separate; 
            border-spacing: 0;  
        }

        #userTable th, #userTable td {
            border: 1px solid #564366;  
            padding: 8px;  
        }
        #userTable th {
            color: white;
            text-align: center;
        }
        div.dt-container{
            width: 90%;
            margin-left: auto;
            margin-right: auto;
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
                    <li class="nav-item"><a class="nav-link" href="find_question.php">Find question</a></li>
                    <li class="nav-item"><a class="nav-link" href="add_question_admin.php">Add question</a></li>
                    <li class="nav-item"><a class="nav-link" href="add_user.php">Add user</a></li>
                    <li class="nav-item"><a class="nav-link" href=<?php echo "view_questions_user.php?user_id=" . $_SESSION["user_id"] ?>>My questions</a></li>
                    <li class="nav-item"><a class="nav-link" href="q&a_to_csv.php">Export Q&A</a></li>
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
<br><br>
    <table id="userTable" class="display responsive nowrap text-center" style="width:100%">
=======
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

</head>

<body>
    <div class="navigation_bar">
        <div class="navbar">
            <a href="add_question_admin.php">Add question</a>
            <a href="add_user.php">Add User</a>
            <a href="find_question.php">Add User</a>
            <a href=<?php echo "view_questions_user.php?user_id=" . $_SESSION["user_id"] ?>>My questions</a>
            <a href="q&a_to_csv.php">Export Q&A</a>
            <a href="logout.php">Log out</a>
            <h2><?php echo "Logged in: " . $_SESSION["username"]; ?></h2>
        </div>
    </div>

    <table id="userTable" class="display">
>>>>>>> 8ef626f8838a4c03cda942bbe2d69551e0b9f6a8
        <thead>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </thead>
        <tbody>
            <?php

            $stmt = $db->prepare("SELECT * FROM users");
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                    echo "<td><a href='view_questions_admin.php?user_id=" . $row['id'] . "'>View Questions</a></td>";
                    echo "<td><a href='edit_user.php?user_id=" . $row['id'] . "'>Edit</a></td>";
                    echo "<td><a href='delete_user.php?user_id=" . $row['id'] . "'>Delete</a></td>";
                    echo "<td><a href='change_password_admin.php?user_id=" . $row['id'] . "'>Change Password</a></td>";
                    echo "</tr>";
                }
            }
            ?>

        </tbody>
<<<<<<< HEAD
=======


>>>>>>> 8ef626f8838a4c03cda942bbe2d69551e0b9f6a8
    </table>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<<<<<<< HEAD
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            $('#userTable').DataTable({
            responsive: true,
            scrollX: true
        });;
=======
    <script>
        $(document).ready(function() {
            $('#userTable').DataTable();
>>>>>>> 8ef626f8838a4c03cda942bbe2d69551e0b9f6a8
        });


        // toastr nastavenia
        toastr.options = {
<<<<<<< HEAD
            "positionClass": "toast-bottom-right", // tu sa meni pozicia toastr
=======
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
    <script src="script.js"></script>
</body>

</html>