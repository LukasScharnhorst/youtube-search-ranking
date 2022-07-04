<?php

    include 'functions.php';

    function channel_abfragen() {

        if (!empty($_POST['channel_id'])) {

            $dbVerbindung = db_verbindung_herstellen();

            $channel_id = $_POST['channel_id']; // kann nicht direkt eingesetzt werden wegen zu vielen ''
            $sql_query = "SELECT * FROM kanal WHERE id = '$channel_id';";
            $result = $dbVerbindung->query($sql_query);

            if ($result->num_rows == 0) { // wenn die Tabelle der Abfrage leer ist (Channel noch nicht vorhanden)

                //Channel_ID in die Datenbank schreiben
                $sql_query = "INSERT INTO kanal (id) VALUES ('$channel_id');";
                $dbVerbindung->query($sql_query);

                echo '<script>channel_speichern_hinweis();</script>';

            } else {

                echo '<script>channel_vorhanden_hinweis();</script>';

            }

            db_verbindung_schliessen($dbVerbindung);

        }

    }

?>


<!doctype html>

<html>

    <head>
        <link rel="stylesheet" href="style.css" type="text/css">
        <script type="text/javascript" src="index.js"></script>
        <title>Channel</title>
    </head>

    <body>

    <div class="rand">

        <h1 id="topic">YT Rankings</h1>

        <nav class="navigation">
            <a href="home.php">Home</a> |
            <a href="keywords.php">Keywords</a> |
            <a href="channel.php" class="current_page">Channel</a>
        </nav>

        <?php channel_abfragen(); ?>

        <form action="channel.php" method="post" id="channel_form">
            <label for="channel_id">ID of Channel</label>
            <input type="text" name="channel_id" id="channel_id" required><br>
            <button type="submit">Save</button>
        </form>

    </div>

    </body>

</html>