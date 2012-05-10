<?php

class rex_xform_generate_key extends rex_xform_abstract
{

  function enterObject()
  {
    $this->setValue(md5($this->params["form_name"] . substr(md5(microtime()), 0, 6)));
    $this->params["form_output"][$this->getId()] = '';
    $this->params["value_pool"]["email"][$this->getElement(1)] = stripslashes($this->getValue());
    if (!isset($element[2]) || $element[2] != "no_db") $this->params["value_pool"]["sql"][$this->getElement(1)] = $this->getValue();
  }

  function getDescription()
  {
    return "generate_key -> Beispiel: generate_key|activation_key|[no_db]";
  }
}

?>