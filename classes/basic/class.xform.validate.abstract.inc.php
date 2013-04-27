<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_validate_abstract
{

  var $params = array();
  var $obj;
  var $elements;
  var $obj_array;
  var $Objects; // die verschiedenen Value Objekte

  function loadParams(&$params, $elements)
  {
    $this->params = &$params;
    $this->elements = $elements;
  }

  function setObjects(&$Objects)
  {
    $this->obj = &$Objects;
    $tmp_Objects = explode(',', $this->getElement(2));

    foreach ($tmp_Objects as $tmp_Object) {
      $tmp_FoundObject = false;
      foreach ($Objects as $Object) {
        if (strcmp($Object->getName(), trim($tmp_Object)) == 0) {
          $this->obj_array[] = &$Object;
          $tmp_FoundObject = true;
          break;
        }
      }
    }

  }

  function enterObject()
  {
    return '';
  }

  function getDescription()
  {
    return '';
  }

  function getLongDescription()
  {
    return '';
  }

  function getDefinitions()
  {
    return array();
  }

  function getElement($i)
  {
    if (!isset($this->elements[$i]))
      return '';
    else
      return $this->elements[$i];
  }

  function postValueAction()
  {
  }

}
