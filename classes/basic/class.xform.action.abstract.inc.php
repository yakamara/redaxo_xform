<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_action_abstract
{

  var $obj;

  var $params = array();
  var $elements = array();
  var $action = array();

  function loadParams(&$params, $elements)
  {
    $this->params = &$params;
    $this->elements = $elements;
  }

  function setObjects(&$obj)
  {
    $this->obj = &$obj;
  }

  function execute()
  {
    return false;
  }

  function getDescription()
  {
    return 'Es existiert keine Klassenbeschreibung';
  }

  function getLongDescription()
  {
    return 'Es existiert keine ausfuehrliche Klassenbeschreibung';
  }

  function getDefinitions()
  {
    return array();
  }

  function getElement($i)
  {
    if (!isset($this->elements[$i]))
      return false;
    else
      return $this->elements[$i];
  }

  function getParam($param)
  {
    return $this->params[$param];
  }

}
