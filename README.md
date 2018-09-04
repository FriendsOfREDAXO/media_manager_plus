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

#### Variante 1
Liefert einen komplett fertigen picture Tag aus, inkl. der source Angaben, jeweiligen Pixel Ratio größen sowie der Breakpoints.
`<?php echo mmp::image('bild.jpg', 'gruppenname', 'Dies ist der Alternativ Text des Bildes', 'img-css-classes', 'id="bild"'); ?>`

#### Variante 2
Liefert lediglich den source Tags zurück, um eigene Picture Tag mit img ausgaben zu gestalten.
`<?php echo mmp::imageSrcSet('bild.jpg', 'gruppenname'); ?>` 

## URL Aufbereitung

Falls das Addon media manager autorewrite installiert ist, so wird die Bildausgabe automatisch auf diesen Typ angepasst.
 
**Achtung:** Die Opt-In und Opt-Out Methode stellt **nur** die Callbacks zu dem Ablehnen oder Aktzeptieren der Cookies bereit. [Callbacks](https://cookieconsent.insites.com/documentation/disabling-cookies/)

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

First Readme Release: [Markus Schnieder](https://github.com/mschnieder)

