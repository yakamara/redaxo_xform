<?php

class rex_xform_manager_field
{
    protected $values = array();
    protected static $debug = false;
    protected static $types = array('value', 'validate', 'action');

    function __construct(array $values)
    {
        global $I18N;
        if (count($values) == 0) {
            throw new Exception($I18N->msg('xform_field_not_found'));
        }
        $this->values = $values;

        if (!class_exists('rex_xform_' . $this->getTypeName())) {
            rex_xform::includeClass($this->getType(), $this->getTypeName());
        } else {
            // echo '<pre>';      var_dump($values);echo '</pre>';
        }
    }

    public static function table()
    {
        global $REX;
        return $REX['TABLE_PREFIX'] . 'xform_field';
    }

    // value, validate, action
    public function getType()
    {
        $type_id =  $this->values['type_id'];
        if (!in_array($type_id, self::$types)) {
            return false;
        }
        return $type_id;
    }

    // rex_xform_select
    public function getTypeName()
    {
        if (!isset($this->values['type_name'])) {
            return '';
        }
        return $this->values['type_name'];
    }

    public function getName()
    {
        return $this->values['name'];
    }

    public function getLabel()
    {
        return $this->values['label'];
    }

    public function getElement($k)
    {
        if (!isset($this->values[$k])) {
            return null;
        }
        return $this->values[$k];
    }

    public function isSearchable()
    {
        if ($this->values['search'] == 1) {
            return true;
        }
        return false;
    }

    public function isHiddenInList()
    {
        if ($this->values['list_hidden'] == 1) {
            return true;
        }
        return false;
    }

    // deprecated
    // sobald die xform value klassen umgebaut worden sind.
    public function toArray()
    {
        return $this->values;

    }

}
