<?php

/**
 * Basic quiz without any gamification.
 * Here we have the evaluation, question and time management.
 * Class quiz_control
 */
class quiz_control implements iQuestion
{
    //Class variables for question and user management
    public $line;
    public $answersCounter;
    public $rightAnswersCounter;
    public $longestrightstreak;
    public $wrongAnswersCounter;
    public $startTime;
    public $endTime;
    public $mainarray = [[], [], [], [], [], [], []];
    public $rightAnswersStreakCounter;
    public $longestquestion_seconds;
    public $shortestquestion_seconds;
    public $timestamps= [];

    function __construct()
    {
        $this->startTime = getdate()[0];
        $this->line = 0;
        $this->answersCounter = 0;
        $this->wrongAnswersCounter = 0;
        $this->rightAnswersCounter = 0;
        $this->buildQuestionArray();
        $this->longestrightstreak = 0;
        $this->longestquestion_seconds = 0;
        $this->shortestquestion_seconds = 1000;
    }

    /**
     * Construct an multi-array to work on just like the csv.
     * [column] = [frage, antwort1 ,antwort2 ,antwort3 ,antwort4 ,lösung (nummer) ,kategorie]
     * [line] = [0-329]
     */
    function buildQuestionArray()
    {
        $row = 1;
        $questionhander = fopen("csv/Fragen.csv", "r");
        while ($data = fgetcsv($questionhander, null, ";")) {
            $fields = count($data);
            for ($c = 0; $c < $fields; $c++) {
                $this->mainarray[$c][$row - 1] = $data[$c];
            }
            $row++;
        }
        fclose($questionhander);
    }

    function readQuestion($line)
    {
        return $this->mainarray[0][$this->line];
    }

    function getAnswers($line)
    {
        $returner = [];
        for ($c = 1; $c < 5; $c++) {
            array_push($returner, $this->mainarray[$c][$line]);
        }
        return $returner;

    }

    // Check if the given answer is like the disposited solution
    function evaluateAnswer($answer, $line)
    {
        if ($answer == $this->mainarray[5][$line]) {
            return true;
        }
        return false;

    }

    function getCategory($line)
    {
        return $this->mainarray[6][$line];
    }

    function getAnswersRight()
    {
        return $this->rightAnswersCounter;
    }

    function getAnswersWrong()
    {
        return $this->wrongAnswersCounter;
    }

    function getStartTime()
    {
        return $this->startTime;
    }

    function getEndTime()
    {
        return $this->endTime = getdate()[0];
    }

    /**
     * Update the line of the created session id in Database with every question, so the input does not get lost if the user quits
     */
    public function updateDB()
    {

        $con = createDatabaseconnection();
        $this->endTime = getdate()[0];
        $sql = "UPDATE statistics SET `group_number` ='{$_SESSION['group']}',`questions_answered` = '{$this->line}',`right_answered` = '{$this->getAnswersRight()}',`longest_right_streak` = '{$this->longestrightstreak}',`wrong_answered` = '{$this->wrongAnswersCounter}', `collected_achievements` = '-1', `longest_question_seconds` = '{$this->longestquestion_seconds}', `shortest_question_seconds` = '{$this->shortestquestion_seconds}', `last_achievement` = '-1', `submitted_time` = '{$this->endTime}' WHERE `session_id` = '{$_SESSION['session_id']}' AND finally_finished != 1";
        mysqli_query($con, $sql);
        mysqli_close($con);
    }

    // Create different timestamps and write in database, then route to next site
    function endQuiz()
    {
        // Save the most important variables in order to display them later on
        $_SESSION['questions_answered'] = $this->line;
        $_SESSION['right_answered'] = $this->rightAnswersCounter;
        $this->endTime = getdate()[0];
        $_SESSION['$duration_seconds'] = $this->endTime - $this->startTime;

        $con = createDatabaseconnection();

        //Most important, set flag that we are totally finished and never can come back
        $sql = "UPDATE statistics SET `group_number` ='{$_SESSION['group']}',`duration_seconds` = '{$_SESSION['$duration_seconds']}',`questions_answered` = '{$this->line}',`right_answered` = '{$this->getAnswersRight()}',`longest_right_streak` =  '{$this->longestrightstreak}',`wrong_answered` = '{$this->getAnswersWrong()}', `collected_achievements` = '-1', `longest_question_seconds` = '{$this->longestquestion_seconds}', `shortest_question_seconds` = '{$this->shortestquestion_seconds}', `last_achievement` = '-1', `finally_finished` = '1', `submitted_time` = '{$this->endTime}' WHERE session_id = '{$_SESSION['session_id']}' AND finally_finished != 1";
        mysqli_query($con, $sql);
        header('Location: feedback.php');
        mysqli_close($con);
    }
}

