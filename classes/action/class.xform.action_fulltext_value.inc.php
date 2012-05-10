<?php

class rex_xform_action_fulltext_value extends rex_xform_action_abstract
{

  function execute()
  {
    $label = $this->getElement(2);
    $labels = " ,".$this->getElement(3).",";

    $vt = "";
    foreach($this->params["value_pool"]["sql"] as $key => $value)
    {
      if (strpos($labels,",$key,")>0)
      {
        // echo "<br >$key:  $value";
        $this->params["value_pool"]["sql"][$label] .= " ".$value;
      }
    }
    // echo "<br /><br />$label: ".$this->params["value_pool"]["sql"][$label];

    return;

  }

  function getDescription()
  {
    return "action|fulltext_value|label|fulltextlabels with ,";
  }

}

?>