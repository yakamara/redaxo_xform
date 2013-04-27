<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_textarea extends rex_xform_abstract
{

  function enterObject()
  {

    $this->setValue((string) $this->getValue());

    if ($this->getValue() == '' && !$this->params['send']) {
      $this->setValue($this->getElement(3));
    }

    $classes = ' ' . $this->getElement(5);

    $wc = '';
    if (isset($this->params['warning'][$this->getId()])) {
      $wc = ' ' . $this->params['warning'][$this->getId()];
    }

    $this->params['form_output'][$this->getId()] = '
    <p class="formtextarea" id="' . $this->getHTMLId() . '">
      <label class="textarea ' . $wc . '" for="' . $this->getFieldId() . '" >' . $this->getLabel() . '</label>
      <textarea class="textarea' . $classes . $wc . '" name="' . $this->getFieldName() . '" id="' . $this->getFieldId() . '" cols="80" rows="10">' . htmlspecialchars(stripslashes($this->getValue())) . '</textarea>
    </p>';

    $this->params['value_pool']['email'][$this->getName()] = stripslashes($this->getValue());
    if ($this->getElement(4) != 'no_db') {
      $this->params['value_pool']['sql'][$this->getName()] = $this->getValue();
    }
  }

  function getDescription()
  {
    return 'textarea -> Beispiel: textarea|name|label|default|[no_db]';
  }

  function getDefinitions()
  {
    return array(
      'type' => 'value',
      'name' => 'textarea',
      'values' => array(
        array( 'type' => 'name',   'label' => 'Feld' ),
        array( 'type' => 'text',    'label' => 'Bezeichnung'),
        array( 'type' => 'text',    'label' => 'Defaultwert'),
        array( 'type' => 'no_db',   'label' => 'Datenbank',  'default' => 0),
        array( 'type' => 'text',    'label' => 'classes'),
      ),
      'description' => 'Ein mehrzeiliges Textfeld als Eingabe',
      'dbtype' => 'text',
      'famous' => true
    );
  }
}
