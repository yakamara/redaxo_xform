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

class rex_xform_action_abstract
{

  var $obj;

  var $params = array();
  var $elements = array();
  var $action = array();

  function loadParams(&$params, &$elements)
  {
    $this->params = &$params;
    $this->elements = &$elements;
  }

  function setObjects(&$obj)
  {
    $this->obj = &$obj;
  }

  function execute()
  {
    return FALSE;
  }

  function getDescription()
  {
    return "Es existiert keine Klassenbeschreibung";
  }

  function getLongDescription()
  {
    return "Es existiert keine ausfuehrliche Klassenbeschreibung";
  }

  function getDefinitions()
  {
    return array();
  }

  function getElement($i)
  {
    if(!isset($this->elements[$i]))
      return FALSE;
    else
      return $this->elements[$i];
  }

  function getParam($param)
  {
    return $this->params[$param];
  }

}
