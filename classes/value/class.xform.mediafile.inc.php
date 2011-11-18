<?php

class rex_xform_mediafile extends rex_xform_abstract
{

  function enterObject()
  {

    global $REX;

    if ($this->getElement(8) == "") $mediacatid = 0;
    else $mediacatid = (int) $this->getElement(8);

    $minsize = 0;
    $maxsize = 50000;

    $sizes = explode(",",$this->getElement(3));
    if(count($sizes) > 1)
    {
      $minsize = (int) ($sizes[0]*1024); // -> bytes
      $maxsize = (int) ($sizes[1]*1024); // -> bytes
    }

    // Größencheck
    if (	$this->params["send"]
    && isset($_FILES["FORM"]["size"][$this->params["form_name"]]["el_".$this->getId()])
    && $_FILES["FORM"]["size"][$this->params["form_name"]]["el_".$this->getId()] != ""
    && ($_FILES["FORM"]["size"][$this->params["form_name"]]["el_".$this->getId()]>$maxsize || $_FILES["FORM"]["size"][$this->params["form_name"]]["el_".$this->getId()]<$minsize)
    )
    {
      $_FILES["FORM"]["name"][$this->params["form_name"]]["el_".$this->getId()] = "";
      $this->setValue("");
      $this->setElement(5,1); // auf "error message true" setzen, wenn datei fehlerhaft

    }

    if ($this->params["send"])
    {
      if (isset($_FILES["FORM"]["name"][$this->params["form_name"]]["el_".$this->getId()])
      && $_FILES["FORM"]["size"][$this->params["form_name"]]["el_".$this->getId()] != ""
      )
      {

        $FILE["size"] = $_FILES["FORM"]["size"][$this->params["form_name"]]["el_".$this->getId()];
        $FILE["name"] = $_FILES["FORM"]["name"][$this->params["form_name"]]["el_".$this->getId()];
        $FILE["type"] = $_FILES["FORM"]["type"][$this->params["form_name"]]["el_".$this->getId()];
        $FILE["tmp_name"] = $_FILES["FORM"]["tmp_name"][$this->params["form_name"]]["el_".$this->getId()];
        $FILE["error"] = $_FILES["FORM"]["error"][$this->params["form_name"]]["el_".$this->getId()];

        $extensions_array = explode(",",$this->getElement(4));
        $NEWFILE = $this->saveMedia($FILE,$REX["INCLUDE_PATH"]."/../../files/",$extensions_array,$mediacatid);

        if ($NEWFILE["ok"])
        {
          $this->setValue($NEWFILE['filename']);
        }else
        {
          $this->setValue("");
          $this->setElement(5,1);
        }
      }
    }

    if ($this->params["send"])
    {
      if ($this->getValue() == ""
      && @$_REQUEST["FORM"][$this->params["form_name"]]['el_'.$this->getId().'_filename'] != ""
      && @$_REQUEST["FORM"][$this->params["form_name"]]['el_'.$this->getId().'_delete'] != 1)
      {
        $this->setValue($_REQUEST["FORM"][$this->params["form_name"]]['el_'.$this->getId().'_filename']);
      }

      $this->params["value_pool"]["email"][$this->getElement(1)] = stripslashes($this->getValue());
      if ($this->getElement(7) != "no_db") $this->params["value_pool"]["sql"][$this->getElement(1)] = $this->getValue();
    }

    $tmp = "";
    $check_delete = "";
    if ($this->getValue() != "")
    {
      $this->setElement(2, $this->getElement(2).'<br />Dateiname: <a href="files/'.$this->getValue().'">'.$this->getValue().'</a><br />');

      $fileendung = substr(strtolower($this->getValue()),-3);
      if ($fileendung == 'jpg' || $fileendung == 'png' || $fileendung == 'gif') {
        $this->setElement(2,$this->getElement(2).'<br /><img src="?rex_img_type=profileimage&amp;rex_img_file='.$this->getValue().'" />');
      }
      $check_delete = '
   			<span class="formmcheckbox" style="width:300px;clear:none;">
	   			<input id="'.$this->getFieldId("delete").'" type="checkbox" name="FORM['.$this->params["form_name"].'][el_'.$this->getId().'_delete]" value="1" />
	   			<label for="'.$this->getFieldId("delete").'">Datei löschen</label>
   			</span>
   			';
   			// $this->getElement(2) = "";
    }

    if ($this->params["send"] && $this->getElement(5)==1) {
      $this->params["warning"][$this->getId()] = $this->params["error_class"];
      $this->params["warning_messages"][$this->getId()] = $this->getElement(6);
    }

    $wc = "";
    if (isset($this->params["warning"][$this->getId()])) {
      $wc = $this->params["warning"][$this->getId()];
    }

    $out = '
			<input type="hidden" name="FORM['.$this->params["form_name"].'][el_'.$this->getId().'_filename]" value="'.$this->getValue().'" />
			<p class="formfile" id="'.$this->getHTMLId().'">
				<label class="text ' . $wc . '" for="'.$this->getFieldId().'" >' . $this->getElement(2) .'</label>
				'.$check_delete.'
				<input class="uploadbox clickmedia '.$wc.'" id="'.$this->getFieldId().'" name="'.$this->getFieldName().'" type="file" />
			</p>';

    $this->params["form_output"][$this->getId()] = $out;

  }

  function getDescription()
  {
    return "mediafile -> Beispiel: mediafile|label|Bezeichnung|groesseinkb|endungenmitpunktmitkommasepariert|pflicht=1|Fehlermeldung|[no_db]|mediacatid";
  }


  function getDefinitions()
  {

    return array(
						'type' => 'value',
						'name' => 'mediafile',
						'values' => array(
    array( 'type' => 'label',   'label' => 'Label' ),
    array( 'type' => 'text',    'label' => 'Bezeichnung'),
    array( 'type' => 'text',    'label' => 'Maximale Größe in Kb oder Range 100,500'),
    array( 'type' => 'text',    'label' => 'Welche Dateien sollen erlaubt sein, kommaseparierte Liste. ".gif,.png"'),
    array( 'type' => 'boolean', 'label' => 'Pflichtfeld'),
    array( 'type' => 'text',    'label' => 'Fehlermeldung'),
    array( 'type' => 'no_db',   'label' => 'Datenbank',  'default' => 1),
    array( 'type' => 'text',    'label' => 'Mediakategorie ID'),
    ),
						'description' => 'Mediafeld, welches Dateien aus dem Medienpool holen',
						'dbtype' => 'text'
						);
  }

  function saveMedia($FILE,$filefolder,$extensions_array,$rex_file_category){

    global $REX;

    $FILENAME = $FILE['name'];
    $FILESIZE = $FILE['size'];
    $FILETYPE = $FILE['type'];
    $NFILENAME = "";
    $message = '';

    // ----- neuer filename und extension holen
    $NFILENAME = strtolower(preg_replace("/[^a-zA-Z0-9.\-\$\+]/","_",$FILENAME));
    if (strrpos($NFILENAME,".") != "")
    {
      $NFILE_NAME = substr($NFILENAME,0,strlen($NFILENAME)-(strlen($NFILENAME)-strrpos($NFILENAME,".")));
      $NFILE_EXT  = substr($NFILENAME,strrpos($NFILENAME,"."),strlen($NFILENAME)-strrpos($NFILENAME,"."));
    }else
    {
      $NFILE_NAME = $NFILENAME;
      $NFILE_EXT  = "";
    }

    // ---- ext checken
    $ERROR_EXT = array(".php",".php3",".php4",".php5",".phtml",".pl",".asp",".aspx",".cfm");
    if (in_array($NFILE_EXT,$ERROR_EXT))
    {
      $NFILE_NAME .= $NFILE_EXT;
      $NFILE_EXT = ".txt";
    }

    $standard_extensions_array = array(".rtf",".pdf",".doc",".gif",".jpg",".jpeg");
    if (count($extensions_array) == 0) $extensions_array = $standard_extensions_array;

    if (!in_array($NFILE_EXT,$extensions_array))
    {
      $RETURN = FALSE;
      $RETURN['ok'] = FALSE;
      return $RETURN;
    }

    $NFILENAME = $NFILE_NAME.$NFILE_EXT;

    // ----- datei schon vorhanden -> namen aendern -> _1 ..
    if (file_exists($filefolder."/$NFILENAME"))
    {
      for ($cf=1;$cf<1000;$cf++)
      {
        $NFILENAME = $NFILE_NAME."_$cf"."$NFILE_EXT";
        if (!file_exists($filefolder."/$NFILENAME")) break;
      }
    }

    // ----- dateiupload
    $upload = true;
    if(!move_uploaded_file($FILE['tmp_name'],$filefolder."/$NFILENAME") )
    {
      if (!copy($FILE['tmp_name'],$filefolder."/$NFILENAME"))
      {
        $message .= "move file $NFILENAME failed | ";
        $RETURN = FALSE;
        $RETURN['ok'] = FALSE;
        return $RETURN;
      }
    }

    @chmod($filefolder."/$NFILENAME", $REX['FILEPERM']);
    $RETURN['type'] = $FILETYPE;
    $RETURN['msg'] = $message;
    $RETURN['ok'] = TRUE;
    $RETURN['filename'] = $NFILENAME;

    $FILESQL = rex_sql::factory();
    // $FILESQL->debugsql=1;
    $FILESQL->setTable($REX['TABLE_PREFIX']."file");
    $FILESQL->setValue("filetype",$FILETYPE);
    $FILESQL->setValue("filename",$NFILENAME);
    $FILESQL->setValue("originalname",$FILENAME);
    $FILESQL->setValue("filesize",$FILESIZE);
    $FILESQL->setValue("category_id",$rex_file_category);
    $FILESQL->setValue("createdate",time());
    $FILESQL->setValue("createuser","system");
    $FILESQL->setValue("updatedate",time());
    $FILESQL->setValue("updateuser","system");
    $FILESQL->insert();

    return $RETURN;
  }


}

?>