# youtube-search-ranking



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

Für einen Kanal werden alle seine Videos aufgelistet, welche in den angegebenen Keywords jeweils zum aktuellen Zeitpunkt in den Top 50 sind. Die Keywords werden absteigend aufgelistet und die dazugehörigen Videos nach der heutigen Platzierung angezeigt.

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

1. Die zip-Datei, welche den Code enthält, herunterladen und irgendwo auf dem System entpacken.

2. Verwaltungswerkzeug öffnen

3. aus dem Verwaltungswerkzeug heraus die skript.sql-Datei, welche sich in dem entpackten Ordner befindet, wo die zip-Datei entpackt wurde, öffnen und komplett ausführen

4. die Datei functions.php öffnen und bei $dbUsername, $dbPasswort und $dbHost jeweils in die Anführungszeichen die Daten eingeben, welche verwendet wurden, um sich beim Verwaltungswerkzeug anzumelden

5. das Terminal in dem Ordner öffnen, in welchem die zip-Datei entpackt wurde und den lokalen Webserver starten. Unter Linux lautet das Kommando: php -S localhost:8000

6. einen beliebigen Browser öffnen und in die URL-Zeile folgendes eingeben: Unter Linux ist dies http://localhost:8000/home.php

<br>
<br>
<br>

**3. Bedienung**

- Unter der Überschrift *YT Rankings* befindet sich die Navigationsleiste
- Klickt man in der Navigation auf *Channel*, so ergibt sich die Möglichkeit die ID des gewünschten Channels anzugeben. Dabei ist jedoch zu beachten, dass die Channel-ID folgendes ist: ht<span>tp://</span>ww<span>w.youtube.com</span>/channel/**UCSLzUlTiImWtWEFPmtogs6w**
- Im Bereich *Keywords* können Keywords hinzugefügt oder, durch das Klicken auf *X* gelöscht werden
- 
