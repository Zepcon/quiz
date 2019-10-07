<?php
include "timeout_connection.php";
include "database.php";

// ################### Functions ###################

$user_agent = $_SERVER['HTTP_USER_AGENT'];

/** Fetching the browser name of the user by analyzing user agent in parts
 * @return mixed|string
 */
function getBrowser()
{
    global $user_agent;
    $browser = "Unknown Browser";
    $browser_array = array(
        '/msie/i' => 'Internet Explorer',
        '/firefox/i' => 'Firefox',
        '/safari/i' => 'Safari',
        '/chrome/i' => 'Chrome',
        '/edge/i' => 'Edge',
        '/opera/i' => 'Opera',
        '/netscape/i' => 'Netscape',
        '/maxthon/i' => 'Maxthon',
        '/konqueror/i' => 'Konqueror',
        '/mobile/i' => 'Handheld Browser'
    );

    foreach ($browser_array as $regex => $value) {
        if (preg_match($regex, $user_agent)) {
            $browser = $value;
        }
    }
    return $browser;
}

/** Fetching the os name of the user by analyzing user agent in parts
 * @return mixed|string
 */
function getOS()
{
    global $user_agent;
    $os_platform = "Unknown OS Platform";
    $os_array = array(
        '/windows nt 10/i' => 'Windows 10',
        '/windows nt 6.3/i' => 'Windows 8.1',
        '/windows nt 6.2/i' => 'Windows 8',
        '/windows nt 6.1/i' => 'Windows 7',
        '/windows nt 6.0/i' => 'Windows Vista',
        '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
        '/windows nt 5.1/i' => 'Windows XP',
        '/windows xp/i' => 'Windows XP',
        '/windows nt 5.0/i' => 'Windows 2000',
        '/windows me/i' => 'Windows ME',
        '/win98/i' => 'Windows 98',
        '/win95/i' => 'Windows 95',
        '/win16/i' => 'Windows 3.11',
        '/macintosh|mac os x/i' => 'Mac OS X',
        '/mac_powerpc/i' => 'Mac OS 9',
        '/linux/i' => 'Linux',
        '/ubuntu/i' => 'Ubuntu',
        '/iphone/i' => 'iPhone',
        '/ipod/i' => 'iPod',
        '/ipad/i' => 'iPad',
        '/android/i' => 'Android',
        '/blackberry/i' => 'BlackBerry',
        '/webos/i' => 'Mobile'
    );

    foreach ($os_array as $regex => $value) {
        if (preg_match($regex, $user_agent)) {
            $os_platform = $value;
        }
    }
    return $os_platform;
}

// ################### Running code ###################

// Check for session
if (session_status() == 1) {
    // Cookie lifetime 30 days
    session_set_cookie_params(time()+30*24*60*60);
    session_start();
    // set the start time for the user in seconds and session id
    $_SESSION['start_date'] = getdate()[0];
    $_SESSION['session_id'] = session_id();
    $_SESSION['operating_system'] = getOS();
    $_SESSION['browser'] = getBrowser();
    $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
}

// Draw a random group_Number for the User, 1 = Control, 2 = Achievement normal, 3= Achievement hyper
if (!isset($_SESSION['group'])) {
    $_SESSION['group'] = mt_rand(1, 3);
}

// Call the quiz PHP, only if the user selected values in the form
$input_set = false;

if (isset($_POST['sex']) AND ($_POST['age'] != '')) {
    $_SESSION['sex'] = $_POST['sex'];
    $_SESSION['age'] = $_POST['age'];
    $input_set = true;
}

// When complete, send information to database and route user to quiz
if ($input_set) {

    $con = createDatabaseconnection();
    $sql = "INSERT INTO `person` (IP, Session_ID, Gruppennummer, Geschlecht, Person_Alter, Betriebssystem, Browser, Startzeitpunkt) VALUES ('{$_SERVER['REMOTE_ADDR']}', '{$_SESSION['session_id']}', '{$_SESSION['group']}', '{$_SESSION['sex']}', '{$_SESSION['age']}','{$_SESSION['operating_system']}', '{$_SESSION['browser']}', '{$_SESSION['start_date']}')";
    mysqli_query($con, $sql);
    mysqli_close($con);
    header('Location: quiz_main.php');
}
?>

<!DOCTYPE html>

<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Steckbrief</title>
    <link rel="icon" href="img/quiz.ico">
    <!-- CSS -->
    <link rel="stylesheet" href="Bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="Bootstrap/css/Likert.css" type="text/css">

    <!-- Javascript -->
    <script src="jQuery/js/jquery.min.js"></script>
    <script src="Bootstrap/js/bootstrap.js"></script>

</head>

<body background="img/watercolour.jpg">

<!--Placeholder -->
<div style="height: 2vw;"></div>

<div align="center" class="container ">
    <div class="card">
        <div class="card-body">
            <h6>
                Quiz als Teil einer Bachelorarbeit
            </h6>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <p class="h4">Christian-Albrechts-Universität zu Kiel</p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <p><strong>Bitte trage hier dein Alter und dein Geschlecht ein, die Daten werden anonym behandelt.</strong></p>
            <form method="post">
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label" for="age"><strong>Alter</strong></label>
                    <div class="col-sm-9">
                        <input class="form-control" max="99" min="10" name="age" type="number" id="age" style="-webkit-appearance: none" required>
                    </div>
                </div>
                <fieldset class="form-group">
                    <div class="row">
                        <label class="col-form-label col-sm-3" for="sex"><strong>Geschlecht</strong></label>
                        <div class="col-sm-9" align="left">
                            <div class="form-check">
                                <label class="form-check-label">
                                <input class="form-check-input" type="radio" name="sex" id="sex" value="maennlich"> Männlich
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                <input class="form-check-input" type="radio" name="sex" id="sex" value="weiblich"> Weiblich
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                <input class="form-check-input" type="radio" name="sex" id="sex" value="keine_angabe" required> Keine Angabe
                                </label>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <div>
                    <button type="submit" class="btn btn-success" style="width: 100%;">Weiter zum Quiz</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
