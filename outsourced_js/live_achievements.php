<script type="text/javascript">
    /**
     * Live achievements for first selecting one radion button and then changing to another one
     * (and maybe one more time to another one in the hyper group)
     * @type {boolean}
     */
        // These are just the ticks in order to get the achievement live
    var firsttick = false;
    var secondtick = false;
    // This is the information, if the achievement has already been got in an earlier post
    var group = "<?php echo $_SESSION['group'] ?>";
    var achievedhin;
    var achievedher;
    // Live information within the post
    var reachedhin = false;
    var reachedher = false;

    function alarm() {
        // only show achievements after evaluation
        if ('<?php echo $_SESSION['eval'] ?>' === "") {
            // Take no look at all in the control group
            if (group !== "1") {
                achievedhin = "<?php if ($_SESSION['group'] != 1) {
                    echo $_SESSION['quiz']->achieve_array[5][6];
                }?>";
                if (group === "3") {
                    achievedher = "<?php if ($_SESSION['group'] == 3) {
                        echo $_SESSION['quiz']->achieve_array[5][66];
                    } ?>";
                }
                // First if case also means that this is the second tick, so display the first achievement
                if ((firsttick) && (achievedhin !== "1") && (!reachedhin)) {
                    document.getElementById('pic').src = "img/Achievements/hinundher.png";
                    document.getElementById('textual').innerHTML = 'Ändere Deine ausgewählte Antwort';
                    $('#textual').css("font-size", "3vw");
                    // secret radio to inform the the server that we got the achievement
                    $('#secrethin').prop("checked", true);
                    $('#master').delay(150).fadeIn('slow').delay(showtime).fadeOut('slow');
                    secondtick = true;
                    reachedhin = true;
                    // maybe we can also show the second achievement in the same frame
                } else if ((group === "3") && firsttick && secondtick && (achievedher !== "1") && !reachedher) {
                    //secret radio to inform the the server that we got the achievement
                    $('#secrether').prop("checked", true);
                    reachedher = true;
                    // just a little timeout if we reach it in frame
                    if (achievedhin !== "1") {
                        setTimeout(function () {
                            document.getElementById('pic').src = "img/Achievements/hinundherundhin.png";
                            document.getElementById('textual').innerHTML = 'Ändere Deine ausgewählte Antwort 2 mal';
                            $('#textual').css("font-size", "2.5vw");
                            $('#master').delay(300).fadeIn('slow').delay(showtime).fadeOut('slow');
                        }, showtime + 1500);
                    } else {
                        document.getElementById('pic').src = "img/Achievements/hinundherundhin.png";
                        document.getElementById('textual').innerHTML = 'Ändere Deine ausgewählte Antwort 2 mal';
                        $('#textual').css("font-size", "2.5vw");
                        $('#master').delay(300).fadeIn('slow').delay(showtime).fadeOut('slow');
                    }
                    // maybe we already reached the first in an earlier post, in this case we have to make three ticks again
                } else if ((group === "3") && firsttick && (achievedher !== "1") && !reachedher) {
                    secondtick = true;
                } else {
                    firsttick = true;
                }
            }
        }
    }
</script>