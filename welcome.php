<?php
/**
 * Basically just HTML, no php needed. Just for info
 */
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Info</title>
    <!-- Bootstrap annotation css-->
    <link rel="stylesheet" href="Bootstrap/css/bootstrap.css">
    <link rel="icon" href="img/quiz.ico">
    <!-- Bootstrap annotation javascript-->
    <script src="Bootstrap/js/bootstrap.js"></script>
</head>

<body background="img/watercolour.jpg">
<!--Placeholder -->
<div style="height: 8vw;"></div>

<div class="container " align="center">
    <div class="card">
        <div class="card-body">
            <h6>
                Quiz als Teil einer Bachelorarbeit
            </h6>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <p class="h4">Christian-Albrechts-Universität zu Kiel</p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <p><strong> Vielen Dank, dass du an diesem Experiment teilnehmen willst.</strong></p>
            <h5><span class="badge badge-pill badge-success">Ablauf</span></h5>
            <p>Zuerst werden Alter und Geschlecht erfragt. Diese Daten werden anonym behandelt und können nicht zurückgeführt
                werden.</p>
            <h5><span class="badge badge-pill badge-success">Das Quiz</span></h5>
            <p>Es wird dir immer eine Frage mit 4 möglichen Antworten gezeigt, von welchen Eine richtig ist. Wie bei einem klassischen Quiz
                halt.</p>
            <p> Wenn du keine Lust mehr hast, weiterzuspielen, dann klicke auf den Button <strong>"Quiz beenden"</strong></p>
            <h5><span class="badge badge-pill badge-success">Wichtig</span></h5>
            <p>Während dem ganzen Quiz werden die "Aktualisieren"-Funktion und die "Zurück"-Funktion von deinem Browser <strong>nicht</strong> unterstützt (also nicht den zurück-Pfeil
                oben links klicken). Wenn du also
                eine Frage beantwortet hast und bei der Nächsten bist, gibt es keine Möglichkeit mehr zurück zu kommen. So ist das Leben. 🤷‍♂️ Pro Person bitte nur einmal am Quiz teilnehmen.
            </p>
            <p>Deswegen auch bitte nicht zurückwischen oder ähnliches, wenn du auf dem Smartphone oder Tablet teilnimmst.</p>
            <b></b>
            <p>Die Leitung von diesem Experiment liegt bei Dr. Athanasios Mazarakis. Bei Fragen wende dich bitte an <a
                    href="mailto:a.mazarakis@zbw.eu?cc=flavioschroeder@gmx.de">A.Mazarakis@zbw.eu</a></p>
            <form action="person.php">
                <input type="submit" class="btn btn-success btn-lg" value="Quiz starten" style="width: 100%">
            </form>
        </div>
    </div>
</div>

</body>
</html>
