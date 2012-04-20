<?php

class rex_xform_captcha_calc extends rex_xform_abstract
{

	function enterObject()
	{
	
		// var_dump($this->params);
		global $REX; 

		error_reporting(E_ALL);
		ini_set("display_errors",1);

		
		require_once (realpath(dirname (__FILE__).'/../../ext/captcha_calc/class.captcha_calc_x.php'));

    	$captcha = new captcha_calc_x ();
		$captchaRequest = rex_request('captcha_calc', 'string');
		
		if ($captchaRequest == "show")
		{
      // alle offenen buffer schliessen
			while(@ob_end_clean());
			
			$captcha->handle_request();
			exit;
		}

		$wc = "";
		// TODO jan ?!?!
		// hier bewusst nur ein "&" (konditionales und, kein boolsches und!)
		if ( $this->params["send"] == 1 & $captcha->validate($this->value)) 
		{
			// Alles ist gut.
		}elseif($this->params["send"]==1)
		{
			// Error. Fehlermeldung ausgeben
			$this->params["warning"][] = $this->elements[2];
			$this->params["warning_messages"][] = $this->elements[2];
			$wc = $this->params["error_class"];
		}

		$link = rex_getUrl($this->params["article_id"],$this->params["clang"],array("captcha_calc"=>"show"),"&");
		/*
		$form_output[] = '
			<p class="formcaptcha">
				<span class="' . $wc . '">'.htmlspecialchars($this->elements[1]).'</span>
				<label class="captcha ' . $wc . '"><img 
					src="'.$link.'" 
					onclick="javascript:this.src=\''.$link.'&\'+Math.random();" 
					alt="CAPTCHA image" 
					/></label>
				<input class="' . $wc . '" maxlength="5" size="5" name="FORM['.$this->params["form_name"].'][el_'.$this->id.']" type="text" />
			</p>';
		*/
		
		// 22.05.2009 - Tab Aenderung
		if ($wc != '')
			$wc = ' '.$wc;
			
		$this->params["form_output"][$this->getId()] = '
			<p class="formcaptcha">
				<label class="captcha' . $wc . '" for="el_' . $this->id . '">
					'.htmlspecialchars(rex_translate($this->getElement(1))).'
				</label>
				<span class="as-label' . $wc . '"><img 
					src="'.$link.'" 
					onclick="javascript:this.src=\''.$link.'&\'+Math.random();" 
					alt="CAPTCHA image" 
					/></span>
				<input class="captcha' . $wc . '" maxlength="5" size="5" id="el_' . $this->id . '" name="FORM['.$this->params["form_name"].'][el_'.$this->id.']" type="text" />
			</p>';
		// Ende
	}
	
	function getDescription()
	{
		return "captcha_calc -> Beispiel: captcha|Beschreibungstext|Fehlertext";
	}
}

?>