<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_remembervalues extends rex_xform_abstract
{

  function postValidateAction()
  {
    if ($this->params['send'] == 0) {
      $fields = explode(',', $this->getElement(2));
      $cookiename = $this->getName();
      if ($cookiename == '') $cookiename = 'dummyremembercookie';
      if (isset($_COOKIE[$cookiename])) {
        $fields = unserialize(base64_decode($_COOKIE[$cookiename]));
      } else {
        $fields = array();
      }
      if (is_array($fields)) {
        foreach ($this->obj as $o) {
          if (array_key_exists($o->getName(), $fields)) {
            $o->setValue($fields[$o->getName()]);
            $this->setValue(1); // checked = ' checked="checked"';
          }
        }
      }
    }

  }

  function enterObject()
  {
    $checked = '';
    if ( ($this->getValue() == 1) || ($this->params['send'] == 0 && $this->getElement(4) == 1)) {
      $checked = ' checked="checked"';
    }
    $form_output[$this->getId()] = '
      <p class="formcheckbox formlabel-' . $this->getName() . '" id="' . $this->getHTMLId() . '">
        <input type="checkbox" class="checkbox" name="' . $this->getFieldname() . '" id="' . $this->getFieldId() . '" value="1" ' . $checked . ' />
        <label class="checkbox" for="' . $this->getFieldId() . '" >' . $this->getElement(3) . '</label>
      </p>';
  }

  function postFormAction()
  {
    if ($this->params['send'] == 1) {
      $c = array();
      if ($this->getValue() == 1) {
        $fields = explode(',', $this->getElement(2));
        foreach ($this->obj as $o) {
          if (in_array($o->getName(), $fields)) {
            $c[$o->getName()] = $o->getValue();
          }
        }
      }
      $c = base64_encode(serialize($c));
      $cookiename = $this->getName();
      if ($cookiename == '') $cookiename = 'dummyremembercookie';
      $lastfor = (int) $this->getElement(5);
      if ($lastfor < 3600) $lastfor = 4 * 7 * 24 * 60 * 60; // if < 1 hour -> one month
      setcookie($cookiename, $c , time() + $lastfor, '/');
    }
  }

  function getDescription()
  {
    return 'remembervalues -> Beispiel: remembervalues|name|label1,label2,label3,label4|Bezeichnung|opt:default:1/0|opt:dauerinsekunden';
  }

}
