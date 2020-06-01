<?php
require 'pdo.php';
session_start();

if (isset($_POST['cancel'])) {
  header("Location: index.php");
  return;
}

if (isset($_POST['email']) && $_POST['pword']) {
  // Data validation
  if (strlen($_POST['email']) < 1 || strlen($_POST['pword']) < 1) {
    $_SESSION['error'] = 'Missing data';
    header("Location: login.php");
    return;
  }

  if (strpos($_POST['email'], '@') === false) {
    $_SESSION['error'] = 'Incorrect email';
    header("Location: login.php");
    return;
  }
  //salt the password
  $salt = 'XyZzy12*_';
  $check = hash('md5', $salt . $_POST['pword']);

  //prepare adn execute sql query
  $sql = "SELECT user_id, name FROM users 
        WHERE email = :em AND password = :pw";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':em' => $_POST['email'],
    ':pw' => $check,
  ]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  //redirect if valid or show error
  if ($row === false) {
    $_SESSION['error'] = "Incorrect login credentials";
    header("Location: login.php");
    return;
  } else {
    $_SESSION['name'] = $row['name'];
    $_SESSION['user_id'] = $row['user_id'];
    header("Location: index.php");
    return;
  }
}
?>
<html>

<head>
    <title>Resume Registry Login</title>
    <!-- bootstrap.php - this is HTML -->

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

    <link rel="stylesheet" href="style.css">


</head>

<div class="container">

    <body style="font-family: sans-serif;">
        <h1>Please Log In</h1>
        <?php if (isset($_SESSION["error"])) {
          echo '<p style="color:red">' . $_SESSION["error"] . "</p>\n";
          unset($_SESSION["error"]);
        } ?>
        <form method="post" action="login.php">
            <label for="email">Email</label>
            <input type="text" name="email" id="email"><br>
            <label for="pword">Password</label>
            <input type="password" name="pword" id="pword"> <br>
            <!-- password is umsi -->
            <input type="submit" onclick="return doValidate();" value="Log In">
            <input type="submit" name="cancel" value="Cancel">
        </form>
</div>


<script src="script.js"></script>
</body>