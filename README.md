# youtube-search-ranking
listing all videos from a channel to selected keywords



Voraussetzung:

    • lokaler Webserver
    • maximal PHP-Version 7.4.x aktiviert haben
    • Verwaltungswerkzeug für Datenbanken (z.B. MySQL Workbench)



Anleitung

1. Die zip-Datei, welche den Code enthält, herunterladen und irgendwo auf dem System entpacken. Den Ort, 

2. Verwaltungswerkzeug öffnen

3. aus dem Verwaltungswerkzeug heraus die skript.sql-Datei, welche sich dort befindet wo die zip-Datei entpackt wurde, öffnen und komplett ausführen

4. die Datei functions.php öffnen und bei $dbUsername, $dbPasswort und $dbHost jeweils in die Anführungszeichen die Daten eingeben, welche verwendet wurden, um sich beim Verwaltungswerkzeug anzumelden

5. das Terminal in dem Ordner öffnen, in welchem die zip-Datei entpackt wurde und den lokalen Webserver starten. Unter Linux lautet das Kommando: php -S localhost:8000

6. einen beliebigen Browser öffnen und in die URL-Zeile folgendes eingeben: http://localhost:8000/home.php → unter Linux
