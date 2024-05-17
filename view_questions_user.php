<?php
include "../.configFinal.php"; // Include your database connection setup
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header("Location: index.php");
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
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.2/css/dataTables.dataTables.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
</head>

<body>
    <div class="navigation_bar">
        <div class="navbar">
            <a href="find_question.php">Find question</a>
            <a href="add_question_user.php">Add question</a>
            <a href="logged_in.php">Home</a>
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
                <th>Subject</th>
                <th>Date Created</th>
                <th>Code</th>
                <th>Active</th>
                <th>Edit</th>
                <th>Delete</th>
                <th>Copy</th>
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
                    echo "<td><input type='checkbox' class='isActiveCheckbox' data-id='" . $row['id'] . "' " . ($row['isActive'] ? 'checked' : '') . "></td>";
                    echo "<td><a href='edit_question_open.php?id=" . $row['id'] . "'>Edit</a></td>";
                    echo "<td><a href='#' class='delete-link' data-id='" . $row['id'] . "' data-type='open'>Delete</a></td>";
                    echo "<td><a href='#' class='copy-link' data-id='" . $row['id'] . "' data-type='open'>Copy</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td>No Open questions found</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
            }
            ?>
        </tbody>
    </table>
    <h2>Questions with Options</h2>
    <table id="questionTableOption" class="display">
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
                <th>Active</th>
                <th>Edit</th>
                <th>Delete</th>
                <th>Copy</th>
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
                    echo "<td><input type='checkbox' class='isActiveCheckbox' data-id='" . $row['id'] . "' " . ($row['isActive'] ? 'checked' : '') . "></td>";
                    echo "<td><a href='edit_question_option.php?id=" . $row['id'] . "'>Edit</a></td>";
                    echo "<td><a href='#' class='delete-link' data-id='" . $row['id'] . "' data-type='option'>Delete</a></td>";
                    echo "<td><a href='#' class='copy-link' data-id='" . $row['id'] . "' data-type='option'>Copy</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td>No questions with options found</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
            }
            ?>
        </tbody>
    </table>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#questionTableOpen').DataTable();
            $('#questionTableOption').DataTable();
        });

        $(document).on('change', '.isActiveCheckbox', function() {
            var id = $(this).data('id');
            var isActive = $(this).is(':checked') ? 1 : 0;
            var table = $(this).closest('table').attr('id');


            $.ajax({
                url: 'update_isActive.php',
                type: 'POST',
                data: {
                    id: id,
                    isActive: isActive,
                    table: table,
                    toast_success: '<?php echo isset($_SESSION["toast_success"]) ? $_SESSION["toast_success"] : "" ?>',
                    toast_error: '<?php echo isset($_SESSION["toast_error"]) ? $_SESSION["toast_error"] : "" ?>'
                },
                success: function(response) {
                    console.log('Status updated successfully');
                    if (response.toast_success) {
                        toastr.success(response.toast_success);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error updating status:', error);
                    if (response.toast_error) {
                        toastr.error(response.toast_error);
                    }
                }
            });

        });

        $(document).on('click', '.delete-link', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var type = $(this).data('type');

            $.ajax({
                url: 'delete_question.php',
                type: 'POST',
                data: {
                    id: id,
                    type: type,
                    toast_success: '<?php echo isset($_SESSION["toast_success"]) ? $_SESSION["toast_success"] : "" ?>',
                    toast_error: '<?php echo isset($_SESSION["toast_error"]) ? $_SESSION["toast_error"] : "" ?>'
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting question:', error);
                    if (response.toast_error) {
                        toastr.error(response.toast_error);
                    }
                }
            });
        });

        $(document).on('click', '.copy-link', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var type = $(this).data('type');

            $.ajax({
                url: 'copy_question.php',
                type: 'POST',
                data: {
                    id: id,
                    type: type,
                    toast_success: '<?php echo isset($_SESSION["toast_success"]) ? $_SESSION["toast_success"] : "" ?>',
                    toast_error: '<?php echo isset($_SESSION["toast_error"]) ? $_SESSION["toast_error"] : "" ?>'
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr, status, error) {
                    console.error('Error copying question:', error);
                    if (response.toast_error) {
                        toastr.error(response.toast_error);
                    }
                }
            });
        });

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
</body>

</html>