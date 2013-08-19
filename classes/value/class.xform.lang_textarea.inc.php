<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_lang_textarea extends rex_xform_abstract
{

  static function getLangDivider()
  {
    return '^^^^°°°°';
  }

  function enterObject()
  {
    global $REX;

    $text = array();
    if (is_array($this->getvalue())) {
      foreach ($this->getvalue() as $k => $t) {
        $text[$k] = $t;

      }

    } elseif (is_string($this->getvalue()) and $this->getvalue() != '') {
      $text = explode(self::getLangDivider(), $this->getValue());

    }

    $wc = '';
    if (isset($this->params['warning'][$this->getId()])) {
      $wc = $this->params['warning'][$this->getId()];
    }

    $tmp = '
    <div class="formtextarea formlangtextarea " id="' . $this->getHTMLId() . '">
      <p><label class="textarea ' . $wc . '" for="' . $this->getFieldId() . '" >' . $this->getLabel() . '</label></p>
      ';

    $tmp .= '<div class="tabs">';
    $tmp .= '<ul class="navi-tab">';
    foreach ($REX['CLANG'] as $l => $lang) {
      $tmp .= '<li><a id="tab_a_' . $l . '" href="#tab_' . $l . '">' . $lang . '</a></li>';
    }
    $tmp .= '</ul>';

    foreach ($REX['CLANG'] as $l => $lang) {
      $t = '';
      if (isset($text[$l]))
        $t = $text[$l];

      $tmp .= '<p class="tab" id="tab_' . $l . '">
        <textarea class="textarea ' . $wc . '" name="' . $this->getFieldName($l) . '" id="' . $this->getFieldId($l) . '" cols="80" rows="10">' .
        htmlspecialchars(stripslashes($t)) .
        '</textarea>
        </p>
        ';
    }

    $tmp .= '</div>';
    $tmp .= '</div>';

    $script = '
      <script type="text/javascript">
      jQuery(function($) {
              var tabContainers = $(\'#' . $this->getHTMLId() . ' div.tabs > p.tab\');

              $(\'#' . $this->getHTMLId() . ' div.tabs .navi-tab a\').click(function () {

                      tabContainers.hide().filter(this.hash).show();
                      $(\'#' . $this->getHTMLId() . ' .tabs .navi-tab a\').removeClass(\'active\');
                      $(this).addClass(\'active\');
                      return false;

              }).filter(\'#tab_a_' . $REX['CUR_CLANG'] . '\').click();

      });
      </script>';

    $this->params['form_output'][$this->getId()] = $tmp . $script;

    $this->setValue(implode(self::getLangDivider(), $text));

    $this->params['value_pool']['email'][$this->getName()] = stripslashes($this->getValue());
    if ($this->getElement(3) != 'no_db')
      $this->params['value_pool']['sql'][$this->getName()] = $this->getValue();
  }

  function getDescription()
  {
    return 'textarea -> Beispiel: lang_textarea|name|label|[no_db]';
  }

  function getDefinitions()
  {
    return array(
      'type' => 'value',
      'name' => 'lang_textarea',
      'values' => array(
        array( 'type' => 'name',   'label' => 'Feld' ),
        array( 'type' => 'text',    'label' => 'Bezeichnung'),
        array( 'type' => 'no_db',   'label' => 'Datenbank',  'default' => 0),
      ),
      'description' => 'Ein mehrzeiliges mehrsprachiges Textfeld als Eingabe',
      'dbtype' => 'text'
    );
  }
}
