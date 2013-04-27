<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_validate_existintable extends rex_xform_validate_abstract
{

  function enterObject()
  {
    if ($this->params['send'] == '1') {
      foreach ($this->obj_array as $Object) {
        $sql = 'select ' . $this->getElement(2) . ' from ' . $this->getElement(3) . ' WHERE ' . $this->getElement(4) . '="' . $Object->getValue() . '" LIMIT 2';
        $cd = rex_sql::factory();
        if ($this->params['debug']) {
          $cd->debugsql = 1;
        }
        $cd->setQuery($sql);
        if ($cd->getRows() != 1) {
          $this->params['warning'][$Object->getId()] = $this->params['error_class'];
          $this->params['warning_messages'][$Object->getId()] = $this->getElement(5);
        }
      }
    }
  }

  function getDescription()
  {
    return 'existintable -> prüft ob vorhanden, beispiel: validate|existintable|label|tablename|feldname|warning_message';
  }
}
