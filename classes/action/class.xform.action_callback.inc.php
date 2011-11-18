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

class rex_xform_action_callback extends rex_xform_action_abstract
{

  function execute()
  {
  
    if(!$this->getElement(2))
      return FALSE;
      
    $f = $this->getElement(2);
  
    if (function_exists($f))
    {
      $f($this);
    }  
  
    return;

  }

  function getDescription()
  {
    return "action|callback|function";
  }

}
