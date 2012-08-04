<?php

$REX['ADDON']['xform']['classpaths']['value'][] = $REX["INCLUDE_PATH"]."/addons/xform/plugins/geo/xform/value/";

if($REX["REDAXO"] && !$REX['SETUP'])
{

  // $I18N->appendFile($REX['INCLUDE_PATH'].'/addons/xform/plugins/geo/lang/');

  rex_register_extension('XFORM_MANAGER_TABLE_FIELD_FUNC', 'rex_xform_geo_page');
  function rex_xform_geo_page($params) {
    global $REX;
    include $REX["INCLUDE_PATH"].'/addons/xform/plugins/geo/pages/ep_geotagging.inc.php';
    return TRUE;
  }

  rex_register_extension('XFORM_MANAGER_DATA_EDIT_FUNC', 'rex_xform_geo_data');
  function rex_xform_geo_data($params) {
    global $REX;
    return TRUE;
  }

  $REX['ADDON']['version']['geo'] = '2.9.3';
  $REX['ADDON']['author']['geo'] = 'Jan Kristinus';
  $REX['ADDON']['supportpage']['geo'] = 'www.yakamara.de/tag/redaxo/';

}

