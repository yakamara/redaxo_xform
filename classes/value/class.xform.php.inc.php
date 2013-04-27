<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_php extends rex_xform_abstract
{

  function enterObject()
  {
    ob_start();
    eval('?>' . $this->getElement(2));
    $out = ob_get_contents();
    ob_end_clean();
    $this->params['form_output'][$this->getId()] = $out;
  }

  function getDescription()
  {
    return htmlspecialchars(stripslashes('php -> Beispiel: php|name|<?php echo date("mdY"); ?>'));
  }
}
