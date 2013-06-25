<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

$mypage = 'xform';

$REX['ADDON']['name'][$mypage] = 'XForm';
$REX['ADDON']['perm'][$mypage] = 'xform[]';
$REX['ADDON']['version'][$mypage] = '4.5.1';
$REX['ADDON']['author'][$mypage] = 'Jan Kristinus';
$REX['ADDON']['supportpage'][$mypage] = 'www.yakamara.de/tag/xform/';
$REX['PERM'][] = 'xform[]';

if (empty($REX['ADDON']['xform']['classpaths']['value']) or !is_array($REX['ADDON']['xform']['classpaths']['value'])) {
  $REX['ADDON']['xform']['classpaths']['value'] = array();
}
if (empty($REX['ADDON']['xform']['classpaths']['action']) or !is_array($REX['ADDON']['xform']['classpaths']['action'])) {
  $REX['ADDON']['xform']['classpaths']['action'] = array();
}
if (empty($REX['ADDON']['xform']['classpaths']['validate']) or !is_array($REX['ADDON']['xform']['classpaths']['validate'])) {
  $REX['ADDON']['xform']['classpaths']['validate'] = array();
}

$REX['ADDON']['xform']['classpaths']['value'][] = $REX['INCLUDE_PATH'] . '/addons/xform/classes/value/';
$REX['ADDON']['xform']['classpaths']['validate'][] = $REX['INCLUDE_PATH'] . '/addons/xform/classes/validate/';
$REX['ADDON']['xform']['classpaths']['action'][] = $REX['INCLUDE_PATH'] . '/addons/xform/classes/action/';

include $REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/classes/basic/class.rex_radio.inc.php';
include $REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/classes/basic/class.rex_xform_list.inc.php';
include $REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/classes/basic/class.rex_xform.inc.php';

if ($REX['REDAXO'] && $REX['USER']) {
  $I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/lang/');

  $REX['ADDON'][$mypage]['SUBPAGES'] = array();
  $REX['ADDON'][$mypage]['SUBPAGES'][] = array( '' , $I18N->msg('xform_overview'));
  if ($REX['USER']->isAdmin())
    $REX['ADDON'][$mypage]['SUBPAGES'][] = array('description' , $I18N->msg('xform_description'));

  function rex_xform_css($params)
  {
    global $REX;
    $params['subject'] .= "\n  " . '<link rel="stylesheet" type="text/css" href="../files/addons/xform/xform.css" media="screen, projection, print" />';
    $params['subject'] .= "\n  " . '<script src="../files/addons/xform/manager.js" type="text/javascript"></script>';
    if ($REX['REDAXO']) {
      $params['subject'] .= "\n  " . '<link rel="stylesheet" type="text/css" href="../files/addons/xform/manager.css" media="screen, projection, print" />';
    }
    return $params['subject'];
  }

  rex_register_extension('PAGE_HEADER', 'rex_xform_css');

}
