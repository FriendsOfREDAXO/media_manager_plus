# Media Manager Plus

Ermöglicht das Gruppieren von Media-Manager-Typen und stellt eine Frontend-API (PictureTag) bereit.
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
 
alternativen
```
<?php 
echo mmp::generatePictureTag('bildTyp', 'image.jpg');
?>
```

## Extension Points
Für die eigene Anpassung von Ausgaben, existieren folgende Extension Points

- MMP_BEFORE_PICTURETAG
- MMP_AFTER_PICTURETAG
- MMP_IMG_CLASS
- MMP_IMGTAG

### MP_BEFORE_PICTURE_TAG
Ermöglicht vor dem Picture Tag eigene Ausgaben zu gestalten. Es stehen in dem EP folgende Angaben zur Verfügung:
- mediatype
- filename
- filenamesByBreakpoint
- lazyload

### MMP_AFTER_PICTURE_TAG
Ermöglicht nach dem Picture Tag eigene Ausgaben zu gestalten. Es stehen die gleichen Parameter zur Verfügung wie bei MMP_BEFORE_PICTURE_TAG

### MMP_IMG_CLASS
Setzen von eigenen CSS Klassen auf dem IMG Tag. Dies ist wie folgt möglich

######Registierung EP

```
rex_extension::register('MMP_IMG_CLASS', function(rex_extension_point $ep) {
    $classes = $ep->getSubject();
    $classes = array_merge(beispiel::getClass(), $classes);
    return $classes;
}, rex_extension::LATE);
```

### Ausgabe eines Bild mit eigener CSS Klasse

```
class beispiel {
   private static $bildClasses = [];
   
   public static function getClass() {
    return self::$bildClasses;
   }
   
   public static function setClass($klassen) {
    self::$bidClasses = $klassen;
   }
}

beispiel::setClass(['class-1', 'class-2', 'class-3'])
media_manager_plus_frontend::generatePictureTag('eigenerTyp', 'bild.jpg');
```

### MMP_IMGTAG
ermöglicht eine eigene Ausgabe von dem tag "IMG". Es stehen die Parameter `mediatype`, `filename`, `filenamesByBreakpoint` und `lazyload` zur verfügung.

## Requirements

### Optional
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

