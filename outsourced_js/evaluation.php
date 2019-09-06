
<script type="text/javascript">
    /**
     * Get the asnswer of the user, compare it to the right answer and display
     */
    $(document).ready(function () {
        if ('<?= $_SESSION['eval']?>' === '1') {
            var right = "<?php if ($_SESSION['eval']) { echo $_SESSION['quiz']->mainarray[5][$_SESSION['quiz']->line]; }?>";
            var wrong = "<?php if (isset($_POST['answer']) and ($_SESSION['eval'])) {echo $_POST['answer'];} ?>";
            if ('<?= $_SESSION['quiz']->last_evaluation ?>' === '1') {
                $('#answer'.concat(right)).css('color', 'green');
            } else {
                $('#answer'.concat(right)).css('color', 'green');
                $('#answer'.concat(wrong)).css('color', 'red');
            }
            $(':radio').remove();
        }
    });
</script>