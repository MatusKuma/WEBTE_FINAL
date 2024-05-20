<?php
include "../.configFinal.php"; // Include your database connection setup

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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>View Questions</title>
<<<<<<< HEAD
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.2/css/dataTables.dataTables.css" />
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
            color: #e0cee0;
            padding-top: 5px;
            padding-bottom: 5px;
            margin-bottom: 40px;
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

        h1{
            color: white;
            font-size: 3.5rem;
            margin-top: 1%;
        }
        @media (max-width: 992px) {
            h1 {
                font-size: 3rem; 
            }
        }


        @media (max-width: 768px) {
            h1 {
                font-size: 2.5rem; 
            }
        }


        @media (max-width: 576px) {
            h1 {
                font-size: 2rem; 
            }
        }

        #questionTableOpen  {
            background-color: #320636;
            color: #B0A8B9;  
            border-collapse: separate; 
            border-spacing: 0;  
        }
        #questionTableOption{
            background-color: #320636;
            color: #B0A8B9;  
            border-collapse: separate; 
            border-spacing: 0;  
        }

        #questionTableOpen th, #questionTableOpen td{
            border: 1px solid #564366;  
            padding: 8px;  
        }
        #questionTableOption th, #questionTableOption td {
            border: 1px solid #564366;  
            padding: 8px;  
        }
        #questionTableOpen th {
            color: white;
            text-align: center;
        }
        #questionTableOption th{
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
    <h1 class="mb-3 text-center">User Questions</h1>
    <br>
    <h2 class="mb-3 text-center">Open Questions</h2>
    <table id="questionTableOpen" class="display responsive nowrap text-center" style="width:100%">
=======
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.2/css/dataTables.dataTables.css" />
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
    <h1>User Questions</h1>
    <h2>Open Questions</h2>
    <table id="questionTableOpen" class="display">
>>>>>>> 8ef626f8838a4c03cda942bbe2d69551e0b9f6a8
        <thead>
            <tr>
                <th>Question Title</th>
                <th>Subject</th>
                <th>Date Created</th>
                <th>Code</th>
            </tr>
        </thead>
        <tbody>
            <?php


            $user_id = $_GET['user_id'];
            $stmt = $db->prepare("SELECT * FROM questions_open WHERE creator_id = ?");
            $stmt->execute([$user_id]);

            if ($stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['subject']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['timestamp']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['code']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td>No questions found for this user</td><td></td><td></td><td></td></tr>";
            }
            ?>
        </tbody>
    </table>
<<<<<<< HEAD
    <h2 class="mb-3 text-center">Questions with Options</h2>
    <table id="questionTableOption" class="display responsive nowrap text-center" style="width:100%">
=======
    <h2>Questions with Options</h2>
    <table id="questionTableOption" class="display">

>>>>>>> 8ef626f8838a4c03cda942bbe2d69551e0b9f6a8
        <thead>
            <tr>
                <th>Question Title</th>
                <th>Subject</th>
                <th>Date Created</th>
                <th>Code</th>
                <th>Option 1</th>
                <th>Option 2</th>
                <th>Option 3</th>
                <th>Option 4</th>
                <th>Correct Options</th>
            </tr>
        </thead>
        <tbody>
            <?php

            $user_id = $_GET['user_id'];
            $stmt = $db->prepare("SELECT * FROM questions_options WHERE creator_id = ?");
            $stmt->execute([$user_id]);

            if ($stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $correct_answers = str_split($row["correct_answer"]);
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['subject']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['timestamp']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['code']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['option_1']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['option_2']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['option_3']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['option_4']) . "</td>";
                    echo "<td>";
                    foreach ($correct_answers as $i) {
                        echo $row["option_" . $i] . ",";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td>No questions found for this user</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
            }
            ?>
        </tbody>
    </table>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>
<<<<<<< HEAD
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            $('#questionTableOpen').DataTable({
            responsive: true,
            scrollX: true
        });;

        $('#questionTableOption').DataTable({
            responsive: true,
            scrollX: true
        });;
=======
    <script>
        $(document).ready(function() {
            $('#questionTableOpen').DataTable();
        });
        $(document).ready(function() {
            $('#questionTableOption').DataTable();
>>>>>>> 8ef626f8838a4c03cda942bbe2d69551e0b9f6a8
        });
    </script>
</body>

</html>