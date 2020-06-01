<?php
require_once 'pdo.php';
session_start();

//when form is submitted, check validity then insert into database and redirect to index.php
if (isset($_POST['done'])) {
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile view</title>
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

    <div class="container">
        <h1>Profile Information</h1><br>
        <p>First Name:&nbsp;<?= $fn ?></p>
        <p>Last Name:&nbsp;<?= $ln ?></p>
        <p>Email:&nbsp;<?= $em ?></p>
        <p>Headline: <br>
            <?= $he ?></p>
        <p>Summary: <br>
            <?= $su ?></p>
        <form action="" method="post">
            <input type="submit" value="Done" name="done">
        </form>

    </div>
</body>

</html>