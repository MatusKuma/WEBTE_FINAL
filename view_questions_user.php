<?php
include "../.configFinal.php"; // Include your database connection setup
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
            <a href="add_user.php">Add User</a>
            <a href="admin.php">Home</a>
            <a href="logout.php">Log out</a>
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
                <th>Active</th>
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
                    echo "<td><input type='checkbox' class='isActiveCheckbox' data-id='" . $row['id'] . "' " . ($row['isActive'] ? 'checked' : '') . "></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No Open questions found</td></tr>";
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
                <th>Active</th>
            </tr>
        </thead>
        <tbody>
            <?php
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
                    echo "<td><input type='checkbox' class='isActiveCheckbox' data-id='" . $row['id'] . "' " . ($row['isActive'] ? 'checked' : '') . "></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No questions with options found</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>
    <script>
        $(document).ready(function () {
            $('#questionTableOpen').DataTable();
            $('#questionTableOption').DataTable();
        });

        $(document).on('change', '.isActiveCheckbox', function () {
            var id = $(this).data('id');
            var isActive = $(this).is(':checked') ? 1 : 0;
            var table = $(this).closest('table').attr('id');

            $.ajax({
                url: 'update_isActive.php',
                type: 'POST',
                data: {
                    id: id,
                    isActive: isActive,
                    table: table
                },
                success: function (response) {
                    console.log('Status updated successfully');
                },
                error: function (xhr, status, error) {
                    console.error('Error updating status:', error);
                }
            });
        });
    </script>
</body>
</html>
