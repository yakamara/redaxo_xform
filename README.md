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

### Version 4.7.1 // xxx

#### Neu

* Manager: Migrationsmanager ist über das Backend verfügbar und einsetzbar.
* Manager: Migrationsmanager unterstützt weitere Feldtypen
* Prio-Value-Klasse für Bestimmung der Reihenfolge von Datensätzen (wird auch in der Tabellen- und Felder-Verwaltung des Managers verwendet)
* Manager: Standardsortierung kann festgelegt werden
* Manager: Echte Relationstabellen können verwendet werden

#### Bugs

* Manager: Tabellen laufen nun über die Translatefunktion und können mehrsprachig sein.
* E-Mail-Validierung: Adressen mit UTF8-Zeichen werden akzeptiert.
* Typ-Validierung: Missverständlicher Key "required" in "not_required" umbenannt
* XForm-Felder für vorhandene Spalten konnten nicht angelegt werden, falls bereits eine Validierung für das Feld vorhanden war
* Manager: Beim Editieren und Löschen von Tabellen und Feldern bleibt man auf der entsprechenden Seite
* Manager: Für Fieldsets keine Spalten in Datentabelle anlegen
* select_sql: Bei Verwendung der Leeroption kam es zu ungewollten Verhalten


### Version 4.7 // 25. Juli 2014

#### Neu

* Migrationsmanager (Basis) / rex_xform_manager_table_api::migrateTable($tablename);
* Manager Api, damit andere AddOns XForm Felder automatisiert anlegen können /rex_xform_manager_table_api::setTable($tablearray, $fieldsarray)

#### Bugs

* Manager: Beim Löschen von Datensätzen bleibt man nun auf der entsprechenden Seite


### Version 4.6.3 // 22. Juli 2014

#### Neu

* date, datetime und time haben nun auch die no_db option
* aus tabelle heraus als admin direkt zum Felder bearbeiten springen
* Felder und Tabellenlisten im Manager sind umstrukturiert (Feldnamen sortiert und übersetzt)


#### Änderungen

* Default Klassenbeschreibung in base abstract entfernt.
* Benennungen geändert

#### Bugs

* Export der Daten nicht erlauben - wird nun richtig ausgewertet
* einige individuelle css klassen wurden nicht getrennt gesetzt
* Ausgabe der Klassenbeschreibungen werden nun nur mit richtig definierte Dateiname (class.*.inc.php) angezeigt
* E-Mail Validierung geht wieder und wird auch nach ".." kontrolliert.
* GeoPlugin funktionierte nicht richtig. Achtung lat/lng Reihenfolge ist nun umgedreht.


### Version 4.6.2

#### Bugs

* db2email action ging nicht.


### Version 4.6.1

#### New

* Manager: 1-n Verknüpfungen ergänzt, inkl Darstellung in der Popupansicht.
* Elemente kann man und sollte man nun mit Namen verwenden (nicht mehr ids/Zahlen). Vorhandene Klassen wurden bereits angepasst
* Templates integriert: Man kann nun eigenes Markup für die Formulare verwenden. Default skin entspricht classic XForm Markup
* php 5.3 Anpassungen inkl. Formularverwendung von PHP aus verbessert.
* Codeeinrückungen/Darstellung angepasst
* Texte und Benennungen geändert und korrigiert.
* Deleted: jquery.value class weil zu kompliziert und kaum verwendet


#### Bugs

* Objectparams in runtime mode are available now
