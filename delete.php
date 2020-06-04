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

//check that profile id is present
if (!isset($_GET['profile_id'])) {
  $_SESSION['error'] = 'Invalid profile id';
  header("Location: index.php");
  return;
}

//when user hits submit prepare sql statement and execute then redirect to index.php
if (isset($_POST['delete']) && isset($_POST['profile_id'])) {
  $sql = 'DELETE FROM Profile WHERE profile_id = :zip';
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':zip' => $_POST['profile_id'],
  ]);

  //remove the old position entries
  $sql = 'DELETE FROM Position WHERE profile_id = :zip';
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':zip' => $_POST['profile_id'],
  ]);

  $_SESSION['success'] = 'Profile deleted';
  header("Location: index.php");
  return;
}

//check that profile id is present
if (!isset($_GET['profile_id'])) {
  $_SESSION['error'] = 'Invalid profile id';
  header("Location: index.php");
  return;
}

//prepare user id and name from database
$sql =
  'SELECT first_name, last_name, headline, profile_id, user_id FROM profile where profile_id = :id';
$stmt = $pdo->prepare($sql);
$stmt->execute([
  ':id' => $_GET['profile_id'],
]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

//check that the value for user id is valid and if not  redirect
if ($row == false) {
  $_SESSION['error'] = "Invalid Profile ID";
  header("Location: index.php");
  return;
}

//check that this is the users profile
if ($row['user_id'] != $_SESSION['user_id']) {
  $_SESSION['error'] = "You do not have permission to delete this profile";
  header("Location: index.php");
  return;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php require "head.php"; ?>
  <title>Delete Profile</title>
</head>

<body>
  <div class="container">
    <h1>Deleting Profile</h1>
    <?php flashMessages(); ?>
    <p>First Name: <?= htmlspecialchars($row['first_name']) ?></p>
    <p>Last Name: <?= htmlspecialchars($row['last_name']) ?></p>
    <p>Headline: <?= htmlspecialchars($row['headline']) ?></p>
    <form action="" method="post">
      <input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
      <input type="submit" value="Delete" name="delete">
      <input type="submit" name="cancel" value="Cancel">
    </form>

  </div>

</body>

</html>