<?php

$page = rex_request("page","string");
$searchall = rex_request("searchall","int",0);
$page_size = 50;

// $zip_table
// $zip_fields plz/lat/lng/city/state


if($page == "print") {

  ob_end_clean();
  ob_end_clean();
	
	$plz = rex_request("plz","string");
	
	$geo_search_text = rex_request("geo_search_text","string");
	$geo_search_page = rex_request("geo_search_page","int");
	$geo_search_page_size = rex_request("geo_search_page_size","int",50);
	if($geo_search_page_size < 0 or $geo_search_page_size > 200) $geo_search_page_size = 50;
	
	$geo_bounds_top = rex_request("geo_bounds_top","string");
	$geo_bounds_right = rex_request("geo_bounds_right","string");
	$geo_bounds_bottom = rex_request("geo_bounds_bottom","string");
	$geo_bounds_left = rex_request("geo_bounds_left","string");
	
	$geo_search_zoom = rex_request("geo_search_zoom","int");

	$geo_center_lng = ($geo_bounds_right + $geo_bounds_left)/2;
	$geo_center_lat = ($geo_bounds_top + $geo_bounds_bottom)/2;
	
	$radius = 6368; // Erdradius (geozentrischer Mittelwert) in Km
	
	$rad_l = $geo_center_lng / 180 * M_PI;
	$rad_b = $geo_center_lat / 180 * M_PI;

	if($geo_search_zoom < 8) {
		// Zufaellsliste
			$distance_field = "";
			$distance_where = "";
			$distance_order = 'rand('.date('Ymd').')';
	
	}else{
		// Zoom 10 = Standard
		
		$umkreis = 130;
		
		$distance_field = "(".$radius." * SQRT(2*(1-cos(RADIANS(pos_lat)) * cos(".$rad_b.") * (sin(RADIANS(pos_lng)) *
 sin(".$rad_l.") + cos(RADIANS(pos_lng)) * cos(".$rad_l.")) - sin(RADIANS(pos_lat)) * sin(".$rad_b.")))) AS Distance,";
 
		$distance_where = "".$radius." * SQRT(2*(1-cos(RADIANS(pos_lat)) *  cos(".$rad_b.") * (sin(RADIANS(pos_lng)) * sin(".$rad_l.") + cos(RADIANS(pos_lng)) * cos(".$rad_l.")) - sin(RADIANS(pos_lat)) * sin(".$rad_b."))) <= ".$umkreis." and ";

		$distance_order = 'Distance';
	
	
	}

	$sql_pos_add = ' '.$pos_lng.'<>"" and '.$pos_lat.'<>"" ';
	
	if($searchall != 1)
		if($geo_bounds_top != "" && $geo_bounds_bottom != "" && $geo_bounds_left != "" && $geo_bounds_right != "") {
			$sql_pos_add = '
				('.$pos_lng.'>'.$geo_bounds_left.' and '.$pos_lng.'<'.$geo_bounds_right.')
				and ('.$pos_lat.'<'.$geo_bounds_top.' and '.$pos_lat.'>'.$geo_bounds_bottom.')
			';
		}

	$sql_where = "";
	if($where != "") {
		$sql_where = ' AND ('.$where.') ';
	}
	
	$sql_vt_add = '';

	if($geo_search_page<0) $geo_search_page = 0;
	$sql_limit_from = ($geo_search_page*$geo_search_page_size);
	$sql_limit_to = (($geo_search_page+1)*$geo_search_page_size)+1;
	$sql_limit = ' Order by '.$distance_order; // .' LIMIT '.$sql_limit_from.','.$sql_limit_to; // rand(20)

	$max_fields = 'min('.$pos_lng.') as min_lng,
					max('.$pos_lng.') as max_lng,
					min('.$pos_lat.') as min_lat,
					max('.$pos_lat.') as max_lat';

	$sql = 'select '.$max_fields.' from '.$table.' where '.$sql_pos_add.$sql_where.$sql_vt_add;
	$gd = rex_sql::factory();
	// $gd->debugsql = 1;
	$gd->setQuery($sql);
	$bounds = $gd->getArray();








	$sql = 'select '.$distance_field.$fields.','.$pos_lng.' as lng,'.$pos_lat.' as lat from '.$table.' where '.$distance_where.$sql_pos_add.$sql_where.$sql_vt_add.$sql_limit;
	$gd = rex_sql::factory();
	// $gd->debugsql = 1;
	$gd->setQuery($sql);
	
	$output = array();
	$output["data"] = $gd->getArray();
	$output["bounds"] = $bounds;
	
	// echo json_encode($gd->getArray());
	
	?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Buchschenkservice - PopUp Druckadressen</title>
	<meta name="language" content="de" />
	</head>
	<body><?php

  echo "<h2>Druckansicht</h2>";
	
	echo '<p>';
	if($plz != "")
  	echo 'PLZ: '.$plz.' <br /><br />';
	
	foreach($output["data"] as $d) {
	
	  $print = $print_view; // '<p><b>###firma_1###<br />###firma_2###</b><br />###strasse_1_hausnummer###<br />###plz_strasse### ###ort###<br />###telefon_nummern###<br /><a href="http://###url_adressen###" target="_blank">###url_adressen###</a></p>';
	  
	  foreach($d as $k => $v) {
	    $print = str_replace("###".$k."###",htmlspecialchars($v), $print);
	    $print = str_replace("***".$k."***",urlencode($v), $print);
	    $print = str_replace("---".$k."---",$v, $print);
	  }

    echo ''.$print;
	
	}

	echo '</p>';

  ?><script>
  window.print();
  </script><style>

  p,h2 {
    font-size: 12px;
    font-family: Arial, Verdana;
  }  
  
  p {
    border-bottom: 1px solid #333;
    padding-bottom: 10px;
  }
  
  h2 {
    font-size: 16px;
  }
  
  </style><?php

  echo '</body></html>';

  exit;


}





// Data as json
$rex_geo_func = rex_request("rex_geo_func","string");
switch($rex_geo_func)
{

	case("plz"):

		ob_end_clean();
		ob_end_clean();
		
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		// header('Content-type: application/json');

		$return = array();
		$plz = rex_request("plz","string");

		if(strlen($plz) > 2)
		{
			$s = rex_sql::factory();
			$s->setQuery('select * from '.$zip_table.' where '.$zip_fields[0].' LIKE "'.mysql_real_escape_string($plz).'%"');

			foreach($s->getArray() as $p)
			{
				$return[] = array(
					"id" => $p[$zip_fields[0]],
					"label" => $p[$zip_fields[0]].' - '.$p[$zip_fields[3]].' / '.$p[$zip_fields[4]],
					"value" => $p[$zip_fields[3]],
					"lat" => $p[$zip_fields[1]],
					"lng" => $p[$zip_fields[2]]
				);
			}
			
		}

		echo json_encode($return);
		exit;

	case("city"):

		ob_end_clean();
		ob_end_clean();
		
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		// header('Content-type: application/json');

		$return = array();
		$city = rex_request("city","string");

		if(strlen($city) > 3)
		{
			$s = rex_sql::factory();
			// $s->debugsql = 1;
			$s->setQuery('select '.$zip_fields[0].', '.$zip_fields[3].', '.$zip_fields[4].', '.$zip_fields[1].', '.$zip_fields[2].' from '.$zip_table.' where '.$zip_fields[3].' LIKE "'.mysql_real_escape_string($city).'%" group by '.$zip_fields[0].','.$zip_fields[3].' LIMIT 40');

			foreach($s->getArray() as $p)
			{
				$return[] = array(
					"id" => $p[$zip_fields[0]],
					"label" => $p[$zip_fields[0]].' - '.$p[$zip_fields[3]].' / '.$p[$zip_fields[4]],
					"value" => $p[$zip_fields[3]],
					"lat" => $p[$zip_fields[1]],
					"lng" => $p[$zip_fields[2]]

				);
			}
			
		}

		echo json_encode($return);
		exit;

	case("datalist"):
		ob_end_clean();
		ob_end_clean();
		
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		// header('Content-type: application/json');

		$geo_search_text = rex_request("geo_search_text","string");
		$geo_search_page = rex_request("geo_search_page","int");
		$geo_search_page_size = rex_request("geo_search_page_size","int",50);
		if($geo_search_page_size < 0 or $geo_search_page_size > 200) $geo_search_page_size = 50;
		
		$geo_bounds_top = rex_request("geo_bounds_top","string");
		$geo_bounds_right = rex_request("geo_bounds_right","string");
		$geo_bounds_bottom = rex_request("geo_bounds_bottom","string");
		$geo_bounds_left = rex_request("geo_bounds_left","string");
		
		$geo_search_zoom = rex_request("geo_search_zoom","int");

		$geo_center_lng = ($geo_bounds_right + $geo_bounds_left)/2;
		$geo_center_lat = ($geo_bounds_top + $geo_bounds_bottom)/2;
		
		$radius = 6368; // Erdradius (geozentrischer Mittelwert) in Km
		
		$rad_l = $geo_center_lng / 180 * M_PI;
		$rad_b = $geo_center_lat / 180 * M_PI;

		if($geo_search_zoom < 8) {
			// Zufaellsliste
				$distance_field = "";
				$distance_where = "";
				$distance_order = 'rand('.date('Ymd').')';
		
		}else{
			// Zoom 10 = Standard
			
			$umkreis = 130;
			
			$distance_field = "(".$radius." * SQRT(2*(1-cos(RADIANS(pos_lat)) * cos(".$rad_b.") * (sin(RADIANS(pos_lng)) *
	 sin(".$rad_l.") + cos(RADIANS(pos_lng)) * cos(".$rad_l.")) - sin(RADIANS(pos_lat)) * sin(".$rad_b.")))) AS Distance,";
	 
			$distance_where = "".$radius." * SQRT(2*(1-cos(RADIANS(pos_lat)) *  cos(".$rad_b.") * (sin(RADIANS(pos_lng)) * sin(".$rad_l.") + cos(RADIANS(pos_lng)) * cos(".$rad_l.")) - sin(RADIANS(pos_lat)) * sin(".$rad_b."))) <= ".$umkreis." and ";
	
			$distance_order = 'Distance';
		
		
		}


		




		$sql_pos_add = ' '.$pos_lng.'<>"" and '.$pos_lat.'<>"" ';
		
		if($searchall != 1)
			if($geo_bounds_top != "" && $geo_bounds_bottom != "" && $geo_bounds_left != "" && $geo_bounds_right != "") {
				$sql_pos_add = '
					('.$pos_lng.'>'.$geo_bounds_left.' and '.$pos_lng.'<'.$geo_bounds_right.')
					and ('.$pos_lat.'<'.$geo_bounds_top.' and '.$pos_lat.'>'.$geo_bounds_bottom.')
				';
			}

		$sql_where = "";
		if($where != "") {
			$sql_where = ' AND ('.$where.') ';
		}
		
		$sql_vt_add = '';

		if($geo_search_page<0) $geo_search_page = 0;
		$sql_limit_from = ($geo_search_page*$geo_search_page_size);
		$sql_limit_to = (($geo_search_page+1)*$geo_search_page_size)+1;
		$sql_limit = ' Order by '.$distance_order.' LIMIT '.$sql_limit_from.','.$sql_limit_to; // rand(20)

		$max_fields = 'min('.$pos_lng.') as min_lng,
						max('.$pos_lng.') as max_lng,
						min('.$pos_lat.') as min_lat,
						max('.$pos_lat.') as max_lat';

		$sql = 'select '.$max_fields.' from '.$table.' where '.$sql_pos_add.$sql_where.$sql_vt_add;
		$gd = rex_sql::factory();
		// $gd->debugsql = 1;
		$gd->setQuery($sql);
		$bounds = $gd->getArray();

		$sql = 'select '.$distance_field.$fields.','.$pos_lng.' as lng,'.$pos_lat.' as lat from '.$table.' where '.$distance_where.$sql_pos_add.$sql_where.$sql_vt_add.$sql_limit;
		$gd = rex_sql::factory();
		// $gd->debugsql = 1;
		$gd->setQuery($sql);
		
		$output = array();
		$output["data"] = $gd->getArray();
		$output["bounds"] = $bounds;
		
		// echo json_encode($gd->getArray());
		
		echo json_encode($output);
		
		exit;
		break;
}

?>

<link rel="stylesheet" href="/files/addons/xform/plugins/geo/jquery_ui.css" type="text/css" media="all" />
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js" type="text/javascript"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript" src="/files/addons/xform/plugins/geo/geo_zip.js"></script>
<link rel="stylesheet" type="text/css" href="/files/addons/xform/plugins/geo/geo_zip.css" />

<div id="rex-googlemap" style="width:<?php echo $map_width; ?>px; height:<?php echo $map_height; ?>px;"></div>

<script type="text/javascript">

jQuery(document).ready(function(){

	var map_options = {
		div_id: "rex-googlemap",
		dataUrl: "<?php echo rex_getUrl($REX["ARTICLE_ID"],'',array('rex_geo_func' => 'datalist'),'&'); ?>",
		plzUrl: "<?php echo rex_getUrl($REX["ARTICLE_ID"],'',array('rex_geo_func' => 'plz'),'&'); ?>",
		cityUrl: "<?php echo rex_getUrl($REX["ARTICLE_ID"],'',array('rex_geo_func' => 'city'),'&'); ?>",
		page_size: <?php echo $page_size; ?>,
		page_loading: '<div class="rex-geo-loading"></div>',
		sidebar_view: '<?php echo $view; ?>',
		print_view: '<?php echo $print_view; ?>',
		map_view: '<?php echo $map_view; ?>',
		fulltext: 1,
		zoom:5,
    marker_icon_normal: "/files/addons/xform/plugins/geo/icon_normal.png",
    marker_icon_active: "/files/addons/xform/plugins/geo/icon_active.png",
	};

	map_explorer = new rex_xform_geomap(map_options); //
	map_explorer.initialize();


});
	    
</script>