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
        $multiple = $this->getElement('multiple') == 1;

        $options = $this->getArrayFromString($this->getElement('options'));

        if ($multiple) {
            $size = (int) $this->getElement(7);
            if ($size < 2) {
                $size = count($options);
            }
        } else {
            $size = 1;
        }

        if (!$this->params['send'] && $this->getValue() == '' && $this->getElement('default') != '') {
            $this->setValue($this->getElement('default'));
        }

        if (!is_array($this->getValue())) {
            $this->setValue(explode(',', $this->getValue()));
        }

        $this->params['form_output'][$this->getId()] = $this->parse('value.select.tpl.php', compact('options', 'multiple', 'size'));

        $this->setValue(implode(',', $this->getValue()));

        $this->params['value_pool']['email'][$this->getName()] = $this->getValue();
        if ($this->getElement('no_db') != 'no_db') {
            $this->params['value_pool']['sql'][$this->getName()] = $this->getValue();
        }
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
                'name'     => array( 'type' => 'name',   'label' => 'Feld' ),
                'label'    => array( 'type' => 'text',    'label' => 'Bezeichnung'),
                'options'  => array( 'type' => 'text',    'label' => 'Selectdefinition, kommasepariert'),
                'no_db'    => array( 'type' => 'no_db',   'label' => 'Datenbank',          'default' => 0),
                'default'  => array( 'type' => 'text',    'label' => 'Defaultwert'),
                'multiple' => array( 'type' => 'boolean', 'label' => 'Mehrere Felder möglich'),
                'size'     => array( 'type' => 'text',    'label' => 'Höhe der Auswahlbox'),
            ),
            'description' => 'Ein Selectfeld mit festen Definitionen',
            'dbtype' => 'text'
        );

    }

    static function getListValue($params)
    {
        $return = array();

        $new_select = new rex_xform_select();
        $values = $new_select->getArrayFromString($params['params']['field']['options']);

        foreach (explode(',', $params['value']) as $k) {
            if (isset($values[$k])) {
                $return[] = $values[$k];
            }
        }

        return implode('<br />', $return);
    }

}
