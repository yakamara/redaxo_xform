<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_showvalue extends rex_xform_abstract
{

  function enterObject()
  {

    if ($this->getValue() == '' && !$this->params['send']) {
      $this->setValue($this->getElement(3));
    }

    $this->params['form_output'][$this->getId()] = '
      <p class="formtext ' . $this->getHTMLClass() . '"  id="' . $this->getHTMLId() . '">
      <label class="text" for="' . $this->getFieldId() . '">' . $this->getLabel() . '</label>
      <input type="hidden" name="' . $this->getFieldName() . '" value="' . htmlspecialchars(stripslashes($this->getValue())) . '" />
      <input type="text" class="inp_disabled" disabled="disabled" id="' . $this->getFieldId() . '" value="' . htmlspecialchars(stripslashes($this->getValue())) . '" />
      </p>';

    $this->params['value_pool']['email'][$this->getName()] = stripslashes($this->getValue());

  }

  function getDescription()
  {
    return 'showvalue -> Beispiel: showvalue|name|label|defaultwert';
  }
}
