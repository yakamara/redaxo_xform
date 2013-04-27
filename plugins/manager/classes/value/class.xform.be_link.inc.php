<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_be_link extends rex_xform_abstract
{

  function enterObject()
  {
    global $REX;

    if (!isset($REX['xform_classes_be_link'])) {
      $REX['xform_classes_be_link'] = 0;
    }

    $REX['xform_classes_be_link']++;

    $i = $REX['xform_classes_be_link'];

    if ($this->getValue() == '' && !$this->params['send']) {
      $this->setValue($this->getElement(3));
    }

    $wc = '';
    if (isset($this->params['warning'][$this->getId()])) {
      $wc = $this->params['warning'][$this->getId()];
    }

    $linkname = '';
    if ($this->getValue() != '' && $a = OOArticle::getArticleById($this->getValue())) {
      $linkname = $a->getName();
    }

      $this->params['form_output'][$this->getId()] = '

    <div class="xform-element formbe_mediapool ' . $this->getHTMLClass() . '">
        <label class="text ' . $wc . '" for="' . $this->getFieldId() . '" >' . $this->getLabel() . '</label>
    <div class="rex-widget">
    <div class="rex-widget-link">
      <p class="rex-widget-field">
        <input type="hidden" name="' . $this->getFieldName() . '" id="LINK_' . $i . '" value="' . $this->getValue() . '" />
        <input type="text" size="30" name="LINK_' . $i . '_NAME" value="' . htmlspecialchars($linkname) . '" id="LINK_' . $i . '_NAME" readonly="readonly" />
      </p>

       <p class="rex-widget-icons rex-widget-1col">
        <span class="rex-widget-column rex-widget-column-first">
          <a href="#" class="rex-icon-file-open" onclick="openLinkMap(\'LINK_' . $i . '\', \'&clang=0&category_id=1\');return false;" title="Link auswählen" tabindex="21"></a>
          <a href="#" class="rex-icon-file-delete" onclick="deleteREXLink(' . $i . ');return false;" title="Ausgewählten Link löschen" tabindex="22"></a>
        </span>
      </p>
    </div>
    </div>
    <div class="rex-clearer"></div>
    </div>
  ';

    $this->params['value_pool']['email'][$this->getName()] = stripslashes($this->getValue());
    if ($this->getElement(4) != 'no_db') {
      $this->params['value_pool']['sql'][$this->getName()] = $this->getValue();
    }
  }


  function getDescription()
  {
    return 'be_link -> Beispiel: be_link|name|label|defaultwert|no_db';
  }


  function getDefinitions()
  {
    return array(
      'type' => 'value',
      'name' => 'be_link',
      'values' => array(
        array( 'type' => 'name',   'label' => 'Name' ),
        array( 'type' => 'text',   'label' => 'Bezeichnung'),
      ),
      'description' => 'Hiermit kann man einen Link zu einem REDAXO Artikel setzen.',
      'dbtype' => 'text'
    );
  }


  static function getListValue($params)
  {
    if (intval($params['value']) < 1) {
      return '-';
    }

    if (($art = new rex_article($params['value']))) {
      return $art->getValue('name');
    } else {
      return 'article ' . $params['value'] . ' not found';
    }
  }

}
