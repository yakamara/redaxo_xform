<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

abstract class rex_xform_base_abstract
{
    var $params = array();
    var $obj;
    var $elements;
    protected $elementMapping;

    function loadParams(&$params, $elements)
    {
        $this->params = &$params;
        foreach ($elements as $key => $value) {
            $this->setElement($key, $value);
        }
    }

    protected function loadElementMapping()
    {
        if (!is_null($this->elementMapping)) {
            return;
        }

        $this->elementMapping = array();
        $definitions = $this->getDefinitions();
        if (isset($definitions['values'])) {
            $i = $this->getElementMappingOffset();
            foreach ($definitions['values'] as $key => $_) {
                $this->elementMapping[$i] = is_int($key) ? $i : $key;
                $i++;
            }
        }
    }

    abstract protected function getElementMappingOffset();

    function setElement($i, $v)
    {
        $this->loadElementMapping();
        if (is_int($i) && isset($this->elementMapping[$i])) {
            $i = $this->elementMapping[$i];
        }
        $this->elements[$i] = $v;
    }

    function getElement($i)
    {
        if (isset($this->elements[$i])) {
            return $this->elements[$i];
        }
        if (isset($this->elementMapping[$i]) && isset($this->elements[$this->elementMapping[$i]])) {
            return $this->elements[$this->elementMapping[$i]];
        }
        return false;
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
