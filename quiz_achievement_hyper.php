<?php
/**
 * Basically like the normal achievement class, just with more functionalities
 * Class quiz_achievement_hyper
 */
class quiz_achievement_hyper extends quiz_achievement_normal implements iAchievement
{

    public function __construct()
    {
        $this->buildAchievementArray();
        $this->startTime = getdate()[0];
        $this->line = 0;
        $this->answersCounter = 0;
        $this->wrongAnswersCounter = 0;
        $this->rightAnswersCounter = 0;
        $this->buildQuestionArray();
        $this->achievementCounter = 0;
        $this->rightAnswersStreakCounter = 0;
        $this->last_evaluation = false;
        $this->json_array = [];
        $this->timestamps = [];
        $this->json_array = "{}";
        $this->longestquestion_seconds = 0;
        $this->shortestquestion_seconds = 1000;
    }

    /**
     * Just another file, the loading process is the same
     * Renamed the method of the parent class, because otherwise this object would have taken the wrong file
     */
    public function buildAchievementArray()
    {
        $row = 1;
        $filename = "csv/Achievements_hyper.csv";
        $questionhander = fopen($filename, "r");
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


    /**
     * Checks if any of the counter-variables can trigger an achievement, same process
     * @param $line Number of questions the user answered
     * @param $rightanswers overall by the user
     * @param $rightanswersstreak running streak of user
     */
    function callByCounter($line, $rightanswers, $wronganswers, $rightanswersstreak)
    {
        // Go through the achievement array and check which counter has to be checked
        for ($c = 0; $c < count($this->achieve_array[0]); $c++) {
            // if achievement already got, no need to check at all
            if ($this->achieve_array[5][$c] == false) {
                // achievements for just reaching a question
                if (($this->achieve_array[3][$c] == 'rightanswers' and intval($this->achieve_array[4][$c]) == $rightanswers)
                    // check achievements for collected wrong answers
                    or ($this->achieve_array[3][$c] == 'wronganswers' and intval($this->achieve_array[4][$c]) == $wronganswers)
                    // achievements for right answers streak
                    or ($this->achieve_array[3][$c] == 'rightanswersstreak' and intval($this->achieve_array[4][$c]) == $rightanswersstreak)
                    // achievements for special questions answered right
                    or ($this->achieve_array[3][$c] == 'lineright' and intval($this->achieve_array[4][$c]) == $line and $this->last_evaluation)) {

                    // Append filenames of achievements with description which have been unlocked to our array
                    $this->achievementsToDisplay[$this->generateFilename($this->achieve_array[0][$c])] = $this->achieve_array[2][$c];
                    $this->last_achievement = $this->achieve_array[0][$c];
                    // mark as achieved in main array
                    $this->achieve_array[5][$c] = true;
                    $this->achievementCounter++;
                }
                // Run through the line part 2 times, some lines have 2 achievements
                if ($this->achieve_array[3][$c] == 'line' and (intval($this->achieve_array[4][$c]) == $line) and ($this->achieve_array[5][$c] == false)) {
                    // Append filenames and text of achievements which have been unlocked to our array
                    $this->achievementsToDisplay[$this->generateFilename($this->achieve_array[0][$c])] = $this->achieve_array[2][$c];
                    $this->last_achievement = $this->achieve_array[0][$c];
                    // mark as achieved in main array
                    $this->achieve_array[5][$c] = true;
                    $this->achievementCounter++;
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
     * The running time achievements which always have to be checked
     */
    function checkEverytime()
    {
        // ######### Respond in time and maybe also right ###########

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
        // Luftschuss
        if (!$this->achieve_array[5][60]) {
            if (count($this->timestamps) > 0) {
                if (getdate()[0] - end($this->timestamps) <= 2) {
                    $this->achievementsToDisplay[$this->generateFilename($this->achieve_array[0][60])] = $this->achieve_array[2][60];
                    $this->last_achievement = $this->achieve_array[0][60];
                    $this->achieve_array[5][60] = true;
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
        // Marathonläufer
        if (!$this->achieve_array[5][61]) {
            if (count($this->timestamps) > 9) {
                if (getdate()[0] - $this->timestamps[count($this->timestamps) - 10] <= 60) {
                    $this->achievementsToDisplay[$this->generateFilename($this->achieve_array[0][61])] = $this->achieve_array[2][61];
                    $this->last_achievement = $this->achieve_array[0][61];
                    $this->achieve_array[5][61] = true;
                    $this->achievementCounter++;
                }
            }
        }
        // Lichtgeschwindigkeit
        if (!$this->achieve_array[5][62]) {
            if (count($this->timestamps) > 9) {
                if (getdate()[0] - $this->timestamps[count($this->timestamps) - 10] <= 5) {
                    $this->achievementsToDisplay[$this->generateFilename($this->achieve_array[0][62])] = $this->achieve_array[2][62];
                    $this->last_achievement = $this->achieve_array[0][63];
                    $this->achieve_array[5][62] = true;
                    $this->achievementCounter++;
                }
            }
        }
        // ######### Do nothing for some time #########

        // Eingeschlafen?
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
        // Stillgestanden
        if (!$this->achieve_array[5][58]) {
            if (count($this->timestamps) > 0) {
                if (getdate()[0] - end($this->timestamps) >= 5) {
                    $this->achievementsToDisplay[$this->generateFilename($this->achieve_array[0][58])] = $this->achieve_array[2][58];
                    $this->last_achievement = $this->achieve_array[0][58];
                    $this->achieve_array[5][58] = true;
                    $this->achievementCounter++;
                }
            }
        }
        // Toilettenpause
        if (!$this->achieve_array[5][59]) {
            if (count($this->timestamps) > 0) {
                if (getdate()[0] - end($this->timestamps) >= 600) {
                    $this->achievementsToDisplay[$this->generateFilename($this->achieve_array[0][59])] = $this->achieve_array[2][59];
                    $this->last_achievement = $this->achieve_array[0][59];
                    $this->achieve_array[5][59] = true;
                    $this->achievementCounter++;
                }
            }
        }

    }

    /**
     * Basically same as normal, besides adding 2 achievements that will be displayed
     */
    function endQuiz()
    {
        //mark achievements for ending the quiz
        $this->achieve_array[5][7] = true;
        $this->achievementCounter++;

        // do not show "bestätige, dass du beenden willst" if users plays through whole quiz
        if (!$_SESSION['playedthrough']) {
            $this->achieve_array[5][67] = true;
            $this->achievementCounter++;
        }

        // Save the most important variables in order to display them later on
        $_SESSION['questions_answered'] = $this->line;
        $_SESSION['right_answered'] = $this->rightAnswersCounter;
        $_SESSION['achievements_got'] = $this->achievementCounter;
        $_SESSION['achievearray'] = $this->achieve_array;
        $this->endTime = getdate()[0];
        $_SESSION['$duration_seconds'] = $this->endTime - $this->startTime;

        $con = createDatabaseconnection();
        $sql = "UPDATE statistics SET `Gruppennummer` = '{$_SESSION['group']}', `Fragen_beantwortet` = '{$this->line}', `Richtige_Antworten` = '{$this->getAnswersRight()}', `Laengste_richtig_Serie` ='{$this->longestrightstreak}', `Falsche_Antworten` = '{$this->getAnswersWrong()}', `Spieldauer_Sekunden` = '{$_SESSION['$duration_seconds']}',`Gesammelte_Achievements` = '{$this->achievementCounter}', `Laengste_Antwort_Sekunden` = '{$this->longestquestion_seconds}', `Kuerzeste_Antwort_Sekunden` = '{$this->shortestquestion_seconds}', `Letztes_Achievement` = '{$this->last_achievement}', `Quiz_beendet` = '1', `Zeitpunkt_Quiz_beendet` = '{$this->endTime}' WHERE Session_ID = '{$_SESSION['session_id']}' AND Quiz_beendet != 1";
        mysqli_query($con, $sql);
        header('Location: feedback.php');
        mysqli_close($con);
    }
}