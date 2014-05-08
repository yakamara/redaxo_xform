<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

$REX['ADDON']['xform']['classpaths']['value'][] = $REX['INCLUDE_PATH'] . '/addons/xform/plugins/geo/classes/value/';

$REX['ADDON']['xform']['templatepaths'][] = $REX['INCLUDE_PATH'] . '/addons/xform/plugins/geo/templates/';

if ($REX['REDAXO'] && !$REX['SETUP']) {

    // $I18N->appendFile($REX['INCLUDE_PATH'].'/addons/xform/plugins/geo/lang/');

    rex_register_extension('XFORM_MANAGER_TABLE_FIELD_FUNC', 'rex_xform_geo_page');
    function rex_xform_geo_page($params)
    {
        global $REX;
        include $REX['INCLUDE_PATH'] . '/addons/xform/plugins/geo/pages/ep_geotagging.inc.php';
        return true;
    }

    rex_register_extension('XFORM_MANAGER_DATA_EDIT_FUNC', 'rex_xform_geo_data');
    function rex_xform_geo_data($params)
    {
        global $REX;
        return true;
    }

    $REX['ADDON']['version']['geo'] = '4.5.1';
    $REX['ADDON']['author']['geo'] = 'Jan Kristinus';
    $REX['ADDON']['supportpage']['geo'] = 'www.yakamara.de/tag/redaxo/';

}
