<?php

class rex_xform_objparams extends rex_xform_abstract
{

  function init()
  {
    $value = trim($this->getElement(2));
    switch($value)
    {
      case 'false';
        $value = false;
        break;

      case 'true';
        $value = true;
        break;

      default:
        if(preg_match("/^[0-9]+$/i", $value) > 0)
        {
          // $value = (int) $value;
        }
    }
    
    $this->params[trim($this->getElement(1))] = $value;
  }

  function enterObject()
  {
  }

  function getDescription()
  {
    return "objparams -> Beispiel: objparams|key|newvalue|";
  }

}

?>
