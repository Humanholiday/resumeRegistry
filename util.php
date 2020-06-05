<?php

//flash messages
function flashMessages()
{
    if (isset($_SESSION['error'])) {
        echo '<p style="color:red">' . $_SESSION['error'] . "</p>\n";
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        echo '<p style="color:green">' . $_SESSION['success'] . "</p>\n";
        unset($_SESSION['success']);
    }
}

//check if user is logged in
function loginCheck()
{
    if (!isset($_SESSION['user_id'])) {
        return "You are not logged in";
    }
    return;
};

//return to index.php if user clicks cancel
function clickCancel()
{
    if (isset($_POST['cancel'])) {
        unset($_POST);
        header("Location: index.php");
        return;
    }
}

//prepare all user data from database
function prepUser($pdo, $profile_id)
{
    $sql = 'SELECT * FROM profile where profile_id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $profile_id
    ]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row;
}

//validate profile content when adding or editing profile
function validateProfile()
{
    if (
        strlen($_POST['first_name']) < 1 ||
        strlen($_POST['last_name']) < 1 ||
        strlen($_POST['email']) < 1 ||
        strlen($_POST['headline']) < 1 ||
        strlen($_POST['summary']) < 1
    ) {
        return 'All fields must be completed';
    }

    if (strpos($_POST['email'], '@') === false) {
        return 'Invalid email';
    }
    return true;
}

function validatePos()
{
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year' . $i])) continue;
        if (!isset($_POST['desc' . $i])) continue;
        if (
            strlen($_POST['year' . $i]) < 1 ||
            strlen($_POST['desc' . $i]) < 1
        ) {
            return 'All fields must be completed';
        }

        if (!is_numeric($_POST['year' . $i])) {
            return 'Position year must be numeric';
        }
    }
    return true;
}

function validateEdu()
{
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['edu_year' . $i])) continue;
        if (!isset($_POST['edu_school' . $i])) continue;
        if (
            strlen($_POST['edu_year' . $i]) < 1 ||
            strlen($_POST['edu_school' . $i]) < 1
        ) {
            return 'All fields must be completed';
        }

        if (!is_numeric($_POST['edu_year' . $i])) {
            return 'Position year must be numeric';
        }
    }
    return true;
}

function loadPos($pdo, $profile_id)
{
    $sql = 'SELECT * FROM Position WHERE profile_id = :prof ORDER BY rank';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':prof' => $profile_id]);
    $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $positions;
}

function loadEdu($pdo, $profile_id)
{
    $sql = 'SELECT year, name, rank FROM Education JOIN Institution ON Education.institution_id = Institution.institution_id WHERE profile_id = :prof ORDER BY rank';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':prof' => $profile_id]);
    $educations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $educations;
}

function insertPositions($pdo, $profile_id)
{
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
            ':pid' => $profile_id,
            ':ra' => $rank,
            ':yr' => $_POST['year' . $i],
            ':de' => $_POST['desc' . $i],
        ]);
        $rank++;
    }
}

// TODO: update the function
function insertEducations($pdo, $profile_id)
{
    $rank = 1;
    for (
        $i = 0;
        $i <= 9;
        $i++
    ) {
        if (!isset($_POST['edu_year' . $i])) continue;
        if (!isset($_POST['edu_school' . $i])) continue;
        $year = $_POST['edu_year' . $i];
        $school = $_POST['edu_school' . $i];

        //look up the school if it is already there
        $institution_id = false;
        $stmt = $pdo->prepare('SELECT institution_id FROM Institution WHERE name = :name');
        $stmt->execute([
            ':name' => $school
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row !== false) $institution_id = $row['institution_id'];

        // if there is no institution insert it
        if ($institution_id === false) {
            $stmt = $pdo->prepare('INSERT INTO Institution (name) VALUES (:name)');
            $stmt->execute([
                ':name' => $school
            ]);
            $institution_id = $pdo->lastInsertId();
        }

        //insert into education 
        $stmt = $pdo->prepare("INSERT INTO Education (profile_id, rank, year, institution_id)
              VALUES (:pid, :ra, :yr, :iid)");
        $stmt->execute([
            ':pid' => $profile_id,
            ':ra' => $rank,
            ':yr' => $_POST['edu_year' . $i],
            ':iid' => $institution_id
        ]);
        $rank++;
    }
}
