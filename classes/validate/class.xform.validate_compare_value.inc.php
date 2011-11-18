<?PHP

class rex_xform_validate_compare_value extends rex_xform_validate_abstract 
{

	function enterObject()
	{
		if($this->params["send"]=="1")
		{
			$field = $this->getElement(2);
			$value = -1;
			foreach($this->obj_array as $o)
			{
				if ($o->getName() == $field)
				{
					$value = $o->getValue();
				}
			}
			if ($value === -1 || strtolower($value) != strtolower($this->getElement(3)))
			{
				$this->params["warning"][] = $this->getElement(4);
				$this->params["warning_messages"][] = $this->getElement(4);
			}
		}
	}
	
	function getDescription()
	{
		return "compare_value -> compare label with value, example: validate|compare_value|label|value|warning_message ";
	}
}

?>