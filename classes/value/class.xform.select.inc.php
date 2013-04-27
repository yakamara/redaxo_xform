<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_select extends rex_xform_abstract
{

  function enterObject()
  {

    $multiple = false;
    if ($this->getElement(6) == 1) {
      $multiple = true;
    }

    $SEL = new rex_select();
    $SEL->setId($this->getFieldId());

    $value_encoded = $this->getElement(3);
    $value_encoded = $this->encodeChars(',', $value_encoded);

    $options = explode(',', $value_encoded);

    foreach ($options as $option_encoded) {

      $option = $this->encodeChars('=', $option_encoded);

      $t = explode('=', $option);
      $v = $t[0];

      if (isset($t[1])) {
        $k = $t[1];
      } else {
        $k = $t[0];
      }

      $v = $this->decodeChars(',', $v);
      $v = $this->decodeChars('=', $v);
      $k = $this->decodeChars(',', $k);
      $k = $this->decodeChars('=', $k);
      $t[0] = $this->decodeChars(',', $t[0]);
      $t[0] = $this->decodeChars('=', $t[0]);

      $SEL->addOption($this->getLabelStyle($v), $k);
      $sqlnames[$k] = $t[0];
    }

    if ($multiple) {
      $size = (int) $this->getElement(7);
      if ($size < 2)
        $size = count($fields);

      $SEL->setName($this->getFieldName() . '[]');
      $SEL->setSize($size);
      $SEL->setMultiple(1);
    } else {
      $SEL->setName($this->getFieldName());
      $SEL->setSize(1);
    }


    if (!$this->params['send'] && $this->getValue() == '' && $this->getElement(5) != '') {
      $this->setValue($this->getElement(5));
    }

    if (!is_array($this->getValue())) {
      $this->setValue(explode(',', $this->getValue()));
    }

    foreach ($this->getValue() as $v) {
      $SEL->setSelected($v);
    }

    $this->setValue(implode(',', $this->getValue()));

    $wc = '';
    if (isset($this->params['warning'][$this->getId()])) {
      $wc = $this->params['warning'][$this->getId()];
    }

    $SEL->setStyle(' class="select ' . $wc . '"');

    $this->params['form_output'][$this->getId()] = '
      <p class="formselect ' . $this->getHTMLClass() . '" id="' . $this->getHTMLId() . '">
      <label class="select ' . $wc . '" for="' . $this->getFieldId() . '" >' . $this->getLabel() . '</label>' .
    $SEL->get() .
      '</p>';

    $this->params['value_pool']['email'][$this->getName()] = $this->getValue();
    if ($this->getElement(4) != 'no_db') $this->params['value_pool']['sql'][$this->getName()] = $this->getValue();

  }

  function getDescription()
  {
    return 'select -> Beispiel: select|name|label|Frau=w,Herr=m|[no_db]|defaultwert|multiple=1|selectsize';
  }

  function getDefinitions()
  {
    return array(
      'type' => 'value',
      'name' => 'select',
      'values' => array(
        array( 'type' => 'name',   'label' => 'Feld' ),
        array( 'type' => 'text',    'label' => 'Bezeichnung'),
        array( 'type' => 'text',    'label' => 'Selectdefinition, kommasepariert',   'example' => 'w=Frau,m=Herr'),
        array( 'type' => 'no_db',   'label' => 'Datenbank',          'default' => 0),
        array( 'type' => 'text',    'label' => 'Defaultwert'),
        array( 'type' => 'boolean', 'label' => 'Mehrere Felder möglich'),
        array( 'type' => 'text',    'label' => 'Höhe der Auswahlbox'),
      ),
      'description' => 'Ein Selectfeld mit festen Definitionen',
      'dbtype' => 'text'
    );

  }

  static function getListValue($params)
  {
    $return = array();

    $values = array();
    foreach (explode(',', $params['params']['field']['f3']) as $v) {
      $entry = explode('=', $v);
      if (isset($entry[1]))
        $values[$entry[1]] = rex_translate($entry[0]); // .' ['.$entry[1].']';
      else
        $values[$entry[0]] = rex_translate($entry[0]); // .' ['.$entry[0].']';
    }

    foreach (explode(',', $params['value']) as $k)
      if (isset($values[$k]))
        $return[] = $values[$k];

    return implode('<br />', $return);
  }

}
