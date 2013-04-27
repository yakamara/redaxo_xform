<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_be_medialist extends rex_xform_abstract
{

  function enterObject()
  {

    global $I18N;

    static $tmp_medialist = 0;

    // if (!isset($tmp_medialist)) $tmp_medialist = 0;
    $tmp_medialist++;

    $ausgabe = '';
    $options = '';
    $medialistarray = explode(',', $this->getValue());
    if (is_array($medialistarray)) {
      for ($j = 0; $j < count($medialistarray); $j++) {
        if (current($medialistarray) != '')
          $options .= "<option value='" . current($medialistarray) . "'>" . current($medialistarray) . "</option>\n";
        next($medialistarray);
      }
    }

    // Preview
    (intval($this->getElement(3)) == 1) ? $widgetclass = 'rex-widget-medialist rex-widget-preview rex-widget-preview-image-manager' : $widgetclass = 'rex-widget-medialist';

    // Medialist arguments
    $args = '';
    // Category ID
    $argvalue = intval($this->getElement(4));
    if ($argvalue > 0)
        $args .= '&amp;rex_file_category=' . $argvalue;

    // Types
    $argvalue = trim($this->getElement(5));
    if (!empty($argvalue)) {
        $argvalue = str_replace(',', '%2C', $argvalue);
        $args .= '&amp;args[types]=' . $argvalue;
    }

    (empty($args)) ? $tmp_medialist_open = $tmp_medialist : $tmp_medialist_open = $tmp_medialist . ',\'' . $args . '\'';

    $ausgabe .= '
    <div class="rex-widget">
      <div class="' . $widgetclass . '">
        <input type="hidden" name="' . $this->getFieldName() . '" id="REX_MEDIALIST_' . $tmp_medialist . '" value="' . htmlspecialchars(stripslashes($this->getValue())) . '" />
        <p class="rex-widget-field">
          <select name="MEDIALIST_SELECT[' . $tmp_medialist . ']" id="REX_MEDIALIST_SELECT_' . $tmp_medialist . '" size="8">
          ' . $options . '
          </select>
        </p>

        <p class="rex-widget-icons rex-widget-2col">
          <span class="rex-widget-column rex-widget-column-first">
            <a href="#" class="rex-icon-file-top" onclick="moveREXMedialist(' . $tmp_medialist . ',\'top\');return false;" title="' . $I18N->msg('var_medialist_move_top') . '"></a>
            <a href="#" class="rex-icon-file-up" onclick="moveREXMedialist(' . $tmp_medialist . ',\'up\');return false;" title="' . $I18N->msg('var_medialist_move_up') . '"></a>
            <a href="#" class="rex-icon-file-down" onclick="moveREXMedialist(' . $tmp_medialist . ',\'down\');return false;" title="' . $I18N->msg('var_medialist_move_down') . '"></a>
            <a href="#" class="rex-icon-file-bottom" onclick="moveREXMedialist(' . $tmp_medialist . ',\'bottom\');return false;" title="' . $I18N->msg('var_medialist_move_bottom') . '"></a>
          </span>
          <span class="rex-widget-column">
            <a href="#" class="rex-icon-file-open" onclick="openREXMedialist(' . $tmp_medialist_open . ');return false;" title="' . $I18N->msg('var_media_open') . '"></a>
            <a href="#" class="rex-icon-file-add" onclick="addREXMedialist(' . $tmp_medialist . ');return false;" title="' . $I18N->msg('var_media_new') . '"></a>
            <a href="#" class="rex-icon-file-delete" onclick="deleteREXMedialist(' . $tmp_medialist . ');return false;" title="' . $I18N->msg('var_media_remove') . '"></a>
            <a href="#" class="rex-icon-file-view" onclick="viewREXMedialist(' . $tmp_medialist . ');return false;" title="' . $I18N->msg('var_media_open') . '"></a>
          </span>
        </p>
        <div class="rex-media-preview"></div>
      </div>
    </div>
    <div class="rex-clearer"></div>
    ';

    $wc = '';
    if (isset($this->params['warning'][$this->getId()])) $wc = $this->params['warning'][$this->getId()];

    $this->params['form_output'][$this->getId()] = '
      <div class="xform-element formbe_medialist ' . $this->getHTMLClass() . '">
        <label class="text ' . $wc . '" for="' . $this->getFieldId() . '" >' . $this->getLabel() . '</label>
        ' . $ausgabe . '
      </div>';

    $this->params['value_pool']['email'][$this->getElement(1)] = stripslashes($this->getValue());
    if ($this->getElement(6) != 'no_db') $this->params['value_pool']['sql'][$this->getElement(1)] = $this->getValue();

  }

  function getDescription()
  {
    return 'be_medialist -> Beispiel: be_medialist|name|label|preview|category|types|no_db|';
  }

  function getDefinitions()
  {
    return array(
      'type' => 'value',
      'name' => 'be_medialist',
      'values' => array(
        array( 'type' => 'name',   'label' => 'Name' ),
        array( 'type' => 'text',   'label' => 'Bezeichnung'),
        array( 'type' => 'text',   'label' => 'Preview (0/1) (opt)'),
        array( 'type' => 'text',   'label' => 'Medienpool Kategorie (opt)'),
        array( 'type' => 'text',   'label' => 'Types (opt)')
      ),
      'description' => 'Medialiste, welches Dateien aus dem Medienpool holt',
      'dbtype' => 'text'
    );
  }

  static function getListValue($params)
  {

    $return = $params['subject'];

    if ($return != '' && $returns = explode(',', $return)) {
      $return = array();
      foreach ($returns as $r) {
        if (strlen($r) > 16) {
          $return[] = '<span style="white-space:nowrap;" title="' . htmlspecialchars($r) . '">' . substr($r, 0, 6) . ' ... ' . substr($r, -6) . '</span>';
        }
      }
      $return = implode('<br />', $return);
    }
    return $return;
  }
}
