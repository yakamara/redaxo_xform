<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_be_mediapool extends rex_xform_abstract
{

  function enterObject()
  {
    global $REX;

    if (!isset($REX['xform_classes_be_mediapool']))
      $REX['xform_classes_be_mediapool'] = 0;

    $REX['xform_classes_be_mediapool']++;

    $i = $REX['xform_classes_be_mediapool'];

    if ($this->getValue() == '' && !$this->params['send'])
      $this->setValue($this->getElement(3));

    $wc = '';
    if (isset($this->params['warning'][$this->getId()]))
      $wc = $this->params['warning'][$this->getId()];

      $this->params['form_output'][$this->getId()] = '
    <div class="xform-element ' . $this->getHTMLClass() . '">
        <label class="text ' . $wc . '" for="' . $this->getFieldId() . '" >' . $this->getLabel() . '</label>

      <div class="rex-widget">
          <div class="rex-widget-media">
            <p class="rex-widget-field">
              <input type="text" class="text ' . $wc . '" name="' . $this->getFieldName() . '" id="REX_MEDIA_' . $i . '" readonly="readonly" value="' . htmlspecialchars(stripslashes($this->getValue())) . '" />
            </p>
            <p class="rex-widget-icons rex-widget-1col">
              <span class="rex-widget-column rex-widget-column-first">
                <a href="#" class="rex-icon-file-open" onclick="openREXMedia(' . $i . ',\'\');return false;" title="Medium auswählen"></a>
                <a href="#" class="rex-icon-file-add" onclick="addREXMedia(' . $i . ');return false;" title="Neues Medium hinzufügen"></a>
                <a href="#" class="rex-icon-file-delete" onclick="deleteREXMedia(' . $i . ');return false;" title="Ausgewähltes Medium löschen"></a>
                <a href="#" class="rex-icon-file-view" onclick="viewREXMedia(' . $i . ');return false;" title="Medium auswählen"></a>
              </span>
            </p>
            <div class="rex-media-preview"></div>
          </div>
        </div>
        <div class="rex-clearer"></div>
    </div>
  ';

    $this->params['value_pool']['email'][$this->getElement(1)] = stripslashes($this->getValue());
    if ($this->getElement(4) != 'no_db') $this->params['value_pool']['sql'][$this->getElement(1)] = $this->getValue();
  }

  function getDescription()
  {
    return 'be_mediapool -> Beispiel: be_mediapool|name|label|defaultwert|no_db';
  }

  function getDefinitions()
  {
    return array(
      'type' => 'value',
      'name' => 'be_mediapool',
      'values' => array(
        array( 'type' => 'name',   'label' => 'Name' ),
        array( 'type' => 'text',    'label' => 'Bezeichnung'),
        array( 'type' => 'text',     'label' => 'Defaultwert'),
      ),
      'description' => 'Mediafeld, welches eine Datei aus dem Medienpool holt',
      'dbtype' => 'text'
    );
  }


  static function getListValue($params)
  {
    $return = $params['subject'];
    if (strlen($return) > 16) {
      $return = '<span style="white-space:nowrap;" title="' . htmlspecialchars($return) . '">' . substr($return, 0, 6) . ' ... ' . substr($return, -6) . '</span>';
    }
    return $return;

  }



}
