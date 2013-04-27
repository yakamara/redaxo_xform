<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_validate_compare_value extends rex_xform_validate_abstract
{

  function enterObject()
  {
    if ($this->params['send'] == '1') {
      if (is_array($this->obj_array)) {
        foreach ($this->obj_array as $o) {
          if ($o->getName() == $this->getElement(2)) {
            $value = $o->getValue();
            if (strtolower($value) != strtolower($this->getElement(3))) {
              $this->params['warning'][$o->getId()] = $this->params['error_class'];
              $this->params['warning_messages'][$o->getId()] = $this->getElement(4);
            }
          }
        }
      }
    }

  }

  function getDescription()
  {
    return 'compare_value -> compare label with value, example: validate|compare_value|label|value|warning_message ';
  }
}
