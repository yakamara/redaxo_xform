<?php

class rex_xform_action_readtable extends rex_xform_action_abstract
{

  function execute()
  {

    foreach($this->params["value_pool"]["email"] as $k => $v)
    {
      if ($this->getElement(4) == $k) $value = $v;
    }

    $gd = rex_sql::factory();
    if ($this->params["debug"]) $gd->debugsql = 1;
    $gd->setQuery('select * from '.$this->getElement(2).' where '.$this->getElement(3).'="'.addslashes($value).'"');

    if ($gd->getRows()==1)
    {
      $ar = $gd->getArray();

      foreach($ar[0] as $k => $v)
      {
        $this->params["value_pool"]["email"][$k] = $v;
      }
    }

    return;


  }

  function getDescription()
  {
    return "action|readtable|tablename|feldname|label";
  }

}

?>