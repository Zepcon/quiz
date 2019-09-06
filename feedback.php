<?php
include "timeout_connection.php";
include "database.php";

// Check for session
if (session_status() == 1) {
    session_start();
    $_SESSION['session_id'] = session_id();
    $_SESSION['shown'] = false;
}

$input_set = false;
$show_achievement_likert = $thanks = 'none';

//Get the user_group and display the the achievement likert question for achievement guys
if (isset($_SESSION['group'])) {
    if ($_SESSION['group'] == 2 or $_SESSION['group'] == 3) {
        $show_achievement_likert = 'content';
    }
}

// Display achievement only once
if ($_POST) {
    $_SESSION['shown'] = true;
}


//Check if the user selected every likert, for now do not check if the comment field has a value or not
if (isset($_POST['finish']) and (!isset($_SESSION['finished_completely']))) {
// Achievement question is not displayed to control user, so do not check
    if (isset($_POST['Frage2']) and isset($_POST['Frage3']) and isset($_POST['Frage4'])) {
        $_SESSION['Frage1'] = -1;
        $_SESSION['Frage2'] = $_POST['Frage2'];
        $_SESSION['Frage3'] = $_POST['Frage3'];
        $_SESSION['Frage4'] = $_POST['Frage4'];
        // add comment
        if ($_POST['comment'] != '') {
            $_SESSION['comment'] = $_POST['comment'];
        } else {
            $_SESSION['comment'] = '/';
        }
        if ($_SESSION['group'] == 1) {
            $input_set = true;
        } else {
            // Achievement groups get one more question to answer
            if (isset($_POST['Frage1'])) {
                $_SESSION['Frage1'] = $_POST['Frage1'];
                $input_set = true;
            }
        }
    }
}

// Write in Database and say thank you
if ($input_set) {

    $con = createDatabaseconnection();

    // Generate the total time the user has spend on the quiz-site
    if (isset($_SESSION['start_date'])) {
        $_SESSION['total_time_seconds'] = getdate()[0] - $_SESSION['start_date'];
    } else {
        $_SESSION['total_time_seconds'] = -1;
    }


    $_SESSION['send_time'] = getdate()[0];

    //Write in Database, (hopefully) injection safe
    $sql = $con->prepare("INSERT INTO `feedback` (`session_id`, `comment`, `likert1`, `likert2`, `likert3`, `likert4`, `total_time_seconds`, `send_time`) VALUES (?,?,?,?,?,?,?,?)");
    $sql->bind_param('ssiiiiii', $_SESSION['session_id'], $_SESSION['comment'], $_SESSION['Frage1'], $_SESSION['Frage2'], $_SESSION['Frage3'], $_SESSION['Frage4'], $_SESSION['total_time_seconds'], $_SESSION['send_time']);
    $sql->execute();
    $sql->close();
    mysqli_close($con);
    $thanks = 'content';
}

?>
<!DOCTYPE html>

<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <link rel="icon" href="img/quiz.ico">
    <!-- CSS -->
    <link rel="stylesheet" href="Bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="Bootstrap/css/Likert.css" type="text/css">

    <!-- Javascript -->
    <script src="jQuery/js/jquery.min.js"></script>
    <script src="Bootstrap/js/bootstrap.js"></script>

    <!-- Custom CSS -->
    <style type="text/css">
        label {
            margin: 5px
        }

        input[type="radio"] {
            margin-right: 2px;
        }

    </style>

</head>

<body background="img/watercolour.jpg">


<div class="container" style="display: <?php if ($thanks == 'none') {
    echo "content";
} else {
    echo "none";
} ?>">
    <div style="height: 2vw"></div>
    <div style="height: 12vw" class="card text-center" <?php if (isset($_SESSION['group'])) {
        if ($_SESSION['group'] == 1) {
            echo 'style="display:none"';
        }
    } ?>>
        <div id="master" class="row" style="height: 12vw; display: none; position: absolute; align-items: center">
            <!-- This is the actual achievement, which is faded in and out-->
            <div class="col" id="achievement">
                <img class="img-fluid" id="pic" src="" alt="Achievement">
            </div>
            <div class="col" id="description">
               <strong><span class="text-center" id="textual" style="text-align: center; font-family: 'Comic Sans MS', serif; line-height: 100%"></span></strong>
            </div>
        </div>
        <div style="height: 10vw" <?php if (isset($_SESSION['group'])) {
            if ($_SESSION['group'] == 1) {
                echo 'style="display:none"';
            }
        } ?>></div>
    </div>
    <div class="card">
        <div class="card-body" align="center">
            <strong>
                Glückwunsch zu deinem Ergebnis!
            </strong>
            <p>
                <strong style="color: #222bff">
                    Fragen beantwortet: <?php if (isset($_SESSION['questions_answered'])) {
                        echo $_SESSION['questions_answered'] . ",";
                    } ?>
                    Richtige Antworten: <?php if (isset($_SESSION['right_answered'])) {
                        echo $_SESSION['right_answered'];
                    } ?>
                    <?php if (isset($_SESSION['achievements_got'])) {
                        echo ", Achievements erhalten: " . $_SESSION['achievements_got'];
                    } ?>
                </strong>
            </p>
            <hr>
            <strong>
                Bitte hinterlasse Feedback zu der Anwendung in dem freien Textfeld (egal in welcher Form) und beantworte die Fragen, dann ist es
                geschafft.
            </strong>
            <form method="post">
                <div class="form-group">
                    <textarea class="form-control border border-success" name="comment" autofocus></textarea>
                </div>
                <div style="display: <?php echo $show_achievement_likert ?>">
                    <div class="card">
                        <div class="card-body">
                            <p>
                                <strong>Die Achievements haben mich dazu motiviert, weiterzuspielen.</strong>
                            </p>
                            <label>
                                <input name="Frage1" type="radio" value="1"><small>Stimme voll zu</small>
                            </label>
                            <label class="radio-inline">
                                <input name="Frage1" type="radio" value="2"><small>Stimme weitgehend zu</small>
                            </label>
                            <label class="radio-inline">
                                <input name="Frage1" type="radio" value="3"><small>Stimme teilweise zu</small>
                            </label>
                            <label class="radio-inline">
                                <input name="Frage1" type="radio" value="4"><small>Neutral</small>
                            </label>
                            <label class="radio-inline">
                                <input name="Frage1" type="radio" value="5"><small>Stimme teilweise nicht zu</small>
                            </label>
                            <label class="radio-inline">
                                <input name="Frage1" type="radio" value="6"><small>Stimme weitgehend nicht zu</small>
                            </label>
                            <label class="radio-inline">
                                <input name="Frage1" type="radio" value="7" <?php if ($_SESSION['group'] != 1) {
                                    echo 'required';
                                } ?>><small>Stimme gar nicht zu</small>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <p>
                            <strong>Ich fand die gestellten Fragen schwer</strong>
                        </p>
                        <label>
                            <input name="Frage2" type="radio" value="1"><small>Stimme voll zu</small>
                        </label>
                        <label class="radio-inline">
                            <input name="Frage2" type="radio" value="2"><small>Stimme weitgehend zu</small>
                        </label>
                        <label class="radio-inline">
                            <input name="Frage2" type="radio" value="3"><small>Stimme teilweise zu</small>
                        </label>
                        <label class="radio-inline">
                            <input name="Frage2" type="radio" value="4"><small>Neutral</small>
                        </label>
                        <label class="radio-inline">
                            <input name="Frage2" type="radio" value="5"><small>Stimme teilweise nicht zu</small>
                        </label>
                        <label class="radio-inline">
                            <input name="Frage2" type="radio" value="6"><small>Stimme weitgehend nicht zu</small>
                        </label>
                        <label class="radio-inline">
                            <input name="Frage2" type="radio" value="7" required><small>Stimme gar nicht zu</small>
                        </label>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <p>
                            <strong>Ich bin mit meinem Ergebnis zufrieden</strong>
                        </p>
                        <label>
                            <input name="Frage3" type="radio" value="1"><small>Stimme voll zu</small>
                        </label>
                        <label class="radio-inline">
                            <input name="Frage3" type="radio" value="2"><small>Stimme weitgehend zu</small>
                        </label>
                        <label class="radio-inline">
                            <input name="Frage3" type="radio" value="3"><small>Stimme teilweise zu</small>
                        </label>
                        <label class="radio-inline">
                            <input name="Frage3" type="radio" value="4"><small>Neutral</small>
                        </label>
                        <label class="radio-inline">
                            <input name="Frage3" type="radio" value="5"><small>Stimme teilweise nicht zu</small>
                        </label>
                        <label class="radio-inline">
                            <input name="Frage3" type="radio" value="6"><small>Stimme weitgehend nicht zu</small>
                        </label>
                        <label class="radio-inline">
                            <input name="Frage3" type="radio" value="7" required><small>Stimme gar nicht zu</small>
                        </label>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <p>
                            <strong>Das Quiz hat mir Spaß gemacht</strong>
                        </p>
                        <label>
                            <input name="Frage4" type="radio" value="1"><small>Stimme voll zu</small>
                        </label>
                        <label class="radio-inline">
                            <input name="Frage4" type="radio" value="2"><small>Stimme weitgehend zu</small>
                        </label>
                        <label class="radio-inline">
                            <input name="Frage4" type="radio" value="3"><small>Stimme teilweise zu</small>
                        </label>
                        <label class="radio-inline">
                            <input name="Frage4" type="radio" value="4"><small>Neutral</small>
                        </label>
                        <label class="radio-inline">
                            <input name="Frage4" type="radio" value="5"><small>Stimme teilweise nicht zu</small>
                        </label>
                        <label class="radio-inline">
                            <input name="Frage4" type="radio" value="6"><small>Stimme weitgehend nicht zu</small>
                        </label>
                        <label class="radio-inline">
                            <input name="Frage4" type="radio" value="7" required><small>Stimme gar nicht zu</small>
                        </label>
                    </div>
                </div>
                <div class="form-group row" align="center" style="margin-top: 1%">
                    <button type="submit" name="finish" class="btn btn-success" style="width: 100%">Daten absenden</button>
                </div>
            </form>
        </div>
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
                    <?php if ($_SESSION['group'] != 1) {
                        $_SESSION['achievearray'][5][7] = true;
                        for ($c = 0; $c < count($_SESSION['achievearray'][0]); $c++): ?>
                            <tr class="rounded <?php if ($_SESSION['achievearray'][5][$c]) {
                                echo 'bg-warning';
                            } else {
                                echo 'table-active';
                            } ?>">
                                <td><?php echo $_SESSION['achievearray'][0][$c] ?></td>
                                <td><?php echo $_SESSION['achievearray'][2][$c] ?></td>
                            </tr>
                        <?php endfor;
                    } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div align="center" style="margin-top: 2vw; display: <?php echo $thanks ?>">
    <img src="img/thankyou.gif" alt="Thanks"/>
    <p><small><a style="color: black" href="https://giphy.com/gifs/justin-barnaby-xULW8v7LtZrgcaGvC0">Quelle:
                https://giphy.com/gifs/justin-barnaby-xULW8v7LtZrgcaGvC0</a></small></p>
</div>
<!--Display the achievement for finishing the quiz-->
<script type='text/javascript'>
    const showtime = 3000;
    $(document).ready(function () {
        if (("<?php echo $_SESSION['shown'] ?>".length === 0) && ("<?php echo $_SESSION['group'] ?>" !== "1") && ("<?php echo $thanks ?>" !== 'content')) {
            document.getElementById('pic').src = 'img/Achievements/feierabend.png';
            document.getElementById('textual').innerHTML = 'Beende das Quiz';
            $('#textual').css("font-size", "3vw");
            $('#master').delay(150).fadeIn('slow').delay(showtime).fadeOut('slow');
            if (("<?php echo $_SESSION['group'] ?>" === "3") && ("<?php echo $_SESSION['playedthrough']?>" === "")) {
                setTimeout(function () {
                    document.getElementById('pic').src = "img/Achievements/ausausdasspielistaus.png";
                    document.getElementById('textual').innerHTML = 'Bestätige, dass du das Quiz beenden willst';
                    $('#textual').css("font-size", "2.5vw");
                    $('#master').delay(300).fadeIn('slow').delay(showtime).fadeOut('slow');
                }, showtime + 1700);
            }
        }
    });
</script>
</body>

</html>