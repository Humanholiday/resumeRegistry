<?php

require 'pdo.php';
session_start();

if (isset($_POST['cancel'])) {
  header("Location: index.php");
  return;
}

if (!isset($_SESSION['user_id'])) {
  $_SESSION['error'] = 'Not logged in';
  header("Location: login.php");
  return;
}

if (
  isset($_POST['first_name']) &&
  isset($_POST['first_name']) &&
  isset($_POST['email']) &&
  isset($_POST['headline']) &&
  isset($_POST['summary'])
) {
  // Data validation
  if (
    strlen($_POST['first_name']) < 1 ||
    strlen($_POST['last_name']) < 1 ||
    strlen($_POST['email']) < 1 ||
    strlen($_POST['headline']) < 1 ||
    strlen($_POST['summary']) < 1
  ) {
    $_SESSION['error'] = 'All fields must be completed';
    header("Location: add.php");
    return;
  }

  if (strpos($_POST['email'], '@') === false) {
    $_SESSION['error'] = 'Bad data';
    header("Location: add.php");
    return;
  }

  //prepare and execute query
  $sql = "INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary)
              VALUES (:uid, :fn, :ln, :em, :he, :su)";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':uid' => $_SESSION['user_id'],
    ':fn' => $_POST['first_name'],
    ':ln' => $_POST['last_name'],
    ':em' => $_POST['email'],
    ':he' => $_POST['headline'],
    ':su' => $_POST['summary'],
  ]);
  $_SESSION['success'] = 'Record Added';
  header('Location: index.php');
  return;
}

// Flash pattern
if (isset($_SESSION['error'])) {
  echo '<p style="color:red">' . $_SESSION['error'] . "</p>\n";
  unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Profile</title>
    <!-- bootstrap.php - this is HTML -->

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php if (isset($_SESSION["error"])) {
      echo '<p style="color:red">' . $_SESSION["error"] . "</p>\n";
      unset($_SESSION["error"]);
    } ?>

    <div class="container">
        <h1>Adding entry for <?= htmlspecialchars($_SESSION['name']) ?></h1>
        <form method="post">
            <p>First Name:
                <input type="text" name="first_name" size="60"></p>
            <p>Last Name:
                <input type="text" name="last_name" size="60"></p>
            <p>Email:
                <input type="text" name="email" size="60"></p>
            <p>Headline: <br>
                <input type="text" name="headline" size="80"></p>
            <p>Summary: <br>
                <textarea name="summary" rows="8" cols="80"></textarea></p>
            <p><input type="submit" value="Add" />
                <input type="submit" name="cancel" value="Cancel" /></p>
        </form>
    </div>

</body>

</html>