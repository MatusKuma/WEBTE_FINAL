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
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

</head>

<body>
    <div class="navigation_bar">
        <div class="navbar">
            <a href="add_question_admin.php">Add question</a>
            <a href="add_user.php">Add User</a>
            <a href=<?php echo "view_questions_user.php?user_id=" . $_SESSION["user_id"] ?>>My questions</a>
            <a href="logout.php">Log out</a>
            <h2><?php echo "Logged in: " . $_SESSION["username"]; ?></h2>
        </div>
    </div>

    <table id="userTable" class="display">
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


    </table>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#userTable').DataTable();
        });


        // toastr nastavenia
        toastr.options = {
            "positionClass": "toast-top-right", // tu sa meni pozicia toastr
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