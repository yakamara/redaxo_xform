<?php

class rex_xform_showvalue extends rex_xform_abstract
{

  function enterObject()
  {

    if ($this->getValue() == "" && !$this->params["send"]) {
      $this->setValue($this->getElement(3));
    }

    $this->params["form_output"][$this->getId()] = '
      <p class="formtext '.$this->getHTMLClass().'"  id="'.$this->getHTMLId().'">
      <label class="text" for="'.$this->getFieldId().'">'.$this->getElement(2).'</label>
      <input type="hidden" name="'.$this->getFieldName().'" value="'.htmlspecialchars(stripslashes($this->getValue())).'" />
      <input type="text" class="inp_disabled" disabled="disabled" id="'.$this->getFieldId().'" value="'.htmlspecialchars(stripslashes($this->getValue())).'" />
      </p>';

    $this->params["value_pool"]["email"][$this->getElement(1)] = stripslashes($this->getValue());

  }

  function getDescription()
  {
    return "showvalue -> Beispiel: showvalue|login|Loginname|defaultwert";
  }
}

?>