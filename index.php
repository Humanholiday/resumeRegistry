<?php
require_once "pdo.php";
require "util.php";
session_start();
?>
<html>

<head>
  <?php require "head.php"; ?>
  <title>Jack Healy's Resume Registry</title>
</head>

<body>

  <div class='container'>
    <h1>Jack Healy's Resume Registry</h1>
    <?php

    flashMessages();


    if (!isset($_SESSION['user_id'])) {
      echo '<h4><a href="login.php">Please Log In</a></h4>';
    }

    echo '<table border="1">' . "\n";
    $stmt = $pdo->query(
      "SELECT profile_id, first_name, last_name, email, headline, summary FROM profile"
    );
    echo "<tr><th>Name</th><th>Headline</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      echo "<tr><td>";
      echo '<a href="view.php?profile_id=' .
        $row['profile_id'] .
        '">' .
        htmlentities($row['first_name']) .
        " " .
        htmlentities($row['last_name']) .
        '</a>';
      echo "</td><td>";
      echo htmlentities($row['headline']);
      if (isset($_SESSION['user_id'])) {
        echo "</td><td>";
        echo '<a href="edit.php?profile_id=' .
          $row['profile_id'] .
          '">Edit</a> / ';
        echo '<a href="delete.php?profile_id=' .
          $row['profile_id'] .
          '">Delete</a>';
      }
    }
    echo "</td></tr>\n";
    echo "</table>";
    if (isset($_SESSION['user_id'])) {
      echo '<br><p><a href="add.php">Add New Entry</a></p>';
      echo '<p><a href="logout.php">Logout</a></p>';
    }
    ?>

  </div>