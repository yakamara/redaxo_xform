<?php

class rex_xform_time extends rex_xform_abstract
{

	function enterObject()
	{

    $hour = date('H');
    $min = date('i');

		if (!is_array($this->getValue()) && (strlen($this->getValue()) == 2 || strlen($this->getValue()) == 4))
		{
			$hour = (int) substr($this->getValue(),0,2);
			$min = (int) substr($this->getValue(),2,2);
		}else
		{
			if (isset($_REQUEST["FORM"][$this->params["form_name"]]['el_'.$this->getId()]["hour"])) $hour = $_REQUEST["FORM"][$this->params["form_name"]]['el_'.$this->getId()]["hour"];
			if (isset($_REQUEST["FORM"][$this->params["form_name"]]['el_'.$this->getId()]["min"])) $min = $_REQUEST["FORM"][$this->params["form_name"]]['el_'.$this->getId()]["min"];
			if($hour != "") { $hour = (int) $hour; $hour = substr($hour,0,2); $hour = str_pad($hour, 2, "0", STR_PAD_LEFT); }
			if($min != "") { $min = (int) $min; $min = substr($min,0,2); $min = str_pad($min, 2, "0", STR_PAD_LEFT); }
		}
		
		$formname = $this->getFieldName();

		if($hour != "")
		{
			$this->params["value_pool"]["email"][$this->getName()] = "$hour$min";
			$this->params["value_pool"]["sql"][$this->getName()] = "$hour$min";
		}
		
		$out = "";
		$out .= '
		<p class="formtime '.$this->getHTMLClass().'" id="'.$this->getHTMLId().'">
			<label class="select" for="'.$this->getFieldId().'" >'.$this->getElement(2).'</label>';
					
		$hsel = new rex_select;
		$hsel->setName($formname.'[hour]');
		$hsel->setStyle("width:55px;");
		$hsel->setId('el_'.$this->getId().'_hour');
		$hsel->setSize(1);
		$hsel->addOption("HH","");
		$hsel->addOption("01","01");
		$hsel->addOption("02","02");
		$hsel->addOption("03","03");
		$hsel->addOption("04","04");
		$hsel->addOption("05","05");
		$hsel->addOption("06","06");
		$hsel->addOption("07","07");
		$hsel->addOption("08","08");
		$hsel->addOption("09","09");
		$hsel->addOption("10","10");
		$hsel->addOption("11","11");
		$hsel->addOption("12","12");
		$hsel->addOption("13","13");
		$hsel->addOption("14","14");
		$hsel->addOption("15","15");
		$hsel->addOption("16","16");
		$hsel->addOption("17","17");
		$hsel->addOption("18","18");
		$hsel->addOption("19","19");
		$hsel->addOption("20","20");
		$hsel->addOption("21","21");
		$hsel->addOption("22","22");
		$hsel->addOption("23","23");
		$hsel->addOption("24","24");
		$hsel->setSelected($hour);
		$out .= $hsel->get();

		$msel = new rex_select;
		$msel->setName($formname.'[min]');
    $msel->setStyle('width:55px;');
		$msel->setId('el_'.$this->getId().'_min');
		$msel->setSize(1);
    $msel->addOption('MM','0');

    $mmm = array();
    if($this->getElement(3) != '')
      $mmm = explode(',',trim($this->getElement(3)));

    if(count($mmm)>0) {
      foreach($mmm as $m) {
        $msel->addOption($m,$m);
      }
    } else {
      for($i=0;$i<61;$i++) {
        $msel->addOption(str_pad($i,2,'0',STR_PAD_LEFT),str_pad($i,2,'0',STR_PAD_LEFT));
      }
    }
		$msel->setSelected($min);
		$out .= $msel->get();

		$out .= '</p>';

		$this->params["form_output"][$this->getId()] = $out;

	}
	function getDescription()
	{
    return "date -> Beispiel: time|feldname|Text *|jahrstart|jahrend|minutenrater 00,15,30,45";
  }


	function getDefinitions()
	{
		return array(
					'type' => 'value',
					'name' => 'time',
					'values' => array(
								array( 'type' => 'name',   'label' => 'Feld' ),
								array( 'type' => 'text',    'label' => 'Bezeichnung'),
                array( 'type' => 'text',   'label' => 'Minutenraster'),
                array( 'type' => 'text',    'label' => 'Format (H:i)'),
							),
					'description' => 'Uhrzeitfeld Eingabe',
					'dbtype' => 'text'
					);

	}
	
  function getListValue($params)
  {
        $format = $params['params']['field']['f4']!=''
                ? $params['params']['field']['f4']
                : 'H:i';
        return date($format,strtotime($params['subject']));
  }


}

?>