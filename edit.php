<?php
require_once 'pdo.php';
require 'util.php';
session_start();

clickCancel();

if (!isset($_SESSION['user_id'])) {
  $_SESSION['error'] = 'Not logged in';
  header("Location: login.php");
  return;
}

//update database
if (
  isset($_POST['first_name']) &&
  isset($_POST['first_name']) &&
  isset($_POST['email']) &&
  isset($_POST['headline']) &&
  isset($_POST['summary']) &&
  isset($_POST['profile_id'])
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
    $pid = $_POST['profile_id'];
    header("Location: edit.php?profile_id=$pid");
    return;
  }

  if (strpos($_POST['email'], '@') === false) {
    $_SESSION['error'] = 'Invalid email';
    $pid = $_POST['profile_id'];
    header("Location: edit.php?profile_id=$pid");
    return;
  }

  $sql =
    "UPDATE profile SET first_name = :fn, last_name= :ln, email = :em, headline = :he, summary = :su WHERE profile_id = :profile_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':fn' => $_POST['first_name'],
    ':ln' => $_POST['last_name'],
    ':em' => $_POST['email'],
    ':he' => $_POST['headline'],
    ':su' => $_POST['summary'],
    ':profile_id' => $_POST['profile_id'],
  ]);
  $_SESSION['success'] = "Entry updated";
  header("Location: index.php");
  return;
}

//prepare all user data from database
$sql = 'SELECT * FROM profile where profile_id = :id';
$stmt = $pdo->prepare($sql);
$stmt->execute([
  ':id' => $_GET['profile_id'],
]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

//check that the value for user id is valid and if not redirect
if ($row == false) {
  $_SESSION['error'] = "Invalid Profile ID";
  header("Location: index.php");
  return;
}

//check that this is the users profile
if ($row['user_id'] != $_SESSION['user_id']) {
  $_SESSION['error'] = "You do not have permission to edit this profile";
  header("Location: index.php");
  return;
}

//assign database data to variables
$fn = htmlspecialchars($row['first_name']);
$ln = htmlspecialchars($row['last_name']);
$em = htmlspecialchars($row['email']);
$he = htmlspecialchars($row['headline']);
$su = htmlspecialchars($row['summary']);
$pid = $row['profile_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php require "head.php"; ?>
  <title>Update entry</title>
</head>

<body>
  <!-- check for error flash message -->
  <?php flashMessages(); ?>

  <div class="container">
    <h1>Update Entry</h1><br>
    <form method="post">
      <p>First Name:
        <input type="text" name="first_name" size="60" value=" <?= $fn ?>"></p>
      <p>Last Name:
        <input type="text" name="last_name" size="60" value="<?= $ln ?>"></p>
      <p>Email:
        <input type="text" name="email" size="60" value="<?= $em ?>"></p>
      <p>Headline: <br>
        <input type="text" name="headline" size="80" value="<?= $he ?>"></p>
      <p>Summary: <br>
        <textarea name="summary" rows="8" cols="80"><?= $su ?></textarea></p>
      <input type="hidden" name="profile_id" value="<?= $pid ?>">
      <p><input type="submit" value="Update" />
        <input type="submit" name="cancel" value="Cancel" /></p>
    </form>
  </div>
</body>

</html>