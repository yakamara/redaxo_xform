<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_radio extends rex_xform_abstract
{

    function enterObject()
    {
        $value_encoded = $this->getElement(3);
        $value_encoded = $this->encodeChars(',', $value_encoded);

        $rawOptions = explode(',', $value_encoded);
        $options = array();
        foreach ($rawOptions as $option_encoded) {

            $option = $this->encodeChars('=', $option_encoded);

            $t = explode('=', $option);
            $v = $t[0];

            if (isset($t[1])) {
                $k = $t[1];
            } else {
                $k = $t[0];
            }

            $v = $this->decodeChars(',', $v);
            $v = $this->decodeChars('=', $v);
            $k = $this->decodeChars(',', $k);
            $k = $this->decodeChars('=', $k);
            $t[0] = $this->decodeChars(',', $t[0]);
            $t[0] = $this->decodeChars('=', $t[0]);

            $options[$k] = $v;
        }

        if ($this->getValue() == '' && $this->getElement(4) != '') {
            $this->setValue($this->getElement(4));
        }

        $this->params['form_output'][$this->getId()] = $this->parse('value.radio.tpl.php', compact('options'));

        $this->params['value_pool']['email'][$this->getName()] = stripslashes($this->getValue());
        if ($this->getElement(5) != 'no_db') {
            $this->params['value_pool']['sql'][$this->getName()] = $this->getValue();
        }

    }

    function getDescription()
    {
        return 'radio -> Beispiel: radio|name|label|Frau=w,Herr=m|[no_db]|defaultwert';
    }
}
