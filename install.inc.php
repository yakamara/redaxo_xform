<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

$addonname = 'xform';
$I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/xform/lang/');

$msg = '';

if ($REX['VERSION'] != '4' || $REX['SUBVERSION'] < '5') {
  $msg = $I18N->msg('xform_install_redaxo_version_problem', '4.5');

} elseif (version_compare(PHP_VERSION, '5.3.0', '<')) {
  $msg = $I18N->msg('xform_install_checkphpversion', PHP_VERSION);

} elseif (OOAddon::isAvailable('phpmailer') != 1 || OOAddon::getVersion('phpmailer') < '2.8') {
  $msg = $I18N->msg('xform_install_phpmailer_version_problem', '2.8');

} else {

  // AUTOINSTALL THESE PLUGINS
  $autoinstall = array('email', 'setup', 'manager');

  // GET ALL ADDONS & PLUGINS
  $all_addons = rex_read_addons_folder();
  $all_plugins = array();
  foreach ($all_addons as $_addon) {
    $all_plugins[$_addon] = rex_read_plugins_folder($_addon);
  }

  // DO AUTOINSTALL
  $pluginManager = new rex_pluginManager($all_plugins, $addonname);
  foreach ($autoinstall as $pluginname) {
    // INSTALL PLUGIN
    if (($instErr = $pluginManager->install($pluginname)) !== true) {
      $msg = $instErr;
    }

    // ACTIVATE PLUGIN
    if ($msg == '' && ($actErr = $pluginManager->activate($pluginname)) !== true) {
      $msg = $actErr;
    }

    if ($msg != '') {
      break;
    }
  }

}


if ($msg != '') {
  $REX['ADDON']['installmsg'][$addonname] = $msg;

} else {
  // INSTALL ADDON
  $sql = rex_sql::factory();

  if ($sql->hasError()) {
    $msg = 'MySQL-Error: ' . $sql->getErrno() . '<br />';
    $msg .= $sql->getError();

    $REX['ADDON']['install'][$addonname] = 0;
    $REX['ADDON']['installmsg'][$addonname] = $msg;
  } else {
    $REX['ADDON']['install'][$addonname] = 1;
  }
}
