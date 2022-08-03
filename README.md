# youtube-search-ranking

<br>

**Inhaltsverzeichnis**

1. Zielgruppe
2. Funktionsweise
3. Installationsanleitung
    1. Voraussetzung
    2. Anleitung
4. Bedienung

<br>
<br>
<br>

**1. Zielgruppe**

Als Zielgruppe werden die Personen betrachtet, welche erfahren wollen, wie sich die Videos eines YouTube-Kanals in Bezug auf die zum Kanal in Verbindung gebrachten Suchbegriffe vom Ranking her aus täglich verändern.

<br>
<br>
<br>

**2. Funktionsweise**

Für einen Kanal werden alle seine Videos aufgelistet, welche zu den angegebenen Suchbegriffen jeweils zum aktuellen Zeitpunkt in den Top 50 sind. Die Suchbegriffe werden alphabetisch absteigend aufgelistet und die dazugehörigen Videos nach der heutigen Platzierung angezeigt. Mit dem Wort *Suchbegriff* ist genau das gemeint, was man auch auf YouTube selbst in die Suchleiste eingeben würde.

<br>
<br>
<br>

**3. Installationsanleitung**

<br>

**i. Voraussetzung**

- lokaler Webserver
- maximal PHP-Version 7.4.x aktiviert haben
- Verwaltungswerkzeug für Datenbank (z.B. MySQL Workbench)

<br>

**ii. Anleitung**

1. Die zip-Datei, welche den Code enthält, herunterladen und irgendwo auf dem System entpacken. Dazu einfach weiter oben auf den grünen Button klicken, auf welchem *Code* steht und *Download ZIP* auswählen

2. Verwaltungswerkzeug öffnen

3. aus dem Verwaltungswerkzeug heraus die skript.sql-Datei, welche sich in dem entpackten Ordner befindet, öffnen und komplett ausführen

4. die Datei functions.php öffnen und bei *$dbUsername*, *$dbPasswort* und *$dbHost* jeweils zwischen die Anführungszeichen die Daten eingeben, welche verwendet wurden, um sich beim Verwaltungswerkzeug anzumelden

5. das Terminal aus dem Ordner heraus öffnen, welcher aus der zip-Datei entpackt wurde und den lokalen Webserver starten. Unter Linux und macOS lautet das Kommando: *php -S localhost:8000*

6. einen beliebigen Browser öffnen und in die Adressleiste folgendes eingeben: unter Linux und macOS ist dies *ht<span>tp://</span>localhost:8000/home.php*

<br>
<br>
<br>

**4. Bedienung**

- Unter der Überschrift *YT Rankings* befindet sich die Navigationsleiste.
- Klickt man in der Navigation auf *Channel*, so ergibt sich die Möglichkeit die ID des gewünschten Kanals anzugeben. Die Channel-ID erhält man, wenn man auf YouTube einen Kanal auswählt und den hintersten Teil der URL kopiert. Die Channel-ID für den YouTube-Kanal *The MAUTICAST* lautet beispielsweise *UCSLzUlTiImWtWEFPmtogs6w* und ist Teil der URL *ht<span>tp://</span>ww<span>w.youtube.com</span>/channel/**UCSLzUlTiImWtWEFPmtogs6w***. Derzeit werden jedoch nur Videos des YouTube-Kanals *The MAUTICAST* gefiltert, egal welche Channel-ID eingegeben wird.
- Im Bereich *Keywords* können Keywords hinzugefügt oder durch das Klicken auf *X* gelöscht werden. Ein Keyword ist hierbei jeder beliebige Suchbegriff, wie man ihn auch auf YouTube eingeben würde, um Videos zu finden. Ein Keyword kann auch aus mehreren Wörten bestehen. Möchte man auf einen Schlag mehrere Keywords gleichzeitig hinzufügen, müssen diese durch ein *Komma mit anschließendem Leerzeichen ohne Zeilenumbruch* voneinander separiert werden. Beispielhafte Keywords sind *The Mauticast* oder *Mautic*.
- Auf der Seite *Home* werden alle, in den Top 50, gefundenen Videos zu den entsprechenden Keywords mit Platzierung angezeigt. Möchte man gerade hinzugefügte Keywords anzeigen lassen oder gerade gelöschte Keywords nicht mehr anzeigen lassen, muss der Refresh-Button auf der *Home*-Seite gedrückt werden. Falls am aktuellen Tag noch kein Refresh durchgeführt wurde, werden mit dem Klick auf den Refresh-Button die Listen aktualisiert. Wurde am heutigen Tag schon auf den Refresh-Button geklickt, dann werden alle schon aktualisierten Videos nicht nochmal aktualisiert.
