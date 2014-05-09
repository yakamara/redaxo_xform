<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_base_abstract
{
    var $params = array();
    var $obj;
    var $elements;

    function loadParams(&$params, $elements)
    {
        $this->params = &$params;
        $this->elements = $elements;
    }

    function setElement($i, $v)
    {
        $this->elements[$i] = $v;
    }

    function getElement($i)
    {
        if (!isset($this->elements[$i])) {
            return false;
        } else {
            return $this->elements[$i];
        }
    }

    function getParam($param)
    {
        return $this->params[$param];
    }

    function setObjects(&$obj)
    {
        $this->obj = &$obj;
    }

    function getObjects()
    {
        return $this->obj;
    }

    function getDescription()
    {
        return 'Es existiert keine Klassenbeschreibung';
    }

    function getDefinitions()
    {
        return array();
    }
}
