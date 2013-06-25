<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_objparams extends rex_xform_abstract
{

  function init()
  {
    $value = trim($this->getElement(2));
    $when = trim($this->getElement(3));

    if($when != "runtime") { // -> init

      switch ($value) {

       case 'false';
         $value = false;
         break;

       case 'true';
         $value = true;
         break;

       default:
         $value = ((string) (int) $value === $value) ? (int) $value : $value;

      }

      $this->params[trim($this->getElement(1))] = $value;

    }

  }

  function enterObject()
  {
    $value = trim($this->getElement(2));
    $when = trim($this->getElement(3));

    if($when == "runtime") {

      switch ($value) {

        case 'false';
          $value = false;
          break;

        case 'true';
          $value = true;
          break;

        default:
          $value = ((string) (int) $value === $value) ? (int) $value : $value;

      }

      $this->params[trim($this->getElement(1))] = $value;

    }

  }

  function getDescription()
  {
    return 'objparams -> Beispiel: objparams|key|newvalue|[init/runtime]';
  }

}
