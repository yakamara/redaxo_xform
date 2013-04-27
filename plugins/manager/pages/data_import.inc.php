<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

ini_set('auto_detect_line_endings', true);

$show_importform = true;
$show_list = false;

$rfields = rex_xform_manager_table::getFields($table['table_name']);

$replacefield = rex_request('replacefield', 'string');
$divider = rex_request('divider', 'string', ';');
$missing_columns = rex_request('missing_columns', 'int');
$debug = rex_request('debug', 'string');

if ($replacefield == '') $replacefield = 'id';
if (!in_array($divider, array(';', ',', 'tab'))) $divider = ',';
if ($missing_columns != 2 && $missing_columns != 3) $missing_columns = 1;
if ($debug != 1) $debug = 0;


if (rex_request('send', 'int', 0) == 1) {
  // Daten wurden übertragen

  if (!isset($_FILES['file_new']) || $_FILES['file_new']['tmp_name'] == '') {
    echo rex_warning('Bitte laden Sie eine Importdatei hoch');

  } else {

    $func = '';
    $show_importform = false;

    $fieldarray = array();
    $filename = $_FILES['file_new']['tmp_name'];

    $div = $divider;
    if ($div == 'tab') $div = "\t";

    $counter = 0;  // importierte
    $dcounter = 0; // nicht imporierte
    $ecounter = 0; // leere reihen
    $rcounter = 0; // replace counter
    $icounter = 0; // insert counter
    $errorcounter = 0;

    $i = rex_sql::factory();
    if ($debug) {
      $i->debugsql = 1;
    }
    $fp = fopen($filename, 'r');
    while ( ($line_array = fgetcsv($fp, 30384, $div)) !== false ) {

      if (count($fieldarray) == 0) {
        // ******************* first line

        $fieldarray = $line_array;

        $mc = array();
        foreach ($fieldarray as $k => $v) {
          if (!array_key_exists($fieldarray[$k], $rfields) && $fieldarray[$k] != 'id') {
            $mc[$fieldarray[$k]] = $fieldarray[$k];
          }
        }

        if (count($mc) > 0) {
          if ($missing_columns == 3) {
            echo rex_warning('Es fehlen folgende Felder: ' . implode(', ', $mc));
            $show_importform = true;
            $func = 'import';
            break;

          } elseif ($missing_columns == 2) {
            $error = false;
            foreach ($mc as $mcc) {
              $sql = 'ALTER TABLE `' . $table['table_name'] . '` ADD `' . mysql_real_escape_string($mcc) . '` TEXT NOT NULL;';
              $upd = rex_sql::factory();
              $upd->setQuery($sql);

              if ($upd->getError()) {
                $error = true;
                echo rex_warning('Feld "' . $mcc . '" konnte nicht angelegt werden: ' . $upd->getError());

              } else {
                echo rex_info('Feld "' . $mcc . '" wurde angelegt');
              }

            }
            if ($error) {
              echo rex_warning('Import wurde abgebrochen, da Fehler aufgetaucht sind.');
              $show_importform = true;
              break;
            }

            $rfields = rex_xform_manager_table::getFields($table['table_name']);

          }

        }


      } else {
        if (!$line_array) {
          break;

        } else {

          $counter++;
          $i->setTable($table['table_name']);
          $replacevalue = '';
          foreach ($line_array as $k => $v) {
            if ($fieldarray[$k] != '' && (array_key_exists($fieldarray[$k], $rfields) || $fieldarray[$k] == 'id')) {
              $i->setValue($fieldarray[$k], mysql_real_escape_string($v));
              if ($replacefield == $fieldarray[$k]) {
                $replacevalue = $v;
              }
            }
          }

          // noch abfrage ob
          // $replacefield
          $cf = rex_sql::factory();
          $cf->setQuery('select * from ' . $table['table_name'] . ' where ' . $replacefield . '="' . mysql_real_escape_string($replacevalue) . '"');

          if ($cf->getRows() > 0) {
            $i->setWhere($replacefield . '="' . mysql_real_escape_string($replacevalue) . '"');
            $i->update();
            $error = $i->getError();
            if ($error == '') {
              $rcounter++;
            } else {
              $dcounter++;
              echo rex_warning('Datensatz konnte nicht importiert werden: ' . $error);
            }
          } else {
            $i->insert();
            $error = $i->getError();
            if ($error == '') {
              $icounter++;
            } else {
              $dcounter++;
              echo rex_warning('Datensatz konnte nicht importiert werden: ' . $error);
            }
          }

        }
      }

      $show_list = true;

    }

    echo rex_info('Es wurden ' . ($icounter + $rcounter) . ' Datensätze importiert. Davon waren ' . $icounter . ' neue Datensätze und ' . $rcounter . ' Datensätze wurden ersetzt.');

    if ($dcounter > 0) {
      echo rex_warning('Es wurde/n ' . $dcounter . ' Datensätze nicht importiert.');
    }

  }

}







if ($show_importform) {

  ?>
  <div class="rex-addon-output"><h3 class="rex-hl2">CSV Datei importieren</h3><div class="rex-addon-content"><div id="rex-xform-import" class="xform">

  <form action="index.php" method="post" enctype="multipart/form-data">

      <p class="rex-tx1">Hiermit können Sie Daten in diese Tabelle importieren. BItte beachten Sie, dass Sie nur Textdateien importieren können und in der ersten Zeile die Felddefinitionen vorhanden sein müssen</p>

      <?php
      foreach ($this->getLinkVars() as $k => $v) {
        echo '<input type="hidden" name="' . $k . '" value="' . addslashes($v) . '" />';
      }
      ?>
      <input type="hidden" name="func" value="import" />
      <input type="hidden" name="send" value="1" />

<!--
        <p class="formcheckbox formlabel-debug" id="xform-formular-debug">

        <input type="checkbox" class="checkbox" name="debug" id="xform-debug" value="1" <?php if ($debug == 1) echo 'checked'; ?> />
        <label class="checkbox " for="xform-debug" >Debug</label>
      </p>
-->


      <?php

      // ignore_missing_columns

      echo '
      <p class="formradio formlabel-missing_columns"  id="xform-formular-missing_columns">
        <strong>Wenn Spalte in der Datenbank nicht vorhanden ist, </strong>
      </p>';

      $radio = new rex_radio();
      $radio->setId('missing_columns');
      $radio->setName('missing_columns');
      $radio->addOption('dann Spalte ignorieren.', '1');
      $radio->addOption('dann Spalte als TEXT in der Datenbank anlegen (Zur Verwaltung müssen später diese Felder noch bestimmt werden).', '2');
      $radio->addOption('dann Import abbrechen.', '3');
      // $SEL->setStyle(' class="select ' . $wc . '"');
      $radio->setSelected($missing_columns);
      echo $radio->get();

      ?>

        <p class="rex-form-select">
        <label class="select " for="divider" >Trennzeichen</label>
        <?php
        $a = new rex_select();
        $a->setName('divider');
        $a->setId('divider');
        $a->setSize(1);
        $a->addOption('Semikolon (;)', ';');
        $a->addOption('Komma (,)', ',');
        $a->addOption('Tabulator', 'tab');
        $a->setSelected($divider);
        echo $a->get();
        ?>
          </p>

        <p class="rex-form-text">
          <label for="rex-form-error-replacefield">Wenn dieses Feld identisch ist, dann wird der Datensatz ersetzt</label>
          <input class="rex-form-text" type="text" id="rex-form-replacefield" name="replacefield" value="<?php echo htmlspecialchars(stripslashes($replacefield)); ?>" />
        </p>

        <p class="rex-form-file">
          <label for="file_new">Datei</label>
          <input class="rex-form-file" type="file" id="file_new" name="file_new" size="30" />
        </p>

        <p class="rex-form-submit">
         <input class="submit" type="submit" name="save" value="Hinzufügen" title="Hinzufügen" />
        </p>

  </form>
  </div></div></div>
  <?php

}
