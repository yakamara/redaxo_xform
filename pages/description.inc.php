<?php

rex_title('XForm', $REX['ADDON'][$page]['SUBPAGES']);

echo '<div class="rex-addon-output">
  <h2 class="rex-hl2">Beschreibung</h2>
  <div class="rex-addon-content">
  <div class="xform-description">' . $I18N->msg('xform_description_all') . '</div>';

echo rex_xform::showHelp(true, true);

echo '
  </div>
</div>';
