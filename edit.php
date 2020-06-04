<?php
require_once "pdo.php";
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
  isset($_REQUEST['profile_id'])
) {
  // Data validation
  $msg = validateProfile();
  if (is_string($msg)) {
    $_SESSION['error'] = $msg;
    $pid = $_REQUEST['profile_id'];
    header("Location: edit.php?profile_id=$pid");
    return;
  }

  //data validation for position fields if they exist
  $msg = validatePos();
  if (is_string($msg)) {
    $_SESSION['error'] = $msg;
    $pid = $_REQUEST['profile_id'];
    header("Location: edit.php?profile_id=$pid");
    return;
  }

  $sql =
    "UPDATE profile SET first_name = :fn, last_name= :ln, email = :em, headline = :he, summary = :su WHERE profile_id = :pid AND user_id = :uid";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':fn' => $_POST['first_name'],
    ':ln' => $_POST['last_name'],
    ':em' => $_POST['email'],
    ':he' => $_POST['headline'],
    ':su' => $_POST['summary'],
    ':pid' => $_REQUEST['profile_id'],
    ':uid' => $_SESSION['user_id']
  ]);

  //remove the old position entries
  $sql = 'DELETE FROM Position WHERE profile_id = :pid';
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':pid' => $_REQUEST['profile_id'],
  ]);

  //Insert the position entries
  $rank = 1;
  for (
    $i = 0;
    $i <= 9;
    $i++
  ) {
    if (!isset($_POST['year' . $i])) continue;
    if (!isset($_POST['desc' . $i])) continue;
    $sql = "INSERT INTO Position (profile_id, rank, year, description)
              VALUES (:pid, :ra, :yr, :de)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':pid' => $_REQUEST['profile_id'],
      ':ra' => $rank,
      ':yr' => $_POST['year' . $i],
      ':de' => $_POST['desc' . $i],
    ]);
    $rank++;
  }

  $_SESSION['success'] = "Profile updated";
  header("Location: index.php");
  return;
}

//prepare all user data from database
$sql = 'SELECT * FROM profile where profile_id = :id';
$stmt = $pdo->prepare($sql);
$stmt->execute([
  ':id' => $_REQUEST['profile_id'],
]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

//load up the position rows
$positions = loadPos($pdo, $_REQUEST['profile_id']);



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

<!-- assign number of position items to js variable -->
<script type="text/javascript">
  var countEdit = <?php echo json_encode(count($positions)) ?>;
</script>



<!DOCTYPE html>
<html lang="en">

<head>
  <?php require "head.php"; ?>
  <title>Update entry</title>
</head>

<body>
  <div class="container">

    <h1>Update Entry</h1><br>
    <?php flashMessages(); ?>
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
      <!-- <input type="hidden" name="profile_id" value="<?= $pid ?>"> -->
      <div id="position_fields_database">
        <?php

        foreach ($positions as $position) {
          echo  '<div id="position' .
            $position['rank'] .
            '"><p>Year: <input type="text" name="year' .
            $position['rank']  .
            '" value="'
            . htmlspecialchars($position['year']) .
            '"/><input type="button" value="-"onclick="$(\'#position' .
            $position['rank'] .
            '\').remove();countEdit--; return false;"></p><textarea name="desc' .
            $position['rank']  .
            '" rows="8" cols="80">' .
            htmlspecialchars($position['description']) .
            '</textarea></div>';
        }
        ?>
      </div>
      <p> Position: <input type="submit" id="editPos" value="+">
        <div id="position_fields"></div>
      </p>

      <p><input type="submit" value="Update" />
        <input type="submit" name="cancel" value="Cancel" /></p>
    </form>
  </div>
</body>

</html>