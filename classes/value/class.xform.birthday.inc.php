<?php

class rex_xform_birthday extends rex_xform_abstract
{

	function enterObject()
	{
		
		$day = 0;
		$month = 0;
		$year = 0;
		
		if (@strlen($this->getValue()) == 10)
		{
			$day = (int) substr($this->getValue(),8,2);
			$month = (int) substr($this->getValue(),5,2);
			$year = (int) substr($this->getValue(),0,4);
			
		}else
		{
			if (isset($_REQUEST["FORM"][$this->params["form_name"]]['el_'.$this->getId()]["day"])) $day = (int) $_REQUEST["FORM"][$this->params["form_name"]]['el_'.$this->getId()]["day"];
			if (isset($_REQUEST["FORM"][$this->params["form_name"]]['el_'.$this->getId()]["month"])) $month = (int) $_REQUEST["FORM"][$this->params["form_name"]]['el_'.$this->getId()]["month"];
			if (isset($_REQUEST["FORM"][$this->params["form_name"]]['el_'.$this->getId()]["year"])) $year = (int) $_REQUEST["FORM"][$this->params["form_name"]]['el_'.$this->getId()]["year"];
		}
		
		$formname = 'FORM['.$this->params["form_name"].'][el_'.$this->getId().']';

		$isodatum = sprintf ("%04d-%02d-%02d", $year, $month, $day);
		$datum = $isodatum;

		$twarning = "";
		if ($this->getElement(4)==1 && !checkdate($month,$day,$year) && $this->params["send"] == 1)
		{
			$twarning = 'border:1px solid #f99;background-color:#f9f3f3;';
			$this->params["warning"][$this->getId()] = "Geburtsdatum ist falsch";
		}else
		{
			$this->params["value_pool"]["email"][$this->getElement(1)] = "$day.$month.$year";
			$this->params["value_pool"]["sql"][$this->getElement(1)] = $datum;
		}

		$out = "";
		$out .= '
		<p class="formbirthday" id="'.$this->getHTMLId().'">
					<label class="select" for="'.$this->getFieldId().'" >'.$this->getElement(2).'</label>';
					
		$dsel = new rex_select;
		$dsel->setName($formname.'[day]');
		$dsel->setStyle("width:50px;".$twarning);
		$dsel->setId('el_'.$this->getId().'_day');
		$dsel->setSize(1);
		$dsel->addOption("TT","0");
		for($i=1;$i<32;$i++)
		{
			$dsel->addOption($i,$i);
		}
		$dsel->setSelected($day);
		$out .= $dsel->get();

		$msel = new rex_select;
		$msel->setName($formname.'[month]');
		$msel->setStyle("width:50px;".$twarning);
		$msel->setId('el_'.$this->getId().'_month');
		$msel->setSize(1);
		$msel->addOption("MM","0");
		for($i=1;$i<13;$i++)
		{
			$msel->addOption($i,$i);
		}
		$msel->setSelected($month);
		$out .= $msel->get();

		$ysel = new rex_select;
		$ysel->setName($formname.'[year]');
		$ysel->setStyle("width:80px;".$twarning);
		$ysel->setId('el_'.$this->getId().'_year');
		$ysel->setSize(1);
		$ysel->addOption("YYYY","0");
		for($i=1930;$i<2000;$i++)
		{
			$ysel->addOption($i,$i);
		}
		$ysel->setSelected($year);
		$out .= $ysel->get();

		$out .= '</p>';

		$this->params["form_output"][$this->getId()] = $out;

	}
	function getDescription()
	{
		return "birthday -> Beispiel: birthday|feldname|Text *|[format: Y-m-d]|Pflicht";
	}
}

?>