<?php

class rex_xform_google_geocode extends rex_xform_abstract
{

  function enterObject()
  {

    $labels       = explode(",",$this->getElement(2)); // Fields of Position
    $fields_count = count($labels);

    $address = explode(",",$this->getElement(3)); // Fields of getPosition

    $label = $this->getElement(4);

    $map_width = 400;
    if ($this->getElement(5) != "") {
      $map_width = (int) $this->getElement(5);
    }
    $map_height = 200;
    if ($this->getElement(6) != "") {
      $map_height = (int) $this->getElement(6);
    }

    foreach($this->obj as $o) {
      switch($fields_count) {
        // one field for latlng
        case(1):
          if($o->getName() == $labels[0]) {
            if($o->getValue()!='' && strpos($o->getValue(),',')) {
              $tmp       = explode(',',$o->getValue());
              $value_lat = str_replace(',','.',$tmp[0]);
              $value_lng = str_replace(',','.',$tmp[1]);
            } else {
              $value_lat = $value_lng = 0;
            }
            $labels[1] = 0;
          }
          break;
        // two fields for latlng
        case(2):
          if($o->getName() == $labels[0]) {
            $value_lng = $o->getValue()!= '' ? str_replace(',','.',$o->getValue()) : 0;
          }
          if($o->getName() == $labels[1]) {
            $value_lat = $o->getValue()!= '' ? str_replace(',','.',$o->getValue()) : 0;
          }
          break;
      }
    }

    if ($this->getValue() == "" && !$this->params["send"]) {
      $this->setValue($this->getElement(4));
    }

    $wc = "";
    if (isset($this->params["warning"][$this->getId()])) {
      $wc = $this->params["warning"][$this->getId()];
    }

    $output = "";
    // Script nur beim ersten mal ausgeben
    if (!defined('REX_XFORM_GOOGLE_GEOCODE_JSCRIPT')) {
      define('REX_XFORM_GOOGLE_GEOCODE_JSCRIPT', true);
      $output .= '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>';
    }

    $map_id = 'map_canvas'.$this->getId();

  echo '<script type="text/javascript">
  //<![CDATA[
  var fields_count = '.$fields_count.';

  var rex_geo_coder = function()
  {

    var myLatlng = new google.maps.LatLng('.$value_lat.', '.$value_lng.');
      var myOptions = {
        zoom: 8,
        center: myLatlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
      }

      var map = new google.maps.Map(document.getElementById("'.$map_id.'"), myOptions);

    var marker = new google.maps.Marker({
          position: myLatlng,
          map: map,
          draggable: true
      });

    google.maps.event.addListener(marker, "dragend", function() {
      rex_geo_updatePosition(marker.getPosition());
    });

    geocoder = new google.maps.Geocoder();

    rex_geo_updatePosition = function(latLng) {
      if(fields_count==2) {
        jQuery(".formlabel-'.$labels[1].' input").val(latLng.lat());
        jQuery(".formlabel-'.$labels[0].' input").val(latLng.lng());
      } else {
        jQuery(".formlabel-'.$labels[0].' input").val(latLng.lat()+","+latLng.lng());
      }

    }

    rex_geo_getPosition = function(address) {

      fields = address.split(",");
      for(i=0;i<fields.length;i++)
      {
        jQuery(function($){
          fields[i] = $(".formlabel-"+fields[i].trim()+" input").val();
        });
      }

      address = fields.join(",");

      geocoder.geocode( { "address": address}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
              if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {

                map.setCenter(results[0].geometry.location);

                marker = new google.maps.Marker({
                    position: results[0].geometry.location,
                    map: map,
                    title:address,
                draggable: true
                });

          rex_geo_updatePosition(marker.getPosition());

              } else {
                // alert("No results found");
              }
            } else {
              // alert("Geocode was not successful for the following reason: " + status);
            }
          });

    }

    rex_geo_resetPosition = function() {

      jQuery(function($){
        if(fields_count==2) {
          $(".formlabel-'.$labels[1].' input").val("0");
          $(".formlabel-'.$labels[0].' input").val("0");
        } else {
          $(".formlabel-'.$labels[0].' input").val("0");
        }
      });

      marker.setMap(null);

    }

  }

  jQuery(function($){
    rex_geo_coder'.$this->getId().' = new rex_geo_coder();
  });

  //]]>
  </script>
  ';

    $output .= '
      <div class="xform-element form_google_geocode '.$this->getHTMLClass().'" id="'.$this->getHTMLId().'">
        <label class="text '.$wc.'" for="'.$this->getFieldId().'">'.$label.'</label>
        <p class="form_google_geocode">';

    $output .= '<a href="#" onclick="rex_geo_getPosition(\''.implode(",",str_replace('"','',$address)).'\')">Geodaten holen</a> | ';

    $output .= '<a href="#" onclick="rex_geo_resetPosition()">Geodaten nullen</a></p>
        <div class="form_google_geocode_map" id="'.$map_id.'" style="width:'.$map_width.'px; height:'.$map_height.'px">Google Map</div>
      </div>';

    $this->params["form_output"][$this->getId()] = $output;
  }


  function getDescription()
  {
    return "google_geocode -> Beispiel: google_geocode|gcode|lng_label,lat_label|strasse,plz,ort|Google Map|width|height|
    ";
  }


  function getDefinitions()
  {
    return array(
      'type' => 'value',
      'name' => 'google_geocode',
      'values' => array(
        array( 'type' => 'name',     'label' => 'Name' ),
        array( 'type' => 'getNames', 'label' => '"lng"-Feldname,"lat"-Feldname'),
        array( 'type' => 'getNames', 'label' => 'Names Positionsfindung'),
        array( 'type' => 'text',     'label' => 'Bezeichnung'),
        array( 'type' => 'text',     'label' => 'Map-Breite'),
        array( 'type' => 'text',     'label' => 'Map-H&ouml;he'),
        ),
      'description' => 'GoogeMap Positionierung',
      'dbtype' => 'text'
      );
  }


}

?>