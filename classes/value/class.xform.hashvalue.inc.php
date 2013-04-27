<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_hashvalue extends rex_xform_abstract
{
  function postFormAction()
  {
    ## if source not empty
    if ($this->params['value_pool']['email'][$this->getElement(3)] != '') {
      ## Get values
      $salt = '';
      $salt = $this->getElement(5);
      $origin = $this->params['value_pool']['email'][$this->getElement(3)];

      ##Generate Hash
      $hash = hash($this->getElement(4), $origin . $salt);

      $this->params['value_pool']['email'][$this->getName()] = $hash;

      if ($this->getElement(6) != 'no_db')
        $this->params['value_pool']['sql'][$this->getName()] = $hash;
    } else {
      ## get current hash vor email
      $this->params['value_pool']['email'][$this->getName()] = $this->getValue();
    }
  }

  function getDescription()
  {
    return 'hashvalue -> Beispiel: hashvalue|name|[title]|field|(md5/sha1/sha512/...)|[salt]|[no_db]';
  }

  function getLongDescription()
  {
    return '
      Erzeug einen Hash-Wert eines anderen Formularfeldes, wenn dieses nicht leer ist.

      hashvalue|label|[bezeichnung]|field|(md5/sha1/sha512/...)|[salt]|[no_db]

      Kann dazu eingesetzt werden den Wert aus einem no_db Feld zu übernehmen, daraus einen Hash zu erstellen und in die Datenbank zu schreiben. Ein neuer Hash-Wert wird nur erstellt, wenn das Ursprungsfeld nicht leer ist.

      Mit dem salt kann dem Ursprungs-Wert eine Zeichenkette anhängen und gemeinsam gehasht werden.
      ';
  }

  function getDefinitions()
  {
    return array(
      'type' => 'value',
      'name' => 'hashvalue',
      'values' => array(
        array( 'type' => 'name',    'label' => 'Feld' ),
        array( 'type' => 'text',    'label' => 'Bezeichnung'),
        array( 'type' => 'text',    'label' => 'Input-Feld'),
        array( 'type' => 'text',    'label' => 'Algorithmus'),
        array( 'type' => 'text',   'label' => 'Salt'),
        array( 'type' => 'no_db'),
      ),
      'description' => 'Erzeug Hash-Wert von anderem Feld und speichert ihn',
      'dbtype' => 'text',
      'famous' => false
    );
  }
}
