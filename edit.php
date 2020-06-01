<?php
require_once 'pdo.php';
session_start();

//when form is submitted, check validity then insert into database and redirect to index.php
if (isset($_POST['cancel'])) {
  header("Location: index.php");
  return;
}

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update entry</title>
    <!-- bootstrap.php - this is HTML -->

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- check for error flash message -->
    <?php if (isset($_SESSION['error'])) {
      echo '<p style="color:red">' . $_SESSION['error'] . "</p>\n";
      unset($_SESSION['error']);
    } ?>

    <div>
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