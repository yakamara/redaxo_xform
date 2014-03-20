<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_checkbox_sql extends rex_xform_abstract
{

  static $getListValues = array();

  function enterObject()
  {

    if (!is_array($this->getValue())) {
      $this->setValue(explode(',', stripslashes($this->getValue())));

    }

    $values = $this->getValue();


    // ----- query
    $sql = $this->getElement(3);

    $options_sql = rex_sql::factory();
    $options_sql->debugsql = $this->params['debug'];
    $options = array();
    foreach($options_sql->getArray($sql) as $option) {
        $options[$option["id"]] = $option["name"];
    }
    
    
    $proofed_values = array();
    foreach($values as $value) {
      if (array_key_exists($value, $options)) {
         $proofed_values[$value] = $value;
      }
    }

    $wc = "";

    // ----- build checkboxes

    $html_checkboxes = array();
    foreach($options as $k => $v) {
    
      $checked = "";
      if (in_array($k, $proofed_values)) {
        $checked = ' checked="checked"';
      }
    
      $html_checkboxes[] = '
      <p class="formcheckbox formlabel-' . $this->getName($k) . '" id="' . $this->getHTMLId($k) . '">
        <input type="checkbox" class="checkbox ' . $wc . '" name="' . $this->getFieldName() . '[]" id="' . $this->getFieldId($k) . '" value="' . $k . '" ' . $checked . ' />
        <label class="checkbox ' . $wc . '" for="' . $this->getFieldId($k) . '" >' . $v . '</label>
      </p>';
    
    }

    $this->params['form_output'][$this->getId()] = implode("\n", $html_checkboxes);
    $this->params['value_pool']['email'][$this->getName()] = implode(",",$this->getValue());
    if ($this->getElement("no_db") != 1) {
        $this->params['value_pool']['sql'][$this->getName()] = $this->getValue();
    }

    return;
    
  }


  function getDescription()
  {
    return 'checkbox_sql -> Beispiel: checkbox_sql|label|Bezeichnung:|select id,name from table order by name|';
  }


  function getDefinitions()
  {
    return array(
      'type' => 'value',
      'name' => 'checkbox_sql',
      'values' => array(
        array( 'type' => 'name',    'label' => 'Name' ),
        array( 'type' => 'text',    'label' => 'Bezeichnung'),
        array( 'type' => 'text',    'label' => 'Query mit "select id, name from .."')
      ),
      'description' => 'Hiermit kann man SQL Abfragen als Checkboxliste nutzen',
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