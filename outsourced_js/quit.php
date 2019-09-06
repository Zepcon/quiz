<script>
    /**
     * If the user refreshes the page, clicks on back or if he closes the browser without ending the quiz, mark it as crashed
     * and make shure that the user can not continue with the quiz, when he comes back
     */
    var ticked;
    function dontask() {
        ticked = true;
    }
    window.onbeforeunload = confirmExit;
    function confirmExit() {
        if (!ticked) {
            $.ajax({
                url: "",
                type: "POST",
                data: {crash: "1"},
                async:false
            });
        }
    };
</script>