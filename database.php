<?php
/**
 * Just a little class for the database management
 */
function createDatabaseconnection() {
// Components for the connection
    // Host = helicon/localhost, user = ciugc, password = aus txt, db erstellen kann dann so bleiben
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $db = 'quiz_flavio';
    $connection = mysqli_connect($host, $user, $password, $db);

    if (!$connection) {
        die("Es ist ein Fehler aufgetreten");
    }

    return $connection;

}