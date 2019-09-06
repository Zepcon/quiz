<?php
include "iQuestion.php";
include "iAchievement.php";
include "quiz_control.php";
include "quiz_achievement_normal.php";
include "quiz_achievement_hyper.php";
include "timeout_connection.php";
include "database.php";

//##################################################  Main Code  ############################################################################

// Session and Quiz management
if (session_status() == 1) {
    session_start();
    $_SESSION['session_id'] = session_id();
}

//just a little flag to check if the user played through the whole quiz
$_SESSION['playedthrough'] = false;

// Database management
$updateconnection = createDatabaseconnection();
$result = mysqli_query($updateconnection, "SELECT count(*) from statistics where session_id='{$_SESSION['session_id']}'");
$resultarray = $result->fetch_array();

// No entry at all in the database, so put the session id in database in order to update it later on
if ($resultarray[0] == "0") {
    $sql = "INSERT INTO statistics (session_id) VALUES ('{$_COOKIE['PHPSESSID']}')";
    mysqli_query($updateconnection, $sql);
}
// Session id already exists in database
if ($resultarray[0] != "0") {
    //check if there is a session id where the final flag is not set
    $result = mysqli_query($updateconnection, "SELECT count(session_id) FROM statistics WHERE finally_finished != 1 AND session_id='{$_SESSION['session_id']}'");
    $resultarray = $result->fetch_array();
    // We have no open quiz for this session, so start a new one
    if ($resultarray[0] == "0") {
        unset($_SESSION['quiz']);
        $_SESSION['group'] = mt_rand(1,3);
        $sql = "INSERT INTO statistics (session_id) VALUES ('{$_COOKIE['PHPSESSID']}')";
        mysqli_query($updateconnection, $sql);
    }
}
mysqli_close($updateconnection);

// Saver for the second post to display the right or wrong answer
if (!isset($_SESSION['eval'])) {
    $_SESSION['eval'] = false;
}

// Create the object, depending on which group the user was assigned to
if (!isset($_SESSION['quiz'])) {
    if (isset($_SESSION['group'])) {
        if ($_SESSION['group'] == 1) {
            $_SESSION['quiz'] = new quiz_control();
        }
        if ($_SESSION['group'] == 2) {
            $_SESSION['quiz'] = new quiz_achievement_normal();
        }
        if ($_SESSION['group'] == 3) {
            $_SESSION['quiz'] = new quiz_achievement_hyper();

        }
        //Todo nur zum Testen!!
        if (isset($_SESSION['frage']) and isset($_SESSION['selecter'])) {
            $_SESSION['quiz']->line = $_SESSION['frage'];
        }
    }
}

// Timestamp management for the very first page load
if (!isset($_SESSION['quiz']->timestamp_load)) {
    $_SESSION['quiz']->timestamp_load = getdate()[0];
    array_push($_SESSION['quiz']->timestamps, getdate()[0]);
}

//kill the old achievement array on post, so the achievements will only be shown once
if (!empty($_POST) and $_SESSION['group'] != 1) {
    $_SESSION['quiz']->json_array = "{}";
}

// Check if the user has given an answer, then evaluate and update the counters and values -> "Main-Gameloop"
if (isset($_POST['evaluate_button']) and isset($_POST['answer'])) {

    //get the time it took to answer the question and eventually update the trackers
    $questiontime = getdate()[0]-end($_SESSION['quiz']->timestamps);
    if ($questiontime < $_SESSION['quiz']->shortestquestion_seconds) {
        $_SESSION['quiz']->shortestquestion_seconds = $questiontime;
        }
    if ($questiontime > $_SESSION['quiz']->longestquestion_seconds) {
        $_SESSION['quiz']->longestquestion_seconds = $questiontime;
    }

    // mark that we have evaluated
    $_SESSION['eval'] = true;

    //check if "hin und her" was achieved
    if (isset($_POST['hin'])) {
        $_SESSION['quiz']->achieve_array[5][6] = true;
    }
    // check for "hin und her und hin"
    if (isset($_POST['her'])) {
        $_SESSION['quiz']->achieve_array[5][66] = true;
    }

    // answer is true
    if ($_SESSION['quiz']->evaluateAnswer($_POST['answer'], $_SESSION['quiz']->line)) {
        $_SESSION['quiz']->last_evaluation = true;
        $_SESSION['quiz']->rightAnswersCounter++;
        $_SESSION['quiz']->rightAnswersStreakCounter++;
        // update streak
        if ($_SESSION['quiz']->rightAnswersStreakCounter > $_SESSION['quiz']->longestrightstreak) {
            $_SESSION['quiz']->longestrightstreak = $_SESSION['quiz']->rightAnswersStreakCounter;
        }
        // answer is false
    } else {
        $_SESSION['quiz']->wrongAnswersCounter++;
        $_SESSION['quiz']->last_evaluation = false;
        $_SESSION['quiz']->rightAnswersStreakCounter = 0;
    }

    // Check for achievements by counters or by time
    if ($_SESSION['group'] != 1) {
        $_SESSION['quiz']->checkEverytime();
        $_SESSION['quiz']->callByCounter($_SESSION['quiz']->line, $_SESSION['quiz']->rightAnswersCounter, $_SESSION['quiz']->wrongAnswersCounter, $_SESSION['quiz']->rightAnswersStreakCounter, $_SESSION['quiz']->last_evaluation);
    }
    // Update database with every question
    $_SESSION['quiz']->updateDB();


}

// Continue to next question and display achievements
if (isset($_POST['continue']) and $_SESSION['eval']) {
    $_SESSION['eval'] = false;
    $_SESSION['quiz']->line++;

    // Update achievements
    if ($_SESSION['group'] != 1) {
        $_SESSION['quiz']->makeJsonReady();
        array_push($_SESSION['quiz']->timestamps, getdate()[0]);
    }
// Check if user played through whole quiz
    if ($_SESSION['quiz']->line == 330) {
        $_SESSION['playedthrough'] = true;
        $_SESSION['quiz']->endQuiz();
    }
}

//Listener to end the quiz
if (isset($_POST["exit_button"])) {
    $_SESSION['quiz']->endQuiz();
}

//##################################################  HTML  ############################################################################
?>

<!DOCTYPE html>

<html lang="de">

<head>
    <!--Wait, what are you doing here?!-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz</title>
    <link rel="icon" href="img/quiz.ico">
    <!-- CSS -->
    <link rel="stylesheet" href="Bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="Bootstrap/css/Likert.css" type="text/css">

    <!-- Javascript -->
    <script src="jQuery/js/jquery.min.js"></script>
    <script src="Bootstrap/js/bootstrap.js"></script>
    <script src="jQuery/js/jquery.min.js"></script>

    <?php
    // include connected javascript and php stuff
    include "outsourced_js/evaluation.php";
    include "outsourced_js/achievement.php";
    include "outsourced_js/live_achievements.php";
    include "outsourced_js/quit.php";

    ?>

</head>

<body background="img/watercolour.jpg">

<?php
include "HTML/Modal.html";
?>


<div class="container">
    <div style="height: 2vw"></div>
    <div style="height: 12vw <?php if (isset($_SESSION['group'])) {if ($_SESSION['group'] == 1) {echo '; visibility: hidden';}} ?>" class="card text-center">
            <div id="master" class="row" style="height: 12vw; display: none; position: absolute; align-items: center">
                <!-- This is the actual achievement, which is faded in and out-->
                <div class="col" id="achievement">
                    <img class="img-fluid" id="pic" src="" alt="Achievement">
                </div>
                <div class="col" id="description">
                   <strong> <span class="text-center" id="textual" style="text-align: center; font-family: 'Comic Sans MS', serif; line-height: 100%"></span></strong>
                </div>
            </div>
            <div style="height: 12vw"></div>
    </div>
    <div class="card" align="center">
        <div class="card-body">
            <strong> Kategorie:
                <span style="font-style: italic">
                        <?php echo $_SESSION['quiz']->getCategory($_SESSION['quiz']->line); ?>
                    </span>
            </strong>
            <br>
            <div style="font-family: 'Comic Sans MS',serif">
                <strong>
                    <?php
                    echo $_SESSION['quiz']->readQuestion($_SESSION['quiz']->line);
                    ?>
                </strong>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body d-flex justify-content-center">
            <form method="post">
                <div class="form-check">
                    <label class="form-check-label" id="answer1">
                        <input class="form-check-input" onclick="alarm()" name="answer" type="radio" value="1">
                        <strong>
                            <?php
                            echo $_SESSION['quiz']->getAnswers($_SESSION['quiz']->line)[0];
                            ?>
                        </strong>
                    </label>
                </div>
                <div class="form-check">
                    <label class="form-check-label" id="answer2">
                        <input class="form-check-input" onclick="alarm()" type="radio" name="answer" value="2">
                        <strong>
                            <?php
                            echo $_SESSION['quiz']->getAnswers($_SESSION['quiz']->line)[1];
                            ?>
                        </strong>
                    </label>
                </div>
                <div class="form-check">
                    <label class="form-check-label" id="answer3">
                        <input class="form-check-input" onclick="alarm()" type="radio" name="answer" value="3">
                        <strong>
                            <?php
                            echo $_SESSION['quiz']->getAnswers($_SESSION['quiz']->line)[2];
                            ?>
                        </strong>
                    </label>
                </div>
                <div class="form-check">
                    <label class="form-check-label" id="answer4">
                        <input class="form-check-input" onclick="alarm()" type="radio" name="answer" value="4" required>
                        <strong>
                            <?php
                            echo $_SESSION['quiz']->getAnswers($_SESSION['quiz']->line)[3];
                            ?>
                        </strong>
                    </label>
                </div>
                <div id="hiddendiv" style="display: none">
                    <input type="radio" name="hin" id="secrethin" value="keinplan">
                    <input type="radio" name="her" id="secrether" value="immernochkeinplan">
                </div>
        </div>
        <div class="form-group" align="center">
            <?php if (!$_SESSION['eval']) {
                echo "<button type='submit' onclick='dontask()' name='evaluate_button' class='btn btn-success'>Bestätigen</button>
                      <button type='button' class='btn btn-primary' onclick='dontask()' data-toggle='modal' data-target='#exitModal'>Quiz beenden</button>";
            } else {
                echo "<button type='submit' onclick='dontask()' name='continue' class='btn btn-info'>Nächste Frage</button>";
            } ?>
        </div>
        </form>

        <?php
        // Just an extra card with debug info, may delete later
        if (isset($_SESSION['selecter'])) {include "debug_info.php";}
        ?>
    </div>

    <!--Achievement Table-->
    <div class="card" style="<?php if ($_SESSION['group'] == 1) {
        echo "display: none";
    } ?>">
        <div class="card-body">
            <div style="position: relative; height: 360px; overflow: auto">
                <table class="table mb-0">
                    <thead class="thead-light">
                    <tr>
                        <th style="width: 30%"> Achievement</th>
                        <th style="width: 70%"> Beschreibung</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if($_SESSION['group'] != 1) { for ($c = 0; $c < count($_SESSION['quiz']->achieve_array[0]); $c++): ?>
                        <tr class="rounded <?php if ($_SESSION['quiz']->achieve_array[5][$c]) {
                            echo 'bg-warning';
                        } else {
                            echo 'table-active';
                        } ?>">
                            <td><?php echo $_SESSION['quiz']->achieve_array[0][$c] ?></td>
                            <td><?php echo $_SESSION['quiz']->achieve_array[2][$c] ?></td>
                        </tr>
                    <?php endfor;}?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!--Include Bootstrap-->
<script src="Bootstrap/js/bootstrap.js"></script>
</body>
</html>


<?php
// Check interruption of user by just closing the website and make sure the flag is set
if (isset($_POST['crash'])) {
    $con = createDatabaseconnection();
    $sql = "UPDATE statistics SET `finally_finished` = '2' WHERE session_id = '{$_SESSION['session_id']}' AND finally_finished = 0";
    mysqli_query($con, $sql);
    mysqli_close($con);
}
?>