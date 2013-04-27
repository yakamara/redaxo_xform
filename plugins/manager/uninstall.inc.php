<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

$REX['ADDON']['install']['manager'] = 0;

$sql = rex_sql::factory();
$sql->setQuery('DROP TABLE `' . $REX['TABLE_PREFIX'] . 'xform_table`;');
$sql->setQuery('DROP TABLE `' . $REX['TABLE_PREFIX'] . 'xform_field`;');
$sql->setQuery('DROP TABLE `' . $REX['TABLE_PREFIX'] . 'xform_relation`;');
