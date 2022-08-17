# Media Manager Plus

Ermöglicht das Gruppieren von Media-Manager-Typen und stellt eine Frontend-API (PictureTag) bereit.
Das AddOn erweitert den Media Manager von REDAXO. Es ist mit dem Media Manager Plus möglich, verschiedene Breakpoints
für ein Bild zu hinterlegen. Somit besteht die Möglichkeit, verschiedene Auflösungen von Bildern zur Verfügung zu stellen.


## Features

- Erweiterung um Gruppen und deren Verwaltung
- Erweiterung um Breakpoints
- Zentrale Ausgabefunktion


## Installation

1. Über Installer laden oder ZIP-Datei im AddOn-Ordner entpacken, der Ordner muss `media_manager_plus` heißen.
2. AddOn installieren und aktivieren


## Verwendung

### Verwendung von Bildausgaben

Es besteht die Möglichkeit, über die statischen PHP-Methoden die Ausgaben automatisch im passenden Format zurückzugeben.

Liefert einen komplett fertigen picture Tag aus, inkl. der source Angaben, jeweiligen Pixel-Ratio-Größen sowie der Breakpoints.

```php
echo media_manager_plus_frontend::generatePictureTag('bildTyp', 'image.jpg');
```
 
oder alternativ:

```php
echo mmp::generatePictureTag('bildTyp', 'image.jpg');
```

### Verwendung von LazyLoad
Für die Verwendung des LazyLoad (kleines bild anzeigen => großes Bild nachträglich laden) wird eine Javascript Library benötigt.
Dies kann mit nachfolgendem Code eingebunden werden. Das Javascript wird mit dem Tag Attribut "defer" ausgegeben.

```php
echo mmp::getLazyLoad();
```

### Verwendung von Breakpoint abhängigen Bildern
Um je nach Breakpoint ein anders Bild auszugeben, kann bei dem aufruf von "generatePictureTag" ein dritter Parameter vom Typ Array übergeben werden.

Dieses Beispiel sorgt dafür, das bei dem Breakpoint XL das Bild anderes_image.jpg ausgegeben wird.

```php
echo mmp::generatePictureTag('bildTyp', 'image.jpg', ['XL' => 'anderes_image.jpg']);
```

## Extension Points

Für die eigene Anpassung von Ausgaben, existieren folgende Extension Points

- `MMP_BEFORE_PICTURETAG`
- `MMP_AFTER_PICTURETAG`
- `MMP_IMG_CLASS`
- `MMP_IMGTAG`
- `MMP_IMG_ALT`

### `MMP_BEFORE_PICTURETAG`

Ermöglicht vor dem Picture Tag eigene Ausgaben zu gestalten. Es stehen in dem EP folgende Angaben zur Verfügung:

- `mediatype`
- `filename`
- `filenamesByBreakpoint`
- `lazyload

### `MMP_AFTER_PICTURETAG`

Ermöglicht nach dem Picture Tag eigene Ausgaben zu gestalten. Es stehen die gleichen Parameter zur Verfügung wie bei `MMP_BEFORE_PICTURETAG`

### `MMP_IMG_CLASS`

Setzen von eigenen CSS Klassen auf dem IMG Tag. Dies ist wie folgt möglich:

```php
rex_extension::register('MMP_IMG_CLASS', function(rex_extension_point $ep) {
    $classes = $ep->getSubject();
    $classes = array_merge(beispiel::getClass(), $classes);
    return $classes;
}, rex_extension::LATE);
```

### Ausgabe eines Bild mit eigener CSS-Klasse

```php
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

// Für nachfolgende Aufrufe ohne eigener Class, einfach danach den Standard festlegen
beispiel::setClass(['standard_klasse']);
```

### `MMP_IMGTAG`

ermöglicht eine eigene Ausgabe von dem tag "IMG". Es stehen die Parameter `mediatype`, `filename`, `filenamesByBreakpoint` und `lazyload` zur verfügung.

### `MMP_IMG_ALT`

über diesen Endpoint kann der wert des alt attributes durch einen eigenen Wert überschrieben werden.

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

[Markus Schnieder](https://github.com/mschnieder)

## Credits:

vorheriger Lead: [Thomas Kaegi](https://github.com/phoebusryan)

Readme: [Markus Schnieder](https://github.com/mschnieder)

