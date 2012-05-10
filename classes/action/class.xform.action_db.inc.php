<?php

class rex_xform_action_db extends rex_xform_action_abstract
{

  function execute()
  {

    $sql = rex_sql::factory();
    if ($this->params["debug"]) $sql->debugsql = TRUE;

    $main_table = "";
    if (!$main_table = $this->getElement(2))
    {
      $main_table = $this->params["main_table"];
    }

    if ($main_table == "")
    {
        $this->params["form_show"] = TRUE;
        $this->params["hasWarnings"] = TRUE;
        $this->params["warning_messages"][] = $this->params["Error-Code-InsertQueryError"];
        return FALSE;
    }

    $sql->setTable($main_table);

    $where = "";
    if ($where = $this->getElement(3))
    {
      if($where == "main_where")
      {
        $where = $this->params["main_where"];
      }
    }

    foreach($this->params["value_pool"]["sql"] as $key => $value)
    {
      $sql->setValue($key, $value);
      if ($where != "") $where = str_replace('###'.$key.'###',addslashes($value),$where);
    }

    if ($where != "")
    {
      $sql->setWhere($where);
      $saved = $sql->update();
      $flag = "update";
    }else
    {
      $saved = $sql->insert();
      $flag = "insert";
      $id = $sql->getLastId();

      $this->params["main_id"] = $id;
      $this->params["value_pool"]["email"]["ID"] = $id;
      // $this->params["value_pool"]["sql"]["ID"] = $id;
      if ($id == 0)
      {
        $this->params["form_show"] = TRUE;
        $this->params["hasWarnings"] = TRUE;
        $this->params["warning_messages"][] = $this->params["Error-Code-InsertQueryError"];
      }
    }

    rex_register_extension_point('REX_XFORM_SAVED', $saved, array('form' => $this, 'sql' => $sql, 'xform' => true));
  }

  function getDescription()
  {
    return "action|db|tblname|[where(id=2)/main_where]";
  }

}

?>