<?php
require_once "pdo.php";
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

  //data validation for education fields if they exist
  $msg = validateEdu();
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
  insertPositions($pdo, $_REQUEST['profile_id']);

  //remove the old education entries
  $sql = 'DELETE FROM Education WHERE profile_id = :pid';
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':pid' => $_REQUEST['profile_id'],
  ]);

  //Insert the education entries
  insertEducations($pdo, $_REQUEST['profile_id']);

  $_SESSION['success'] = "Profile updated";
  header("Location: index.php");
  return;
}

//prepare all user data from database
$row = prepUser($pdo, $_REQUEST['profile_id']);

//load up the position rows
$positions = loadPos($pdo, $_REQUEST['profile_id']);

//load up the education rows
$schools = loadEdu($pdo, $_REQUEST['profile_id']);


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
  var countPos = <?php echo json_encode(count($positions)) ?>;
  var countEdu = <?php echo json_encode(count($schools)) ?>;
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

      <div id="edu_fields_database">
        <p> Institution: <input type="submit" id="addEdu" value="+"></p>
        <?php
        foreach ($schools as $school) {
          echo  '<div id="edu' .
            $school['rank'] .
            '"><p>Year: <input type="text" name="edu_year' .
            $school['rank']  .
            '" value="'
            . htmlspecialchars($school['year']) .
            '"/><input type="button" value="-"onclick="$(\'#edu' .
            $school['rank'] .
            '\').remove(); return false;"></p><p>School: <input type="text" size="80" name="edu_school' .
            $school['rank'] .
            '" class="school" value="'
            . htmlspecialchars($school['name']) .
            '"/></p></div>';
        }
        ?>
        <div id="edu_fields">
        </div>
      </div>


      <div id="position_fields_database">
        <p> Position: <input type="submit" id="addPos" value="+"></p>
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
            '\').remove();countPos--; return false;"></p><textarea name="desc' .
            $position['rank']  .
            '" rows="8" cols="80">' .
            htmlspecialchars($position['description']) .
            '</textarea></div>';
        }
        ?>
        <div id="position_fields"></div>
      </div>

      <p><input type="submit" value="Update" />
        <input type="submit" name="cancel" value="Cancel" /></p>
    </form>
  </div>

  <?php require "foot.php" ?>
</body>

</html>