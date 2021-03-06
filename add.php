<?php

require 'pdo.php';
require 'util.php';
session_start();

clickCancel();

//check if user is logged in, if not redirect to login.php
$logMsg = loginCheck();
if (is_string($logMsg)) {
  $_SESSION['error'] = $logMsg;
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
  // Data validation for profile fields
  $msg = validateProfile();
  if (is_string($msg)) {
    $_SESSION['error'] = $msg;
    header("Location: add.php");
    return;
  }
  //data validation for position fields if they exist
  $msg = validatePos();
  if (is_string($msg)) {
    $_SESSION['error'] = $msg;
    header("Location: add.php");
    return;
  }

  //data validation for education fields if they exist
  $msg = validatePos();
  if (is_string($msg)) {
    $_SESSION['error'] = $msg;
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

  //get the profile id
  $profile_id = $pdo->lastInsertId();

  //Insert the position entries
  insertPositions($pdo, $profile_id);

  //Insert the education entries
  insertEducations($pdo, $profile_id);

  //success message and redirect
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


  <div class="container">
    <h1>Adding profile for <?= htmlspecialchars($_SESSION['name']) ?></h1>
    <?php flashMessages(); ?>
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

      <p> Institution: <input type="submit" id="addEdu" value="+">
        <div id="edu_fields">
        </div>
      </p>


      <p> Position: <input type="submit" id="addPos" value="+">
        <div id="position_fields">
        </div>
      </p>


      <p><input type="submit" value="Add" />
        <input type="submit" name="cancel" value="Cancel" /></p>
    </form>
  </div>

  <?php require 'foot.php'; ?>
</body>

</html>