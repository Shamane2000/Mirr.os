[![build status](https://gitlab.com/glancr/system/badges/master/build.svg)](https://gitlab.com/glancr/system/commits/master)

# Glancr System
Die Basisapplikation für den mirr.OS Smart Mirror. 

## Releases
Unter [Tags](https://gitlab.com/glancr/system/tags) sind alle Releases gelistet. Jedes Release hat auch ein fertiges Build-Artefakt, das über den Download-Button > Download 'release-zip' geladen werden kann. Die Builds enthalten die komplette Anwendung inkl. aller Bibliotheken.

## Mitentwickeln
Wer an der mirr.OS-Anwendung mitentwickeln möchte:


#### Voraussetzungen
* git
* [composer](https://getcomposer.org)

#### Ablauf
1. Repo clonen
2. `composer install` im Root-Verzeichnis
3. Fröhliches Entwickeln ;-)

Um Merge Requests stellen zu können einfach hier Zugriff aufs Repo anfordern.

Die einzelnen Module gibt's [in einem eigenen Repo.](https://gitlab.com/glancr/modules)


## Verzeichnisstruktur
```
system
|- 404/ <- Fehlerseite mit Back-Link
|- classes/ <- Utility-Klassen für Mailversand, Updater usw.
|- config/ <- Logik und Templates für die Konfigurationsoberfläche (local.ip/config/, was man aus den E-Mails aufruft)
|- glancr/ <- Frontend-Code: Was auf dem Spiegel-Display ausgegeben wird
|- locale/ <- Lokalisierungen der System-Anwendung
|- modules/ <- Installierte Module im jeweiligen Unterverzeichnis
|- reset/ <- Seite zum Zurücksetzen auf Werkseinstellungen
|- vendor/ <- Via composer installierte Drittbibliotheken
|- wlanconfig/ <- Logik & Templates für den Setupprozess (WLAN einrichten, Grundeinstellungen)
|- cron.php <- wird alle 15 min vom System-Cron aufgerufen, prüft u.a. auf Updates für Module & System
|- index.php <- redirect auf config/
|- info.json <- mirr.OS-Versionsnummer usw.
|- nonet.php <- Template für den initialen Einrichtungsprozess oder falls keine (W)LAN-Verbindung besteht
``