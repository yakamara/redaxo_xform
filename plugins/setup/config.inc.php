<?php

$mypage = 'setup';

if($REX["REDAXO"] && !$REX['SETUP'])
{
	// Sprachdateien anhaengen
	$I18N->appendFile($REX['INCLUDE_PATH'].'/addons/xform/plugins/setup/lang/');

	$REX['ADDON']['version'][$mypage] = '2.8';
	$REX['ADDON']['author'][$mypage] = 'Jan Kristinus';
	$REX['ADDON']['supportpage'][$mypage] = 'www.yakamara.de/tag/xform/';

   	if ($REX['USER'] && $REX['USER']->isAdmin())
		$REX['ADDON']['xform']['SUBPAGES'][] = array ('setup' , $I18N->msg("xform_setup"));

}

