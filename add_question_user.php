<?php 
    session_start();
    // echo $_SESSION['admin'] ? 'Admin is true' : 'Admin is false';
    include "../.configFinal.php";

    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
        header("Location: index.php");
        exit;
    } else {
        if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
            header("Location: admin.php");
            exit;
        }
    }

    $username = $_SESSION["username"];
    // Query to fetch the creator_id based on the username
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Check if a user with the given username exists
    if ($user) {
        // If a user is found, retrieve the id
        $creator_id = $user['id'];
    } else {
        // Handle the case where the user doesn't exist (optional)
        // For example, you can set a default value or display an error message
        $creator_id = null; // Set a default value
        // You can also redirect the user to a login page or display an error message
        // header("Location: login.php");
        // exit();
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $title = $_POST['title'];
        $subject = $_POST['subject'];
        
        $timestamp = date("Y-m-d H:i:s");

        // Check which option is selected
        if ($_POST['option'] == 'option1') {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);

            // Process data for Option 1
            $answers = array();
            $correct_answer = '';
            for ($i = 1; $i <= 4; $i++) {
                $answer = $_POST['answer' . $i];
                $correct = isset($_POST['correct' . $i]) ? '1' : '0';
                $answers[] = $answer;
                if ($correct === '1') {
                    $correct_answer .= $i; // Concatenate the correct answer indices
                }
            }
            // Convert the array of answers to a string
            $answers_string = implode(',', $answers);
            // Insert data into your database
            try {
                $stmt = $db->prepare("INSERT INTO questions_options (title, correct_answer, option_1, option_2, option_3, option_4, subject, timestamp, creator_id, isActive) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
                $stmt->execute([$title, $correct_answer, $answers[0], $answers[1], $answers[2], $answers[3], $subject, $timestamp, $creator_id]);
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        } else if ($_POST['option'] == 'option2') {
            // Process data for Option 2
            $singleAnswer = $_POST['singleAnswer'];
            // Insert data into your database
            try {
                $stmt  = $db->prepare("INSERT INTO questions_open (creator_id, timestamp, isActive, title, subject) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$creator_id, $timestamp, '1', $title, $subject]);
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }
    }

?>
<!DOCTYPE html>
<html>
<head>
    <title>WEBTE FINAL</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
  .hidden {
    display: none;
  }

  .form-wrapper{
    display: flex;
    justify-content: center;
    margin: 10px;
  }
  input{
    margin-top: 5px;
  }
</style>
<body>
    <div class="navigation_bar">
        <h2>HOME</h2>
        <div class="navbar">
            <a href="add_question_user.php">Pridaj otázku</a>
            <a href="logout.php">Log out</a>
            <h2><?php echo "Logged in: " . $_SESSION["username"]; ?></h2> 
        </div>
    </div>

<div class="form-wrapper">
<form id="myForm"  action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
<input type="radio" name="option" id="option1" value="option1" checked>
<label for="option1">Otázka s výberom</label>
<input type="radio" name="option" id="option2" value="option2">
<label for="option2">Otvorená otázka</label><br><br>

  <label for="title">Title:</label><br>
  <input type="text" id="title" name="title" required><br>

  <div id="answers" class="hidden">
    <label>Answers:</label><br>
    <input type="text" name="answer1" placeholder="Answer 1">
    <input type="checkbox" name="correct1"> Correct<br>
    <input type="text" name="answer2" placeholder="Answer 2">
    <input type="checkbox" name="correct2"> Correct<br>
    <input type="text" name="answer3" placeholder="Answer 3">
    <input type="checkbox" name="correct3"> Correct<br>
    <input type="text" name="answer4" placeholder="Answer 4">
    <input type="checkbox" name="correct4"> Correct<br><br>
  </div>


  <label for="subject">Subject:</label><br>
  <input type="text" id="subject" name="subject" required><br><br>

  <input type="submit" value="Submit">
</form>

</div>
<script>
  document.getElementById('option1').addEventListener('change', function() {
    document.getElementById('answers').classList.remove('hidden');
  });

  document.getElementById('option2').addEventListener('change', function() {
    document.getElementById('answers').classList.add('hidden');
  });

  // Ensure the answers section is displayed if the first option is selected by default
  if (document.getElementById('option1').checked) {
    document.getElementById('answers').classList.remove('hidden');
  }
</script>
<script src="script.js"></script>
</body>
</html>
