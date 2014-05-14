<?php

if (OOPlugin::isInstalled('xform', 'manager')) {
    include rex_path::plugin('xform', 'manager', 'install.inc.php');
}

$REX['ADDON']['update']['xform'] = true;
