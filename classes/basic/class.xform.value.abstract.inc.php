<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

abstract class rex_xform_abstract extends rex_xform_base_abstract
{
    var $element_values = array();

    var $id;
    var $value;
    var $name;
    var $label;
    var $type;
    var $keys = array();


    // ------------

    function getId()
    {
        return $this->id;
    }

    function setId($id)
    {
        $this->id = $id;
    }

    function setArticleId($aid)
    {
        $this->aid = $aid;
    }

    function setValue($value)
    {
        $this->value = $value;
    }

    function getValue()
    {
        return $this->value;
    }



    //  ------------ keys

    function getFieldId($k = '')
    {
        if ($k === '') {
            return 'xform-' . $this->params['form_name'] . '-field-' . $this->getId();
        }
        return 'xform-' . $this->params['form_name'] . '-field-' . $this->getId() . '_' . $k;
    }

    function getFieldName($k = '')
    {
        return $this->params['this']->getFieldName($this->getId(), $k, $this->getName());
    }

    function getHTMLId($suffix = '')
    {
        if ($suffix != '') {
            return 'xform-' . $this->params['form_name'] . '-' . $this->getName() . '-' . $suffix;
        } elseif ($this->getName() != '') {
            return 'xform-' . $this->params['form_name'] . '-' . $this->getName();
        }

        return '';
    }

    function getHTMLClass()
    {
        return 'form' . $this->type;
    }


    // ------------ helpers

    function setKey($k, $v)
    {
        $this->keys[$k] = $v;
    }

    function getKeys()
    {
        return $this->keys;
    }

    function getValueFromKey($v = '')
    {
        if ($v == '') {
            $v = $this->getValue();
        }

        if (is_array($v)) {
            return $v;

        } else {
            if (isset($this->keys[$v]))  {
                return $this->keys[$v];
            } else {
                return $v;
            }
        }
    }

    function emptyKeys()
    {
        $this->keys = array();
    }

    function encodeChars($chars, $text)
    {
        $text_encoded = str_replace('@' . $chars . '@', sha1('@' . $chars . '@'), $text);
        return $text_encoded;
    }

    function decodeChars($chars, $text)
    {
        $text_decoded = str_replace( sha1('@' . $chars . '@'), $chars, $text);
        return $text_decoded;
    }

    function parse($template, $params = array())
    {
        global $REX, $I18N;

        extract($params);

        ob_start();
        include $this->params['this']->getTemplatePath($template);
        return ob_get_clean();
    }

    function getAttributeElement($attribute, $boolean = false)
    {
        $element = $this->getElement($attribute);
        if ($element) {
            return ' ' . $attribute . '="' . ($boolean ? $attribute : htmlspecialchars($element)) . '"';
        }
        return '';
    }

    function getWarningClass()
    {
        if (isset($this->params['warning'][$this->getId()])) {
            return ' ' . $this->params['warning'][$this->getId()];
        }
        return '';
    }

    // ------------

    function loadParams(&$params, $elements = array())
    {
        parent::loadParams($params, $elements);
        $this->setLabel($this->getElement(2));
        $this->setName($this->getElement(1));
        $this->type = $this->getElement(0);
    }

    protected function getElementMappingOffset()
    {
        return 1;
    }

    function setName($name)
    {
        $this->name = $name;
    }

    function getName()
    {
        return $this->name;
    }

    function setLabel($label)
    {
        $this->label = $label;
    }

    function getLabel()
    {
        return $this->getLabelStyle($this->label);
    }

    function getLabelStyle($label)
    {

        $label = rex_translate($label, null, false);

        if ($this->params['form_label_type'] == 'html') {

        } else {
            $label = nl2br(htmlspecialchars($label));

        }
        return $label;
        return '<span style="color:#f90">' . ($label) . '</span>';
    }

    // ------------ Trigger

    function enterObject()
    {
    }

    function init()
    {
    }

    function preValidateAction()
    {
    }

    function postValidateAction()
    {
    }

    function postFormAction()
    {
    }

    function postAction()
    {
    }

}
