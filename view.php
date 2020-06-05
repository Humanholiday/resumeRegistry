<?php
require_once 'pdo.php';
require "util.php";
session_start();

//when form is submitted, check validity then insert into database and redirect to index.php
if (isset($_POST['done'])) {
  header("Location: index.php");
  return;
}

//prepare all user data from database
$row = prepUser($pdo, $_REQUEST['profile_id']);

//load up the position rows
$positions = loadPos($pdo, $_REQUEST['profile_id']);

//load up the education rows
$educations = loadEdu($pdo, $_REQUEST['profile_id']);

//assign database data to variables
$fn = htmlspecialchars($row['first_name']);
$ln = htmlspecialchars($row['last_name']);
$em = htmlspecialchars($row['email']);
$he = htmlspecialchars($row['headline']);
$su = htmlspecialchars($row['summary']);
$pid = $row['profile_id'];

//check that the value for user id is valid and if not redirect
if ($row == false) {
  $_SESSION['error'] = "Invalid Profile ID";
  header("Location: index.php");
  return;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

  <title>Profile view</title>
  <?php require "head.php"; ?>
</head>

<body>


  <div class="container">
    <h1>Profile Information</h1><br>
    <?php flashMessages(); ?>
    <p>First Name:&nbsp;<?= $fn ?></p>
    <p>Last Name:&nbsp;<?= $ln ?></p>
    <p>Email:&nbsp;<?= $em ?></p>
    <p>Headline: <br>
      <?= $he ?></p>
    <p>Summary: <br>
      <?= $su ?></p>
    <?php

    foreach ($positions as $position) {
      echo "<p>Position Year:&nbsp" . htmlspecialchars($position['year']) . "</p>";
      echo "<p>Position Description:<br>" . htmlspecialchars($position['description']) . "</p>";
    }

    foreach ($educations as $education) {
      echo "<p>Education Year:&nbsp" . htmlspecialchars($position['year']) . "</p>";
      echo "<p>Institution:<br>" . htmlspecialchars($position['name']) . "</p>";
    }
    ?>
    <form action="" method="post">
      <input type="submit" value="Done" name="done">
    </form>

  </div>
</body>

</html>