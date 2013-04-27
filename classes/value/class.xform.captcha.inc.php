<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_captcha extends rex_xform_abstract
{

  function enterObject()
  {

    global $REX;

    require_once realpath(dirname(__FILE__) . '/../../ext/captcha/class.captcha_x.php');

    $captcha = new captcha_x();
    $captchaRequest = rex_request('captcha', 'string');

    if ($captchaRequest == 'show') {
      while (@ob_end_clean());
      $captcha->handle_request();
      exit;
    }

    $wc = '';
    if ( $this->params['send'] == 1 & $captcha->validate($this->getValue())) {
      if (isset($_SESSION['captcha'])) {
        unset($_SESSION['captcha']);
      }
    } elseif ($this->params['send'] == 1) {
      // Error. Fehlermeldung ausgeben
      $this->params['warning'][$this->getId()] = $this->getElement(2);
      $this->params['warning_messages'][$this->getId()] = $this->getElement(2);
      $wc = $this->params['error_class'];
    }

    if ($this->getElement(3) != '') {
      $link = $this->getElement(3) . '?captcha=show&' . time() . microtime();
    } else {
      $link = rex_getUrl($this->params['article_id'], $this->params['clang'], array('captcha' => 'show'), '&') . '&' . time() . microtime();
    }

    if ($wc != '')
      $wc = ' ' . $wc;

    $this->params['form_output'][$this->getId()] = '
      <p class="formcaptcha" id="' . $this->getHTMLId() . '">
        <label class="captcha' . $wc . '" for="' . $this->getFieldId() . '">' . $this->getLabelStyle($this->getElement(1)) . '</label>
        <span class="as-label' . $wc . '"><img  src="' . $link . '" onclick="javascript:this.src=\'' . $link . '&\'+Math.random();" alt="CAPTCHA image" /></span>
        <input class="captcha' . $wc . '" maxlength="5" size="5" id="' . $this->getFieldId() . '" name="' . $this->getFieldName() . '" type="text" />
      </p>';
    // Ende
  }

  function getDescription()
  {
    return 'captcha -> Beispiel: captcha|Beschreibungstext|Fehlertext|[link]';
  }

}
