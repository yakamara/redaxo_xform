<?php

class rex_xform_showvalue extends rex_xform_abstract
{

  function enterObject()
  {

    $this->setValue((string) $this->getValue());

    if ($this->getValue() == "" && !$this->params["send"]) {
      $this->setValue($this->getElement(3));
    }

    $output_type = $this->getElement(4)!=''
               ? $this->getElement(4)
               : 'input[text]';
    $output = '';

    switch($output_type)
    {
      case 'input[text]':
      $output = '
        <p class="formtext '.$this->getHTMLClass().'"  id="'.$this->getHTMLId().'">
        <label class="text" for="'.$this->getFieldId().'">'.$this->getElement(2).'</label>
        <input type="hidden" name="'.$this->getFieldName().'" value="'.htmlspecialchars(stripslashes($this->getValue())).'" />
        <input type="text" class="inp_disabled" disabled="disabled" id="'.$this->getFieldId().'" value="'.htmlspecialchars(stripslashes($this->getValue())).'" />
        </p>';
        break;

      case 'textarea':
      $output = '
        <p class="formtextarea '.$this->getHTMLClass().'" id="'.$this->getHTMLId().'">
          <label class="textarea" for="'.$this->getFieldId().'" >' . rex_translate($this->getElement(2)) . '</label>
          <input type="hidden" name="'.$this->getFieldName().'" value="'.htmlspecialchars(stripslashes($this->getValue())).'" />
          <textarea class="inp_disabled" disabled="disabled" id="'.$this->getFieldId().'" >' . htmlspecialchars(stripslashes($this->getValue())) . '</textarea>
        </p>';
        break;

      default: // anything: use string with value replacement
      $output = str_replace('###'.$this->getElement(1).'###',
                                  htmlspecialchars(stripslashes($this->getValue())),
                                  $this->getElement(4));
      $output .= '<input type="hidden" name="'.$this->getFieldName().'" value="'.htmlspecialchars(stripslashes($this->getValue())).'" />';
    }

    $this->params["form_output"][$this->getId()] = $output;
    $this->params["value_pool"]["email"][$this->getElement(1)] = stripslashes($this->getValue());
    $this->params["value_pool"]["sql"][$this->getElement(1)] = $this->getValue();

  }

  function getDescription()
  {
    return "showvalue -> Beispiel: showvalue|login|Loginname|defaultwert|(input[text],textarea,some_string_with_value_replacement)";
  }
}

?>
