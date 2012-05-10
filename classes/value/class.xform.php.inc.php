<?php

class rex_xform_php extends rex_xform_abstract
{

  function enterObject()
  {
    ob_start();
    eval("?>".$this->getElement(1));
    $out = ob_get_contents();
    ob_end_clean();

    $this->params["form_output"][$this->getId()] = $out;
  }

  function getDescription()
  {
    return htmlspecialchars(stripslashes('php -> Beispiel: php|<?php echo date("mdY"); ?>'));
  }
}

?>