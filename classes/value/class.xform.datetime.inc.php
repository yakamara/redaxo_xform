<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_datetime extends rex_xform_abstract
{


  function preValidateAction()
  {
    if (is_array($this->getValue())) {
      $a = $this->getValue();

      $year = (int) substr(@$a['year'], 0, 4);
      $month = (int) substr(@$a['month'], 0, 2);
      $day = (int) substr(@$a['day'], 0, 2);
      $hour = (int) substr(@$a['hour'], 0, 2);
      $min = (int) substr(@$a['min'], 0, 2);

      $r =
        str_pad($year, 4, '0', STR_PAD_LEFT) . '-' .
        str_pad($month, 2, '0', STR_PAD_LEFT) . '-' .
        str_pad($day, 2, '0', STR_PAD_LEFT) . ' ' .
        str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' .
        str_pad($min, 2, '0', STR_PAD_LEFT) . ':00';

      $this->setValue($r);
    }
  }


  function enterObject()
  {

    $r = $this->getValue();

    $day = '00';
    $month = '00';
    $year = '0000';
    $hour = '00';
    $min = '00';

    if ($r != '') {
      $year = (int) substr($this->getValue(), 0, 4);
      $month = (int) substr($this->getValue(), 5, 2);
      $day = (int) substr($this->getValue(), 8, 2);
      $hour = (int) substr($this->getValue(), 11, 2);
      $min   = (int) substr($this->getValue(), 14, 2);
    }

    $year = str_pad($year, 4, '0', STR_PAD_LEFT);
    $month = str_pad($month, 2, '0', STR_PAD_LEFT);
    $day = str_pad($day, 2, '0', STR_PAD_LEFT);
    $hour = str_pad($hour, 2, '0', STR_PAD_LEFT);
    $min = str_pad($min, 2, '0', STR_PAD_LEFT);

    $isodatum = sprintf('%04d-%02d-%02d %02d:%02d:%02d', $year, $month, $day, $hour, $min, 0);

    $wc = '';
    if (isset($this->params['warning'][$this->getId()])) {
      $wc = $this->params['warning'][$this->getId()];
    }

    $this->params['value_pool']['email'][$this->getName()] = $isodatum;
    $this->params['value_pool']['sql'][$this->getName()] = $isodatum;


    // ------------- year

    $year_start = (int) $this->getElement(3);
    $year_end = (int) $this->getElement(4);
    if ($year_start == 0) {
      $year_start = 1980;
    }
    if ($year_end == 0) {
      $year_end = 2020;
    }
    if ($year_end < $year_start) {
      $year_end = $year_start;
    }

    $ysel = new rex_select;
    $ysel->setName($this->getFieldName('year'));
    $ysel->setStyle('" class="' . $wc);
    $ysel->setId($this->getFieldId('year'));
    $ysel->setSize(1);
    $ysel->addOption('----', '0000');
    for ($i = $year_start; $i <= $year_end; $i++) {
      $ysel->addOption($i, $i);
    }
    $ysel->setSelected($year);
    $year_out = $ysel->get();


    // ------------- month

    $msel = new rex_select;
    $msel->setName($this->getFieldName('month'));
    $msel->setStyle('" class="' . $wc);
    $msel->setId($this->getFieldId('month'));
    $msel->setSize(1);
    $msel->addOption('--', '00');
    for ($i = 1; $i < 13; $i++) {
      $msel->addOption($i, $i);
    }
    $msel->setSelected($month);
    $month_out = $msel->get();


    // ------------- day

    $dsel = new rex_select;
    $dsel->setName($this->getFieldName('day'));
    $dsel->setStyle('" class="' . $wc);
    $dsel->setId($this->getFieldId('day'));
    $dsel->setSize(1);
    $dsel->addOption('--', '00');
    for ($i = 1; $i < 32; $i++) {
      $dsel->addOption($i, $i);
    }
    $dsel->setSelected($day);
    $day_out = $dsel->get();


    // ------------- hour

    $hsel = new rex_select;
    $hsel->setName($this->getFieldName('hour'));
    $hsel->setStyle('" class="' . $wc);
    $hsel->setId($this->getFieldId('hour'));
    $hsel->setSize(1);
    for ($i = 0; $i < 24; $i++) {
      $hsel->addOption(str_pad($i, 2, '0', STR_PAD_LEFT), str_pad($i, 2, '0', STR_PAD_LEFT));
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
    if ($this->getElement(5) != '')
      $mmm = explode(',', trim($this->getElement(5)));

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
          <label class="select" for="' . $this->getFieldId() . '" >' . $this->getElement(2) . '</label>';

    $format = $this->getElement(6);
    if ($format == '')
      $format = '###Y###-###M###-###D### ###H###h ###I###m';

    $format = str_replace('###Y###', $year_out, $format);
    $format = str_replace('###M###', $month_out, $format);
    $format = str_replace('###D###', $day_out, $format);
    $format = str_replace('###H###', $hour_out, $format);
    $format = str_replace('###I###', $min_out, $format);

    $out .= $format;
    $out .= '</p>';

    $this->params['form_output'][$this->getId()] = $out;
  }


  function getDescription()
  {
    return 'datetime -> Beispiel: datetime|name|label|jahrstart|jahrsende|minutenformate 00,15,30,45|[Anzeigeformat###Y###-###M###-###D### ###H###h ###I###m]';
  }


  function getDefinitions()
  {
    return array(
      'type' => 'value',
      'name' => 'datetime',
      'values' => array(
        array( 'type' => 'name', 'label' => 'Feld' ),
        array( 'type' => 'text', 'label' => 'Bezeichnung'),
        array( 'type' => 'text', 'label' => 'Startjahr'),
        array( 'type' => 'text', 'label' => 'Endjahr'),
        array( 'type' => 'text', 'label' => '[Minutenformate]'),
        array( 'type' => 'text', 'label' => '[Anzeigeformat###Y###-###M###-###D### ###H###h ###I###m]'),
      ),
      'description' => 'Datum & Uhrzeit Eingabe',
      'dbtype' => 'datetime'
    );
  }

  static function getListValue($params)
  {
    global $I18N;
    $format = $I18N->msg('xform_format_datetime');
    if (($d = DateTime::createFromFormat('Y-m-d H:i:s', $params['subject'])) && $d->format('Y-m-d H:i:s') == $params['subject'])
      return $d->format($format);
    return '[' . $params['subject'] . ']';
  }

}
