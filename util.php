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

//return to index.php if user clicks cancel
function clickCancel()
{
    if (isset($_POST['cancel'])) {
        unset($_POST);
        header("Location: index.php");
        return;
    }
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

function loadPos($pdo, $profile_id)
{
    $sql = 'SELECT * FROM Position WHERE profile_id = :prof ORDER BY rank';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':prof' => $profile_id]);
    $positions = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $positions[] = $row;
    }
    return $positions;
}
?>
<script>
    var countEdit = <?php echo count(json_encode($positions)); ?>
</script>