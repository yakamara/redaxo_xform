<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_validate_customfunction extends rex_xform_validate_abstract
{

    function enterObject()
    {
        if ($this->params['send'] == '1') {

            $label = $this->getElement('name');
            $func = $this->getElement('function');
            $parameter = $this->getElement('params');

            $true = true;
            if (substr($func, 0, 1) == '!') {
                    $true = false;
                    $func = substr($func, 1);
            }

            foreach ($this->obj_array as $Object) {

                $method = explode('::', $func);
                if ( count($method) == 2 ) {

                    if ( !method_exists($method[0], $method[1]) ) {
                        $this->params['warning'][$Object->getId()] = $this->params['error_class'];
                        $this->params['warning_messages'][$Object->getId()] = 'ERROR: customfunction "' . $func . '" not found';

                    } elseif ( $method[0]::$method[1]($label, $Object->getValue(), $parameter) === $true) {
                        $this->params['warning'][$Object->getId()] = $this->params['error_class'];
                        $this->params['warning_messages'][$Object->getId()] = $this->getElement('message');

                    }

                } elseif (function_exists($func)) {
                    if ($func($label, $Object->getValue(), $parameter) === $true) {
                        $this->params['warning'][$Object->getId()] = $this->params['error_class'];
                        $this->params['warning_messages'][$Object->getId()] = $this->getElement('message');

                    }

                } else {
                    $this->params['warning'][$Object->getId()] = $this->params['error_class'];
                    $this->params['warning_messages'][$Object->getId()] = 'ERROR: customfunction "' . $func . '" not found';

                }
            }
        }
    }

    function getDescription()
    {
        return 'customfunction -> prüft über customfunc, beispiel: validate|customfunction|label|[!]function/class::method|weitere_parameter|warning_message';
    }

    function getDefinitions()
    {
        return array(
            'type' => 'validate',
            'name' => 'customfunction',
            'values' => array(
                'name'     => array( 'type' => 'select_name', 'label' => 'Name'),
                'function' => array( 'type' => 'text',  'label' => 'Name der Funktion' ),
                'params'   => array( 'type' => 'text',   'label' => 'Weitere Parameter'),
                'message'  => array( 'type' => 'text',   'label' => 'Fehlermeldung'),
            ),
            'description' => 'Mit eigener Funktion vergleichen',
        );

    }

}
