<?php

    include 'PHP/functions.php';

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

            header('Location: index.php');

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

<html lang="de">

    <head>
        <link rel="stylesheet" href="CSS/fonts.css" type="text/css">
        <link rel="stylesheet" href="CSS/style.css" type="text/css">
        <link rel="stylesheet" href="CSS/default_mobile.css" type="text/css">
        <link rel="stylesheet" href="CSS/tablet.css" type="text/css">
        <link rel="stylesheet" href="CSS/desktop.css" type="text/css">
        <link rel="stylesheet" href="CSS/utility.css" type="text/css">
        <!--<link rel="stylesheet" href="https://unpkg.com/sanitize.css" type="text/css">-->
        <script type="text/javascript" src="JavaScript/index.js"></script>
        <title>Home</title>
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
                                <a href="index.php" class="u-link-no-text-decoration u-color-red">Home</a>
                            </li>
                            <li class="c-navigation--text-position">
                                <a href="PHP/keywords.php" class="u-link-no-text-decoration u-color-dark-grey">Keywords</a>
                            </li>
                            <li class="c-navigation--text-position">
                                <a href="PHP/channel.php" class="u-link-no-text-decoration u-color-dark-grey">Channel</a>
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
                        <a href="index.php" class="u-link-no-text-decoration u-color-red">Home</a>
                    </li>
                    <li class="c-navigation--text-position">
                        <a href="PHP/keywords.php" class="u-link-no-text-decoration u-color-dark-grey">Keywords</a>
                    </li>
                    <li class="c-navigation--text-position">
                        <a href="PHP/channel.php" class="u-link-no-text-decoration u-color-dark-grey">Channel</a>
                    </li>
                </ul>
            </div>

        </div>


        <?php

        refresh_time_speichern();

        print '<div class="c-content site-padding--horizontal">';

            print '<div class="device-container">';

                print '<div class="c-content__layout">';

                    print '<div class="c-refresh">';
                        print '<p class="u-margin-right-1_5rem u-font-size-xl u-font-weight-bold u-color-dark-grey">Last Update: </p>';
                        print '<p class="u-margin-right-1_5rem u-font-size-l u-color-dark-grey">' . date("d.m.y H:i", strtotime(last_refresh_abfragen())) . '</p>';
                        print '<form action="index.php" method="post" class="u-align-right" id="home_form">';
                            print '<button class="c-button u-margin-top-0_75rem" type="submit" name="pressed" value="not_empty">Refresh</button>';
                        print '</form>';
                    print '</div>';

                    $keyword_names = array_keys($ergebnis_array);
                    for ($i = 0; $i < count($ergebnis_array); $i++) { // pro Durchlauf ein neues Keyword

                        print '<div class="c-keyword u-margin-top-4rem">';

                            print '<div class="c-keyword__name u-margin-bottom-0_75rem">';
                                print '<p class="u-font-size-xxxl u-font-weight-bold u-color-dark-grey">' . $keyword_names[$i] . '</p>';
                            print '</div>';

                            $video_id_names = array_keys($ergebnis_array[$keyword_names[$i]]);
                            $videos_vorhanden = true;

                            if (count($video_id_names) === 0) { // wenn das Keyword keine Videos des Kanals enthält
                                $videos_vorhanden = false;
                            }

                            $current_video = 0;

                            do {

                                print '<div class="c-card u-margin-bottom-2rem u-border-radius u-shadow u-padding-vertical u-padding-horizontal">';

                                    if ($videos_vorhanden) {
                                        print '<div class="img--center">';
                                            print'<img src="' . $ergebnis_array[$keyword_names[$i]][$video_id_names[$current_video]]['thumbnail_url'] . '">';
                                        print '</div>';
                                        print '<div class="u-margin-right-1_5rem">';
                                            print '<p class="u-font-size-xl u-font-weight-bold u-color-dark-grey">' . $ergebnis_array[$keyword_names[$i]][$video_id_names[$current_video]]['titel'] . '</p>';
                                            print '<a href="https://www.youtube.com/watch?v=' . $video_id_names[$current_video] . '" target="_blank" id="videolink"><button class="c-button u-margin-top-0_75rem">Zum Video</button></a>';
                                        print '</div>';
                                    } else {
                                        print '<p class="u-font-size-xl u-font-weight-bold u-color-dark-grey">' . "No Ranking" . '</p>';
                                    }

                                    print '<div class="c-ranking">';

                                        print '<div class="c-ranking__today">';

                                            print '<p class="u-font-size-l u-color-dark-grey">Today</p>';

                                            if ($videos_vorhanden) {
                                                if (is_null($ergebnis_array[$keyword_names[$i]][$video_id_names[$current_video]]['ranking_heute'])) $ergebnis_array[$keyword_names[$i]][$video_id_names[$current_video]]['ranking_heute'] = '0';
                                                if (is_null($ergebnis_array[$keyword_names[$i]][$video_id_names[$current_video]]['ranking_gestern'])) $ergebnis_array[$keyword_names[$i]][$video_id_names[$current_video]]['ranking_gestern'] = '0';

                                                if (($ergebnis_array[$keyword_names[$i]][$video_id_names[$current_video]]['ranking_heute'] !== '0' and $ergebnis_array[$keyword_names[$i]][$video_id_names[$current_video]]['ranking_gestern'] !== '0') and ($ergebnis_array[$keyword_names[$i]][$video_id_names[$current_video]]['ranking_heute'] < $ergebnis_array[$keyword_names[$i]][$video_id_names[$current_video]]['ranking_gestern'])) {
                                                    print '<div class="number__box u-margin-top-0_75rem u-border-radius u-color-white u-background-color-green">';
                                                        print '<p class="u-font-size-l u-font-weight-bold">' . null_pruefer($ergebnis_array[$keyword_names[$i]][$video_id_names[$current_video]]['ranking_heute']) . '</p>';
                                                    print '</div>';
                                                } elseif ($ergebnis_array[$keyword_names[$i]][$video_id_names[$current_video]]['ranking_heute'] !== '0' and $ergebnis_array[$keyword_names[$i]][$video_id_names[$current_video]]['ranking_gestern'] === '0') {
                                                    print '<div class="number__box u-margin-top-0_75rem u-border-radius u-color-white u-background-color-green">';
                                                        print '<p class="u-font-size-l u-font-weight-bold">' . null_pruefer($ergebnis_array[$keyword_names[$i]][$video_id_names[$current_video]]['ranking_heute']) . '</p>';
                                                    print '</div>';
                                                } elseif (($ergebnis_array[$keyword_names[$i]][$video_id_names[$current_video]]['ranking_heute'] !== '0' and $ergebnis_array[$keyword_names[$i]][$video_id_names[$current_video]]['ranking_gestern'] !== '0') and ($ergebnis_array[$keyword_names[$i]][$video_id_names[$current_video]]['ranking_heute'] > $ergebnis_array[$keyword_names[$i]][$video_id_names[$current_video]]['ranking_gestern'])) {
                                                    print '<div class="number__box u-margin-top-0_75rem u-border-radius u-color-white u-background-color-dark-red">';
                                                        print '<p class="u-font-size-l u-font-weight-bold">' . null_pruefer($ergebnis_array[$keyword_names[$i]][$video_id_names[$current_video]]['ranking_heute']) . '</p>';
                                                    print '</div>';
                                                } else {
                                                    print '<div class="number__box u-margin-top-0_75rem u-border-radius u-color-dark-grey u-background-color-light-grey">';
                                                        print '<p class="u-font-size-l u-font-weight-bold">' . null_pruefer($ergebnis_array[$keyword_names[$i]][$video_id_names[$current_video]]['ranking_heute']) . '</p>';
                                                    print '</div>';
                                                }
                                            } else {
                                                print '<div class="number__box u-margin-top-0_75rem u-border-radius u-background-color-light-grey">';
                                                    print '<p class="u-font-size-l u-font-weight-bold u-color-dark-grey">' . "-" . '</p>';
                                                print '</div>';
                                            }

                                        print '</div>';

                                        print '<div class="c-ranking__yesterday">';

                                            print '<p class="u-font-size-l u-color-dark-grey">' . "Yesterday" . '</p>';

                                            if ($videos_vorhanden) {
                                                print '<div class="number__box u-margin-top-0_75rem u-border-radius u-color-dark-grey u-background-color-light-grey">';
                                                    print '<p class="u-font-size-l u-font-weight-bold">' . null_pruefer($ergebnis_array[$keyword_names[$i]][$video_id_names[$current_video]]['ranking_gestern']) . '</p>';
                                                print '</div>';
                                            } else {
                                                print '<div class="number__box u-margin-top-0_75rem u-border-radius u-color-dark-grey u-background-color-light-grey">';
                                                    print '<p class="u-font-size-l u-font-weight-bold u-color-dark-grey">' . "-" . '</p>';
                                                print '</div>';
                                            }

                                        print '</div>';

                                    print '</div>';

                                print '</div>';

                                $current_video++;

                            } while ($current_video < count($video_id_names));

                        print '</div>';

                    }

                print '</div>';

            print '</div>';

        print '</div>';

        ?>

    </body>

</html>