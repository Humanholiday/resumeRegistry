<?php

require 'pdo.php';
require 'util.php';
session_start();

clickCancel();

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

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php require 'head.php'; ?>
  <title>Add Profile</title>
</head>

<body>
  <?php flashMessages(); ?>

  <div class="container">
    <h1>Adding profile for <?= htmlspecialchars($_SESSION['name']) ?></h1>
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