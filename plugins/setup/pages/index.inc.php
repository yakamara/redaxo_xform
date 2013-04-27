<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

rex_title('XForm', $REX['ADDON']['xform']['SUBPAGES']);

$searchtext = 'module:xform_basic_out';

$gm = rex_sql::factory();
$gm->setQuery('select * from rex_module where ausgabe LIKE "%' . $searchtext . '%"');

$module_id = 0;
$module_name = '';
foreach ($gm->getArray() as $module) {
  $module_id = $module['id'];
  $module_name = $module['name'];
}

if (isset($_REQUEST['install']) && $_REQUEST['install'] == 1) {

  $xform_module_name = 'XForm Formbuilder';

  $in = rex_get_file_contents($REX['INCLUDE_PATH'] . '/addons/xform/plugins/setup/module/module_in.inc');
  $out = rex_get_file_contents($REX['INCLUDE_PATH'] . '/addons/xform/plugins/setup/module/module_out.inc');

  $mi = rex_sql::factory();
  // $mi->debugsql = 1;
  $mi->setTable('rex_module');
  $mi->setValue('eingabe', addslashes($in));
  $mi->setValue('ausgabe', addslashes($out));

  if (isset($_REQUEST['module_id']) && $module_id == $_REQUEST['module_id']) {
    $mi->setWhere('id="' . $module_id . '"');
    $mi->update();
    echo rex_info('Modul "' . $module_name . '" wurde aktualisiert');

  } else {
    $mi->setValue('name', $xform_module_name);
    $mi->insert();
    $module_id = (int) $mi->getLastId();
    echo rex_info('XForm Modul wurde angelegt unter "' . $xform_module_name . '"');

  }

}

echo '

<div class="rex-addon-output">
  <h2 class="rex-hl2">' . $I18N->msg('xform_setup_install_modul') . '</h2>
  <div class="rex-addon-content">
  <p>' . $I18N->msg('xform_setup_install_modul_description') . '</p>
  <ul>
    <li><a href="index.php?page=xform&amp;subpage=setup&amp;install=1">' . $I18N->msg('xform_setup_install_xform_modul') . '</a></li>';

    if ($module_id > 0) {
      echo '<li><a href="index.php?page=xform&amp;subpage=setup&amp;install=1&amp;module_id=' . $module_id . '">' . $I18N->msg('xform_setup_update_following_modul', htmlspecialchars($module_name)) . '</a></li>';
    }

echo '
  </ul>
  </div>
</div>';



if (OOPlugIn::isAvailable('xform', 'manager')) {

  $func = rex_request('func', 'string');
  $table_name = rex_request('table_name', 'string');

  $current_tables = rex_xform_manager_table::getTablesAsArray();

  $gt = rex_sql::factory();
  $gt->setQuery('show tables;');

  $ts = array();
  foreach ($gt->getArray() as $t) {
    $ts[] = current($t);
  }

  $ots = array();
  if (in_array('rex_em_table', $ts)) {

    $gt = rex_sql::factory();
    $gt->setQuery('select * from rex_em_table');
    $gts = $gt->getArray();

    if (count($gts) > 0) {
      foreach ($gts as $gt) {
        if (!in_array($gt['table_name'], $current_tables)) {

          if ($func == 'copyoldtables' && $gt['table_name'] == $table_name) {

            // fields auslesen und übernehmen
            $gfs = rex_sql::factory();
            $gfs->setQuery('select * from rex_em_field where table_name="' . mysql_real_escape_string($gt['table_name']) . '"');

            foreach ($gfs->getArray() as $gf) {
              $u = rex_sql::factory();
              // $u->debugsql = 1;
              $u->setTable('rex_xform_field');
              foreach ($gf as $k => $v) {
                if ($k != 'id') $u->setValue($k, $v);
              }
              $u->insert();
            }

            // relations auslesen und übernehmen
            $gfs = rex_sql::factory();
            // $gfs->debugsql = 1;
            $gfs->setQuery('select * from rex_em_relation where source_table="' . mysql_real_escape_string($gt['table_name']) . '"');

            foreach ($gfs->getArray() as $gf) {
              $u = rex_sql::factory();
              // $u->debugsql = 1;
              $u->setTable('rex_xform_relation');
              foreach ($gf as $k => $v) {
                if ($k != 'id') $u->setValue($k, $v);
              }
              $u->insert();
            }

            // table auslesen und übernehmen
            $gfs = rex_sql::factory();
            // $gfs->debugsql = 1;
            $gfs->setQuery('select * from rex_em_table where table_name="' . mysql_real_escape_string($gt['table_name']) . '"');

            foreach ($gfs->getArray() as $gf) {
              $u = rex_sql::factory();
              // $u->debugsql = 1;
              $u->setTable('rex_xform_table');
              foreach ($gf as $k => $v) {
                if ($k != 'id') $u->setValue($k, $v);
              }
              $u->insert();
            }

            echo rex_info($I18N->msg('xform_setup_table_copied', $gt['table_name']));

          } else {
            $ots[] = $gt['table_name'];
          }
        }
      }
    }

  }

  if (in_array('rex_com_table', $ts)) {

    $gt = rex_sql::factory();
    $gt->setQuery('select * from rex_com_table');
    $gts = $gt->getArray();

    if (count($gts) > 0) {
      foreach ($gts as $gt) {
        if (!in_array($gt['table_name'], $current_tables)) {
          if ($func == 'copyoldtables' && $gt['table_name'] == $table_name) {

            // fields auslesen und übernehmen
            $gfs = rex_sql::factory();
            $gfs->setQuery('select * from rex_com_field where table_name="' . mysql_real_escape_string($gt['table_name']) . '"');

            foreach ($gfs->getArray() as $gf) {
              $u = rex_sql::factory();
              // $u->debugsql = 1;
              $u->setTable('rex_xform_field');
              foreach ($gf as $k => $v) {
                if ($k != 'id') $u->setValue($k, $v);
              }
              $u->insert();
            }

            // relations auslesen und übernehmen
            $gfs = rex_sql::factory();
            // $gfs->debugsql = 1;
            $gfs->setQuery('select * from rex_com_relation where source_table="' . mysql_real_escape_string($gt['table_name']) . '"');

            foreach ($gfs->getArray() as $gf) {
              $u = rex_sql::factory();
              // $u->debugsql = 1;
              $u->setTable('rex_xform_relation');
              foreach ($gf as $k => $v) {
                if ($k != 'id') $u->setValue($k, $v);
              }
              $u->insert();
            }

            // table auslesen und übernehmen
            $gfs = rex_sql::factory();
            // $gfs->debugsql = 1;
            $gfs->setQuery('select * from rex_com_table where table_name="' . mysql_real_escape_string($gt['table_name']) . '"');

            foreach ($gfs->getArray() as $gf) {
              $u = rex_sql::factory();
              // $u->debugsql = 1;
              $u->setTable('rex_xform_table');
              foreach ($gf as $k => $v) {
                if ($k != 'id') $u->setValue($k, $v);
              }
              $u->insert();
            }

            echo rex_info($I18N->msg('xform_setup_table_copied', $gt['table_name']));

          } else {
            $ots[] = $gt['table_name'];
          }
        }
      }
    }

  }


  /*
    $gf = rex_sql::factory();
    $gf->setQuery('select * from rex_em_field');
    echo '<pre>'; var_dump($gf->getArray()); echo '</pre>';


    $gf = rex_sql::factory();
    $gf->setQuery('select * from rex_em_table');
    echo '<pre>'; var_dump($gf->getArray()); echo '</pre>';


    $gf = rex_sql::factory();
    $gf->setQuery('select * from rex_em_relation');
    echo '<pre>'; var_dump($gf->getArray()); echo '</pre>';
  */

  if (count($ots)) {

    echo '
    <div class="rex-addon-output">
      <h2 class="rex-hl2">' . $I18N->msg('xform_setup_oldtables') . '</h2>
      <div class="rex-addon-content">
      <p>' . $I18N->msg('xform_setup_oldtables_description') . '</p>
      <ul>';

    foreach ($ots as $ot) {
      echo '<li><a href="index.php?page=xform&amp;subpage=setup&amp;func=copyoldtables&amp;table_name=' . $ot . '">' . $I18N->msg('xform_setup_copytables', $ot) . '</a></li>';

    }

    echo '
      </ul>
      </div>
    </div>';


  }


}
