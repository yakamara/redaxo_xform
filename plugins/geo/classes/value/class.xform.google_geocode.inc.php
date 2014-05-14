<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_google_geocode extends rex_xform_abstract
{

    function enterObject()
    {
        $labels = explode(',', $this->getElement(2)); // Fields of Position
        $labelLng = $labels[0];
        $labelLat = $labels[1];

        $valueLng = '0';
        $valueLat = '0';

        $mapWidth = 400;
        if ($this->getElement(5) != '') {
            $mapWidth = (int) $this->getElement(5);
        }
        $mapHeight = 200;
        if ($this->getElement(6) != '') {
            $mapHeight = (int) $this->getElement(6);
        }

        foreach ($this->obj as $o) {
            if ($o->getName() == $labelLng) {
                $valueLng = $this->floattostr($o->getValue());
            }
            if ($o->getName() == $labelLat) {
                $valueLat = $this->floattostr($o->getValue());
            }
        }

        if ($this->getValue() == '' && !$this->params['send']) {
            $this->setValue($this->getElement(4));
        }

        // Script nur beim ersten mal ausgeben
        $includeGoogleMaps = false;
        if (!defined('REX_XFORM_GOOGLE_GEOCODE_JSCRIPT')) {
            define('REX_XFORM_GOOGLE_GEOCODE_JSCRIPT', true);
            $includeGoogleMaps = true;
        }

        $this->params['form_output'][$this->getId()] = $this->parse(
            'value.google_geocode.tpl.php',
            compact('includeGoogleMaps', 'labelLng', 'labelLat', 'valueLng', 'valueLat', 'mapWidth', 'mapHeight')
        );
    }

    function getDescription()
    {
        return 'google_geocode -> Beispiel: google_geocode|gcode|Bezeichnung|pos_lng,pos_lat|strasse,plz,ort|width|height|';
    }

    function getDefinitions()
    {
        return array(
            'type' => 'value',
            'name' => 'google_geocode',
            'values' => array(
                'name'     => array( 'type' => 'name',     'label' => 'Name' ),
                'label'    => array( 'type' => 'text',     'label' => 'Bezeichnung'),
                'position' => array( 'type' => 'getNames', 'label' => '"lng"-name,"lat"-name'),
                'address'  => array( 'type' => 'getNames', 'label' => 'Names Positionsfindung'),
                'width'    => array( 'type' => 'text',     'label' => 'Map-Breite'),
                'height'   => array( 'type' => 'text',     'label' => 'Map-H&ouml;he'),
            ),
            'description' => 'GoogeMap Positionierung',
            'dbtype' => 'text'
        );

    }

    function floattostr( $val )
    {
        preg_match( "#^([\+\-]|)([0-9]*)(\.([0-9]*?)|)(0*)$#", trim($val), $o );
        return $o[1] . sprintf('%d', $o[2]) . ($o[3] != '.' ? $o[3] : '');
    }

}
