<?php

class rex_xform_manager_searchform extends rex_xform
{

    function getFieldName($id = '', $k = '', $label = '')
    {
        $label = $this->prepareLabel($label);
        $k = $this->prepareLabel($k);
        if ($label == '') {
            $label = $id;
        }
        if ($k == '') {
            return 'rex_xform_searchvars[' . $label . ']';
        } else {
            return 'rex_xform_searchvars[' . $label . '][' . $k . ']';
        }
    }

    function getFieldValue($id = '', $k = '', $label = '')
    {
        $label = $this->prepareLabel($label);
        $k = $this->prepareLabel($k);
        if ($label == '') {
            $label = $id;
        }
        if ($k == '' && isset($_REQUEST['rex_xform_searchvars'][$label])) {
            return $_REQUEST['rex_xform_searchvars'][$label];
        } elseif (isset($_REQUEST['rex_xform_searchvars'][$label][$k])) {
            return $_REQUEST['rex_xform_searchvars'][$label][$k];
        }
        return '';
    }

    function setFieldValue($id = '', $value = '', $k = '', $label = '')
    {
        $label = $this->prepareLabel($label);
        $k = $this->prepareLabel($k);
        if ($label == '') {
            $label = $id;
        }
        if ($k == '') {
            $_REQUEST['rex_xform_searchvars'][$label] = $value;
        } else {
            $_REQUEST['rex_xform_searchvars'][$label][$k] = $value;
        }
    }

}
