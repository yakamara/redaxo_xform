<?php

class rex_xform_captcha_calc extends rex_xform_abstract
{

  function enterObject()
  {

    // var_dump($this->params);
    global $REX;

    require_once (realpath(dirname (__FILE__).'/../../ext/captcha_calc/class.captcha_calc_x.php'));

    $captcha = new captcha_calc_x ();
    $captchaRequest = rex_request('captcha_calc', 'string');

    if ($captchaRequest == "show")
    {
      // alle offenen buffer schliessen
      while(@ob_end_clean());

      $captcha->handle_request();
      exit;
    }

    $wc = "";
    // TODO jan ?!?!
    // hier bewusst nur ein "&" (konditionales und, kein boolsches und!)
    if ( $this->params["send"] == 1 & $captcha->validate($this->value))
    {
      // Alles ist gut.
    }elseif($this->params["send"]==1)
    {
      // Error. Fehlermeldung ausgeben
      $this->params["warning"][] = $this->elements[2];
      $this->params["warning_messages"][] = $this->elements[2];
      $wc = $this->params["error_class"];
    }

    if($this->getElement(3) != "")
    {
      // TODO: ? vorhanden oder nicht
      $link = $this->getELement(3).'?captcha_calc=show&'.time();
    }else {

      $link = rex_getUrl($this->params["article_id"],$this->params["clang"],array("captcha_calc"=>"show"),"&");
    }

    if ($wc != '')
      $wc = ' '.$wc;

    $this->params["form_output"][$this->getId()] = '
      <p class="formcaptcha">
        <label class="captcha' . $wc . '" for="el_' . $this->id . '">
          '.htmlspecialchars(rex_translate($this->getElement(1))).'
        </label>
        <span class="as-label' . $wc . '"><img
          src="'.$link.'"
          onclick="javascript:this.src=\''.$link.'&\'+Math.random();"
          alt="CAPTCHA image"
          /></span>
        <input class="captcha' . $wc . '" maxlength="5" size="5" id="el_' . $this->id . '" name="'.$this->getFieldName().'" type="text" />
      </p>';
    // Ende
  }

  function getDescription()
  {
    return "captcha_calc -> Beispiel: captcha|Beschreibungstext|Fehlertext";
  }
}

?>