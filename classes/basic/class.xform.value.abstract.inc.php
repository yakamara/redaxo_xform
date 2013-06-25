<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_abstract
{

  var $params = array();
  var $obj;
  var $elements = array();
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
    $this->value=$value;
  }

  function getValue()
  {
    return $this->value;
  }



  //  ------------ keys

  function getFieldId($k="")
  {
    if($k === "") 
    {
      return "xform-".$this->params["form_name"]."-field-".$this->getId();
    }
    return "xform-".$this->params["form_name"]."-field-".$this->getId().'_'.$k;
  }

  function getFieldName($k="")
  {
    return $this->params["this"]->getFieldName($this->getId(), $k, $this->getName());
  }

  function getHTMLId($suffix = "")
  {
    if($suffix != "") 
    {
      return "xform-".$this->params["form_name"]."-".$this->getName()."-".$suffix;
    }else 
    {
      return "xform-".$this->params["form_name"]."-".$this->getName();
    }
  }

  function getHTMLClass()
  {
    return "form".$this->type;
  }


  // ------------ helpers

  function setKey($k,$v)
  {
    $this->keys[$k] = $v;
  }

  function getKeys()
  {
    return $this->keys;
  }

  function getValueFromKey($v = "")
  {
    if($v == "") {
      $v = $this->getValue();
    }

    if(is_array($v)) {
      return $v;
      
    }else {
      if(isset($this->keys[$v]))  {
        return $this->keys[$v];
      }else {
        return $v;
      }
    }
  }

  function emptyKeys()
  {
    $this->keys = array();
  }

  function encodeChars($chars, $text) {
    $text_encoded = str_replace('@'.$chars.'@', sha1('@'.$chars.'@'), $text);
    return $text_encoded;
  }

  function decodeChars($chars, $text) {
    $text_decoded = str_replace( sha1('@'.$chars.'@'), $chars, $text);
    return $text_decoded;
  }

  // ------------

  function loadParams(&$params, $elements = array())
  {
    $this->params = &$params;
    $this->elements = $elements;
    $this->setLabel($this->getElement(2));
    $this->setName($this->getElement(1));
    $this->type = $this->getElement(0);
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

  function getLabelStyle($label) {
  
    $label = rex_translate($label, null, false);

    if($this->params["form_label_type"] == "html") {

    } else {
      $label = nl2br(htmlspecialchars($label));

    }
    return $label;
    return '<span style="color:#f90">'.($label).'</span>';
  }

  function setValueObjects($ValueObjects)
  {
    $this->obj = $ValueObjects;
  }

  function getValueObjects()
  {
    return $this->obj;
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

  // ------------ 

  function getElement($i)
  {
    if(!isset($this->elements[$i])) 
    {
      return "";
    }else 
    {
      return $this->elements[$i];
    }
  }

  function setElement($i,$value)
  {
    $this->elements[$i] = $value;
  }

  // ------------ 

  function getDescription()
  {
    return "Es existiert keine Klassenbeschreibung";
  }

  function getDefinitions() {
    return array();
  }

}
