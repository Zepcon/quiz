<!--Extra part just for debugging-->
<div class="card" <?php if (!isset($_SESSION['selecter'])) {echo 'style="display:none"';} ?>>
    <div class="card-body" align="center">
        <p>Fragen richtig: <?php echo $_SESSION['quiz']->getAnswersRight(); ?></p>
        <p>Fragen richtig Streak: <?php if ($_SESSION['group'] != 1) {
                echo $_SESSION['quiz']->rightAnswersStreakCounter;
            } ?></p>
        <p>Achievements erhalten: <?php if ($_SESSION['group'] != 1) {
                echo $_SESSION['quiz']->achievementCounter;
            } ?></p>
        <p>Fragen falsch: <?php echo $_SESSION['quiz']->getAnswersWrong();  ?></p>
        <p>Fragen beantwortet: <?php echo $_SESSION['quiz']->line; ?></p>
    </div>
</div>