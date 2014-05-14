<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform
{

    function rex_xform()
    {
        global $REX;

        require_once $REX['INCLUDE_PATH'] . '/addons/xform/classes/basic/' . 'class.xform.base.abstract.inc.php';
        require_once $REX['INCLUDE_PATH'] . '/addons/xform/classes/basic/' . 'class.xform.value.abstract.inc.php';
        require_once $REX['INCLUDE_PATH'] . '/addons/xform/classes/basic/' . 'class.xform.action.abstract.inc.php';
        require_once $REX['INCLUDE_PATH'] . '/addons/xform/classes/basic/' . 'class.xform.validate.abstract.inc.php';

        $this->objparams = array();

        // --------------------------- editable via objparams|key|newvalue

        $this->objparams['answertext'] = '';
        $this->objparams['submit_btn_label'] = 'Abschicken';
        $this->objparams['submit_btn_show'] = true;

        $this->objparams['values'] = array();
        $this->objparams['validates'] = array();
        $this->objparams['actions'] = array();

        $this->objparams['error_class'] = 'form_warning';
        $this->objparams['unique_error'] = '';
        $this->objparams['unique_field_warning'] = 'not unique';

        $this->objparams['article_id'] = '';
        $this->objparams['clang'] = '';

        $this->objparams['real_field_names'] = false;

        $this->objparams['form_method'] = 'post';
        $this->objparams['form_action'] = 'index.php';
        $this->objparams['form_anchor'] = '';
        $this->objparams['form_showformafterupdate'] = 0;
        $this->objparams['form_show'] = true;
        $this->objparams['form_name'] = 'formular';
        $this->objparams['form_id'] = 'form_formular';
        $this->objparams['form_class'] = 'rex-xform';
        $this->objparams['form_wrap_id'] = 'rex-xform';

        $this->objparams['form_label_type'] = 'html'; // plain

        $this->objparams['form_skin'] = 'default';

        $this->objparams['actions_executed'] = false;
        $this->objparams['postactions_executed'] = false;

        $this->objparams['Error-occured'] = '';
        $this->objparams['Error-Code-EntryNotFound'] = 'ErrorCode - EntryNotFound';
        $this->objparams['Error-Code-InsertQueryError'] = 'ErrorCode - InsertQueryError';

        $this->objparams['getdata'] = false;


        // --------------------------- do not edit

        $this->objparams['object_path'] = $REX['INCLUDE_PATH'] . '/addons/xform/classes/';
        $this->objparams['debug'] = false;

        $this->objparams['form_data'] = '';
        $this->objparams['output'] = '';

        $this->objparams['main_where'] = ''; // z.B. id=12
        $this->objparams['main_id'] = -1; // unique ID
        $this->objparams['main_table'] = ''; // for db and unique
        $this->objparams['sql_object'] = null;

        $this->objparams['form_hiddenfields'] = array();

        $this->objparams['warning'] = array();
        $this->objparams['warning_messages'] = array();

        $this->objparams['fieldsets_opened'] = 0; //

        $this->objparams['form_elements'] = array();
        $this->objparams['form_output'] = array();

        $this->objparams['value_pool'] = array();
        $this->objparams['value_pool']['email'] = array();
        $this->objparams['value_pool']['sql'] = array();

        $this->objparams['value'] = array(); // reserver for classes - $this->objparams["value"]["text"] ...
        $this->objparams['validate'] = array(); // reserver for classes
        $this->objparams['action'] = array(); // reserver for classes

        $this->objparams['this'] = $this;

    }

    function setDebug($s = true)
    {
        $this->objparams['debug'] = $s;
    }

    function setFormData($form_definitions, $refresh = true)
    {
        $this->setObjectparams('form_data', $form_definitions, $refresh);

        $this->objparams['form_data'] = str_replace("\n\r", "\n" , $this->objparams['form_data']); // Die Definitionen
        $this->objparams['form_data'] = str_replace("\r", "\n" , $this->objparams['form_data']); // Die Definitionen

        if (!is_array($this->objparams['form_elements'])) {
            $this->objparams['form_elements'] = array();
        }

        $form_elements_tmp = array();
        $form_elements_tmp = explode("\n", $this->objparams['form_data']);

        // CLEAR EMPTY AND COMMENT LINES
        foreach ($form_elements_tmp as $form_element) {
            $form_element = trim($form_element);
            if ($form_element != '' && $form_element[0] != '#' && $form_element[0] != '/') {
                $this->objparams['form_elements'][] = explode('|', trim($form_element));
            }
        }
    }

    function setValueField($type = '', $values = array())
    {
        $values = array_merge(array($type), $values);
        $this->objparams['form_elements'][] = $values;
    }

    function setValidateField($type = '', $values = array())
    {
        $values = array_merge(array('validate', $type), $values);
        $this->objparams['form_elements'][] = $values;
    }

    function setActionField($type = '', $values = array())
    {
        $values = array_merge(array('action', $type), $values);
        $this->objparams['form_elements'][] = $values;
    }

    function setRedaxoVars($aid = '', $clang = '', $params = array())
    {
        global $REX;

        if ($clang == '') {
            $clang = $REX['CUR_CLANG'];
        }
        if ($aid == '') {
            $aid = $REX['ARTICLE_ID'];
        }

        $this->setHiddenField('article_id', $aid);
        $this->setHiddenField('clang', $clang);

        $this->setObjectparams('form_action', rex_getUrl($aid, $clang, $params));
    }

    function setHiddenField($k, $v)
    {
        $this->objparams['form_hiddenfields'][$k] = $v;
    }

    function setObjectparams($k, $v, $refresh = true)
    {
        if (!$refresh && isset($this->objparams[$k])) {
            $this->objparams[$k] .= $v;
        } else {
            $this->objparams[$k] = $v;
        }
        return $this->objparams[$k];
    }

    function getObjectparams($k)
    {
        if (!isset($this->objparams[$k])) {
            return false;
        }
        return $this->objparams[$k];
    }

    function getForm()
    {

        global $REX;

        $this->objparams['values'] = array();
        $this->objparams['validates'] = array();
        $this->objparams['actions'] = array();

        $this->objparams['send'] = 0;

        // *************************************************** VALUE OBJECT INIT

        $rows = count($this->objparams['form_elements']);

        for ($i = 0; $i < $rows; $i++) {

            $element = $this->objparams['form_elements'][$i];

            if ($element[0] == 'validate') {

                foreach ($REX['ADDON']['xform']['classpaths']['validate'] as $validate_path) {
                    $classname = 'rex_xform_validate_' . trim($element[1]);
                    if (@include_once ($validate_path . 'class.xform.validate_' . trim($element[1]) . '.inc.php')) {
                        $ValidateObject = new $classname;
                        $ValidateObject->loadParams($this->objparams, $element);
                        $this->objparams['validates'][$element[1]][] = $ValidateObject;
                        break;
                    }
                }

            } elseif ($element[0] == 'action') {
                foreach ($REX['ADDON']['xform']['classpaths']['action'] as $action_path) {
                    $classname = 'rex_xform_action_' . trim($element[1]);
                    if (@include_once ($action_path . 'class.xform.action_' . trim($element[1]) . '.inc.php')) {
                        $this->objparams['actions'][$i] = new $classname;
                        $this->objparams['actions'][$i]->loadParams($this->objparams, $element);
                        break;
                    }
                }

            } else {
                foreach ($REX['ADDON']['xform']['classpaths']['value'] as $value_path) {
                    $classname = 'rex_xform_' . trim($element[0]);
                    if (@include_once ($value_path . 'class.xform.' . trim($element[0]) . '.inc.php')) {
                        $this->objparams['values'][$i] = new $classname;
                        $this->objparams['values'][$i]->loadParams($this->objparams, $element);
                        $this->objparams['values'][$i]->setId($i);
                        $this->objparams['values'][$i]->init();
                        break;
                    }

                }
                $rows = count($this->objparams['form_elements']); // if elements have changed -> new rowcount
            }

            // special case - submit button shows up by default
            if (($rows - 1) == $i && $this->objparams['submit_btn_show']) {
                $rows++;
                $this->objparams['form_elements'][] = array('submit', 'rex_xform_submit', $this->objparams['submit_btn_label'], 'no_db');
                $this->objparams['submit_btn_show'] = false;
            }

        }

        foreach ($this->objparams['values'] as $ValueObject) {
            $ValueObject->setValue($this->getFieldValue($ValueObject->getId(), '', $ValueObject->getName()));
            $ValueObject->setObjects($this->objparams['values']);
        }

        // *************************************************** OBJECT PARAM "send"
        if ($this->getFieldValue('send', '', 'send') == '1') {
            $this->objparams['send'] = 1;
        }

        // *************************************************** PRE VALUES
        // Felder aus Datenbank auslesen - Sofern Aktualisierung
        if ($this->objparams['getdata']) {
            if (!$this->objparams['sql_object'] instanceof rex_sql) {
                $this->objparams['sql_object'] = rex_sql::factory();
                $this->objparams['sql_object']->debugsql = $this->objparams['debug'];
                $this->objparams['sql_object']->setQuery('SELECT * from ' . $this->objparams['main_table'] . ' WHERE ' . $this->objparams['main_where']);
            }
            if ($this->objparams['sql_object']->getRows() > 1 || $this->objparams['sql_object']->getRows() == 0) {
                $this->objparams['warning'][] = $this->objparams['Error-Code-EntryNotFound'];
                $this->objparams['warning_messages'][] = $this->objparams['Error-Code-EntryNotFound'];
                $this->objparams['form_show'] = true;
                unset($this->objparams['sql_object']);
            }
        }


        // ----- Felder mit Werten fuellen, fuer wiederanzeige
        // Die Value Objekte werden mit den Werten befuellt die
        // aus dem Formular nach dem Abschicken kommen
        if (!($this->objparams['send'] == 1) && $this->objparams['main_where'] != '') {
            foreach ($this->objparams['values'] as $i => $valueObject) {
                if ($valueObject->getName()) {
                    if (isset($this->objparams['sql_object'])) {
                        $this->setFieldValue($i, @addslashes($this->objparams['sql_object']->getValue($valueObject->getName())), '', $valueObject->getName());
                    }
                }
                $valueObject->setValue($this->getFieldValue($i, '', $valueObject->getName()));
            }
        }


        // *************************************************** VALIDATE OBJEKTE

        // ***** PreValidateActions
        foreach ($this->objparams['values'] as $ValueObject) {
            $ValueObject->preValidateAction();
        }

        foreach ($this->objparams['validates'] as $ValidateType) {
            foreach ($ValidateType as $ValidateObject) {
                $ValidateObject->setObjects($this->objparams['values']);
            }
        }

        // ***** Validieren
        if ($this->objparams['send'] == 1) {
            foreach ($this->objparams['validates'] as $ValidateType) {
                foreach ($ValidateType as $ValidateObject) {
                    $ValidateObject->enterObject();
                }
            }
        }

        // ***** PostValidateActions
        foreach ($this->objparams['values'] as $ValueObject) {
            $ValueObject->postValidateAction();
        }

        // *************************************************** FORMULAR ERSTELLEN

        foreach ($this->objparams['values'] as $ValueObject) {
            $ValueObject->enterObject();
        }

        if ($this->objparams['send'] == 1) {
            foreach ($this->objparams['validates'] as $ValidateType) {
                foreach ($ValidateType as $ValidateObject) {
                    $ValidateObject->postValueAction();
                }
            }
        }

        // ***** PostFormActions
        foreach ($this->objparams['values'] as $ValueObject) {
            $ValueObject->postFormAction();
        }


        // *************************************************** ACTION OBJEKTE

        // ID setzen, falls vorhanden
        if ($this->objparams['main_id'] > 0) {
            $this->objparams['value_pool']['email']['ID'] = $this->objparams['main_id'];
        }

        $hasWarnings = count($this->objparams['warning']) != 0;
        $hasWarningMessages = count($this->objparams['warning_messages']) != 0;

        // ----- Actions
        if ($this->objparams['send'] == 1 && !$hasWarnings && !$hasWarningMessages) {

            $this->objparams['form_show'] = false;
            foreach ($this->objparams['actions'] as $ActionObject) {
                $ActionObject->setObjects($this->objparams['values']);
            }

            foreach ($this->objparams['actions'] as $ActionObject) {
                $ActionObject->execute();
            }
            $this->objparams['actions_executed'] = true;

            // ----- Value - PostActions
            foreach ($this->objparams['values'] as $ValueObject) {
                $ValueObject->postAction($this->objparams['value_pool']['email'], $this->objparams['value_pool']['sql']);
            }
            $this->objparams['postactions_executed'] = true;

        }

        $hasWarnings = count($this->objparams['warning']) != 0;
        $hasWarningMessages = count($this->objparams['warning_messages']) != 0;

        if ($this->objparams['form_showformafterupdate']) {
            $this->objparams['form_show'] = true;
        }

        if ($this->objparams['form_show']) {

            // -------------------- send definition
            $this->setHiddenField($this->getFieldName('send', '', 'send'), 1);

            // -------------------- form start
            if ($this->objparams['form_anchor'] != '') {
                $this->objparams['form_action'] .= '#' . $this->objparams['form_anchor'];
            }

            // -------------------- formOut
            $this->objparams['output'] .= $this->parse('form.tpl.php');

        }

        return $this->objparams['output'];

    }

    static function includeClass($type_id, $class)
    {
        global $REX;

        if (!class_exists('rex_xform_base_abstract')) {
            require_once $REX['INCLUDE_PATH'] . '/addons/xform/classes/basic/class.xform.base.abstract.inc.php';
        }

        $classname = 'rex_xform_' . $type_id . '_' . $class;
        $filename  = 'class.xform.' . $type_id . '_' . $class . '.inc.php';
        switch ($type_id) {
            case 'value':
                if (!class_exists('rex_xform_abstract')) {
                    require_once $REX['INCLUDE_PATH'] . '/addons/xform/classes/basic/class.xform.value.abstract.inc.php';
                }
                $filename  = 'class.xform.' . $class . '.inc.php';
                $classname = 'rex_xform_' . $class;
                break;
            case 'validate':
                if (!class_exists('rex_xform_validate_abstract')) {
                    require_once $REX['INCLUDE_PATH'] . '/addons/xform/classes/basic/class.xform.validate.abstract.inc.php';
                }
                break;
            case 'action':
                if (!class_exists('rex_xform_action_abstract')) {
                    require_once $REX['INCLUDE_PATH'] . '/addons/xform/classes/basic/class.xform.action.abstract.inc.php';
                }
                break;
            default:
                return false;
        }

        if (class_exists($classname)) {
        return $classname;
        }

        foreach ($REX['ADDON']['xform']['classpaths'][$type_id] as $path) {
            @include_once $path . $filename;

            if (class_exists($classname)) {
                return $classname;
            }
        }
        return false;

    }

    function getTemplatePath($template)
    {
        global $REX;

        $templates = (array) $template;
        $skins[$this->objparams['form_skin']] = true;
        $skins['default'] = true;
        foreach ($templates as $template) {
            foreach ($skins as $skin => $_) {
                foreach (array_reverse($REX['ADDON']['xform']['templatepaths']) as $path) {
                    if (file_exists($path . $skin . '/' . $template)) {
                        return $path . $skin . '/' . $template;
                    }
                }
            }
        }

        trigger_error(sprintf('XForm template %s not found', $template), E_USER_WARNING);
    }

    function parse($template, array $params = array())
    {
        global $REX, $I18N;

        extract($params);

        ob_start();
        include $this->getTemplatePath($template);
        return ob_get_clean();
    }

    static function getTypes()
    {
        return array('value', 'validate', 'action');
    }

    function getFieldName($id = '', $k = '', $label = '')
    {
        $label = $this->prepareLabel($label);
        $k = $this->prepareLabel($k);
        if ($this->objparams['real_field_names'] && $label != '') {
            if ($k == '') {
                return $label;
            } else {
                return $label . '[' . $k . ']';
            }
        } else {
            if ($k == '') {
                return 'FORM[' . $this->objparams['form_name'] . '][' . $id . ']';
            } else {
                return 'FORM[' . $this->objparams['form_name'] . '][' . $id . '][' . $k . ']';
            }
        }
    }

    function getFieldValue($id = '', $k = '', $label = '')
    {
        $label = $this->prepareLabel($label);
        $k = $this->prepareLabel($k);
        if ($this->objparams['real_field_names'] && $label != '') {
            if ($k == '' && isset($_REQUEST[$label])) {
                return $_REQUEST[$label];
            } elseif (isset($_REQUEST[$label][$k])) {
                return $_REQUEST[$label][$k];
            }
        } else {
            if ($k == '' && isset($_REQUEST['FORM'][$this->objparams['form_name']][$id])) {
                return $_REQUEST['FORM'][$this->objparams['form_name']][$id];
            } elseif (isset($_REQUEST['FORM'][$this->objparams['form_name']][$id][$k])) {
                return $_REQUEST['FORM'][$this->objparams['form_name']][$id][$k];
            }
        }
    return '';
    }

    function setFieldValue($id = '', $value = '', $k = '', $label = '')
    {
        $label = $this->prepareLabel($label);
        $k = $this->prepareLabel($k);
        if ($this->objparams['real_field_names'] && $label != '') {
            if ($k == '') {
                $_REQUEST[$label] = $value;
            } else {
                $_REQUEST[$label][$k] = $value;
            }
            return;
        } else {
            if ($k == '') {
                $_REQUEST['FORM'][$this->objparams['form_name']][$id] = $value;
            } else {
                $_REQUEST['FORM'][$this->objparams['form_name']][$id][$k] = $value;
            }
        }
    }

    function prepareLabel($label)
    {
        return preg_replace('/[^a-zA-Z\-\_0-9]/', '-', $label);;
    }

    // ----- Hilfsfunktionen -----

    static function unhtmlentities($text)
    {
        return html_entity_decode($text);
    }


    static function showHelp($return = false, $script = false)
    {

        global $REX;

        if (!class_exists('rex_xform_base_abstract')) {
            require_once $REX['INCLUDE_PATH'] . '/addons/xform/classes/basic/class.xform.base.abstract.inc.php';
        }

        $html = '
<ul class="xform root">
    <li class="type value"><strong class="toggler">Value</strong>
    <ul class="xform type value">
    ';

        if (!class_exists('rex_xform_abstract')) {
            require_once $REX['INCLUDE_PATH'] . '/addons/xform/classes/basic/class.xform.value.abstract.inc.php';
        }

        foreach ($REX['ADDON']['xform']['classpaths']['value'] as $pos => $value_path) {
            if ($pos == 1) {
                $html .= '<li class="value extras"><strong class="toggler opened">Value Extras</strong><ul class="xform type value extras">';
            }
            if ($Verzeichniszeiger = opendir($value_path)) {
                $list = array();
                while ($Datei = readdir($Verzeichniszeiger)) {
                    if (preg_match('/^(class.xform)/', $Datei) && !preg_match('/^(class.xform.validate|class.xform.abstract)/', $Datei)) {
                        if (!is_dir($Datei)) {
                            $classname = (explode('.', substr($Datei, 12)));
                            $classname = 'rex_xform_' . $classname[0];
                            if (file_exists($value_path . $Datei)) {
                                include_once $value_path . $Datei;
                                $class = new $classname;
                                $desc = $class->getDescription();
                                if ($desc != '') {
                                    $list[$classname] = '<li>' . $desc . '</li>';
                                }

                            }
                        }
                    }
                }
                ksort($list);
                foreach ($list as $l) {
                    $html .= $l;
                }
                closedir($Verzeichniszeiger);
            }
        }
        if ($pos > 0) {
            $html .= '</ul></li>';
        }
        $html .= '</ul>
    </li>
    <li class="type validate"><strong class="toggler">Validate</strong>
    <ul class="xform type validate">
    ';

        if (!class_exists('rex_xform_validate_abstract')) {
            require_once $REX['INCLUDE_PATH'] . '/addons/xform/classes/basic/class.xform.validate.abstract.inc.php';
        }

        foreach ($REX['ADDON']['xform']['classpaths']['validate'] as $pos => $validate_path) {
            if ($pos == 1) {
                $html .= '<li class="validate extras"><strong class="toggler opened">Validate Extras</strong><ul class="xform type validate extras">';
            }
            if ($Verzeichniszeiger = opendir($validate_path)) {
                $list = array();
                while ($Datei = readdir($Verzeichniszeiger)) {
                    if (preg_match('/^(class.xform.validate)/', $Datei) && !preg_match('/^(class.xform.validate.abstract)/', $Datei)) {
                        if (!is_dir($Datei)) {
                            $classname = (explode('.', substr($Datei, 12)));
                            $classname = 'rex_xform_' . $classname[0];
                            if (file_exists($validate_path . $Datei)) {
                                include_once $validate_path . $Datei;
                                $class = new $classname;
                                $desc = $class->getDescription();
                                if ($desc != '') {
                                    $list[$classname] = '<li>' . $desc . '</li>';
                                }
                            }
                        }
                    }
                }
                ksort($list);
                foreach ($list as $l) {
                    $html .= $l;
                }
                closedir($Verzeichniszeiger);
            }
        }
        if ($pos > 0) {
            $html .= '</ul></li>';
        }

        $html .= '</ul>
    </li>

    <li class="type action"><strong class="toggler">Action</strong>
    <ul class="xform type action">
    ';

        if (!class_exists('rex_xform_action_abstract')) {
            require_once $REX['INCLUDE_PATH'] . '/addons/xform/classes/basic/class.xform.action.abstract.inc.php';
        }

        foreach ($REX['ADDON']['xform']['classpaths']['action'] as $pos => $action_path) {
            if ($pos == 1) {
                $html .= '<li class="action extras"><strong class="toggler opened">Action Extras</strong><ul class="xform type action extras">';
            }
            if ($Verzeichniszeiger = opendir($action_path)) {
                $list = array();
                while ($Datei = readdir($Verzeichniszeiger)) {
                    if (preg_match('/^(class.xform.action)/', $Datei) && !preg_match('/^(class.xform.action.abstract)/', $Datei)) {
                        if (!is_dir($Datei)) {
                            $classname = (explode('.', substr($Datei, 12)));
                            $classname = 'rex_xform_' . $classname[0];
                            if (file_exists($action_path . $Datei)) {
                                include_once $action_path . $Datei;
                                $class = new $classname;
                                $desc = $class->getDescription();
                                if ($desc != '') {
                                 $list[$classname] = '<li>' . $desc . '</li>';
                                }
                            }
                        }
                    }
                }
                ksort($list);
                foreach ($list as $l) {
                    $html .= $l;
                }
                closedir($Verzeichniszeiger);
            }
        }
        if ($pos > 0) {
            $html .= '</ul></li>';
        }

        $html .= '</ul>
    </li>
</ul>';

        if ($script) {
            $html .= '
<script type="text/javascript">
(function($){

    $("ul.xform strong.toggler").click(function(){
        var me = $(this);
        var target = $(this).next("ul.xform");
        target.toggle(0, function(){
            if(target.css("display") == "block"){
                me.addClass("opened");
            }else{
                me.removeClass("opened");
            }
        });

    });

})(jQuery)
</script>
';
        }

        if ($return) {
            return $html;
        } else {
            echo $html;
        }

    }


    static function getTypeArray()
    {

        global $REX;

        $return = array();

        if (!class_exists('rex_xform_base_abstract')) {
            require_once $REX['INCLUDE_PATH'] . '/addons/xform/classes/basic/class.xform.base.abstract.inc.php';
        }

        // Value

        if (!class_exists('rex_xform_abstract')) {
            require_once $REX['INCLUDE_PATH'] . '/addons/xform/classes/basic/class.xform.value.abstract.inc.php';
        }

        foreach ($REX['ADDON']['xform']['classpaths']['value'] as $pos => $value_path) {
            if ($Verzeichniszeiger = @opendir($value_path)) {
                while ($Datei = readdir($Verzeichniszeiger)) {
                    if (preg_match('/^(class.xform)/', $Datei) && !preg_match('/^(class.xform.validate|class.xform.abstract)/', $Datei)) {
                        if (!is_dir($Datei)) {
                            $classname = (explode('.', substr($Datei, 12)));
                            $name = $classname[0];
                            $classname = 'rex_xform_' . $name;
                            if (file_exists($value_path . $Datei)) {
                                include_once $value_path . $Datei;
                                $class = new $classname;
                                $d = $class->getDefinitions();
                                if (count($d) > 0) {
                                $return['value'][$d['name']] = $d;
                                }
                            }
                        }
                    }
                }
                closedir($Verzeichniszeiger);
            }
        }


        // Validate

        if (!class_exists('rex_xform_validate_abstract')) {
            require_once $REX['INCLUDE_PATH'] . '/addons/xform/classes/basic/class.xform.validate.abstract.inc.php';
        }

        foreach ($REX['ADDON']['xform']['classpaths']['validate'] as $pos => $validate_path) {
            if ($Verzeichniszeiger = @opendir($validate_path)) {
                while ($Datei = readdir($Verzeichniszeiger)) {
                    if (preg_match('/^(class.xform.validate)/', $Datei) && !preg_match('/^(class.xform.validate.abstract)/', $Datei)) {
                        if (!is_dir($Datei)) {
                            $classname = (explode('.', substr($Datei, 12)));
                            $name = $classname[0];
                            $classname = 'rex_xform_' . $name;
                            if (file_exists($validate_path . $Datei)) {
                                include_once $validate_path . $Datei;
                                $class = new $classname;
                                $d = $class->getDefinitions();
                                if (count($d) > 0) {
                                $return['validate'][$d['name']] = $d;
                                }
                            }
                        }
                    }
                }
                closedir($Verzeichniszeiger);
            }
        }


        // Action

        if (!class_exists('rex_xform_action_abstract')) {
            require_once $REX['INCLUDE_PATH'] . '/addons/xform/classes/basic/class.xform.action.abstract.inc.php';
        }

        foreach ($REX['ADDON']['xform']['classpaths']['action'] as $pos => $action_path) {
            if ($Verzeichniszeiger = @opendir($action_path)) {
                while ($Datei = readdir($Verzeichniszeiger)) {
                    if (preg_match('/^(class.xform.action)/', $Datei) && !preg_match('/^(class.xform.action.abstract)/', $Datei)) {
                        if (!is_dir($Datei)) {
                            $classname = (explode('.', substr($Datei, 12)));
                            $name = $classname[0];
                            $classname = 'rex_xform_' . $name;
                            if (file_exists($action_path . $Datei)) {
                                include_once $action_path . $Datei;
                                $class = new $classname;
                                $d = $class->getDefinitions();
                                if (count($d) > 0) {
                                $return['action'][$d['name']] = $d;
                                }
                            }
                        }
                    }
                }
                closedir($Verzeichniszeiger);
            }
        }

        return $return;

    }



}
