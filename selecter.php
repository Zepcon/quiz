<?php

/* This site is basically just to change the group number and the the number of the question to start with.
	It is like a little cheating site for debugging. Should be called if you are within the filer person.php */

if (isset($_POST["Gruppe"])) {
    session_start();
    $_SESSION['group'] = $_POST['Gruppe'];
    $_SESSION['selecter'] = true;
    if (isset($_POST['frage'])) {
        $_SESSION['frage'] = $_POST['frage'];
    }
    header("Location: quiz_main.php");
}
?>

<html>

<head>
    <title>Test</title>
</head>
<div align="center">
    <form method="post">
        <label>Gruppe:
            <select name="Gruppe" size="3">
                <option value="1" style="height: 10vw; width: 10vw">1</option>
                <option value="2" style="height: 10vw; width: 10vw">2</option>
                <option value="3" style="height: 10vw; width: 10vw">3</option>
            </select>
        </label>
        <label>Fragennummer (0-329):
            <input type="number" name="frage" min="0" max="329" value="0">
        </label>
        <button type="submit" style="height: 15vw; width: 15vw;">Los</button>
    </form>
</div>
</html>
