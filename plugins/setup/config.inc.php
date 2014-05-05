<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

$mypage = 'setup';

if ($REX['REDAXO'] && !$REX['SETUP']) {

    $I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/xform/plugins/setup/lang/');

    $REX['ADDON']['version'][$mypage] = '4.5.1';
    $REX['ADDON']['author'][$mypage] = 'Jan Kristinus';
    $REX['ADDON']['supportpage'][$mypage] = 'www.yakamara.de/tag/xform/';

    if ($REX['USER'] && $REX['USER']->isAdmin()) {
    $REX['ADDON']['xform']['SUBPAGES'][] = array('setup' , $I18N->msg('xform_setup'));
    }

}
