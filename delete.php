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


//when user hits submit prepare sql statement and execute then redirect to index.php
if (isset($_POST['delete']) && isset($_POST['profile_id'])) {
  $sql = 'DELETE FROM Profile WHERE profile_id = :zip';
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':zip' => $_POST['profile_id'],
  ]);

  $_SESSION['success'] = 'Profile deleted';
  header("Location: index.php");
  return;
}

//prepare all user data from database
$row = prepUser($pdo, $_REQUEST['profile_id']);

//check that the value for profile id is valid and if not  redirect
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

  <?php require "foot.php" ?>
</body>

</html>