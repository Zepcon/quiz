<?php

interface iQuestion {
    function readQuestion($line);
    function getAnswersRight();
    function getAnswersWrong();
    function getAnswers($line);
    function getStartTime();
    function getEndTime();
    function getCategory($line);
    function endQuiz();
    function evaluateAnswer($answer,$line);
    function buildQuestionArray();
    function updateDB();

}