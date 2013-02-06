<?php

$mypage = 'email';

$REX['ADDON']['xform']['classpaths']['action'][] = $REX["INCLUDE_PATH"]."/addons/xform/plugins/email/classes/action/";

include ($REX['INCLUDE_PATH'].'/addons/xform/plugins/email/classes/basic/class.rex_xform_emailtemplate.inc.php');

if($REX["REDAXO"] && !$REX['SETUP'])
{
  // Sprachdateien anhaengen
  $I18N->appendFile($REX['INCLUDE_PATH'].'/addons/xform/plugins/email/lang/');

  // $REX['ADDON']['name'][$mypage] = $I18N->msg("xform_email_templates");
  $REX['ADDON']['version'][$mypage] = '4.5';
  $REX['ADDON']['author'][$mypage] = 'Jan Kristinus';
  $REX['ADDON']['supportpage'][$mypage] = 'www.yakamara.de/tag/redaxo/';
  $REX['PERM'][] = 'xform[email]';

  if ($REX['USER'] && ($REX['USER']->isAdmin() || $REX['USER']->hasPerm("xform[email]")))
    $REX['ADDON']['xform']['SUBPAGES'][] = array ('email' , $I18N->msg("xform_email_templates"));

}

