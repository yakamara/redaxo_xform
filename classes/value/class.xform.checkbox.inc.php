<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_checkbox extends rex_xform_abstract
{
  function enterObject()
    {

      ## set default value attribute
      if ($this->getElement(3) == '') {
        $v = 1; // gecheckt
        $w = 0; // nicht gecheckt

      } else {
        $values = explode(',', $this->getElement(3));

        if (count($values) == 1) {
          $v = $values[0];
          $w = '';

        } else {
          $v = $values[1];
          $w = $values[0];

        }

      }

      // first time and default is true -> checked
      if ($this->params['send'] != 1 && $this->getElement(4) == 1 && $this->getValue() === '') {
        $checked = ' checked="checked"';
        $this->setValue($v);

      // if check value is fiven -> checked
      } elseif ($this->getValue() == $v) {
        $checked = ' checked="checked"';
        $this->setValue($v);

    // not checked
    } else {
        $checked = '';
        $this->setValue($w);

      }

      $wc = '';
      if (isset($this->params['warning'][$this->getId()]))
        $wc = $this->params['warning'][$this->getId()];

      $this->params['form_output'][$this->getId()] = '
      <p class="formcheckbox formlabel-' . $this->getName() . '" id="' . $this->getHTMLId() . '">
        <input type="checkbox" class="checkbox ' . $wc . '" name="' . $this->getFieldName() . '" id="' . $this->getFieldId() . '" value="' . $v . '" ' . $checked . ' />
        <label class="checkbox ' . $wc . '" for="' . $this->getFieldId() . '" >' . $this->getLabel() . '</label>
      </p>';

      ## set values
      $this->params['value_pool']['email'][$this->getName()] = stripslashes($this->getValue());
      if ($this->getElement(5) != 'no_db')
        $this->params['value_pool']['sql'][$this->getName()] = $this->getValue();
    }

  function getDescription()
  {
  return 'checkbox -> Beispiel: checkbox|name|label|Values(0,1)|default clicked (0/1)|[no_db]';
  }

  function getDefinitions()
  {
    return array(
      'type' => 'value',
      'name' => 'checkbox',
      'values' => array(
        array( 'type' => 'name', 'label' => 'Name' ),
        array( 'type' => 'text', 'label' => 'Bezeichnung'),
        array( 'type' => 'text', 'label' => 'Werte (0,1) (nicht angeklickt,angeklickt)', 'default' => '0,1'),
        array( 'type' => 'boolean', 'label' => 'Defaultstatus', 'default' => 0),
        array( 'type' => 'no_db', 'label' => 'Datenbank', 'default' => 0),
      ),
      'description' => 'Eine Checkbox mit fester Definition.',
      'dbtype' => 'varchar(255)',
      'famous' => true
    );
  }
}
