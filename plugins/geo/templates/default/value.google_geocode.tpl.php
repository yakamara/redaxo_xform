<?php if ($includeGoogleMaps): ?>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
<?php endif ?>
<script type="text/javascript">

    var rex_geo_coder = function() {

        var myLatlng = new google.maps.LatLng(<?php echo $valueLat ?>, <?php echo $valueLng ?>);

        var myOptions = {
            zoom: 8,
            center: myLatlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }

        var map = new google.maps.Map(document.getElementById("map_canvas<?php echo $this->getId() ?>"), myOptions);

        marker = new google.maps.Marker({
            position: myLatlng,
            map: map,
            draggable: true
        });

        google.maps.event.addListener(marker, "dragend", function() {
            rex_geo_updatePosition(marker.getPosition());
        });

        rex_geo_updatePosition = function(latLng) {
            jQuery(".formlabel-<?php echo $labelLat ?> input").val( latLng.lat() );
            jQuery(".formlabel-<?php echo $labelLng ?> input").val( latLng.lng() );
        }

        geocoder = new google.maps.Geocoder();

        rex_geo_getPosition = function(address) {
            fields = address.split(",");
            for(i=0;i<fields.length;i++) {
                jQuery(function($){
                    fields[i] = $(".formlabel-"+fields[i].trim()+" input").val();
                });
            }

            address = fields.join(",");

            geocoder.geocode( { "address": address }, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
                        map.setCenter(results[0].geometry.location);
                        marker.setMap(null);
                        marker = new google.maps.Marker({
                            position: results[0].geometry.location,
                            map: map,
                            title: address,
                            draggable: true
                        });
                        google.maps.event.addListener(marker, "dragend", function() {
                            rex_geo_updatePosition(marker.getPosition());
                        });
                        rex_geo_updatePosition(marker.getPosition());

                    } else {
                        alert("No results found");

                    }
                } else {
                    alert("Geocode was not successful for the following reason: " + status);

                }
            });

        }

        rex_geo_resetPosition = function() {
            jQuery(function($){
                $(".formlabel-<?php echo $labelLat ?> input").val("0");
                $(".formlabel-<?php echo $labelLng ?> input").val("0");
            });
            marker.setMap(null);

        }

    }

    jQuery(function($){
        rex_geo_coder<?php echo $this->getId() ?> = new rex_geo_coder();
    });

</script>

<div class="xform-element form_google_geocode <?php echo $this->getHTMLClass() ?>" id="<?php echo $this->getHTMLId() ?>">
    <label class="text <?php echo $this->getWarningClass() ?>" for="<?php echo $this->getFieldId() ?>"><?php echo $this->getElement(4) ?></label>
    <p class="form_google_geocode">
        <a href="javascript:void(0);" onclick="rex_geo_getPosition('<?php echo $this->getElement(3) ?>')">Geodaten holen</a> |
        <a href="javascript:void(0);" onclick="rex_geo_resetPosition()">Geodaten nullen</a>
    </p>
    <div class="form_google_geocode_map" id="map_canvas<?php echo $this->getId() ?>" style="width:<?php echo $mapWidth ?>px; height:<?php echo $mapHeight ?>px">Google Map</div>
</div>
