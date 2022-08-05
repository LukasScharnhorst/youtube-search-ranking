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

<html>

    <head>
        <link rel="stylesheet" href="Skripte/style.css" type="text/css">
        <script type="text/javascript" src="Skripte/index.js"></script>
        <title>Keywords</title>
    </head>

    <body>

    <div class="rand">

        <h1 id="topic">YT Rankings</h1>

        <nav class="navigation">
            <a href="../index.php">Home</a> |
            <a href="keywords.php" class="current_page">Keywords</a> |
            <a href="channel.php">Channel</a>
        </nav>

        <button onclick="on_overlay()" id="keywords_add_button">Add</button>

        <?php

            keywords_speichern();

            keywords_loeschen();

            $datensaetze_array = keywords_abfragen();

        ?>

        <div id="keywords_ausgabe">

            <?php

                for ($i = 0; $i < count($datensaetze_array); $i++) {

            ?>
                <div id="delete_keyword">
                <?php
                    echo '<br>' . $datensaetze_array[$i] . '
                            <button type="submit" value="' . $datensaetze_array[$i] . '" onclick="on_hinweis(this)" id="keywords_delete_button">X</button>
                        ';
                ?>
                </div>
            <?php
                }
            ?>

        </div>

        <div id="overlay">
            <form action="keywords.php" method="post">
                <label for="keywords">List of Keywords to add</label><br>
                <textarea name="keywords_speichern" id="keywords" required></textarea>
                <p>Comma to separate list of multiple keywords</p>
                <button type="submit">Save</button>
                <button type="reset" onclick="off_overlay()">Cancel</button>
            </form>
        </div>

        <div id="hinweis">
            <form action="keywords.php" method="post">
                <div id="popup">
                </div>
                <button type="reset" onclick="off_hinweis()">Nein</button>
                <button type="submit">Ja</button>
            </form>
        </div>

    </div>

    </body>

</html>
