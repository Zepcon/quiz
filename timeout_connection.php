<?php
/**
 * Manage timeout of the user in the quiz or if the connection is lost
 */

//Create a Session timeout of 30 minutes
const SESSION_TIMEOUT = 1800;

if ((isset($_SESSION['last_activity']) and time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) or (connection_aborted())) {
    //timeout, so write id in the databases
    // Database management
    $host = 'localhost';
    $user = 'ciugc';
    $password = 'Sk34bl8ab';
    $db = 'ciugc';
    $updateconnection = mysqli_connect($host, $user, $password, $db);

    //check table of person
    $result = mysqli_query($updateconnection, "SELECT count(*) from person where Session_ID='{$_SESSION['session_id']}'");
    $resultarray = $result->fetch_array();
    if ($resultarray[0] == "0") {
        $sql = "INSERT INTO person (Session_ID, Geschlecht) VALUES ('{$_SESSION['session_id']}', 'TIMEOUT')";
        mysqli_query($updateconnection, $sql);
    }

    //check table of quiz
    $result = mysqli_query($updateconnection, "SELECT count(*) from statistics where Session_ID='{$_SESSION['session_id']}'");
    $resultarray = $result->fetch_array();
    if ($resultarray[0] == "0") {
        $sql = "INSERT INTO statistics (Session_ID, Quiz_beendet, Letztes_Achievement) VALUES ('{$_SESSION['session_id']}', '1', 'TIMEOUT')";
        mysqli_query($updateconnection, $sql);
    }
    //check table of feedback
    $result = mysqli_query($updateconnection, "SELECT count(*) from feedback where Session_ID='{$_SESSION['session_id']}'");
    $resultarray = $result->fetch_array();
    if ($resultarray[0] == "0") {
        $sql = "INSERT INTO feedback (Session_ID, Kommentar) VALUES ('{$_SESSION['session_id']}', 'TIMEOUT')";
        mysqli_query($updateconnection, $sql);
    }
    mysqli_close($updateconnection);

}
$_SESSION['last_activity'] = time();

?>