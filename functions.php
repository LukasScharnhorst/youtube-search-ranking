<?php

    function db_verbindung_herstellen() {

        mysqli_report(MYSQLI_REPORT_STRICT); // mysqli soll Exceptions werfen

        $dbUsername = "";
        $dbPasswort = "";
        $dbHost = "";
        $dbName = "lsp_2_youtube_api";

        try {
            $dbVerbindung = new mysqli($dbHost, $dbUsername, $dbPasswort, $dbName);
            //echo 'Datenbankverbindung erfolgreich<br />';
            return $dbVerbindung;
        }
        catch (Exception $exception) {
            echo $exception->getMessage();
            //echo '<br />Datenbankverbindung nicht erfolgreich';
        }

    }

    function db_verbindung_schliessen(mysqli $dbVerbindung) {
        $dbVerbindung->close();
    }

?>
