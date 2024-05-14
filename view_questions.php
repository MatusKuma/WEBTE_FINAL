<?php
include "../.configFinal.php"; // Include your database connection setup
            session_start();

            // Check if user is logged in and a user ID is present in URL
            if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_GET['user_id'])) {
                header("Location: index.php.php");
                exit;
            }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Questions</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.2/css/dataTables.dataTables.css" />
</head>

<body>
    <div class="navigation_bar">
        <div class="navbar">
            <a href="add_question_user.php">Add question</a>
            <a href="logout.php">Log out</a>
            <a href="admin.php">Home</a>
            <h2><?php echo "Logged in: " . $_SESSION["username"]; ?></h2>
        </div>
    </div>
    <h1>User Questions</h1>
    <h2>Open Questions</h2>
    <table id="questionTableOpen" class="display">
        <thead>
            <tr>
            <th>Question Title</th>
            <th>Date Created</th>
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
                    echo "<td>" . htmlspecialchars($row['timestamp']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='2'>No questions found for this user.</td><td></td></tr>";
            }
            ?>
        </tbody>
    </table>
    <h2>Questions with Options</h2>
    <table id="questionTableOption" class="display">

        <thead>
        <tr>
            <th>Question Title</th>
            <th>Date Created</th>
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
                    echo "<td>" . htmlspecialchars($row['timestamp']) . "</td>";
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
                echo "<tr><td colspan='2'>No questions found for this user.</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
            }
            ?>
        </tbody>
    </table>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>
    <script>

        $(document).ready(function () {
            $('#questionTableOpen').DataTable();
        });
        $(document).ready(function () {
            $('#questionTableOption').DataTable();
        });
    </script>
</body>

</html>