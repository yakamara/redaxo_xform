<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_password extends rex_xform_abstract
{

  function enterObject()
  {
    if ($this->getValue() == '' && !$this->params['send']) {
      $this->setValue($this->getElement(3));
    }

    $wc = '';
    if (isset($this->params['warning'][$this->getId()])) {
      $wc = $this->params['warning'][$this->getId()];
    }

    $this->params['form_output'][$this->getId()] = '
        <p class="formpassword formlabel-' . $this->getName() . '" id="' . $this->getHTMLId() . '">
          <label class="password ' . $wc . '" for="' . $this->getFieldId() . '" >' . $this->getLabel() . '</label>
          <input type="password" class="password ' . $wc . '" name="' . $this->getFieldName() . '" id="' . $this->getFieldId() . '" value="" />
        </p>';

    $this->params['value_pool']['email'][$this->getName()] = stripslashes($this->getValue());
    if ($this->getElement(4) != 'no_db') $this->params['value_pool']['sql'][$this->getName()] = $this->getValue();
  }

  function getDescription()
  {
    return 'password -> Beispiel: password|name|label|default_value|[no_db]';
  }
}
