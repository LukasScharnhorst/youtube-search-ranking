<?php

    include 'functions.php';

    /* die Uhrzeit und das Datum speichern, während Refresh-Button zuletzt gedrückt wurde */
    function refresh_time_speichern() {

        if (!empty($_POST['pressed'])) {

            $current_datetime = date("y-m-d H:i:s");

            $dbVerbindung = db_verbindung_herstellen();

            $sql_query = "SELECT * FROM refresh;";
            $result = $dbVerbindung->query($sql_query);

            if ($result->num_rows == 0) { // wenn die Tabelle refresh leer ist (noch nach anderer Möglichkeit schauen)

                // aktuelle Uhrzeit und Datum erstmalig in die Datenbank schreiben
                $sql_query = "INSERT INTO refresh (id, last_updated) VALUES ('1', '$current_datetime');";
                $dbVerbindung->query($sql_query);

            } else {

                // aktuelle Uhrzeit und Datum aktualisieren
                $sql_query = "UPDATE refresh SET last_updated = '$current_datetime' WHERE id = 1;";
                $dbVerbindung->query($sql_query);

            }

            db_verbindung_schliessen($dbVerbindung);

            refresh();

            header('Location: home.php');

        }

    }

    /* die Uhrzeit und das Datum herausfinden, wann Refresh-Button zuletzt gedrückt wurde */
    function last_refresh_abfragen() {

        $dbVerbindung = db_verbindung_herstellen();

        //Uhrzeit und Datum des letztmaligen Drückens des Refresh-Buttons aus der Datenbank holen
        $sql_query = "SELECT last_updated FROM refresh WHERE id = 1";
        $result = $dbVerbindung->query($sql_query);

        db_verbindung_schliessen($dbVerbindung);

        $datensatz = $result->fetch_assoc(); //hier gibt es nur einen Datensatz, daher keine for-Schleife

        return $datensatz['last_updated'];

    }

    /* alle Keywords auflisten, welche am aktuellen Tag bzw. noch gar nicht aktualisiert wurden */
    function keywords_not_updated_today(): array {

        $last_refresh = date("Y-m-d", strtotime(last_refresh_abfragen()));

        $dbVerbindung = db_verbindung_herstellen();

        // alle Keywordnamen herausfinden, deren last_updated-Wert noch nicht vorhanden ist oder deren Wert zu klein ist
        $sql_query = "SELECT name FROM keyword WHERE last_updated IS NULL OR DATE(last_updated) < '$last_refresh';";
        $result = $dbVerbindung->query($sql_query);

        $keywords_name_array = [];
        $zeile = 0;
        while($datensatz = $result->fetch_assoc()) { //solange es noch einen Datensatz gibt
            $keywords_name_array[$zeile] = $datensatz['name'];
            $zeile++;
        }

        db_verbindung_schliessen($dbVerbindung);

        return $keywords_name_array;

    }

    /* API-Abfrage auswerten */
    function refresh() {

        // Array holen, in welchem die Keywords stehen, welche heute noch nicht geupdated wurden
        $keywords_names = keywords_not_updated_today();

        $dbVerbindung = db_verbindung_herstellen();

        $last_updated = strtotime(last_refresh_abfragen());

        for ($i = 0; $i < count($keywords_names); $i++) { // API-Abfrage für ein Keyword

            $keyword_name = $keywords_names[$i];

            $api_abfrage_array = api_abfrage(str_replace(" ", "%20", $keyword_name)); //API gerechte Schreibweise für Leerzeichen bei Keywords übergeben

            $platzierung = 0;
            // count($api_abfrage_array['items']) gibt die Anzahl der Suchergebnisse an
            for ($k = 0; $k < count($api_abfrage_array['items']); $k++) { // für ein Keyword die Videos durchgehen

                if (strcmp($api_abfrage_array['items'][$k]['id']['kind'], 'youtube#video') === 0) { // es sollen nur Videos in das Ranking eingehen

                    $platzierung++;

                    if (strcmp($api_abfrage_array['items'][$k]['snippet']['channelId'], 'UCSLzUlTiImWtWEFPmtogs6w') === 0) { // Video ist vom gewünschten Kanal

                        $video_ID = $api_abfrage_array['items'][$k]['id']['videoId'];
                        $sql_query = "SELECT * FROM keyword_video WHERE keyword_name = '$keyword_name' AND video_id = '$video_ID'";
                        $result = $dbVerbindung->query($sql_query);

                        if ($result->num_rows === 0) { // Keyword-Video-Kombination existiert noch nicht in der Datenbank

                            $sql_query = "SELECT * FROM video WHERE id = '$video_ID'";
                            $result = $dbVerbindung->query($sql_query);

                            if ($result->num_rows === 0) { // Video existiert noch nicht in der Datenbank

                                $title = $api_abfrage_array['items'][$k]['snippet']['title'];
                                $thumbnail_url = $api_abfrage_array['items'][$k]['snippet']['thumbnails']['default']['url'];
                                $kanal_id = $api_abfrage_array['items'][$k]['snippet']['channelId'];

                                // Video in Datenbank einfügen
                                $sql_query =    "
                                            INSERT INTO
                                                video (
                                                    id,
                                                    titel,
                                                    thumbnail_url,
                                                    kanal_id
                                                )
                                            VALUES (
                                                    '$video_ID',
                                                    '$title',
                                                    '$thumbnail_url',
                                                    '$kanal_id'
                                                    );
                                            ";
                                $dbVerbindung->query($sql_query);

                            }

                            $updated_today = date("Y-m-d", $last_updated); // MÖGLICHERWEISE NOCH EIN FEHLER DRIN!!!
                            // Keyword-Video-Kombination erstellen
                            $sql_query = "INSERT INTO keyword_video (keyword_name, video_id, last_updated) VALUES ('$keyword_name', '$video_ID', '$updated_today');"; // MÖGLICHERWEISE NOCH EIN FEHLER DRIN!!!
                            $dbVerbindung->query($sql_query);

                        }

                        $updated_today = date("Y-m-d", $last_updated);
                        // Ranking updaten -> Keyword-Video-Kombination existiert schon in der Datenbank
                        $sql_query = "UPDATE keyword_video SET ranking_gestern = ranking_heute, ranking_heute = '$platzierung', last_updated = '$updated_today' WHERE keyword_name = '$keyword_name' AND video_id = '$video_ID';";
                        $dbVerbindung->query($sql_query);

                    }

                }

            }

            $updated_today = date("Y-m-d", $last_updated);
            // letztes Update des Keywords auf heute setzen
            $sql_query = "UPDATE keyword SET last_updated = '$updated_today' WHERE name = '$keyword_name';"; // unnötig?
            $dbVerbindung->query($sql_query);

        }

        $updated_today = date("Y-m-d", $last_updated);
        $yesterday = date("Y-m-d", strtotime(last_refresh_abfragen() . "-1 days"));
        // keyword_videos, welche heute aus den Top 50 für ein Keyword gefallen sind, wurden bisher noch nicht geupdatet.
        // Die keyword_videos updaten, welche nicht mehr in den Top 50 sind und heute noch nicht geupdatet wurden. Durch
        // einen refresh-Vorgang zu einem früheren Zeitpunkt am heutigen Tag können auch schon keyword_videos geupdatet
        // worden sein, welche für ein Keyword nicht mehr in den Top 50 liegen
        $sql_query = "UPDATE keyword_video SET ranking_gestern = ranking_heute, ranking_heute = null, last_updated = '$updated_today' WHERE /*(ranking_heute IS NOT NULL OR ranking_gestern IS NOT NULL) AND*/ date(last_updated) < '$updated_today';"; // MÖGLICHERWEISE NOCH EIN FEHLER DRIN!!!
        $dbVerbindung->query($sql_query);

        db_verbindung_schliessen($dbVerbindung);

    }

    /* API-Abfragen und Abfrageergebnis in ein Array schreiben */
    function api_abfrage(string $keyword_name): array {

        $api_key = 'AIzaSyBztpPDnAkCw0Ho_MCYg-bH4lgJB2qpuQM'; //-> vielleicht in eine eigene Datei scheiben?

        // Channel-ID von The Mauticast: UCSLzUlTiImWtWEFPmtogs6w
        $channel_ID = 'UCSLzUlTiImWtWEFPmtogs6w';
        $anzahl_ergebnisse = 50;

        $anfrage = 'https://youtube.googleapis.com/youtube/v3/search?part=snippet&maxResults=' . $anzahl_ergebnisse . '&order=relevance&q=' . $keyword_name . '&key=' . $api_key;

        $daten = file_get_contents($anfrage);

        $daten_array = json_decode($daten, true);

        return $daten_array;

    }

    /* Infos aus der Datenbank auslesen und in ein Array speichern, welche zum Anzeigen auf der Website benötigt werden */
    function datenbank_auslesen(): array {

        $dbVerbindung = db_verbindung_herstellen();

        $output = array();

        // alle Keywords nach Name aufsteigend sortiert (gerade hinzugefügte Keywords werden erst nach dem
        // nächsten Refresh angezeigt, deswegen das "last_updated IS NOT NULL")
        $sql_query = "SELECT name FROM keyword WHERE last_updated IS NOT NULL ORDER BY name ASC;";
        $result_keyword_datensatz = $dbVerbindung->query($sql_query);

        while ($keyword_datensatz = $result_keyword_datensatz->fetch_assoc()) { //solange es noch einen Datensatz gibt

            $keyword_name = $keyword_datensatz['name'];
            $output[$keyword_name] = array();

            // alle Videos passend zum jeweiligen Keyword
            // alle Videos werden nach der jeweiligen Position aufsteigend sortiert
            // Videos, welche am heutigen Tag nicht geranked sind, also einen Wert von null aufweisen, werden nach dem letzten Video ausgegeben,
            // welches für den heutigen Tag noch eine Platzierung vorweisen kann. Alle Videos, welche zwar heute nicht mehr geranked
            // sind, dies aber gestern noch waren, werden dann nach der Platzierung aufsteigend sortiert
            $sql_query = "SELECT video_id, ranking_heute, ranking_gestern FROM keyword_video WHERE keyword_name = '$keyword_name' ORDER BY -ranking_heute DESC, -ranking_gestern DESC;";
            $result_keyword_video_datensatz = $dbVerbindung->query($sql_query);

            while ($keyword_video_datensatz = $result_keyword_video_datensatz->fetch_assoc()) { // solange es noch ein Video gibt

                $video_id = $keyword_video_datensatz['video_id'];
                $output[$keyword_name][$video_id] = array();

                $video_ranking_heute = $keyword_video_datensatz['ranking_heute'];
                $video_ranking_gestern = $keyword_video_datensatz['ranking_gestern'];
                $output[$keyword_name][$video_id]['ranking_heute'] = $video_ranking_heute;
                $output[$keyword_name][$video_id]['ranking_gestern'] = $video_ranking_gestern;

                // weitere Infos zum augewählten Video
                $sql_query = "SELECT titel, thumbnail_url FROM video WHERE id = '$video_id';";
                $result_video_datensatz = $dbVerbindung->query($sql_query);

                $video_datensatz = $result_video_datensatz->fetch_assoc(); //jedes Video gibt es in der Datenbank nur einmal, daher keine for-Schleife
                $output[$keyword_name][$video_id]['titel'] = $video_datensatz['titel'];
                $output[$keyword_name][$video_id]['thumbnail_url'] = $video_datensatz['thumbnail_url'];

            }

        }

        db_verbindung_schliessen($dbVerbindung);

        return $output;

    }

    $ergebnis_array = datenbank_auslesen();

    /* ersetzt NULL durch ein - */
    function null_pruefer(string $wert): string {

        if ($wert === '0') {
            return '-';
        }
        else {
            return $wert;
        }

    }

?>



<!doctype html>

<html>

    <head>
        <link rel="stylesheet" href="style.css" type="text/css">
        <script type="text/javascript" src="index.js"></script>
        <title>Home</title>
    </head>

    <body>

        <div class="rand">

            <h1 id="topic">YT Rankings</h1>

            <nav class="navigation">
                <a href="home.php" class="current_page">Home</a> |
                <a href="keywords.php">Keywords</a> |
                <a href="channel.php">Channel</a>
            </nav>

        <?php

            refresh_time_speichern();

        ?>

        <div id="updaten">

            <p id="last_update">Last Update: <?php echo date("d.m.y H:i", strtotime(last_refresh_abfragen())); ?></p>

            <form action="home.php" method="post" id="home_form">
                <button type="submit" name="pressed" value="not_empty">Refresh</button><!-- wenn möglich value mit Datum befüllen -->
            </form>

        </div>

        <?php foreach ($ergebnis_array as $keyword => $videos) { ?>

            <table id="home_table">
                <tr>
                    <th id="name"><?php echo $keyword ?></th>
                    <th id="infos"></th>
                    <th id="today">Today</th>
                    <th id="yesterday">Yesterday</th>
                </tr>
                <?php $keys = array_keys($videos);
                if (count($keys) === 0) { // wenn das Keyword keine Videos des Kannals enthält ?>
                    <tr>
                        <td>No Ranking</td>
                        <td></td>
                        <td class="home_td_center">-</td>
                        <td class="home_td_center">-</td>
                    </tr>
                <?php } else {
                    foreach ($videos as $video_id => $infos) { ?>
                        <tr>
                            <td><img src="<?php echo $infos['thumbnail_url']; ?>"></td>
                            <td><?php echo $infos['titel'] . '<br>' . '<a href="https://www.youtube.com/watch?v=' . $video_id . '" target="_blank" id="videolink">Link zum Video</a>'; ?></td>

                            <?php

                            if (is_null($infos['ranking_heute'])) $infos['ranking_heute'] = '0';
                            if (is_null($infos['ranking_gestern'])) $infos['ranking_gestern'] = '0';

                            if (($infos['ranking_heute'] !== '0' AND $infos['ranking_gestern'] !== '0' ) AND ($infos['ranking_heute'] < $infos['ranking_gestern'])) {
                                ?>

                                <td class="gruen home_td_center"><?php echo null_pruefer($infos['ranking_heute']); ?></td>

                                <?php
                            } elseif ($infos['ranking_heute'] !== '0' AND $infos['ranking_gestern'] === '0' ) {
                                ?>

                                <td class="gruen home_td_center"><?php echo null_pruefer($infos['ranking_heute']); ?></td>

                                <?php
                            } elseif (($infos['ranking_heute'] !== '0' AND $infos['ranking_gestern'] !== '0' ) AND ($infos['ranking_heute'] > $infos['ranking_gestern'])) {
                                ?>

                                <td class="rot home_td_center"><?php echo null_pruefer($infos['ranking_heute']); ?></td>

                                <?php
                            } else {
                                ?>

                                <td class="home_td_center"><?php echo null_pruefer($infos['ranking_heute']); ?></td>

                            <?php
                                }
                            ?>

                            <td class="home_td_center"><?php
                                echo null_pruefer($infos['ranking_gestern']);
                                ?></td>
                        </tr>
                    <?php }
                } ?>
            </table>

         <?php } ?>

        </div>

    </body>

</html>