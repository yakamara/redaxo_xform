<?php


/*
 * TODO:
 *
 * Suche nach id noch anpassen
 * Suche mit diversen krietrien ermöglich
 * z.B. selectfeld (genaue suche. like suche. etc.. siehe phpmyadmin)
 * dadurch auch rex_xform_searchvars mit unterarray erlauben
 * getSearchVars entsprechend umbauen
 *
 *
 * text/textarea / s: text / einfach text suche: suchtext / auch mit * -> % und (empty) oder !(empty)
  // o: select, sqlselect / s: multiselect mit checkbox: ist vorhanden, oder ist nicht vorhanden find in set
  // o: relation / s: lookup mit übernehmen.
  andere felder fehlen noch..
  select und co
 */


class rex_xform_manager_search
{
    private $linkVars = array();
    private $scriptPath = '';

    /** @type rex_xform_manager_table */
    protected $table = null;

    public function rex_xform_manager_search(rex_xform_manager_table $table)
    {
        $this->table = $table;
        $this->setScriptPath($_SERVER['PHP_SELF']);
    }

    public function setLinkVar($k, $v)
    {
        $this->linkVars[$k] = $v;
    }

    public function setLinkVars($vars)
    {
       $this->linkVars = array_merge($this->linkVars, $vars);
    }

    public function setScriptPath($scriptpath)
    {
       $this->scriptPath = $scriptpath;
    }

    function getForm()
    {
        global $I18N;

        if (!$this->table->isSearchable()) {
            return '';
        }

        $xform = new rex_xform_manager_searchform();
        $xform->setObjectparams('form_skin', 'default');
        $xform->setObjectparams('form_showformafterupdate', 1);
        $xform->setObjectparams('real_field_names', true);
        $xform->setObjectparams('form_action', $this->scriptPath);
        $xform->setObjectparams('form_method', 'get');

        foreach ($this->linkVars as $k => $v) {
            $xform->setHiddenField($k, $v);
        }

        // $xform->setValueField('text', array('label' => 'ID', 'name' => 'id'));

        foreach ($this->table->getFields() as $field) {

            if ($field->getTypeName() && $field->getType() == 'value' && $field->isSearchable()) {
                rex_xform::includeClass($field->getType(), $field->getTypeName());
                if (method_exists('rex_xform_' . $field->getTypeName(), 'getSearchField')) {
                    call_user_func('rex_xform_' . $field->getTypeName() . '::getSearchField', array(
                        'searchForm' => $xform,
                        'searchObject' => $this,
                        'field' => $field,
                        'fields' => $this->table->getFields()
                    ));
                }
            }

        }
        $xform->setValueField('submit', array('xform_search_submit', $I18N->msg('xform_search')));

        return $xform->getForm();
    }


    function getSearchVars()
    {
        $rex_xform_searchvars = rex_request('rex_xform_searchvars', 'array');
        unset($rex_xform_searchvars['send']);
        unset($rex_xform_searchvars['xform_search_submit']);
        foreach ($rex_xform_searchvars as $k => $v) {
            if ($v == '') {
                unset($rex_xform_searchvars[$k]);
            }
        }
        return array('rex_xform_searchvars' => $rex_xform_searchvars);
    }


    function getQueryFilterArray()
    {
        if (!$this->table->isSearchable()) {
            return array();
        }

        $queryFilter = array();
        $vars = $this->getSearchVars();

        foreach ($this->table->getFields() as $field) {

            if (array_key_exists($field->getName(), $vars['rex_xform_searchvars']) && $field->getType() == 'value' && $field->isSearchable()) {
                rex_xform::includeClass($field->getType(), $field->getTypeName());
                if (method_exists('rex_xform_' . $field->getTypeName(), 'getSearchFilter')) {
                    $qf = call_user_func('rex_xform_' . $field->getTypeName() . '::getSearchFilter',
                        array(
                            'field' => $field,
                            'fields' => $this->table->getFields(),
                            'value' => $vars['rex_xform_searchvars'][$field->getName()]
                        )
                    );
                    if ($qf != '') {
                        $queryFilter[] = $qf;
                    }
                }
            }

        }

        return $queryFilter;
    }


}
