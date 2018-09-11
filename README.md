# Media Manager Plus

Ermöglicht das Gruppieren von Media-Manager-Typen und stellt eine Frontend-API (PictureTag) bereit.

# Media Manager Plus


Das AddOn erweitert den Media Manager von Redaxo. Es ist mit dem Media Manager Plus möglich, verschiedene Breakpoints
für ein Bild zu hinterlegen. Somit besteht die möglichkeit verschiedene Auflösungen von Bildern zur Verfügung zu stellen.

## Features

- Erweiterung um Gruppen und deren Verwaltung
- Erweiterung um Breakpoints
- Zentrale Ausgabefunktion


## Installation

1. Über Installer laden oder ZIP-Datei im AddOn-Ordner entpacken, der Ordner muss „media_manager_plus“ heißen.
2. AddOn installieren und aktivieren

## Verwendung
### Verwendung von Bildausgaben
Es besteht die möglichkeit über die statischen PHP Methoden die Ausgaben automatisch im passenden Format zurück zu geben.


Liefert einen komplett fertigen picture Tag aus, inkl. der source Angaben, jeweiligen Pixel Ratio größen sowie der Breakpoints.

`<?php echo media_manager_plus_frontend::generatePictureTag('bildTyp', 'image.jpg'); ?>` 

## URL Aufbereitung

Falls das Addon media manager autorewrite installiert ist, so wird die Bildausgabe automatisch auf diesen Typ angepasst.

## Requirements

##### Optional
Das FOR-AddOn gestaltet die URL Ausgaben benutzerfreundlich 
* [MM Autorewrite](https://github.com/FriendsOfREDAXO/media_manager_autorewrite)

## Bugtracker

Du hast einen Fehler gefunden oder ein nettes Feature was du gerne hättest? [Lege ein Issue an](https://github.com/FriendsOfREDAXO/media_manager_plus/issues)

## Autor

**Friends Of REDAXO**

* http://www.redaxo.org
* https://github.com/FriendsOfREDAXO

**Projekt-Lead**

[Thomas Kaegi](https://github.com/phoebusryan)


## Credits:

Readme: [Markus Schnieder](https://github.com/mschnieder)

