<?php

class rex_xform_action_db_query extends rex_xform_action_abstract
{

  function execute()
  {

    $query = trim($this->getElement(2));

    if($query == "")
    {
      if ($this->params["debug"])
      {
        echo 'ActionQuery Error: no query';
      }
      return;
    }

    $sql = rex_sql::factory();
    if ($this->params["debug"]) $sql->debugsql = TRUE;

    foreach($this->params["value_pool"]["sql"] as $key => $value)
    {
      $query = str_replace('###'.$key.'###',addslashes($value),$query);
    }

    $sql->setQuery($query);

    if( $sql->getError() != "")
    {
      $this->params["form_show"] = TRUE;
      $this->params["hasWarnings"] = TRUE;
      $this->params["warning_messages"][] = $this->getElement(3);
    }

  }

  function getDescription()
  {
    return "action|db_query|query|Fehlermeldung";
  }

}

?>