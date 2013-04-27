<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_select_sql extends rex_xform_abstract
{

  static $getListValues = array();

  function enterObject()
  {
    $multiple = (int) $this->getElement(8);
    if ($multiple != 1) {
      $multiple = 0;
    }

    $size = (int) $this->getElement(9);
    if ($size < 1) {
      $size = 1;
    }

    $SEL = new rex_select();
    $SEL->setId($this->getHTMLId() . '-s');

    if ($multiple) {
      $SEL->setName($this->getFieldName() . '[]');
      $SEL->setMultiple();
      $SEL->setSize($size);
    } else {
      $SEL->setName($this->getFieldName());
      $SEL->setSize(1);
    }


    $sql = $this->getElement(3);

    $teams = rex_sql::factory();
    $teams->debugsql = $this->params['debug'];
    $teams->setQuery($sql);

    $sqlnames = array();

    if (!$multiple && $this->getElement(6) == 1) {
      $SEL->addOption($this->getElement(7), '0');
    }

    foreach ($teams->getArray() as $t) {
      $v = $t['name'];
      $k = $t['id'];
      $SEL->addOption($this->getLabelStyle($v), $k);
      $sqlnames[$k] = $t['name'];
    }

    $wc = '';
    if (isset($this->params['warning'][$this->getId()])) {
      $wc = $this->params['warning'][$this->getId()];
    }

    $SEL->setStyle(' class="select ' . $wc . '"');

    if ($this->getValue() == '' && $this->getElement(4) != '') {
      $this->setValue($this->getElement(4));
    }

    if (!is_array($this->getValue())) {
      $this->setValue(explode(',', $this->getValue()));
    }

    foreach ($this->getValue() as $v) {
      $SEL->setSelected($v);
    }

    $form_class = '';
    if ($multiple) {
      $form_class = ' formselect-multiple-' . $size;
    }

    $this->params['form_output'][$this->getId()] = '
      <p class="formselect' . $form_class . '"  id="' . $this->getHTMLId() . '">
        <label class="select ' . $wc . '" for="' . $this->getHTMLId() . '-s" >' . $this->getLabel() . '</label>
        ' . $SEL->get() . '
      </p>';

    $this->setValue(implode(',', $this->getValue()));
    $this->params['value_pool']['email'][$this->getName()] = stripslashes($this->getValue());
    if ($this->getElement(5) != 'no_db') {
      $this->params['value_pool']['sql'][$this->getName()] = $this->getValue();
    }

  }


  function getDescription()
  {
    return 'select_sql -> Beispiel: select_sql|name|label|select id,name from table order by name|[defaultvalue]|[no_db]|1/0 Leeroption|Leeroptionstext|1/0 Multiple Feld';
  }


  function getDefinitions()
  {
    return array(
      'type' => 'value',
      'name' => 'select_sql',
      'values' => array(
        array( 'type' => 'name',    'label' => 'Name' ),
        array( 'type' => 'text',    'label' => 'Bezeichnung'),
        array( 'type' => 'text',    'label' => 'Query mit "select id, name from .."'),
        array( 'type' => 'text',    'label' => 'Defaultwert (opt.)'),
        array( 'type' => 'no_db',   'label' => 'Datenbank',  'default' => 0),
        array( 'type' => 'boolean', 'label' => 'Leeroption'),
        array( 'type' => 'text',    'label' => 'Text bei Leeroption (Bitte auswählen)'),
        array( 'type' => 'boolean', 'label' => 'Mehrere Felder möglich'),
        array( 'type' => 'text',    'label' => 'Höhe der Auswahlbox'),
      ),
      'description' => 'Hiermit kann man SQL Abfragen als Selectbox nutzen',
      'dbtype' => 'text'
    );
  }


  static function getListValue($params)
  {
    $return = array();

    $db = rex_sql::factory();
    $db_array = $db->getDBArray($params['params']['field']['f3'] . ' WHERE FIND_IN_SET(`id`,"' . $params['value'] . '")');
    foreach ($db_array as $entry) {
      $return[] = $entry['name'];
    }

    return implode('<br />', $return);
  }

}
