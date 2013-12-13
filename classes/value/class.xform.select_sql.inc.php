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
    if($multiple != 1) {
      $multiple = 0;
    }

    // ----- query
    $sql = $this->getElement(3);

    $options_sql = rex_sql::factory();
    $options_sql->debugsql = $this->params['debug'];
    $options_sql->setQuery($sql);

    $options = array();
    $option_names = array();
    foreach($options_sql->getArray() as $t) {
      $v = $t['name'];
      $k = $t['id'];
      $options[$k] = $v;
      $option_names[$k] = $t['name'];

    }

    // ----- default value
    if ($this->getValue()=='' && $this->getElement(4) != '') {
      $this->setValue($this->getElement(4));

    }

    // ----- build select
    $select = new rex_select();
    $select->setId($this->getHTMLId().'-s');

    if($multiple) {

      $size = (int) $this->getElement(9);
      if($size < 1 || $multiple == 0) {
        $size = 1;
      }

      $select->setName($this->getFieldName().'[]');
      $select->setMultiple();
      $select->setSize($size);

      foreach($options as $k => $v) {
        $select->addOption($v, $k);

      }

      $form_class = 'formselect formselect-multiple-'.$size;

      if (!is_array($this->getValue())) {
        $this->setValue(explode(',', stripslashes($this->getValue())));

      }

      foreach ($this->getValue() as $v) {
        $select->setSelected($v);

      }

      $this->setValue(implode(',',$this->getValue()));


    } else {

      $select->setName($this->getFieldName());
      $select->setSize(1);

      // mit --- keine auswahl ---
      if ($this->getElement(6) == 1) {
        $select->addOption($this->getElement(7), '0');

      }

      foreach($options as $k => $v) {
        $select->addOption($v, $k);

      }

      $select->setSelected( stripslashes($this->getValue()));

      $form_class = 'formselect';
    }


    $wc = '';
    if (isset($this->params['warning'][$this->getId()])) {
      $wc = $this->params['warning'][$this->getId()];
    }
    $select->setStyle(' class="select ' . $wc . '"');

    $this->params["form_output"][$this->getId()] = '
      <p class="formselect'.$form_class.'"  id="'.$this->getHTMLId().'">
        <label class="select ' . $wc . '" for="' . $this->getHTMLId() . '-s" >' . rex_translate($this->getElement(2)) . '</label>
        ' . $select->get() . '
      </p>';

    $this->params['value_pool']['email'][$this->getElement(1)] = stripslashes($this->getValue());
    if ($this->getElement(5) != 'no_db') {
      $this->params['value_pool']['sql'][$this->getElement(1)] = $this->getValue();

    }

  }


  function getDescription()
  {
    return 'select_sql -> Beispiel: select_sql|label|Bezeichnung:|select id,name from table order by name|[defaultvalue]|[no_db]|1/0 Leeroption|Leeroptionstext|1/0 Multiple Feld';
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
        array( 'type' => 'text',    'label' => 'Höhe der Auswahlbox')

      ),
      'description' => 'Hiermit kann man SQL Abfragen als Selectbox nutzen',
      'dbtype' => 'text'
    );
  }


  static function getListValue($params)
  {
    $return = array();

    $query = $params['params']['field']['f3'];
    $pos = strrpos(strtoupper($query), "ORDER BY ");
    if ( $pos !== FALSE) {
      $query = substr($query, 0, $pos);
    }

    $pos = strrpos(strtoupper($query), "LIMIT ");
    if ( $pos !== FALSE) {
      $query = substr($query, 0, $pos);
    }

    $multiple = (int) $params['params']['field']['f8'];
    if($multiple != 1) {
      $where = ' `id`="'.mysql_real_escape_string($params['value']).'"';


    } else {
      $where = ' FIND_IN_SET(`id`,"'.mysql_real_escape_string($params['value']).'")';

    }

    $pos = strrpos(strtoupper($query), "WHERE ");
    if ( $pos !== FALSE) {
      $query = substr($query, 0, $pos).' WHERE '.$where.' AND '.substr($query, $pos + strlen("WHERE "));

    } else {
      $query .= ' WHERE '.$where;

    }

    $db = rex_sql::factory();
    // $db->debugsql = 1;
    $db_array = $db->getArray($query);

    foreach($db_array as $entry) {
      $return[] = $entry['name'];
    }


    if (count($return) == 0 && $params['value'] != "" && $params['value'] != "0") {
      $return[] = $params['value'];
    }

    return implode("<br />",$return);
  }

}