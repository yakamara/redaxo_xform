<?php

$map_width = rex_request("map_width","int",920);
$map_height = rex_request("map_height","int",500);

$table = "REX_VALUE[1]";
$pos_lng = "REX_VALUE[2]";
$pos_lat = "REX_VALUE[3]";
$fields = "REX_VALUE[4]";

$zip_table = "REX_VALUE[8]";
$zip_fields = explode(",","REX_VALUE[9]");

$vt_fields = "REX_VALUE[5]";
$where = "REX_VALUE[6]";
$view = str_replace("<br />","","REX_VALUE[7]");
$view = str_replace("\n","",html_entity_decode($view));
$view = str_replace("\r","",$view);

$map_view = str_replace("<br />","","REX_VALUE[10]");
$map_view = str_replace("\n","",html_entity_decode($map_view));
$map_view = str_replace("\r","",$map_view);

$print_view = str_replace("<br />","","REX_VALUE[11]");
$print_view = str_replace("\n","",html_entity_decode($print_view));
$print_view = str_replace("\r","",$print_view);

if($zip_table != "") {
  include $REX["INCLUDE_PATH"].'/addons/xform/plugins/geo/module/rex_geo_map_zip.php';
} else {
  include $REX["INCLUDE_PATH"].'/addons/xform/plugins/geo/module/rex_geo_map.php';
}

?>