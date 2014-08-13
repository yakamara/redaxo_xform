<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_prio extends rex_xform_abstract
{

    function enterObject()
    {
        global $I18N;

        $options[1] = $I18N->msg('xform_prio_top');

        $sql = rex_sql::factory();
        $fields = $this->getElement('fields');
        if (!is_array($fields)) {
            $fields = array_filter(explode(',', $fields));
        }
        if (empty($fields)) {
            $fields = array('id');
        }
        $selectFields = array();
        foreach ($fields as $field) {
            $selectFields[] = '`' . $sql->escape($field) . '`';
        }
        $sql->setQuery(sprintf(
            'SELECT id, %s, `%s` as prio FROM `%s`%s ORDER BY `%2$s`',
            implode(', ', $selectFields),
            $sql->escape($this->getElement('name')),
            $sql->escape($this->params['main_table']),
            $this->getScopeWhere()
        ));
        $prio = 1;
        while ($sql->hasNext()) {
            if ($sql->getValue('id') != $this->params['main_id']) {
                $prio = $sql->getValue('prio') + 1;
                $label = array();
                foreach ($fields as $field) {
                    $label[] = $sql->getValue($field);
                }
                $options[$prio] = $I18N->msg('xform_prio_after', implode(' | ', $label));
            }
            $sql->next();
        }

        if (!$this->params['send'] && $this->getValue() == '') {
            if ($this->getElement('default') == '') {
                $this->setValue($prio);
            } else {
                $this->setValue($this->getElement('default'));
            }
        }

        if (!is_array($this->getValue())) {
            $this->setValue(explode(',', $this->getValue()));
        }

        $this->params['form_output'][$this->getId()] = $this->parse('value.select.tpl.php', array('options' => $options, 'multiple' => false, 'size' => 1));

        $this->setValue(implode(',', $this->getValue()));

        $this->params['value_pool']['email'][$this->getName()] = $this->getValue();
        $this->params['value_pool']['sql'][$this->getName()] = $this->getValue();
    }

    function getDescription()
    {
        return 'prio -> Beispiel: prio|name|label|fields|scope|defaultwert';
    }

    function getDefinitions()
    {
        return array(
            'type' => 'value',
            'name' => 'prio',
            'values' => array(
                'name'     => array( 'type' => 'name',         'label' => 'Feld' ),
                'label'    => array( 'type' => 'text',         'label' => 'Bezeichnung'),
                'fields'   => array( 'type' => 'select_names', 'label' => 'Tabellenfelder zur Anzeige' ),
                'scope'    => array( 'type' => 'select_names', 'label' => 'Tabellenfelder zur Beschränkung'),
                'default'  => array( 'type' => 'select', 'label' => 'Defaultwert', 'definition' => array(1 => 'Am Anfang', '' => 'Am Ende'), 'default' => ''),
            ),
            'description' => 'Ein Priofeld zur Festlegung der Reihenfolge',
            'dbtype' => 'int'
        );

    }

    function postAction()
    {
        $sql = rex_sql::factory();
        $sql->setQuery('SET @count = 0');

        $sql->setQuery(sprintf(
            'UPDATE `%s` SET `%s` = (SELECT @count := @count + 1)%s ORDER BY `%2$s`, IF(`id` = %d, 0, 1)',
            $sql->escape($this->params['main_table']),
            $sql->escape($this->getElement('name')),
            $this->getScopeWhere(),
            $this->params['main_id']
        ));
    }

    protected function getScopeWhere()
    {
        $scope = $this->getElement('scope');
        if (!is_array($scope) && $scope) {
            $scope = array_filter(explode(',', $scope));
        }
        if (!$scope) {
            return '';
        }
        $where = array();
        foreach ($scope as $column) {
            if (isset($this->params['value_pool']['sql'][$column])) {
                $value = $this->params['value_pool']['sql'][$column];
            } elseif (isset($this->params['sql_object']) && $this->params['sql_object']->hasValue($column)) {
                $value = $this->params['sql_object']->getValue($column);
            } else {
                $sql = rex_sql::factory();
                $sql->setQuery(sprintf(
                    'SELECT `%s` FROM `%s` WHERE id = %d',
                    $sql->escape($column),
                    $sql->escape($this->params['main_table']),
                    $this->params['main_id']
                ));
                $value = $sql->getValue($column);
            }
            $where[] = sprintf('`%s` = "%s"', mysql_real_escape_string($column), mysql_real_escape_string($value));
        }
        return ' WHERE ' . implode(' AND ', $where);
    }

}
