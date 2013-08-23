<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_be_manager_relation extends rex_xform_abstract
{

  static $xform_list_values = array();

  function enterObject()
  {
    global $REX, $I18N;

    // ---------- CONFIG & CHECK

    $this->be_em = array();
    $this->be_em['source_table'] = $this->params['main_table']; // "rex_em_data_" wegcutten
    $this->be_em['label'] = $this->getElement(2);  // HTML Bezeichnung

    $this->be_em['target_table'] = $this->getElement(3); // Zieltabelle
    $this->be_em['target_field'] = $this->getElement(4); // Zielfield welches angezeigt wird.

    $this->be_em['relation_type'] = (int) $this->getElement(5); // select single = 0 / select multiple = 1 / popup single = 2 / popup multiple = 3
    if ($this->be_em['relation_type'] > 4) {
      $this->be_em['relation_type'] = 0;
    }

    $this->be_em['eoption'] = (int) $this->getElement(6); // "Leer" Option

    $this->be_em['page'] = $this->getElement(8); // page=editme
    if ($this->be_em['page'] == '') {
      $this->be_em['page'] = 'editme';
    }

    $this->be_em['subpage'] = $this->getElement(9); // page=editme
    if ($this->be_em['subpage'] == '') {
      $this->be_em['subpage'] = $this->be_em['target_table'];
    }

    if ($this->be_em['eoption'] != 1) {
      $this->be_em['eoption'] = 0;
    }
    $disabled = false;

    // ---------- Datensatz existiert bereits, Values aus verkn�pfungstabelle holen
    if ($this->params['main_id'] > 0 && $this->params['send'] == 0) {
      $values = array();
      if (trim($this->getValue()) != '') {
        $values = explode(',', $this->getValue());

      } else {
        $vs = rex_sql::factory();
        $vs->debugsql = $this->params['debug'];
        $vs->setQuery('
          select
            target_id as id
          from
            '.$REX['TABLE_PREFIX'].'xform_relation
          where
            source_table="' . $this->be_em['source_table'] . '" and
            source_name="' . $this->getName() . '" and
            source_id="' . $this->params['main_id'] . '"');
        $v = $vs->getArray();
        if (count($v) > 0)
          foreach ($v as $w) {
            $values[$w['id']] = $w['id'];
          };

      }
      $this->setValue($values);
      // echo '<pre>++ ';var_dump($this->getValue());echo '</pre>';
    }


    // ---------- connected, fix values
    if (isset($this->params['rex_xform_set'][$this->getName()])) {

      $values = $this->getValue();
      $values[] = $this->params['rex_xform_set'][$this->getName()];
      $this->setValue($values);
      $disabled = true;
    }


    // ---------- Value angleichen -> immer Array mit IDs daraus machen
    if (!is_array($this->getValue())) {

      if (trim($this->getValue()) == '') {
        $this->setValue(array());
      } else {
        $this->setValue(explode(',', $this->getValue()));
      }
    }

    // ---------- (array) $this->getValue()
    // echo '<hr /><pre>'; var_dump($this->getValue()); echo '</pre>';


    // ---------- check values
    $sql = 'select id,' . mysql_real_escape_string($this->be_em['target_field']) . ' from ' . $this->be_em['target_table'];
    $value_names = array();
    $value_name = '';
    $values = array();
    if (count($this->getValue()) > 0) {
      $add_sql = array();
      foreach ($this->getValue() as $v) {
        $add_sql[] = ' id=' . intval($v) . '';
      }
      if (count($add_sql) > 0) {
        $sql .= ' where ' . implode(' OR ', $add_sql);
      }

      $vs = rex_sql::factory();
      $vs->debugsql = $this->params['debug'];
      $vs->setQuery($sql);
      foreach ($vs->getArray() as $v) {
        $value_names[$v['id']] = $v[$this->be_em['target_field']] . ' [id=' . $v['id'] . ']';
        $value_name = $v[$this->be_em['target_field']];
      }
      foreach ($this->getValue() as $v) {
        if (isset($value_names[$v]))
          $values[] = $v;
      }

      $this->setValue($values);
    }

    // ---------- (array) $this->getValue()
    // echo '<pre>'; var_dump($this->getValue()); echo '</pre>';


    // ---------- empty option ?

    if ($this->params['send'] == 1 && $this->be_em['eoption'] == 0 && count($this->getValue()) == 0) {
      // Error. Fehlermeldung ausgeben
      $this->params['warning'][] = $this->getElement(7);
      $this->params['warning_messages'][] = $this->getElement(7);
      $wc = $this->params['error_class'];
    }

    $wc = '';
    if (isset($this->params['warning'][$this->getId()])) {
      $wc = $this->params['warning'][$this->getId()];
    }

    // --------------------------------------- Selectbox, single 0 or multiple 1

    if ($this->be_em['relation_type'] < 2) {

      // ----- SELECT BOX
      $sss = rex_sql::factory();
      $sss->debugsql = $this->params['debug'];
      $sss->setQuery('select * from `' . mysql_real_escape_string($this->be_em['target_table']) . '` order by `' . mysql_real_escape_string($this->be_em['target_field']) . '`');

      $SEL = new rex_select();
      $SEL->setId($this->getFieldId());
      $SEL->setStyle('class="select"');

      $SEL->setDisabled($disabled);
      $SEL->setSize(1);

      // mit --- keine auswahl ---

      $SEL->setName($this->getFieldName());

      if ($this->be_em['relation_type'] == 1) {
        $SEL->setName($this->getFieldName() . '[]');
        $SEL->setMultiple(true);
        $SEL->setSize(5);

      } elseif ($this->be_em['eoption'] == 1) {
        $SEL->addOption('-', '');

      }

      foreach ($sss->getArray() as $v) {
        $s = $v[$this->be_em['target_field']];
        if (strlen($s) > 50) $s = substr($s, 0, 45) . ' ... ';
        $s = $s . ' [id=' . $v['id'] . ']';
        $SEL->addOption($s, $v['id']);
      }

      $SEL->setSelected($this->getValue());

      $this->params['form_output'][$this->getId()] = '
          <p class="' . $this->getHTMLClass() . ' formlabel-' . $this->getName() . '" id="' . $this->getHTMLId() . '">
            <label class="select ' . $wc . '" for="' . $this->getFieldId() . '" >' . $this->getLabelStyle($this->be_em['label']) . '</label>
            ' . $SEL->get() . '
          </p>';

    }


    // ------------------------------------ POPUP, single, multiple 1-1, n-m

    if ($this->be_em['relation_type'] == 2 || $this->be_em['relation_type'] == 3) {

      $multiple = '0';
      if ($this->be_em['relation_type'] == 3) {
        $multiple = '1';
      }

      $link = 'index.php?page=xform&subpage=manager&tripage=data_edit&table_name=' . $this->be_em['target_table'];
      if ($multiple) {
        $out = '
        <div class="rex-widget">
            <div class="rex-widget-xform-manager-datalist">
              <input type="hidden" name="' . $this->getFieldName() . '" id="XFORM_MANAGER_DATALIST_' . $this->getId() . '" value="' . implode(',', $this->getValue()) . '" />
              <p class="rex-widget-field">
                <select name="XFORM_MANAGER_DATALIST_SELECT[' . $this->getId() . ']" id="XFORM_MANAGER_DATALIST_SELECT_' . $this->getId() . '" size="8">';
        foreach ($this->getValue() as $k) {
          $out .= '<option value="' . $k . '">' . $value_names[$k] . '</option>';
        }
        $out .= '
                </select>
              </p>
               <p class="rex-widget-icons rex-widget-2col">
                <span class="rex-widget-column rex-widget-column-first">
                  <a href="#" class="rex-icon-file-top" onclick="xform_manager_moveDatalist(' . $this->getId() . ',\'top\');return false;" title="' . $I18N->msg('xform_relation_move_first_data') . '"></a>
                  <a href="#" class="rex-icon-file-up" onclick="xform_manager_moveDatalist(' . $this->getId() . ',\'up\');return false;" title="' . $I18N->msg('xform_relation_move_up_data') . '"></a>
                  <a href="#" class="rex-icon-file-down" onclick="xform_manager_moveDatalist(' . $this->getId() . ',\'down\');return false;" title="' . $I18N->msg('xform_relation_down_first_data') . '"></a>
                  <a href="#" class="rex-icon-file-bottom" onclick="xform_manager_moveDatalist(' . $this->getId() . ',\'bottom\');return false;" title="' . $I18N->msg('xform_relation_move_last_data') . '"></a>
                </span>
                <span class="rex-widget-column">
                  <a href="#" class="rex-icon-file-open" onclick="xform_manager_openDatalist(' . $this->getId() . ', \'' . $this->be_em['target_field'] . '\', \'' . $link . '\',\'' . $multiple . '\');return false;" title="' . $I18N->msg('xform_relation_choose_entry') . '"></a>
                  <a href="#" class="rex-icon-file-delete" onclick="xform_manager_deleteDatalist(' . $this->getId() . ',\'' . $multiple . '\');return false;" title="' . $I18N->msg('xform_relation_delete_entry') . '"></a>
                </span>
              </p>
            </div>
          </div>
        <div class="rex-clearer"></div>
        ';

      } else {
        $out = '

        <div class="rex-widget">
          <div class="rex-widget-data">

          <p class="rex-widget-field">
          <input type="hidden" name="' . $this->getFieldName() . '" id="XFORM_MANAGER_DATA_' . $this->getId() . '" value="' . implode(',', $this->getValue()) . '" />
          <input type="text" size="30" name="XFORM_MANAGER_DATANAME[' . $this->getId() . ']" value="' . htmlspecialchars($value_name) . '" id="XFORM_MANAGER_DATANAME_' . $this->getId() . '" readonly="readonly" class="text" />
          </p>
          <p class="rex-widget-icons rex-widget-1col">
          <span class="rex-widget-column rex-widget-column-first">
            <a href="#" class="rex-icon-file-open" onclick="xform_manager_openDatalist(' . $this->getId() . ', \'' . $this->be_em['target_field'] . '\', \'' . $link . '\',\'' . $multiple . '\');return false;" title="' . $I18N->msg('xform_relation_choose_entry') . '"></a>
            <a href="#" class="rex-icon-file-delete" onclick="xform_manager_deleteDatalist(' . $this->getId() . ',\'' . $multiple . '\');return false;" title="' . $I18N->msg('xform_relation_delete_entry') . '"></a>
          </span>
          </p>
          </div>
        </div>
        <div class="rex-clearer"></div>
        ';

      }

      $this->params['form_output'][$this->getId()] = '
        <div class="xform-element ' . $this->getHTMLClass() . ' formlabel-' . $this->getName() . '" id="' . $this->getHTMLId() . '">
          <label class="select ' . $wc . '" for="' . $this->getFieldId() . '" >' . $this->getLabelStyle($this->be_em['label']) . '</label>
          ' . $out . '
        </div>';
    }


    // --------------------------------------- POPUP, 1-n

    if ($this->be_em['relation_type'] == 4) {

      $text = 'not yet implemented';

      // TODO

      /*
       if($this->params["main_id"] < 1)
       {
       $text = 'Diesen Bereich k&ouml;nnen Sie erst bearbeiten, wenn der Datensatz angelegt wurde.';
       }else
       {

       $link = 'javascript:rex_xform_openRelation('.$this->getId().',\''.$this->be_em["target_table"].'\',\'id'.
       '&rex_em_filter['.$this->be_em["target_field"].']='.$this->params["main_id"].
       '&rex_em_set['.$this->be_em["target_field"].']='.$this->params["main_id"].
       '&page='.$this->be_em["page"].
       '&subpage='.$this->be_em["subpage"].
       '\');';

       $text = '<a href="'.$link.'">'.
       'Link'.
       '</a>';
       }
       */

      /*
      $this->params["form_output"][$this->getId()] = '
      <p class="formhtml '.$this->getHTMLClass().'" id="'.$this->getHTMLId().'">
      <label class="select " for="' . $this->getFieldId() . '" >' . rex_translate($this->be_em["label"]) . '</label>
      <input type="hidden" name="'.$this->getFieldName().'[]" id="REX_RELATION_'.$this->getId().'" />
      <span>'.$text.'</span>
      </p>';
      */

      return;
    }





    // --------------------------------------- save

    $this->params['value_pool']['email'][$this->getName()] = stripslashes(implode(',', $this->getValue()));
    $this->params['value_pool']['sql'][$this->getName()] = implode(',', $this->getValue());

  }




  // -------------------------------------------------------------------------

  /*
   * postAction wird nach dem Speichern ausgef�hrt
   * hier wird entsprechend der entities
   */
  function postAction()
  {
    global $REX;

    return;

    // $this->params["debug"] = TRUE;

    $source_id = -1;
    if (isset($this->params['value_pool']['email']['ID']) && $this->params['value_pool']['email']['ID'] > 0) {
      $source_id = (int) $this->params['value_pool']['email']['ID'];
    }
    if ($source_id < 1 && isset($this->params['main_id']) && $this->params['main_id'] > 0) {
      $source_id = (int) $this->params['main_id'];
    }

    if ($source_id < 1 || $this->params['main_table'] == '') {
      return false;
    }

    // ----- Value angleichen -> immer Array mit IDs daraus machen
    $values = array();
    if (!is_array($this->getValue())) {
      if (trim($this->getValue()) != '') {
        $values = explode(',', $this->getValue());
      }
    } else {
      $values = $this->getValue();
    }

    $d = rex_sql::factory();
    $d->debugsql = $this->params['debug'];
    $d->setQuery('delete from '.$REX['TABLE_PREFIX'].'xform_relation where source_table="' . $this->be_em['source_table'] . '" and source_name="' . $this->getName() . '" and source_id="' . $source_id . '"');

    if (count($values) > 0) {
      $i = rex_sql::factory();
      $i->debugsql = $this->params['debug'];
      foreach ($values as $v) {
        $i->setTable($REX['TABLE_PREFIX'].'xform_relation');
        $i->setValue('source_table', $this->be_em['source_table']);
        $i->setValue('source_name', $this->getName());
        $i->setValue('source_id', $source_id);
        $i->setValue('target_table', $this->be_em['target_table']);
        $i->setValue('target_id', $v);
        $i->insert();
      }

    }

  }

  // -------------------------------------------------------------------------


  /*
   * Allgemeine Beschreibung
   */
  function getDescription()
  {
    // label,bezeichnung,tabelle,tabelle.feld,relationstype,style,no_db
    // return "be_em_relation -> Beispiel: ";
    return '';
  }

  function getDefinitions()
  {
    return array(
      'type' => 'value',
      'name' => 'be_manager_relation',
      'values' => array(
        array( 'type' => 'name',    'label' => 'Name' ),
        array( 'type' => 'text',    'label' => 'Bezeichnung'),
        array( 'type' => 'table',    'label' => 'Ziel Tabelle'),
        array( 'type' => 'text',  'label' => 'Ziel Tabellenfeld zur Anzeige oder Zielfeld'),
        array( 'type' => 'select',      'label' => 'Mehrfachauswahl', 'default' => '', 'definition' => 'select (single)=0,select (multiple)=1,popup (single)=2,popup (multiple)=3' ), // ,popup (multiple / relation)=4
        array( 'type' => 'boolean',    'label' => 'Mit "Leer-Option"' ),
        array( 'type' => 'text',    'label' => 'Fehlermeldung wenn "Leer-Option" nicht aktiviert ist.'),
        array( 'type' => 'text',    'label' => 'REX Page (opt)'),
        array( 'type' => 'text',    'label' => 'REX Subpage (opt)'),
      ),
      'description' => 'Hiermit kann man Verkn&uuml;pfungen zu anderen Tabellen setzen',
      'dbtype' => 'text'
    );
  }

  static function getListValue($params)
  {

    if (!isset(self::$xform_list_values[$params['params']['field']['f3']]) || count(self::$xform_list_values[$params['params']['field']['f3']]) == 0) {
      self::$xform_list_values[$params['params']['field']['f3']] = array();
      $db = rex_sql::factory();
      $db_array = $db->getDBArray('select id, `' . $params['params']['field']['f4'] . '` as name from ' . $params['params']['field']['f3'] . '');
      foreach ($db_array as $entry) {
        self::$xform_list_values[$params['params']['field']['f3']][$entry['id']] = $entry['name'];
      }
    }

    $return = array();
    foreach (explode(',', $params['value']) as $value) {
      if (isset(self::$xform_list_values[$params['params']['field']['f3']][$value])) {
        $return[] = self::$xform_list_values[$params['params']['field']['f3']][$value];
      }
    }

    return implode('<br />', $return);
  }

}
