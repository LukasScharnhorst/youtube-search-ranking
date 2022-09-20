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

<html lang="de">

    <head>
        <link rel="stylesheet" href="../CSS/fonts.css" type="text/css">
        <link rel="stylesheet" href="../CSS/style.css" type="text/css">
        <link rel="stylesheet" href="../CSS/default_mobile.css" type="text/css">
        <link rel="stylesheet" href="../CSS/tablet.css" type="text/css">
        <link rel="stylesheet" href="../CSS/desktop.css" type="text/css">
        <link rel="stylesheet" href="../CSS/utility.css" type="text/css">
        <!--<link rel="stylesheet" href="https://unpkg.com/sanitize.css" type="text/css">-->
        <script type="text/javascript" src="../JavaScript/index.js"></script>
        <title>Channel</title>
    </head>

    <body class="u-background-color-white" data-menu="overflow-hidden">

        <div class="c-header u-background-color-white site-padding--horizontal u-shadow">

            <div class="device--max-width-and-center">

                <div class="c-header__layout">

                    <p class="u-font-size-xxxl u-font-weight-bold">
                        <span class="u-color-red">YT</span><span class="u-color-dark-grey u-font-weight-lighter"> Rankings</span>
                    </p>

                    <button class="u-border-off u-background-color-white c-navigation-icon" data-menu="button">
                        <div class="line u-background-color-dark-grey"></div>
                        <div class="line u-background-color-dark-grey"></div>
                        <div class="line u-background-color-dark-grey"></div>
                    </button>

                    <div class="c-navigation__tablet-desktop">
                        <ul class="c-navigation--text-styling c-navigation--text-layout u-font-size-l">
                            <li class="c-navigation--text-position">
                                <a href="../index.php" class=" link--no-text-decoration u-color-dark-grey">Home</a>
                            </li>
                            <li class="c-navigation--text-position">
                                <a href="keywords.php" class="link--no-text-decoration u-color-dark-grey">Keywords</a>
                            </li>
                            <li class="c-navigation--text-position">
                                <a href="channel.php" class="link--no-text-decoration u-color-red">Channel</a>
                            </li>
                        </ul>
                    </div>

                </div>

            </div>

        </div>

        <div class="device--max-width-and-center">

            <div class="c-navigation__mobile" data-menu="menu">
                <ul class="c-navigation--text-styling c-navigation--text-layout u-font-size-xxxl">
                    <li class="c-navigation--text-position">
                        <a href="../index.php" class=" link--no-text-decoration u-color-dark-grey">Home</a>
                    </li>
                    <li class="c-navigation--text-position">
                        <a href="keywords.php" class="link--no-text-decoration u-color-dark-grey">Keywords</a>
                    </li>
                    <li class="c-navigation--text-position">
                        <a href="channel.php" class="link--no-text-decoration u-color-red">Channel</a>
                    </li>
                </ul>
            </div>

        </div>

        <?php channel_abfragen(); ?>

        <div class="c-content site-padding--horizontal">

            <div class="device--max-width-and-center">

                <div class="c-content__layout">

                    <form action="channel.php" method="post" id="channel_form">
                        <label for="channel_id" class="u-font-size-l u-color-dark-grey">ID of Channel</label><br>
                        <input type="text" name="channel_id" class="channel__text-field u-font-size-xxxl" id="channel_id" required><br>
                        <div class="display--flex">
                            <button type="submit" class="c-button align--right">Save</button>
                        </div>
                    </form>

                </div>

            </div>

        </div>

    </body>

</html>