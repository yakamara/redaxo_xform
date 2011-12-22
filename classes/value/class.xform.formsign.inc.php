<?php

class rex_xform_formsign extends rex_xform_abstract
{
	function enterObject()
	{
		global $REX;
		require_once (realpath(dirname (__FILE__).'/../../ext/formsign/class.formsign.php'));
		
		// Einstellungen
		$debug=(int)$this->params["debug"];
		if($this->getElement(3)=='')
		$salt="XB([ :_Ag97U4";
		else $salt=$this->getElement(3);
		if((int)$this->getElement(1)<=0)
		$minlimit=10;
		else $minlimit=(int)$this->getElement(1);
		if($_POST['md5("plus".$salt)']==1)
		$minlimit=2;
		if((int)$this->getElement(2)<=0)
		$maxlimit=300;
		else $maxlimit=(int)$this->getElement(2);
		$doublemaxlimit=$maxlimit*2;
		// Erstellung eines Objekts der Klasse formsign
		$fso= new formsign($salt, $minlimit, $maxlimit );
		// Einbindung einer sqlite Datenbank
		$db_name='db.sqlite3';
		$DB_Table="tab";
		$fso->use_pdo("sqlite", $db_name, $DB_Table);
		
		// Festlegen den Namen fuer checkfield (dieser wird mit md5 maskiert)
		$fso->set_check_name('this is checkfield');
		
		// Erstellung einer Signatur fuer das Formular; Entsprechend fuer XHTML oder HTML
		if((int)$this->getElement(4)==1)
		$sign=$fso->create_sign(0);
		else  $sign=$fso->create_sign(); // for XHTML tags
		
		if ( $this->params["send"] == 1)
		{
			// Prueffung der gueltigkeit des Siegels
			$testsign=$fso->check_sign($_POST );
			
			if($testsign==1)
			{
				// Alles in Ordnung, formular darf Versendet werden.
				if($debug==1)
				{
					$this->params["warning"][$this->getId()] ='Das Siegel ist gültig, min_limit und max_limit wurden angehalten.';
					$this->params["warning_messages"][$this->getId()] = 'Das Siegel ist gültig, min_limit und max_limit wurden angehalten.';
				}
			}elseif($testsign!=1)
			{
				// Das Siegel ist ungueltig, Formular soll nicht weiter verarbeitet werden
				$this->params["send"]=0;
				// Untersuchung der Ursachen mittels der Differenz zw. Erscheinen und Abschicken des Formulars
				$diff=$fso->get_gs_diff();
				if($diff>=0)
				{
					if($diff<$minlimit)
					{
						// minlimit unterschritten
						if($debug==1)
						{
							$this->params["warning"][$this->getId()] = 'Die voreingestellte minimale Zeit (min_limit)  wurde unterschritten, Versand wird blokiert.';
							$this->params["warning_messages"][$this->getId()] = 'Die voreingestellte minimale Zeit (min_limit) wurde unterschritten, Versand wird blokiert.';
						}
					}
					if($diff>$maxlimit && $diff<$doublemaxlimit)
					{
						// maxlimit ueberschritten, aber verdoppelte max_limit nicht
						if($debug==1)
						{
							$this->params["warning"][$this->getId()] = 'Die voreingestellte maximale Zeit (max_limit) wurde überschritten, Versand wird blokiert.';
							$this->params["warning_messages"][$this->getId()] = 'Die voreingestellte maximale Zeit (max_limit) wurde überschritten, Versand wird blokiert.';
						}
						// zum Siegel wird noch ein Feld hinzugefuegt um min_limit herabzusetzen beim naechstem Versuch
						if((int)$this->getElement(4)==1)
						{
							$sign.='<input type="hidden" name="'.md5("plus".$salt).'" value="1">';
						} else $sign.='<input type="hidden" name="'.md5("plus".$salt).'" value="1" />';
					}
				}
				if($diff==-1)
				{
					// formular bereits gesendet
					if($debug==1) {
						$this->params["warning"][$this->getId()] = 'Dieses Formular wurde bereits gesendet, erneutes Versenden wird blokiert.';
						$this->params["warning_messages"][$this->getId()] = 'Dieses Formular wurde bereits gesendet, erneutes Versenden wird blokiert.';
					} else
					{
						// Formular nicht mehr anzeigen
						$this->params["form_show"]=0;
					}
				}
				$wc = $this->params["error_class"];
			}
		}
		$link = rex_getUrl($this->params["article_id"],$this->params["clang"],array("formsign"=>"show"),"&");
		
		if ($wc != '')
		$wc = ' '.$wc;
		
		$this->params["form_output"][$this->getId()] =$sign;
		// Ende
	}
	
	function getDescription()
	{
		return "formsign -> Beispiel: formsign|min_limit in Sekunden|max_limit in Sekunden|salz: Zeichenkette aus Buchstaben, Ziffern und Sonderzeichen|HTML Tags: 1; XHTML Tags: (default)";
	}
}
?>
