<?php

class rex_xform_resetbutton extends rex_xform_abstract
{

  function enterObject()
  {
    $this->setValue($this->getElement(2));

    $wc = "";
    if ($this->getElement(4) != "") {
      $wc = $this->getElement(3);
    }

    $this->params["form_output"][$this->getId()] = '
        <p class="formsubmit '.$this->getHTMLClass().'" id="'.$this->getHTMLId().'">
        <label class="text ' . $wc . '" for="'.$this->getFieldId().'" >&nbsp;</label>
        <input type="reset" class="submit ' . $wc . '" id="'.$this->getFieldId().'" value="'.htmlspecialchars(stripslashes($this->getValue())).'" />
        </p>';

  }

  function getDescription()
  {
    return "resetbutton -> Beispiel: resetbutton|label|value|cssclassname";
  }
}

?>