<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_resetbutton extends rex_xform_abstract
{

  function enterObject()
  {
    $this->setValue($this->getElement(3));

    $wc = '';
    if ($this->getElement(4) != '') {
      $wc = $this->getElement(4);
    }

    $this->params['form_output'][$this->getId()] = '
        <p class="formsubmit ' . $this->getHTMLClass() . '" id="' . $this->getHTMLId() . '">
          <label class="text ' . $wc . '" for="' . $this->getFieldId() . '" >' . $this->getLabel() . '</label>
          <input type="reset" class="submit ' . $wc . '" id="' . $this->getFieldId() . '" value="' . htmlspecialchars(stripslashes($this->getValue())) . '" />
        </p>';

  }

  function getDescription()
  {
    return 'resetbutton -> Beispiel: resetbutton|name|label|value|cssclassname';
  }
}
