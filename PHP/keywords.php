<?php

    include 'functions.php';

    function keywords_abfragen(): array {

        $dbVerbindung = db_verbindung_herstellen();

        //alle gespeicherten Keywords in alphabetisch aufsteigender Reihenfolge aus der Datenbank holen
        $sql_query = "SELECT name FROM keyword ORDER BY name ASC;";
        $result = $dbVerbindung->query($sql_query);

        db_verbindung_schliessen($dbVerbindung);

        $datensaetze_array = [];
        $zeile = 0;
        while($datensatz = $result->fetch_assoc()) { //solange es noch einen Datensatz gibt
            $datensaetze_array[$zeile] = $datensatz['name'];
            $zeile++;
        }

        return $datensaetze_array;

    }

    function keywords_speichern() {

        if (!empty($_POST['keywords_speichern'])) {

            $keywords = explode(', ', $_POST['keywords_speichern']); //auftrennen, falls mehrere Keywords eingegeben wurden

            $dbVerbindung = db_verbindung_herstellen();

            //Keyword(s) in die Datenbank schreiben
            for ($i = 0; $i < count($keywords); $i++) {

                $sql_query = "INSERT INTO keyword (name) VALUES ('$keywords[$i]');";
                $dbVerbindung->query($sql_query);

            }

            db_verbindung_schliessen($dbVerbindung);

            /*
              Problem vorher:
                wenn man ein Keyword hinzugefügt hat und danach direkt ein Keyword
                löschen möchte, dies dann aber doch abbricht, erscheint die Meldung
                mit dem Inhalt "gesendete Daten erneut senden"

              Problemvermutung:
                der Inhalt des $_POST Arrays wird nicht komplett gelöscht und dadurch
                erscheint die Meldung

              mit dieser Funktion erscheint die Meldung nicht mehr
            */
            header('Location: keywords.php');

        }

    }

    function keywords_loeschen() {

        if (!empty($_POST['zu_loeschendes_keyword'])) {

            $dbVerbindung = db_verbindung_herstellen();

            $keyword_name = $_POST['zu_loeschendes_keyword'];

            // das ausgewählte Keyword wird als Fremdschlüssel gelöscht
            /*
                Überall, wo das ausgewählte Keyword als Fremdschlüssel fungiert, muss die entsprechende Zeile
                gelöscht werden. Dies muss geschehen, bevor das ausgewählte Keyword in der Tabelle gelöscht wird, in
                welcher es als Primärschlüssel fungiert.
            */
            $sql_query = "DELETE FROM keyword_video WHERE keyword_name = '$keyword_name';";
            $dbVerbindung->query($sql_query);

            // das ausgewählte Keyword wird gelöscht
            $sql_query = "DELETE FROM keyword WHERE name = '$keyword_name';";
            $dbVerbindung->query($sql_query);

            db_verbindung_schliessen($dbVerbindung);

            /*
              Problem vorher:
                wenn man ein Keyword gelöscht hat und danach direkt ein Keyword
                löschen möchte, dies dann aber doch abbricht, erscheint die Meldung
                mit dem Inhalt "gesendete Daten erneut senden"

              Problemvermutung:
                der Inhalt des $_POST Arrays wird nicht komplett gelöscht und dadurch
                erscheint die Meldung

              mit dieser Funktion erscheint die Meldung nicht mehr
            */
            header('Location: keywords.php');

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
        <title>Keywords</title>
    </head>

    <body class="u-background-color-white" data-menu="overflow-hidden">

        <div class="c-header u-background-color-white site-padding--horizontal u-shadow">

            <div class="device-container">

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
                                <a href="../index.php" class="u-link-no-text-decoration u-color-dark-grey">Home</a>
                            </li>
                            <li class="c-navigation--text-position">
                                <a href="keywords.php" class="u-link-no-text-decoration u-color-red">Keywords</a>
                            </li>
                            <li class="c-navigation--text-position">
                                <a href="channel.php" class="u-link-no-text-decoration u-color-dark-grey">Channel</a>
                            </li>
                        </ul>
                    </div>

                </div>

            </div>

        </div>

        <div class="device-container">

            <div class="c-navigation__mobile" data-menu="menu">
                <ul class="c-navigation--text-styling c-navigation--text-layout u-font-size-xxxl">
                    <li class="c-navigation--text-position">
                        <a href="../index.php" class="u-link-no-text-decoration u-color-dark-grey">Home</a>
                    </li>
                    <li class="c-navigation--text-position">
                        <a href="keywords.php" class="u-link-no-text-decoration u-color-red">Keywords</a>
                    </li>
                    <li class="c-navigation--text-position">
                        <a href="channel.php" class="u-link-no-text-decoration u-color-dark-grey">Channel</a>
                    </li>
                </ul>
            </div>

        </div>

        <div class="c-content site-padding--horizontal">

            <div class="device-container">

                <div class="c-content__layout">

                    <?php

                        keywords_speichern();

                        keywords_loeschen();

                        $datensaetze_array = keywords_abfragen();

                    ?>

                    <p class="u-font-size-xxxl u-font-weight-bold u-color-dark-grey">Current Keywords</p>

                    <div class="c-list">

                        <?php

                            for ($i = 0; $i < count($datensaetze_array); $i++) {

                        ?>

                                <div class="c-list__item u-border-radius u-background-color-light-grey" data-menu="keyword-text">


                                    <div class="c-list__delete-item">
                                    <?php
                                        echo '<span class="u-font-size-l u-font-weight-bold u-color-dark-grey c-list__delete-item--word-wrap" title="' . $datensaetze_array[$i] .  '">' . $datensaetze_array[$i] . '</span>' .
                                                '<button type="submit" value="' . $datensaetze_array[$i] . '" onclick="on_hinweis(this)" class="delete-keyword-icon__layout u-margin-left-0_75rem u-background-color-light-grey u-color-dark-grey">
                                                    <div class="delete-line u-background-color-dark-grey"></div>
                                                    <div class="delete-line u-background-color-dark-grey"></div>
                                                </button>
                                            ';
                                    ?>
                                    </div>

                                </div>

                        <?php
                            }
                        ?>

                    </div>

                    <button class="c-button u-margin-top-2rem" onclick="on_overlay()">Add Keywords</button>

                    <div class="c-list__item-add__overlay u-display-none u-background-color-white" id="overlay">
                        <form action="keywords.php" method="post">
                            <label for="keywords" class="u-font-size-l u-color-dark-grey">List of Keywords to add</label><br>
                            <textarea name="keywords_speichern" class="keywords__text-field u-margin-top-0_75rem u-font-size-l u-font-weight-bold u-set-font-family" required></textarea>
                            <p class="u-margin-top-0_75rem u-margin-bottom-2rem u-font-size-m u-color-grey">Comma to separate list of multiple keywords</p>
                            <button type="reset" onclick="off_overlay()" class="c-button u-background-color-white u-color-red u-margin-right-1_5rem">Cancel</button>
                            <button type="submit" class="c-button">Save</button>
                        </form>
                    </div>

                    <div class="c-list__item-delete__overlay u-display-none u-background-color-white" id="hinweis">
                        <form action="keywords.php" method="post">
                            <div id="popup">
                            </div>
                            <button type="reset" onclick="off_hinweis()" class="c-button u-background-color-white u-color-red u-margin-right-1_5rem">Nein</button>
                            <button type="submit" class="c-button">Ja</button>
                        </form>
                    </div>

                </div>

            </div>

        </div>

    </body>

</html>
