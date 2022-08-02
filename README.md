# youtube-search-ranking

<br>

**Inhaltsverzeichnis**

1. Funktionsweise
2. Installationsanleitung
    1. Voraussetzung
    2. Anleitung
3. Bedienung

<br>
<br>
<br>

**1. Funktionsweise**

Für einen Kanal werden alle seine Videos aufgelistet, welche in den angegebenen Keywords jeweils zum aktuellen Zeitpunkt in den Top 50 sind. Die Keywords werden alphabetisch absteigend aufgelistet und die dazugehörigen Videos nach der heutigen Platzierung angezeigt.

<br>
<br>
<br>

**2. Installationsanleitung**

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

5. das Terminal in dem Ordner öffnen, welcher aus der zip-Datei entpackt wurde und den lokalen Webserver starten. Unter Linux lautet das Kommando: *php -S localhost:8000*

6. einen beliebigen Browser öffnen und in die Adressleiste folgendes eingeben: unter Linux ist dies *ht<span>tp://</span>localhost:8000/home.php*

<br>
<br>
<br>

**3. Bedienung**

- Unter der Überschrift *YT Rankings* befindet sich die Navigationsleiste.
- Klickt man in der Navigation auf *Channel*, so ergibt sich die Möglichkeit die ID des gewünschten Channels anzugeben. Dabei ist jedoch zu beachten, dass die Channel-ID nur das Fettgedruckte ist: ht<span>tp://</span>ww<span>w.youtube.com</span>/channel/**UCSLzUlTiImWtWEFPmtogs6w**. Derzeit werden jedoch nur Videos des YouTube-Kanals *The MAUTICAST* gefiltert, egal welche Channel-ID eingegeben wird.
- Im Bereich *Keywords* können Keywords hinzugefügt oder durch das Klicken auf *X* gelöscht werden.
- Auf der Seite *Home* werden alle, in den Top 50, gefundenen Videos zu den entsprechenden Keywords mit Platzierung angezeigt. Mit dem Klicken auf den Refresh-Button werden nun neu hinzugefügte Keywords angezeigt bzw. gerade gelöschte Keywords nicht mehr angezeigt. Falls am aktuellen Tag noch kein Refresh durchgeführt wurde, werden mit dem Klick auf den Refresh-Button die Listen aktualisiert.
