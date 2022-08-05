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

- Apache-Webserver mit PHP und MySQL. Bei einer lokalen Installation gibt es die Möglichkeit die Komponenten als Paket zu downloaden.
    - für Windows: XAMPP
    - für Linux: LAMP
    - für MacOS: MAMP
- PHP-Version 7.4.x oder niedriger empfohlen
- bevorzugter MySQL-Editor z.B. MySQL-Workbench, PHPMyAdmin, Sequel Ace


### ii. Anleitung

1. Die zip-Datei, welche den Code enthält, herunterladen und irgendwo auf dem System entpacken. [Hier](https://github.com//LukasScharnhorst/youtube-search-ranking/archive/refs/heads/main.zip) klicken zum Download.

2. Eine Datenbank namens *lsp_2_youtube_api* erstellen.
    - Möglichkeit 1: Die Datenbank als SQL-Anfrage mit dem folgenden Kommando erstellen: `create database lsp_2_youtube_api;
    - Möglichkeit 2: Über den Wizard im MySQL-Editor die Datenbank erstellen.

2. Die skript.sql-Datei, welche sich in dem entpackten Ordner befindet, komplett ausführen. Nach der Ausführung sollten sich 5 Tabellen in der Datenbank befinden.
    - Möglichkeit 1: Über den Wizar des MySQL-Editors die skript.sql-Datei in den Editor laden und ausführen.
    - Möglichkeit 2: Die skript.sql-Datei öffnen und den Inhalt kopieren. Darauf folgend den MySQL-Editor öffnen und das Kopierte in Schreibfläsche des Editors einfügen und alles ausführen.
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

- Channel-ID eingeben:

![Channel-ID speichern 1](https://github.com/LukasScharnhorst/youtube-search-ranking/blob/main/Dokumentation/Bilder/Channel-ID_speichern_1.png)



- Klickt man in der Navigation auf *Channel*, so ergibt sich die Möglichkeit die Channel-ID des gewünschten Kanals anzugeben. Die Channel-ID erhält man, wenn man auf YouTube einen Kanal auswählt und den hintersten Teil der URL kopiert. Die Channel-ID für den YouTube-Kanal *The MAUTICAST* lautet beispielsweise *UCSLzUlTiImWtWEFPmtogs6w* und ist Teil der URL *ht<span>tp://</span>ww<span>w.youtube.com</span>/channel/**UCSLzUlTiImWtWEFPmtogs6w***. Derzeit werden jedoch nur Videos des YouTube-Kanals *The MAUTICAST* gefiltert, egal welche Channel-ID eingegeben wird.
- Im Bereich *Keywords* können Keywords hinzugefügt oder durch das Klicken auf *X* gelöscht werden. Ein Keyword ist hierbei jeder beliebige Suchbegriff, wie man ihn auch auf YouTube eingeben würde, um Videos zu finden. Ein Keyword kann auch aus mehreren Wörten bestehen. Möchte man auf einen Schlag mehrere Keywords gleichzeitig hinzufügen, müssen diese durch ein *Komma mit anschließendem Leerzeichen ohne Zeilenumbruch* voneinander separiert werden. Beispielhafte Keywords sind *The Mauticast* oder *Mautic*.
- Auf der Seite *Home* werden alle, in den Top 50, gefundenen Videos zu den entsprechenden Keywords mit Platzierung angezeigt. Möchte man gerade hinzugefügte Keywords anzeigen lassen oder gerade gelöschte Keywords nicht mehr anzeigen lassen, muss der Refresh-Button auf der *Home*-Seite gedrückt werden. Falls am aktuellen Tag noch kein Refresh durchgeführt wurde, werden mit dem Klick auf den Refresh-Button die Listen aktualisiert. Wurde am heutigen Tag schon auf den Refresh-Button geklickt, dann werden alle heute schon aktualisierten Videos nicht nochmal aktualisiert.
