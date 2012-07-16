<?php

class rex_xform_submit extends rex_xform_abstract
{

  function init()
  {
    $this->params["submit_btn_show"] = FALSE;
  }

  function enterObject()
  {
    $this->setValue($this->getElement(2));

    $wc = "";
    if ($this->getElement(4) != "") $wc = $this->getElement(4);

    if (isset($this->params["warning"][$this->getId()])) $wc = $this->params["warning"][$this->getId()]." ";

    $this->params["form_output"][$this->getId()] = '
    <p class="formsubmit '.$this->getHTMLClass().'">
    <input type="submit" class="submit '.$wc.'" name="'.$this->getFieldName().'" id="'.$this->getFieldId().'" value="'.htmlspecialchars(stripslashes(rex_translate($this->getValue()))) . '" />
    </p>';

    $this->params["value_pool"]["email"][$this->getElement(1)] = stripslashes($this->getValue());

    if ($this->getElement(3) != "no_db")
    {
      $this->params["value_pool"]["sql"][$this->getElement(1)] = $this->getValue();
    }
    
  }

  function getDescription()
  {
    return "submit -> Beispiel: submit|label|value|[no_db]|cssclassname";
  }
}

?>