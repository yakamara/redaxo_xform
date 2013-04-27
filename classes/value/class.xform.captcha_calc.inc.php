<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_captcha_calc extends rex_xform_abstract
{

  function enterObject()
  {
    global $REX;

    require_once realpath(dirname(__FILE__) . '/../../ext/captcha_calc/class.captcha_calc_x.php');

    $captcha = new captcha_calc_x();
    $captchaRequest = rex_request('captcha_calc', 'string');

    if ($captchaRequest == 'show') {
      while (@ob_end_clean());
      $captcha->handle_request();
      exit;
    }

    $wc = '';
    if ( $this->params['send'] == 1 & $captcha->validate($this->value)) {

    } elseif ($this->params['send'] == 1) {
      $this->params['warning'][] = $this->getElement(2);
      $this->params['warning_messages'][] = $this->getElement(2);
      $wc = $this->params['error_class'];
    }

    if ($this->getElement(3) != '') {
      $link = $this->getELement(3) . '?captcha_calc=show&' . time();

    } else {
      $link = rex_getUrl($this->params['article_id'], $this->params['clang'], array('captcha_calc' => 'show'), '&');

    }

    if ($wc != '')
      $wc = ' ' . $wc;

    $this->params['form_output'][$this->getId()] = '
      <p class="formcaptcha" id="' . $this->getHTMLId() . '">
        <label class="captcha' . $wc . '" for="' . $this->getFieldId() . '">
          ' . $this->getLabelStyle($this->getElement(1)) . '
        </label>
        <span class="as-label' . $wc . '"><img
          src="' . $link . '"
          onclick="javascript:this.src=\'' . $link . '&\'+Math.random();"
          alt="CAPTCHA image"
          /></span>
        <input class="captcha' . $wc . '" maxlength="5" size="5" id="' . $this->getFieldId() . '" name="' . $this->getFieldName() . '" type="text" />
      </p>';
  }

  function getDescription()
  {
    return 'captcha_calc -> Beispiel: captcha|Beschreibungstext|Fehlertext';
  }
}
