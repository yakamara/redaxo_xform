<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_hidden extends rex_xform_abstract
{

  public function setValue($value)
  {
    if ($this->getElement(3) == 'REQUEST' && isset($_REQUEST[$this->getName()])) {
      $this->value = stripslashes(rex_request($this->getName()));

    } else {
      $this->value = $this->getElement(2);

    }

  }

  public function enterObject()
  {
    $this->params['form_output'][$this->getId()] = "\n" . '<input type="hidden" name="' . $this->getName() . '" id="' . $this->getHTMLId() . '" value="' . htmlspecialchars($this->getValue()) . '" />';

    $this->params['value_pool']['email'][$this->getName()] = $this->getValue();
    if ($this->getElement(4) != 'no_db')
      $this->params['value_pool']['sql'][$this->getName()] = $this->getValue();
  }

  public function getDescription()
  {
    return '
        hidden -> Beispiel: hidden|name|(default)value||[no_db]<br />  hidden -> Beispiel: hidden|job_id|my_id|REQUEST|[no_db]
    ';
  }

  public function getLongDescription()
  {
    return '
    Hiermit können Werte fest als Wert zum Formular eingetragen werden z.B.

    hidden|status|abgeschickt

    Dieser Wert kann wie alle anderen Werte bernommen und in der Datenbank gepeichert, oder auch
    im E-Mail Formular anzeigt werden.

    Weiterhin gibt es mit "REQUEST" auch die Mglichkeit, Werte auf der Url oder einem
    vorherigen Formular zu bernehmen.

    hidden -> Beispiel: hidden|job_id|default_value|REQUEST|

    Hier wird die job_id bernommen und direkt wieder ber das Formular mitversendet.

    mit "no_db" wird definiert, dass bei einer eventuellen Datenbankspeicherung, dieser
    Wert nicht bernommen wird.
    ';
  }

}
