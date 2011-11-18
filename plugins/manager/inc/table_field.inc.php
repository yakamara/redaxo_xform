<?php

// TODO: function rex_xform_manager_checkField


// ********************************************* FIELD ADD/EDIT/LIST

$func = rex_request("func","string","list");
$page = rex_request("page","string","");
$subpage = rex_request("subpage","string","");
$tripage = rex_request("tripage","string","");
$type_id = rex_request("type_id","string");
$type_name = rex_request("type_name","string");
$field_id = rex_request("field_id","int");
$show_list = TRUE;

$link_vars = "";
foreach($this->getLinkVars() as $k => $v) {
	$link_vars .= '&'.urlencode($k).'='.urlencode($v);
}

$TYPE = array('value' => $I18N->msg("values"), 'validate' => $I18N->msg("validates"), 'action' => $I18N->msg("action"));


// ********************************** TABELLE HOLEN
unset($table);
$tables = $this->getTables();
if(!isset($table))
{
	if(count($tables) > 0) {
		$table = current($tables);
	}else {
		echo 'Keine Tabelle gefunden';
		exit;
	}
}
foreach($tables as $t) {
	if($t["table_name"] == rex_request("table_name")) {
		$table = $t;
		break;
	}
}


echo '<table cellpadding="5" class="rex-table" id="xform-alltables">';
echo '<tr><td><b>'.$I18N->msg("alltables").':</b> ';
foreach($tables as $t) {
	if($t["table_name"] == $table["table_name"]) { 
		echo ' | <b>'.$t["table_name"].'</b> '; 
	}else { 
		echo ' | <a href="index.php?'.$link_vars.'&table_name='.$t["table_name"].'">'.$t["table_name"].'</a> '; 
	}
}
echo '| </td></tr>';
echo '<tr><td>';
if($table["description"] != "") echo " ".$table["description"];
// if($rex_em_opener_info != "") { echo ' - '.$I18N->msg("openerinfo").': '.$rex_em_opener_info; }
echo '</td></tr></table><br />';
$table["fields"] = $this->getTableFields($table["table_name"]);







// ********************************************* Missing Fields

$mfields = rex_xform_manager_table::getMissingFields($table["table_name"]);
// ksort($mfields);
$type_real_field = rex_request("type_real_field","string");
if($type_real_field != "" && !array_key_exists($type_real_field,$mfields)) $type_real_field = "";

if($type_real_field != "") { ?>
	<div class="rex-addon-output"><h2 class="rex-hl2">Folgendes Feld wird verwendet: <?php echo $type_real_field; ?></h2><div class="rex-addon-content"><p class="rex-tx1"><?php

	$rfields = rex_xform_manager_table::getFields($table["table_name"]);
	foreach($rfields[$type_real_field] as $k => $v)
	{
	  echo '<b>'.$k.':</b> '.$v.'<br />';
	}
	
	?></p></div></div><?php 

}



// ********************************************* CHOOSE FIELD
$types = rex_xform::getTypeArray();
if($func == "choosenadd")
{
	// type and choose !!

	$link = 'index.php?'.$link_vars.'&table_name='.$table["table_name"].'&func=add&';

	if(!rex_xform_manager_table::hasId($table["table_name"])) {

		?>
		<div class="rex-addon-output" id="xform-choosenadd"><h2 class="rex-hl2"><?php echo $I18N->msg('xform_id_is_missing'); ?></h2><div class="rex-addon-content">
		<p class="rex-tx1"><?php echo $I18N->msg('xform_id_missing_info'); ?></p>
		</div></div>
		<?php
		
	}else{

		?>
		<div class="rex-addon-output" id="xform-choosenadd"><h2 class="rex-hl2"><?php echo $I18N->msg('choosenadd'); ?></h2><div class="rex-addon-content">
		<p class="rex-tx1"><?php echo $I18N->msg('choosenadd_description'); ?></p>
		</div></div>
		<?php
	
		if($type_real_field == "" && count($mfields)>0 ) { ?>
			<div class="rex-addon-output"><h2 class="rex-hl2">Es gibt noch Felder in der Tabelle welche nicht zugewiesen sind.</h2><div class="rex-addon-content">
			<?php 
			$d = 0;
			foreach($mfields as $k => $v) {
				$d++;
				if($d>50) break;
				$l = 'index.php?'.$link_vars.'&table_name='.$table["table_name"].'&func=choosenadd&type_real_field='.$k.'&type_layout=t';
				echo '<a href="'.$l.'">'.$k.'</a>, ';
			}
			?></div></div>
	
		<?php } ?>
	
	
		<div class="rex-addon-output"><div class="rex-area-col-2">
		
			<div class="rex-area-col-a"><h3 class="rex-hl2">beliebte <?php echo $TYPE['value']; ?></h3><div class="rex-area-content"><p class="rex-tx1"><?php
			if(isset($types['value']))
			{	
				ksort($types['value']);
				foreach($types['value'] as $k => $v)
				{
					if(isset($v["famous"]) && $v["famous"]) {
						echo '<p class="rex-button"><a class="rex-button" href="'.$link.'type_id=value&type_name='.$k.'&type_real_field='.$type_real_field.'">'.$k.'</a> '.$v['description'].'</p>';
					}
				}
			}
			?></p></div></div>
		
			<div class="rex-area-col-b"><h3 class="rex-hl2">beliebte <?php echo $TYPE['validate']; ?></h3><div class="rex-area-content"><p class="rex-tx1"><?php
			if(isset($types['validate']))
			{
				ksort($types['validate']);
				foreach($types['validate'] as $k => $v)
				{
					if(isset($v["famous"]) && $v["famous"]) { 
					echo '<p class="rex-button"><a class="rex-button" href="'.$link.'type_id=validate&type_name='.$k.'">'.$k.'</a> '.$v['description'].'</p>';
					}
				}
			}
			?></p></div></div>
		
		</div></div>

	
		<div class="rex-addon-output"><div class="rex-area-col-2">
		
			<div class="rex-area-col-a"><h3 class="rex-hl2"><?php echo $TYPE['value']; ?></h3><div class="rex-area-content"><p class="rex-tx1"><?php
			if(isset($types['value']))
			{	
				ksort($types['value']);
				foreach($types['value'] as $k => $v)
				{
					if(!isset($v["famous"]) || $v["famous"] !== TRUE) { 
					echo '<p class="rex-button"><a class="rex-button" href="'.$link.'type_id=value&type_name='.$k.'&type_real_field='.$type_real_field.'">'.$k.'</a> '.$v['description'].'</p>';
					}
				}
			}
			?></p></div></div>
		
			<div class="rex-area-col-b"><h3 class="rex-hl2"><?php echo $TYPE['validate']; ?></h3><div class="rex-area-content"><p class="rex-tx1"><?php
			if(isset($types['validate']))
			{
				ksort($types['validate']);
				foreach($types['validate'] as $k => $v)
				{
					if(!isset($v["famous"]) || $v["famous"] !== TRUE) {
					echo '<p class="rex-button"><a class="rex-button" href="'.$link.'type_id=validate&type_name='.$k.'">'.$k.'</a> '.$v['description'].'</p>';
					}
				}
			}
			?></p></div></div>
		
		</div></div>
		
		<!-- 
		<div class="rex-addon-output">
		<h2 class="rex-hl2"><?php echo $TYPE['action']; ?></h2>
		<div class="rex-addon-content">
		<p class="rex-tx1"><?php
		if(isset($types['action']))
		{
			ksort($types['action']);
			foreach($types['action'] as $k => $v)
			{
				echo '<p class="rex-button">"<a href="'.$link.'type_id=action&type_name='.$k.'">'.$k.'</a>" - '.$v['description'].'</p>';
			}
		}
		?></p>
		</div>
		</div>
		-->
	
		<?php
	
	}
	
	echo '<br /><table cellpadding="5" class="rex-table"><tr><td>
		<a href="index.php?'.$link_vars.'&amp;table_name='.$table["table_name"].'"><b>&laquo; '.$I18N->msg('back_to_overview').'</b></a>
		</td></tr></table>';

}





// ********************************************* FORMULAR


if( ($func == "add" || $func == "edit" )  && isset($types[$type_id][$type_name]) )
{

	$xform = new rex_xform;
	$xform->setDebug(FALSE);

	foreach($this->getLinkVars() as $k => $v)
	{
		$xform->setHiddenField($k, $v);
	}

	$xform->setHiddenField("func", $func);
	$xform->setHiddenField("table_name", $table["table_name"]);
	$xform->setHiddenField("type_real_field", $type_real_field);
	
	$xform->setValueField("hidden", array("table_name",$table["table_name"]));
	$xform->setValueField("hidden", array("type_name",$type_name,"REQUEST"));
	$xform->setValueField("hidden", array("type_id",$type_id,"REQUEST"));
	
	$xform->setValueField("text", array("prio","Prioritaet",(rex_xform_manager_table::getMaximumPrio($table["table_name"])+10)));

	$i = 0;
	foreach($types[$type_id][$type_name]['values'] as $v)
	{
		$i++;

		switch($v['type'])
		{

			case("name"):

				if($func == "edit" )
				{
					$xform->setValueField("showvalue",array("f".$i,"Name"));
				}else
				{
					if(!isset($v["value"]) && $type_real_field != "")
						$v["value"] = $type_real_field;
					elseif(!isset($v["value"]))
						$v["value"] = "";
					
					$xform->setValueField("text",array("f".$i,"Name",$v["value"]));
					$xform->setValidateField("notEmpty",array("f".$i,$I18N->msg("validatenamenotempty")));
					$xform->setValidateField("preg_match",array("f".$i,"/(([a-zA-Z])+([a-zA-Z0-9\_])*)/",$I18N->msg("validatenamepregmatch")));
					$xform->setValidateField("customfunction",array("f".$i,"rex_xform_manager_checkField",array("table_name" => $table["table_name"]), $I18N->msg("validatenamecheck")));
				}
				break;

			case("no_db"):
				$xform->setValueField("checkbox",array("f".$i,$I18N->msg("donotsaveindb"),1,0));
				break;

			case("boolean"):
				// checkbox|check_design|Bezeichnung|Value|1/0|[no_db]
				$xform->setValueField("checkbox",array("f".$i,$v['label']));
				break;

			case("select"):
				// select|gender|Geschlecht *|Frau=w;Herr=m|[no_db]|defaultwert|multiple=1
				$xform->setValueField("select",array("f".$i,$v['label'],$v['definition'],"",$v['default'],0));
				break;

			case("table"):
				// ist fest eingetragen, damit keine Dinge durcheinandergehen
				
				if($func == "edit" )
				{
					$xform->setValueField("showvalue",array("f".$i,$v['label']));
				}else
				{
					$_tables = rex_xform_manager_table::getTables();
					$_options = array();
					foreach($_tables as $_table)
					{
							$_options[$_table->getTableName()] = str_replace("=","-",$_table->getName().' ['.$_table->getTableName().']').'='.$_table->getTableName();
							$_options[$_table->getTableName()] = str_replace(",",".",$_options[$_table->getTableName()]);
					}
					if(!isset($v['default'])) {
						$v['default'] = "";
					}
					$xform->setValueField("select",array("f".$i,$v['label'],implode(",",$_options),"",$v['default'],0));
				
				}
				break;

			case("textarea"):
				$xform->setValueField("textarea",array("f".$i,$v['label']));
				break;

			case("table.field"):
				// Todo:

			case("select_name"):
				$_fields = array();
				foreach(rex_xform_manager_table::getXFormFieldsByType($table["table_name"]) as $_k => $_v) { $_fields[] = $_k; }
				$xform->setValueField("select",array("f".$i,$v['label'],implode(",",$_fields),"","",0));
				break;
				
			case("select_names"):
				// Todo: Mehrere Namen aus denanderen Federn ziehen und als multiselectbox anbieten

			default:
				// nur beim "Bezeichnungsfeld"
				if($i == 2 && $type_real_field != "" && !isset($v["value"]))
					$v["value"] = $type_real_field;
			  elseif(!isset($v["value"]))
			    $v["value"] = "";
				$xform->setValueField("text",array("f".$i,$v['label'],$v["value"]));
		}

	}
	
	$xform->setActionField("showtext",array("",'<p>'.$I18N->msg("thankyouforentry").'</p>'));
	$xform->setObjectparams("main_table",'rex_xform_field'); // f�r db speicherungen und unique abfragen

	if($func == "edit")
	{
		$xform->setHiddenField("field_id",$field_id);
		$xform->setActionField("manage_db",array('rex_xform_field',"id=$field_id"));
		$xform->setObjectparams("main_id",$field_id);
		$xform->setObjectparams("main_where","id=$field_id");
		$xform->setObjectparams("getdata",TRUE);

	}elseif($func == "add")
	{
		$xform->setActionField("manage_db",array('rex_xform_field'));
		
	}

	if($type_id == "value")
	{
		$xform->setValueField("checkbox",array("list_hidden",$I18N->msg("hideinlist"),1,"1"));
    	$xform->setValueField("checkbox",array("search",$I18N->msg("useassearchfieldalidatenamenotempty"),1,"1"));
	
	}elseif($type_id == "validate")
	{
		$xform->setValueField("hidden",array("list_hidden",1));
		
	}

	$form = $xform->getForm();

	if($xform->objparams["form_show"])
	{
		if($func == "add")
			echo '<div class="rex-area"><h3 class="rex-hl2">'.$I18N->msg("addfield").' "'. $type_name .'"</h3><div class="rex-area-content">';
		else
			echo '<div class="rex-area"><h3 class="rex-hl2">'.$I18N->msg("editfield").' "'. $type_name .'"</h3><div class="rex-area-content">';
		echo $form;
		echo '</div></div>';
		echo '<br />&nbsp;<br /><table cellpadding="5" class="rex-table"><tr><td>
			<a href="index.php?'.$link_vars.'&amp;table_name='.$table["table_name"].'"><b>&laquo; '.$I18N->msg('back_to_overview').'</b></a>
			</td></tr></table>';
		$func = "";
	}else
	{
		if($func == "edit")
		{
		  $this->generateAll();
			echo rex_info($I18N->msg("thankyouforupdate"));
			
		}elseif($func == "add")
		{
		  $this->generateAll();
			echo rex_info($I18N->msg("thankyouforentry"));
			
		}
		$func = "list";
	}
}





// ********************************************* LOESCHEN
if($func == "delete"){

	$sf = new rex_sql();
	// $sf->debugsql = 1;
	$sf->setQuery('select * from rex_xform_field where table_name="'.$table["table_name"].'" and id='.$field_id);
	$sfa = $sf->getArray();
	if(count($sfa) == 1)
	{
		$query = 'delete from rex_xform_field where table_name="'.$table["table_name"].'" and id='.$field_id;
		$delsql = new rex_sql;
		// $delsql->debugsql=1;
		$delsql->setQuery($query);
		echo rex_info($I18N->msg("tablefielddeleted"));
		$this->generateAll();
		
	}else
	{
		echo rex_warning($I18N->msg("tablefieldnotfound"));
	}
	$func = "list";
}








// ********************************************* CREATE/UPDATE FIELDS
if($func == "updatetable")
{
	$this->generateAll();
	echo rex_info($I18N->msg("tablesupdated"));
	$func = "list";
}

if($func == "updatetablewithdelete")
{
	$this->generateAll(array("delete_fields" => TRUE));
	echo rex_info($I18N->msg("tablesupdated"));
	$func = "list";
}







// ********************************************* LIST
if($func == "list"){


	
	// ****** EP XFORM_MANAGER_TABLE_FIELD_FUNC

	$show_list = TRUE;
	$show_list = rex_register_extension_point('XFORM_MANAGER_TABLE_FIELD_FUNC', $show_list,
				array(
					'table' => $t,
					'link_vars' => $this->getLinkVars(),
				)
			);
	
	
	
	if($show_list) {
	
	
		function rex_xform_list_format($p, $value = "")
		{
			if($value != "") $p["value"] = $value;
			switch($p["list"]->getValue('type_id'))
			{
				case("validate"):
					$style = 'color:#aaa;'; // background-color:#cfd9d9; 
					break;
				case("action"):
					$style = 'background-color:#cfd9d9;';
					break;
				default:
					$style = 'background-color:#eff9f9;';
					break;
			}
			return '<td style="'.$style.'">'.$p["value"].'</td>';
		}
	
		function rex_xform_list_edit_format($p)
		{
			global $REX,$I18N;
			return rex_xform_list_format($p, $p["list"]->getColumnLink($I18N->msg("edit"),$I18N->msg("edit")));
		}
	
		function rex_xform_list_delete_format($p)
		{
			global $REX,$I18N;
			return rex_xform_list_format($p, $p["list"]->getColumnLink($I18N->msg("delete"),$I18N->msg("delete")));
		}
	
		echo '<table cellpadding=5 class=rex-table>
		<tr><td><a href=index.php?'.$link_vars.'&table_name='.$table["table_name"].'&func=choosenadd><b>+ '.$I18N->msg("addtablefield").'</b></td>
		<td style="text-align:right;">
			<a href=index.php?'.$link_vars.'&table_name='.$table["table_name"].'&func=updatetable><b>o '.$I18N->msg("updatetable").'</b></a>
			<a href=index.php?'.$link_vars.'&table_name='.$table["table_name"].'&func=updatetablewithdelete><b>o '.$I18N->msg("updatetable_with_delete").'</b></a>
			</td></tr>
		</table><br />';
	
		$sql = 'select * from rex_xform_field where table_name="'.$table["table_name"].'" order by prio';
		$list = rex_list::factory($sql,30);
		// $list->debug = 1;
		$list->setColumnFormat('id', 'Id');
		
		foreach($this->getLinkVars() as $k => $v)
		{
			$list->addParam($k, $v);
		}
		
		$list->addParam("table_name", $table["table_name"]);
	
		$list->removeColumn('table_name');
		$list->removeColumn('id');
		$list->removeColumn('list_hidden');
		$list->removeColumn('search');
	
		$list->setColumnLayout('prio', array('<th>###VALUE###</th>','###VALUE###'));
		$list->setColumnFormat('prio', 'custom', 'rex_xform_list_format' );
		$list->setColumnLayout('type_id', array('<th>###VALUE###</th>','###VALUE###'));
		$list->setColumnFormat('type_id', 'custom', 'rex_xform_list_format' );
		$list->setColumnLayout('type_name', array('<th>###VALUE###</th>','###VALUE###'));
		$list->setColumnFormat('type_name', 'custom', 'rex_xform_list_format' );
		$list->setColumnLayout('f1', array('<th>label</th>','###VALUE###')); // ###VALUE###
		$list->setColumnFormat('f1', 'custom', 'rex_xform_list_format' );
	
		for($i=2;$i<10;$i++){ $list->removeColumn('f'.$i); }
	
		$list->addColumn($I18N->msg("edit"),$I18N->msg("edit"));
		$list->setColumnParams($I18N->msg("edit"), array("field_id"=>"###id###","func"=>"edit",'type_name'=>'###type_name###','type_id'=>'###type_id###',));
		$list->setColumnLayout($I18N->msg("edit"), array('<th>###VALUE###</th>','###VALUE###'));
		$list->setColumnFormat($I18N->msg("edit"), 'custom', 'rex_xform_list_edit_format' );
	
		$list->addColumn($I18N->msg("delete"),$I18N->msg("delete"));
		$list->setColumnParams($I18N->msg("delete"), array("field_id"=>"###id###","func"=>"delete"));
		$list->setColumnLayout($I18N->msg("delete"), array('<th>###VALUE###</th>','###VALUE###'));
		$list->setColumnFormat($I18N->msg("delete"), 'custom', 'rex_xform_list_delete_format' );
	
		echo $list->get();

	}

}

