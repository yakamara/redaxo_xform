<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_date extends rex_xform_abstract
{

  function preValidateAction()
  {

    if ($this->getElement(6) == 1 && $this->params['send'] == 0 && $this->params['main_id'] < 1) {
      $this->setValue(date('Y-m-d'));

    }

    if (is_array($this->getValue())) {
      $a = $this->getValue();

      $year = (int) substr(@$a['year'], 0, 4);
      $month = (int) substr(@$a['month'], 0, 2);
      $day = (int) substr(@$a['day'], 0, 2);

      $r =
        str_pad($year, 4, '0', STR_PAD_LEFT) . '-' .
        str_pad($month, 2, '0', STR_PAD_LEFT) . '-' .
        str_pad($day, 2, '0', STR_PAD_LEFT);

      $this->setValue($r);
    }
  }


  function enterObject()
  {

    $r = $this->getValue();

    $day = '00';
    $month = '00';
    $year = '0000';

    if ($r != '') {

      if (strlen($r) == 8) {

        // 20000101
        $year = (int) substr($this->getValue(), 0, 4);
        $month = (int) substr($this->getValue(), 4, 2);
        $day = (int) substr($this->getValue(), 6, 2);

      } else {

        // 2000-01-01
        $year = (int) substr($this->getValue(), 0, 4);
        $month = (int) substr($this->getValue(), 5, 2);
        $day = (int) substr($this->getValue(), 8, 2);

      }
    }

    $year = str_pad($year, 4, '0', STR_PAD_LEFT);
    $month = str_pad($month, 2, '0', STR_PAD_LEFT);
    $day = str_pad($day, 2, '0', STR_PAD_LEFT);

    $isodatum = sprintf('%04d-%02d-%02d', $year, $month, $day);

    $wc = '';
    if (isset($this->params['warning'][$this->getId()])) {
      $wc = ' ' . $this->params['warning'][$this->getId()];
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

    // -------------

    $out = '
    <p class="' . $this->getHTMLClass() . '" id="' . $this->getHTMLId() . '">
          <label class="select' . $wc . '" for="' . $this->getFieldId() . '" >' . $this->getLabel() . '</label>';

    $format = $this->getElement(5);
    if ($format == '')
      $format = '###Y###-###M###-###D###';

    $format = str_replace('###Y###', $year_out, $format);
    $format = str_replace('###M###', $month_out, $format);
    $format = str_replace('###D###', $day_out, $format);

    $out .= $format;
    $out .= '</p>';

    $this->params['form_output'][$this->getId()] = $out;
  }


  function getDescription()
  {
    return 'date -> Beispiel: date|name|label|jahrstart|jahrsende|[Anzeigeformat###Y###-###M###-###D###]|[1/Aktuelles Datum voreingestellt]';
  }

  function getDefinitions()
  {
    return array(
      'type' => 'value',
      'name' => 'date',
      'values' => array(
        array( 'type' => 'name', 'label' => 'Feld' ),
        array( 'type' => 'text', 'label' => 'Bezeichnung'),
        array( 'type' => 'text', 'label' => '[Startjahr]'),
        array( 'type' => 'text', 'label' => '[Endjahr]'),
        array( 'type' => 'text', 'label' => '[Anzeigeformat###Y###-###M###-###D###]]'),
        array( 'type' => 'boolean', 'label' => 'Aktuelles Datum voreingestellt'),
      ),
      'description' => 'Datums Eingabe',
      'dbtype' => 'date'
    );
  }

  static function getListValue($params)
  {
    global $I18N;
    $format = $I18N->msg('xform_format_date');
    if (($d = DateTime::createFromFormat('Y-m-d', $params['subject'])) && $d->format('Y-m-d') == $params['subject'])
      return $d->format($format);
    return '[' . $params['subject'] . ']';
  }

}
