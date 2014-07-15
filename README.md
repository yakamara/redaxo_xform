XForm for REDAXO Version 4.6
=============

AddOn to manage tables and forms for REDAXO CMS Version 4.6


Installation
-------

* Download and unzip
* Rename the unzipped folder from redaxo_xform to xform
* Move the folder to your REDAXO 4.6 System /redaxo/include/addons/
* Install and activate the addon xform and the plugins setup, manager, email in the REDAXO 4.6 backend


Last Changes
-------


Version 4.6...

Neu

* date, datetime und time haben nun auch die no_db option

Änderungen

* Default Klassenbeschreibung in base abstract entfernt.

Bugs

* Export der Daten nicht erlauben - wird nun richtig ausgewertet
* einige individuelle css klassen wurden nicht getrennt gesetzt
* Ausgabe der Klassenbeschreibungen werden nun nur mit richtig definierte Dateiname (class.*.inc.php) angezeigt
* E-Mail Validierung geht wieder
* GeoPlugin funktionierte nicht richtig. Achtung lat/lng Reihenfolge ist nun umgedreht.


Version 4.6.2

Bugs

* db2email action ging nicht.


Version 4.6.1

New

* Manager: 1-n Verknüpfungen ergänzt, inkl Darstellung in der Popupansicht.
* Elemente kann man und sollte man nun mit Namen verwenden (nicht mehr ids/Zahlen). Vorhandene Klassen wurden bereits angepasst
* Templates integriert: Man kann nun eigenes Markup für die Formulare verwenden. Default skin entspricht classic XForm Markup
* php 5.3 Anpassungen inkl. Formularverwendung von PHP aus verbessert.
* Codeeinrückungen/Darstellung angepasst
* Texte und Benennungen geändert und korrigiert.
* Deleted: jquery.value class weil zu kompliziert und kaum verwendet


Bugs

* Objectparams in runtime mode are available now