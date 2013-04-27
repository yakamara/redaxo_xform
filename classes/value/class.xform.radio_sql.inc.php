<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_radio_sql extends rex_xform_abstract
{

  function enterObject()
  {

    $SEL = new rex_radio();
    $SEL->setId($this->getHTMLId());

    $SEL->setName($this->getFieldName());

    $sql = $this->getElement(3);

    $teams = rex_sql::factory();
    $teams->debugsql = $this->params['debug'];
    $teams->setQuery($sql);

    $sqlnames = array();

    foreach ($teams->getArray() as $t) {
      $v = $t['name'];
      $k = $t['id'];
      $SEL->addOption($this->getLabelStyle($v), $k);
      $sqlnames[$k] = $t['name'];
    }

    $wc = '';
    if (isset($this->params['warning'][$this->getId()]))
      $wc = $this->params['warning'][$this->getId()];

    $SEL->setStyle(' class="select ' . $wc . '"');

    if ($this->getElement(4) != '') $this->setValue($this->getElement(4));

    if (!is_array($this->getValue())) {
      $this->setValue(explode(',', $this->getValue()));
    }

    foreach ($this->getValue() as $v) {
      $SEL->setSelected($v);
    }

    $this->params['form_output'][$this->getId()] = '
      <p class="formradio formlabel-' . $this->getName() . '"  id="' . $this->getHTMLId() . '">
        <label class="radio ' . $wc . '" for="' . $this->getHTMLId() . '" >' . $this->getLabel() . '</label>
        ' . $SEL->get() . '
      </p>';

    $this->setValue(implode(',', $this->getValue()));

    $this->params['value_pool']['email'][$this->getName()] = stripslashes($this->getValue());
    if ($this->getElement(5) != 'no_db') {
      $this->params['value_pool']['sql'][$this->getName()] = $this->getValue();
    }
  }

  function getDescription()
  {
    return 'radio_sql -> Beispiel: select_sql|name|label|select id,name from table order by name|[defaultvalue]|[no_db]|';
  }



}
