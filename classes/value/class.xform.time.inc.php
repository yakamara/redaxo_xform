<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_time extends rex_xform_abstract
{

  function preValidateAction()
  {
    if (is_array($this->getValue())) {
      $a = $this->getValue();

      $hour = (int) @$a['hour'];
      $min = (int) @$a['min'];

      $r =
        str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' .
        str_pad($min, 2, '0', STR_PAD_LEFT) . ':00';

      $this->setValue($r);

    }
  }


  function enterObject()
  {

    $r = $this->getValue();

    $hour = '00';
    $min = '00';

    if ($r != '') {
      $r = explode(':', $r);

      if (count($r) == 1) {
        $hour = (int) substr($r[0], 0, 2);
        $min = (int) substr($r[0], 2, 2);

      } else {
        if (isset($r[0])) {
          $hour = (int) substr($r[0], 0, 2);
        }
        if (isset($r[1])) {
          $min  = (int) substr($r[1], 0, 2);
        }
      }

    }

    $hour = str_pad($hour, 2, '0', STR_PAD_LEFT);
    $min = str_pad($min, 2, '0', STR_PAD_LEFT);

    $isotime = $hour . ':' . $min . ':00';



    $wc = '';
    if (isset($this->params['warning'][$this->getId()])) {
      $wc = $this->params['warning'][$this->getId()];
    }

    $this->params['value_pool']['email'][$this->getName()] = $isotime;
    $this->params['value_pool']['sql'][$this->getName()] = $isotime;

    // ------------- hour

    $hsel = new rex_select;
    $hsel->setName($this->getFieldName('hour'));
    $hsel->setStyle('" class="' . $wc);
    $hsel->setId($this->getFieldId('hour'));
    $hsel->setSize(1);

    $mmm = array();
    if ($this->getElement(3) != '')
      $mmm = explode(',', trim($this->getElement(3)));
    if (count($mmm) > 0) {
      foreach ($mmm as $m) {
        $hsel->addOption($m, $m);
      }
    } else {
      for ($i = 0; $i < 24; $i++) {
        $hsel->addOption(str_pad($i, 2, '0', STR_PAD_LEFT), str_pad($i, 2, '0', STR_PAD_LEFT));
      }
    }
    $hsel->setSelected($hour);
    $hour_out = $hsel->get();

    // ------------- min

    $msel = new rex_select;
    $msel->setName($this->getFieldName('min'));
    $msel->setStyle('" class="' . $wc);
    $msel->setId($this->getFieldId('min'));
    $msel->setSize(1);
    $mmm = array();
    if ($this->getElement(4) != '')
      $mmm = explode(',', trim($this->getElement(4)));

    if (count($mmm) > 0) {
      foreach ($mmm as $m) {
        $msel->addOption($m, $m);
      }
    } else {
      for ($i = 0; $i < 60; $i++) {
        $msel->addOption(str_pad($i, 2, '0', STR_PAD_LEFT), str_pad($i, 2, '0', STR_PAD_LEFT));
      }
    }
    $msel->setSelected($min);

    $min_out = $msel->get();

    // -------------

    $out = '
    <p class="' . $this->getHTMLClass() . '" id="' . $this->getHTMLId() . '">
          <label class="select" for="' . $this->getFieldId() . '" >' . $this->getLabel() . '</label>';

    $format = $this->getElement(5);
    if ($format == '')
      $format = '###H###h ###M###m';

    $format = str_replace('###H###', $hour_out, $format);
    $format = str_replace('###M###', $min_out, $format);

    $out .= $format;
    $out .= '</p>';

    $this->params['form_output'][$this->getId()] = $out;
  }

  function getDescription()
  {
    return 'time -> Beispiel: time|name|label|[stundenraster 0,1,2,3,4,5]|[minutenraster 00,15,30,45]|[Anzeigeformat ###H###h ###M###m]';
  }


  function getDefinitions()
  {
    return array(
      'type' => 'value',
      'name' => 'time',
      'values' => array(
        array( 'type' => 'name',   'label' => 'Feld' ),
        array( 'type' => 'text',   'label' => 'Bezeichnung'),
        array( 'type' => 'text',   'label' => '[Stundenraster]'),
        array( 'type' => 'text',   'label' => '[Minutenraster]'),
        array( 'type' => 'text',   'label' => '[Anzeigeformat ###H###h ###M###m]'),
      ),
      'description' => 'Uhrzeitfeld Eingabe',
      'dbtype' => 'time'
    );

  }

  static function getListValue($params)
  {
    global $I18N;
    $format = $I18N->msg('xform_format_time');
    if (($d = DateTime::createFromFormat('H:i:s', $params['subject'])) && $d->format('H:i:s') == $params['subject'])
      return $d->format($format);
    return '[' . $params['subject'] . ']';
  }


}
