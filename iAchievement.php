<?php

interface iAchievement {
    function buildAchievementArray();
    function checkEverytime();
    function callByCounter($line, $rightanswers, $wronganswers, $rightanswersstreak);
    function generateFilename($name);
    function makeJsonReady();

}