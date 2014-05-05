
noch nicht fertig.. Um fremde Tabellen zu übernehmen

<?php

// TODO:


// ********************************************* TABLE IMPORT

/*

$show_tablelist = TRUE;


// Alle vorhandenen Tabellen holen
$REX["EM_TABLES_EXISTS"] = array();
$gt = rex_sql::factory();
$gt->setQuery('select table_name from rex_em_table');
foreach($gt->getArray() as $t) { $REX["EM_TABLES_EXISTS"][] = $t["table_name"]; }

$REX["EM_TABLES_FORBIDDEN"] = array('rex_em_field','rex_em_table');

$table_name = rex_request("table_name","string");


if($table_name != "")
{

    if(in_array($table_name,$REX["EM_TABLES_EXISTS"]))
    {
        echo rex_warning('Tabelle ist bereits aufgenommen.');

    }elseif(in_array($table_name,$REX["EM_TABLES_FORBIDDEN"]))
    {
        echo rex_warning('Der Import dieser Tabelle ist nicht erlaubt.');

    }else
    {

        $t = rex_sql::factory();
        $t->debugsql = 1;
        $t->setQuery('SELECT COLUMN_NAME, EXTRA, COLUMN_KEY, DATA_TYPE, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = "'.$table_name.'"');

        if($t->getRows()==0)
        {
            echo rex_warning('Tabelle wurde nicht gefunden oder hat keine Spalten.');

        }else
        {
            $show_tablelist = FALSE;
            $c = array();
            foreach($t->getArray() as $v) { $c[$v["COLUMN_NAME"]] = $v; }

            if( !isset($c["id"]) || $c["id"]["EXTRA"] != "auto_increment")
            {
                echo rex_warning('Es können nur Tabellen importiert werden die ein Feld "id" mit EXTRA: "auto_increment" und COLUMN_KEY:"unique" haben.');

            }else
            {

                echo "<br />Tabelle wird umbenannt in : newname. ";

                $imf = array()
                $prio = 0;
                foreach($c as $feld => $v)
                {
                    $prio = $prio + 10;
                    switch($v["DATA_TYPE"])
                    {
                        case("int"):

                            break;
                        case("varchar"):

                            break;
                        case("float"):

                            break;
                        case("varchar"):

                            break;
                        case("text"):
                        default:

                            break;
                    }




                    echo "<br />$feld -> ".$v["DATA_TYPE"]."--".$v["EXTRA"]."--".$v["COLUMN_DEFAULT"];





                rex_em_data_buchhandlung     100     value     text     name     Name





                }


                // echo '<pre>'; var_dump($c); echo '</pre>';

                $show_tablelist = FALSE;
                // $list = rex_list::factory('SELECT COLUMN_NAME, EXTRA, COLUMN_KEY, DATA_TYPE, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = "'.$table_name.'"', 100);
                // echo $list->get();

            }



        }

    }

    // rex_yb_book


}







function rex_em_status_col($params)
{
    global $I18N,$REX;
    $list = $params["list"];
    if(in_array($list->getValue("table_name"),$REX["EM_TABLES_EXISTS"]))
        return 'bereits aufgenommen';
    elseif(in_array($list->getValue("table_name"),$REX["EM_TABLES_FORBIDDEN"]))
        return 'Import nicht erlaubt';
    else
        return '<a href="?page=editme&subpage=table_import&table_name='.$list->getValue("table_name").'">Tabelle importieren</a>';
}


if($show_tablelist) {

    $sql = 'SELECT table_name, table_type, engine FROM INFORMATION_SCHEMA.TABLES where table_type="BASE TABLE"';

    $list = rex_list::factory($sql,100);

    // $list->setColumnParams("id", array("table_id"=>"###id###","func"=>"edit"));
    $list->removeColumn("id");
    $list->removeColumn("table_type");
    $list->removeColumn("engine");

    $list->addColumn("status","status");
    $list->setColumnFormat('status', 'custom', 'rex_em_status_col');
    $list->setColumnParams("name", array("table_id"=>"###id###","func"=>"edit"));

    echo $list->get();

}

*/
