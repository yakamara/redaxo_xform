<?php

/**
 * XForm
 *
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 *
 * @package redaxo4
 * @version svn:$Id$
 */

class rex_xform_abstract
{

  var $params = array(); // allgemeine parameter der
  var $obj;
  var $elements = array(); // lokale elemente
  var $element_values = array(); // Werte aller Value Objekte

  var $id;
  var $value;
  var $name;
  var $type;
  var $keys = array();

  function setId($id)
  {
    $this->id = $id;
  }

  function setArticleId($aid)
  {
    $this->aid = $aid;
  }

  // **************************

  function setValue($value)
  {
    $this->value=$value;
  }

  function getValue()
  {
    return $this->value;
  }

  // **************************

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

    if(is_array($v))
    {
      return $v;
    }else
    {
      if(isset($this->keys[$v])) {
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

  // **************************

  function loadParams(&$params, $elements = array())
  {
    $this->params = &$params;
    $this->elements = $elements;
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

  function setObjects(&$obj)
  {
    $this->obj = &$obj;
  }

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

  function postSQLAction($sql,$flag="insert")
  {
    if ($flag=="insert")
    {
      // $id = $sql->getLastId();
    }
  }

  function getElement($i)
  {
    if(!isset($this->elements[$i])) {
      return "";
    }else {
      return $this->elements[$i];
    }
  }

  function setElement($i,$value)
  {
    $this->elements[$i] = $value;
  }

  function getId()
  {
    return $this->id;
  }

  function getFieldId($k="")
  {
    if($k === "") return "xform-".$this->params["form_name"]."-field-".$this->getId();
    return "xform-".$this->params["form_name"]."-field-".$this->getId().'_'.$k;
  }

  function getFieldName($k="")
  {
  	return $this->params["this"]->getFieldName($this->getId(), $k, $this->getName());
  }

  function getHTMLId($suffix = "")
  {
    if($suffix != "") {
      return "xform-".$this->params["form_name"]."-".$this->getName()."-".$suffix;
    }else {
      return "xform-".$this->params["form_name"]."-".$this->getName();
    }
  }

  function getHTMLClass()
  {
    return "form".$this->type;
  }

  function getDescription()
  {
    return "Es existiert keine Klassenbeschreibung";
  }

  function getDefinitions() {
    return array();
  }

}
