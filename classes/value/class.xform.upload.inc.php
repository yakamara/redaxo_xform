<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_upload extends rex_xform_abstract
{

    function enterObject()
    {

        global $REX;

        $rfile    = 'file_' . md5($this->getFieldName('file'));
        $error = array();

        $err_msgs = $this->getElement('messages'); // min_err,max_err,type_err,empty_err
        if (!is_array($err_msgs)) {
            $err_msgs = explode(',', $err_msgs);
        }

        $err_msgs['min_error']   = $err_msgs[0];
        $err_msgs['max_error']   = isset($err_msgs[1]) ? $err_msgs[1] : 'max_error';
        $err_msgs['type_error']  = isset($err_msgs[2]) ? $err_msgs[2] : 'type_error';
        $err_msgs['empty_error'] = isset($err_msgs[3]) ? $err_msgs[3] : 'empty_error';

        // SIZE CHECK
        $sizes   = explode(',', $this->getElement('max_size'));
        $minsize = count($sizes) > 1 ? (int) ($sizes[0] * 1024) : 0;
        $maxsize = count($sizes) > 1 ? (int) ($sizes[1] * 1024) : (int) ($sizes[0] * 1024);
        if ( $this->params['send'] && isset($_FILES[$rfile]) && $_FILES[$rfile]['name'] != '' && ($_FILES[$rfile]['size'] > $maxsize || $_FILES[$rfile]['size'] < $minsize) ) {
            if ($_FILES[$rfile]['size'] < $minsize) {
                $error[] = $err_msgs['min_error'];
            }
            if ($_FILES[$rfile]['size'] > $maxsize) {
                $error[] = $err_msgs['max_error'];
            }
            unset($_FILES[$rfile]);
        }

        if ($this->params['send']) {

            if (isset($_FILES[$rfile]) &&  $_FILES[$rfile]['name'] != '' ) {
                $FILE['size']     = $_FILES[$rfile]['size'];
                $FILE['name']     = $_FILES[$rfile]['name'];
                $FILE['type']     = $_FILES[$rfile]['type'];
                $FILE['tmp_name'] = $_FILES[$rfile]['tmp_name'];
                $FILE['error']    = $_FILES[$rfile]['error'];
                $FILE['name_normed'] = strtolower(preg_replace('/[^a-zA-Z0-9.\-\$\+]/', '_', $FILE['name']));

                // EXTENSION CHECK
                $extensions_array = explode(',', $this->getElement('types'));
                $ext = '.' . pathinfo($FILE['name'], PATHINFO_EXTENSION);
                if (!in_array(strtolower($ext), $extensions_array) && !in_array(strtoupper($ext), $extensions_array)) {

                    $error[] = $err_msgs['type_error'];

                } else {

                    switch ($this->getElement('modus')) {

                        case('upload'):
                            $upload_folder = $this->getElement('upload_folder');
                            $prefix = $this->getElement('prefix');
                            $file_normed = $FILE['name_normed'];

                            $file_normed_new = $prefix.$file_normed;
                            if (file_exists($upload_folder . '/' . $file_normed_new)) {
                                for ($cf = 1; $cf < 1000; $cf++) {
                                    $file_normed_new = $prefix . '_' . $cf . '_' . $file_normed ;
                                    if (!file_exists($upload_folder . '/' . $file_normed_new)) {
                                        break;
                                    }
                                }
                            }

                            if (!move_uploaded_file($FILE['tmp_name'], $upload_folder . '/' . $file_normed_new ) ) {
                                if (!copy($FILE['tmp_name'], $upload_folder . '/' . $file_normed_new )) {
                                    $error[] = $err_msgs['save_error'];
                                } else {
                                    @chmod($upload_folder . '/' . $file_normed_new, $REX['FILEPERM']);
                                }
                            } else {
                                @chmod($upload_folder . '/' . $file_normed_new, $REX['FILEPERM']);
                            }

                            if (count($error) == 0) {
                                $this->params['value_pool']['email'][$this->getName()] = $file_normed_new;
                                $this->params['value_pool']['sql'][$this->getName()] = $file_normed_new;
                                $this->setValue($file_normed_new);

                            } else {
                                $this->params['value_pool']['email'][$this->getName()] = '';
                                $this->params['value_pool']['sql'][$this->getName()] = '';
                                $this->setValue('');

                            }
                            break;

                        case('database'):
                            $database_filename_field = $this->getElement('database_filename_field');
                            $prefix = $this->getElement('prefix');
                            $FILE['name_normed'] = $prefix.$FILE['name_normed'];

                            if ($database_filename_field != "") {
                                $this->params['value_pool']['email'][$database_filename_field] = $FILE['name_normed'];
                                $this->params['value_pool']['sql'][$database_filename_field] = $FILE['name_normed'];
                            }

                            $content = file_get_contents($FILE['tmp_name']);
                            $this->params['value_pool']['email'][$this->getName()] = $content;
                            $this->params['value_pool']['sql'][$this->getName()] = $content;
                            break;

                        default:
                            $this->setValue($FILE['tmp_name']);
                            $this->params['value_pool']['email'][$this->getName()] = $this->getValue();
                            break;

                    }

                }

            }

        }

        ## check for required file
        if ($this->params['send'] && $this->getElement('required') == 1 && $this->getValue() == '') {
            $error[] = $err_msgs['empty_error'];
        }

        ## setting up error Message
        if ($this->params['send'] && count($error) > 0) {
            $this->params['warning'][$this->getId()] = $this->params['error_class'];
            $this->params['warning_messages'][$this->getId()] = implode(', ', $error);
        }

        $this->params['form_output'][$this->getId()] = $this->parse('value.upload.tpl.php');

    }

    function getDescription()
    {
        return 'upload -> Beispiel: file|name|label|groesseinkb|endungenmitpunktmitkommasepariert|pflicht=1|min_err,max_err,type_err,empty_err|';
    }

    function getDefinitions()
    {
        return array(
            'type' => 'value',
            'name' => 'upload',
            'values' => array(
                'name'     => array( 'type' => 'label',   'label' => 'Label' ),
                'label'    => array( 'type' => 'text',    'label' => 'Bezeichnung'),
                'max_size' => array( 'type' => 'text',    'label' => 'Maximale Größe in Kb oder Range 100,500'),
                'types'    => array( 'type' => 'text',    'label' => 'Welche Dateien sollen erlaubt sein, kommaseparierte Liste. ".gif,.png"'),
                'required' => array( 'type' => 'boolean', 'label' => 'Pflichtfeld'),
                'messages' => array( 'type' => 'text',    'label' => 'min_err,max_err,type_err,empty_err'),
                'modus'    => array( 'type' => 'select',  'label' => 'Speichermodus', 'definition' => array('upload', 'database', 'no_save'), 'default' => 'upload'),
                'database_filename_field'    => array( 'type' => 'text',  'label' => '`database`: Dateiname wird gespeichert in Feldnamen'),
                'upload_folder'   => array( 'type' => 'text',    'label' => '`upload`: Folder' ),
                'file_prefix'   => array( 'type' => 'text',    'label' => 'Dateiprefix [optional]' ),
                'no_db'     => array( 'type' => 'no_db',   'label' => 'Datenbank',  'default' => 0),
            ),
            'description' => 'Dateifeld, welches eine Datei in einen Ordner oder in der Datenbank speichert',
            'dbtype' => 'blob'
        );
    }

    static function getListValue($params)
    {
       if (strlen($params['value']) < 200) {
           return $params['value'];
       }
       return '';
    }

}
