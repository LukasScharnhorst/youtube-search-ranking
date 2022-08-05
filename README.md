# youtube-search-ranking



## Inhaltsverzeichnis

1. Zielgruppe
2. Funktionsweise
3. Installationsanleitung
    1. Voraussetzung
    2. Anleitung
4. Bedienung




## 1. Zielgruppe

Als Zielgruppe werden Personen betrachtet, welche die tägliche Entwicklung der Rankings von Videos in Bezug auf Suchbegriffe interessiert.



## 2. Funktionsweise

Für einen Kanal werden alle seine Videos aufgelistet, welche zu den angegebenen Suchbegriffen jeweils zum aktuellen Zeitpunkt in den Top 50 sind. Die Suchbegriffe werden alphabetisch absteigend aufgelistet und die dazugehörigen Videos nach der heutigen Platzierung angezeigt. Mit dem Wort "Suchbegriff" ist genau das gemeint, was man auch auf YouTube selbst in die Suchleiste eingeben würde.



## 3. Installationsanleitung


### i. Voraussetzung

- Apache-Webserver mit PHP und MySQL. Bei einer lokalen Installation gibt es die Möglichkeit die Software gemeinsam zu downloaden.
    - Für Windows: XAMPP
    - Für Linux: LAMP
    - Für MacOS: MAMP
- PHP-Version 7.4.x oder niedriger empfohlen.
- Bevorzugter MySQL-Editor z.B. MySQL-Workbench, PHPMyAdmin, Sequel Ace.


### ii. Anleitung

1. Die zip-Datei, welche den Code enthält, herunterladen und irgendwo auf dem System entpacken. [Hier](https://github.com//LukasScharnhorst/youtube-search-ranking/archive/refs/heads/main.zip) klicken zum Download.

2. Eine Datenbank namens *lsp_2_youtube_api* erstellen.
    - Möglichkeit 1: Die Datenbank als SQL-Anfrage mit dem folgenden Kommando erstellen: `create database lsp_2_youtube_api;`
    - Möglichkeit 2: Über den Wizard im MySQL-Editor die Datenbank erstellen.

2. Die skript.sql-Datei, welche sich in dem entpackten Ordner befindet, komplett ausführen. Nach der Ausführung sollten sich 5 Tabellen in der Datenbank befinden.
    - Möglichkeit 1: Über den Wizard des MySQL-Editors die skript.sql-Datei in den Editor laden und ausführen.
    - Möglichkeit 2: Die skript.sql-Datei öffnen und den Inhalt kopieren. Darauf folgend den MySQL-Editor öffnen, das Kopierte in die Schreibfläsche des Editors einfügen und alles ausführen.
    - Möglichkeit 3: Über das Terminal die skript.sql-Datei laden und ausführen.

3. Die Datei functions.php öffnen und bei *$dbUsername*, *$dbPasswort* und *$dbHost* jeweils zwischen die Anführungszeichen die Daten eingeben, welche verwendet werden, um sich beim MySQL-Editor anzumelden.

4. das Terminal aus dem Ordner heraus öffnen, welcher aus der zip-Datei entpackt wurde und den von PHP bereitgestellten lokalen Webserver starten. Das Kommando lautet: `php -S localhost:8000`.
    - Möglichkeit 1: Über die GUI in den entpackten Ordner navigieren. Im Ordner selbst einen Rechtsklick ausführen und sinngemäß auswählen 'im Terminal öffnen'
    - Möglichkeit 2: Über das Terminal in den entpackten Ordner navigieren. Hierzu die Befehle:
        - `pwd`: Gibt den aktuellen Ordner an.
        - `cd/Name des Unterordners`: Wechselt in den Unterorder des aktuellen Ordners.
        - `cd ..`: Wechselt in den Oberordner des aktuellen Ordners.

5. Einen beliebigen Browser öffnen und in die Adressleiste folgendes eingeben: *http://localhost:8000*



## 4. Bedienung

- Navigation:

![Navigation](https://github.com/LukasScharnhorst/youtube-search-ranking/blob/main/Dokumentation/Bilder/Navigation.png)

- Channel-ID herausfinden:

1. Auf YouTube den gewünschten Kanal aufrufen
2. Der fett markierte Teil in der URL ist die Channel-ID: ht<span>tp://</span>ww<span>w.youtube.com</span>/channel/**UCSLzUlTiImWtWEFPmtogs6w**

- Channel-ID eingeben und speichern:

    -> Derzeit werden jedoch nur Videos des YouTube-Kanals "The MAUTICAST" gefiltert, egal welche Channel-ID eingegeben wird.

![Channel-ID speichern 1](https://github.com/LukasScharnhorst/youtube-search-ranking/blob/main/Dokumentation/Bilder/Channel-ID_speichern_1.png)

![Channel-ID speichern 2](https://github.com/LukasScharnhorst/youtube-search-ranking/blob/main/Dokumentation/Bilder/Channel-ID_speichern_2.png)

- Keyword hinzufügen:

    -> Ein Keyword ist jeder beliebige Suchbegriff, wie man ihn auch auf YouTube eingeben würde, um Videos zu finden und kann auch aus mehreren Wörten bestehen.

![Keyword hinzufügen 1](https://github.com/LukasScharnhorst/youtube-search-ranking/blob/main/Dokumentation/Bilder/Keyword_hinzufuegen_1.png)

![Keyword hinzufügen 2](https://github.com/LukasScharnhorst/youtube-search-ranking/blob/main/Dokumentation/Bilder/Keyword_hinzufuegen_2.png)

![Keyword hinzufügen 3](https://github.com/LukasScharnhorst/youtube-search-ranking/blob/main/Dokumentation/Bilder/Keyword_hinzufuegen_3.png)

![Keyword hinzufügen 4](https://github.com/LukasScharnhorst/youtube-search-ranking/blob/main/Dokumentation/Bilder/Keyword_hinzufuegen_4.png)

- Keyword löschen:

![Keyword löschen](https://github.com/LukasScharnhorst/youtube-search-ranking/blob/main/Dokumentation/Bilder/Keyword_loeschen.png)

- Funktionen des Refresh-Buttons, wenn man diesen drückt:
    - Anzeigen von Keywords, welche hinzugefügt wurden. Falls auch Videos zu den neuen Keywords gefunden wurden, werden diese ebenfalls angezeigt.
    - Ausblenden von Keywords, welche gelöscht wurden.  Falls auch Videos zu den gelöschten Keywords gvorhanden waren, werden diese ebenfalls ausgeblendet.
    - Aktualisierung des Rankings der Videos (pro Tag einmal).

![Refresh Button](https://github.com/LukasScharnhorst/youtube-search-ranking/blob/main/Dokumentation/Bilder/Refresh-Button.png)
