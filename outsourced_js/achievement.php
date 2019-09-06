

<script>
    /**
     * Get the achievement json from php and display achievements, as long as there are achievements to be displayed
     * @type {number}
     */
    const showtime = 3000;
    // This is the display loop for the achievements, running through json object
    $(document).ready(function () {
        if ('<?php echo $_SESSION['eval'] ?>' === "") {
            // check the json object for achievements, that may be displayed
            var achievements = JSON.parse('<?= $_SESSION['quiz']->json_array ?>');
            var keys = Object.keys(achievements);
            var i = 1;
            // no delay on the first run, so don't put the first achievment in the interval
            if (keys.length > 0) {
                document.getElementById('pic').src = "img/Achievements/" + keys[0];
                document.getElementById('textual').innerHTML = achievements[keys[0]];
                // change font size depending on text length
                var $numwords = $('#textual').text().split(" ").length;
                if (($numwords >= 1) && ($numwords < 3)) {
                    $('#textual').css("font-size", "3vw");
                } else if (($numwords >= 3) && ($numwords < 5)) {
                    $('#textual').css("font-size", "2.5vw");
                } else {
                    $('#textual').css("font-size", "2vw");
                }
                $('#master').delay(150).fadeIn('slow').delay(showtime).fadeOut('slow');
                // Display achievements after the first one with pause
                if (i < keys.length) {
                    var sleepyAlert = setInterval(function () {
                        document.getElementById('pic').src = "img/Achievements/" + keys[i];
                        document.getElementById('textual').innerHTML = achievements[keys[i]];
                        $numwords = $('#textual').text().split(" ").length;
                        // Change font size depending on text length
                        if (($numwords >= 1) && ($numwords < 3)) {
                            $('#textual').css("font-size", "3vw");
                        } else if (($numwords >= 3) && ($numwords < 5)) {
                            $('#textual').css("font-size", "2.5vw");
                        } else {
                            $('#textual').css("font-size", "2vw");
                        }
                        $('#master').delay(500).fadeIn('slow').delay(showtime).fadeOut('slow');
                        if (i === keys.length - 1) {
                            clearInterval(sleepyAlert);
                        }
                        i++;
                    }, showtime+1700);
                }
            }
        }
    });

</script>

