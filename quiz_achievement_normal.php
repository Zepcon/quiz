<?php
/**
 * We extend the normal quiz with new functions like achievements and management of the achievements
 * Class quiz_achievement_normal
 */
class quiz_achievement_normal extends quiz_control implements iAchievement
{

    //class variables
    public $achieve_array = [[], [], [], [], [], []];
    public $achievementCounter;
    public $last_achievement;
    public $last_evaluation;
    public $achievementsToDisplay = [];
    public $timestamp_load;
    public $timestamps = [];
    public $json_array;


    public function __construct()
    {
        $this->startTime = getdate()[0];
        $this->line = 0;
        $this->answersCounter = 0;
        $this->wrongAnswersCounter = 0;
        $this->rightAnswersCounter = 0;
        $this->buildQuestionArray();
        $this->buildAchievementArray();
        $this->achievementCounter = 0;
        $this->rightAnswersStreakCounter = 0;
        $this->last_evaluation = false;
        $this->json_array = "{}";
        $this->longestquestion_seconds = 0;
        $this->shortestquestion_seconds = 1000;
    }

    /**
     * Building the achievement array to work on like
     * [ [name], [achievement-category], [description], [counter:number / special], [number], [achieved: bool] ]
     */
    function buildAchievementArray()
    {
        $row = 1;
        $questionhander = fopen("csv/Achievements_normal.csv", "r");
        while ($data = fgetcsv($questionhander, null, ";")) {
            $fields = count($data);
            $this->achieve_array[5][$row - 1] = false;
            for ($c = 0; $c < $fields; $c++) {
                $this->achieve_array[$c][$row - 1] = $data[$c];
            }
            $row++;
        }
        fclose($questionhander);
    }

    /**Checks if any of the counter-variables can trigger an achievement
     * @param $line
     * @param $rightanswers
     * @param $wronganswers
     * @param $rightanswersstreak
     */
    function callByCounter($line, $rightanswers, $wronganswers, $rightanswersstreak)
    {
        // Go through the achievement array and check which counter has to be checked
        for ($c = 0; $c < count($this->achieve_array[0]); $c++) {
            // achievement already gotten, no need to check at all
            if ($this->achieve_array[5][$c] == false) {
                // achievements for collected right answers
                if (($this->achieve_array[3][$c] == 'rightanswers' and intval($this->achieve_array[4][$c]) == $rightanswers)
                    // achievements for right answers streak
                    or ($this->achieve_array[3][$c] == 'rightanswersstreak' and intval($this->achieve_array[4][$c]) == $rightanswersstreak)
                    // achievements for special questions answered right
                    or ($this->achieve_array[3][$c] == 'lineright' and intval($this->achieve_array[4][$c]) == $line and $this->last_evaluation)) {

                    // Append filenames and text of achievements which have been unlocked to our array
                    $this->achievementsToDisplay[$this->generateFilename($this->achieve_array[0][$c])] = $this->achieve_array[2][$c];
                    $this->last_achievement = $this->achieve_array[0][$c];
                    // mark as achieved in main array
                    $this->achieve_array[5][$c] = true;
                    $this->achievementCounter++;
                }
                // Run 2 times through loop for line, maybe there are 2 achievements
                if ($this->achieve_array[3][$c] == 'line' and (intval($this->achieve_array[4][$c]) == $line) and ($this->achieve_array[5][$c] == false)) {
                    // Append filenames and text of achievements which have been unlocked to our array
                    $this->achievementsToDisplay[$this->generateFilename($this->achieve_array[0][$c])] = $this->achieve_array[2][$c];
                    $this->last_achievement = $this->achieve_array[0][$c];
                    // mark as achieved in main array
                    $this->achieve_array[5][$c] = true;
                    $this->achievementCounter++;
                    // second run, this could probably be coded much smarter
                    for ($k = 0; $k < count($this->achieve_array[0]); $k++) {
                        if ($this->achieve_array[3][$c] == 'line' and (intval($this->achieve_array[4][$c]) == $line) and ($this->achieve_array[5][$c] == false)) {
                            // Append filenames and text of achievements which have been unlocked to our array
                            $this->achievementsToDisplay[$this->generateFilename($this->achieve_array[0][$c])] = $this->achieve_array[2][$c];
                            $this->last_achievement = $this->achieve_array[0][$c];
                            // mark as achieved in main array
                            $this->achieve_array[5][$c] = true;
                            $this->achievementCounter++;
                        }
                    }
                }
            }
        }
    }

    /**
     * Achievements depending on time, which always have to be checked
     * Basically just check if the achievement has already been got and then check the timestamps
     */
    function checkEverytime()
    {
        // Schnellfeuer
        if (!$this->achieve_array[5][2]) {
            if (count($this->timestamps) > 0) {
                if ((getdate()[0] - end($this->timestamps) <= 2) and ($this->last_evaluation)) {
                    $this->achievementsToDisplay[$this->generateFilename($this->achieve_array[0][2])] = $this->achieve_array[2][2];
                    $this->last_achievement = $this->achieve_array[0][2];
                    $this->achieve_array[5][2] = true;
                    $this->achievementCounter++;
                }
            }
        }
        // Spritner
        if (!$this->achieve_array[5][3]) {
            if (count($this->timestamps) > 4) {
                if (getdate()[0] - $this->timestamps[count($this->timestamps) - 5] <= 30) {
                    $this->achievementsToDisplay[$this->generateFilename($this->achieve_array[0][3])] = $this->achieve_array[2][3];
                    $this->last_achievement = $this->achieve_array[0][3];
                    $this->achieve_array[5][3] = true;
                    $this->achievementCounter++;
                }
            }
        }
        // Eingeschlafen erst einmal stumpf machen, also 30 Sekunden keine Frage beantworten
        if (!$this->achieve_array[5][1]) {
            if (count($this->timestamps) > 0) {
                if (getdate()[0] - end($this->timestamps) >= 30) {
                    $this->achievementsToDisplay[$this->generateFilename($this->achieve_array[0][1])] = $this->achieve_array[2][1];
                    $this->last_achievement = $this->achieve_array[0][1];
                    $this->achieve_array[5][1] = true;
                    $this->achievementCounter++;
                }
            }
        }
    }

    /**
     * Convert the new achievements to json format for javascript and kill the last achievement, so they will not get displayed again
     */
    function makeJsonReady()
    {
        $this->json_array = json_encode($this->achievementsToDisplay);
        $this->achievementsToDisplay = [];
    }

    /**
     * Strip off every sign, spaces etc from the string to get the filename of the img
     * @param $name Name of the achievement, which will be displayed
     * @return mixed|string The png filename of the achievement
     */
    function generateFilename($name)
    {
        // Catch every Unicode character from beggining on, because replacer might not get it
        $name = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $name);
        // Delete all signs etc. to properly display the filename
        $newname = str_replace(array(' ', '+', '*', '!', '?', "'", '"', '.', ',', 'ä', 'ö', 'ü', '?', '-', ':', '&'), '', $name);
        $newname = mb_strtolower($newname);
        $newname .= '.png';

        return $newname;
    }

    /**
     * Update the database line with every answer, just in case anything crashes so nothing is lost
     */
    public function updateDB()
    {
        // Save the most important variables in order to display them later on
        $_SESSION['questions_answered'] = $this->line;
        $_SESSION['right_answered'] = $this->rightAnswersCounter;
        $_SESSION['achievements_got'] = $this->achievementCounter;
        $_SESSION['achievearray'] = $this->achieve_array;
        $this->endTime = getdate()[0];
        $_SESSION['$duration_seconds'] = $this->endTime - $this->startTime;

        $con = createDatabaseconnection();
        $sql = "UPDATE statistics SET `group_number` = '{$_SESSION['group']}', `questions_answered` = '{$this->line}', `right_answered` = '{$this->getAnswersRight()}', `longest_right_streak` ='{$this->longestrightstreak}', `wrong_answered` = '{$this->getAnswersWrong()}', `duration_seconds` = '{$_SESSION['$duration_seconds']}',`collected_achievements` = '{$this->achievementCounter}', `longest_question_seconds` = '{$this->longestquestion_seconds}', `shortest_question_seconds` = '{$this->shortestquestion_seconds}', `last_achievement` = '{$this->last_achievement}', `submitted_time` = '{$this->endTime}' WHERE `session_id` = '{$_SESSION['session_id']}' AND finally_finished != 1";
        mysqli_query($con, $sql);
        mysqli_close($con);
    }

    /**
     * Override the parents method in order to get further information in the database
     */
    public function endQuiz()
    {
        //mark achievement for ending the quiz
        $this->achieve_array[5][7] = true;
        $this->achievementCounter++;

        // Save the most important variables in order to display them later on
        $_SESSION['questions_answered'] = $this->line;
        $_SESSION['right_answered'] = $this->rightAnswersCounter;
        $_SESSION['achievements_got'] = $this->achievementCounter;
        $_SESSION['achievearray'] = $this->achieve_array;
        $this->endTime = getdate()[0];
        $_SESSION['$duration_seconds'] = $this->endTime - $this->startTime;

        $con = createDatabaseconnection();
        $sql = "UPDATE statistics SET `group_number` = '{$_SESSION['group']}', `questions_answered` = '{$this->line}', `right_answered` = '{$this->getAnswersRight()}', `longest_right_streak` ='{$this->longestrightstreak}', `wrong_answered` = '{$this->getAnswersWrong()}', `duration_seconds` = '{$_SESSION['$duration_seconds']}',`collected_achievements` = '{$this->achievementCounter}', `longest_question_seconds` = '{$this->longestquestion_seconds}', `shortest_question_seconds` = '{$this->shortestquestion_seconds}', `last_achievement` = '{$this->last_achievement}', `finally_finished` = '1', `submitted_time` = '{$this->endTime}' WHERE session_id = '{$_SESSION['session_id']}' AND finally_finished != 1";
        mysqli_query($con, $sql);
        header('Location: feedback.php');
        mysqli_close($con);
    }

}