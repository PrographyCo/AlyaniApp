<?php
    global $db, $session, $lang;
    
    $edit = false;
    $title = HM_OurLocations;
    $table = 'ourlocations';
    $table_id = 'loc_id';
    
    if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) $id = $_REQUEST['id'];
    else $id = '';
    
    if ($_POST) {
        
        try {
            $sql = $db->prepare("REPLACE INTO $table VALUES (
                           :id,
                           :loc_title_ar,
                           :loc_title_en,
                           :loc_title_ur,
                           :loc_photo,
                           :loc_phones,
                           :loc_order,
                           :loc_active,
                           :loc_lat,
                           :loc_lng,
                           :loc_dateadded,
                           :loc_lastupdated
                       )");
            
            // check if new or update
            
            $sql->bindValue("id", $id);
            $sql->bindValue("loc_title_ar", $_POST['loc_title_ar']);
            $sql->bindValue("loc_title_en", $_POST['loc_title_en']);
            $sql->bindValue("loc_title_ur", $_POST['loc_title_ur']);
            $sql->bindValue("loc_photo", $_POST['loc_photo']);
            $sql->bindValue("loc_phones", $_POST['loc_phones']);
            $sql->bindValue("loc_order", $_POST['loc_order']);
            $sql->bindValue("loc_active", (isset($_POST['loc_active']) ? 1 : 0));
            $sql->bindValue("loc_lat", $_POST['field_lat']);
            $sql->bindValue("loc_lng", $_POST['field_lon']);
            $sql->bindValue("loc_dateadded", ($_POST['loc_dateadded'] ?? time()));
            $sql->bindValue("loc_lastupdated", time());
            
            if ($sql->execute()) {
                $result = '';
                
                $id = $db->lastInsertId();
                
                if ($_FILES['loc_uphoto']['tmp_name']) {
                    $ext = strtolower(pathinfo($_FILES['loc_uphoto']['name'], PATHINFO_EXTENSION));
                    if (copy($_FILES['loc_uphoto']['tmp_name'], 'assets/media/ourlocations/' . $id . '.' . $ext)) {
                        
                        $sql2 = $db->query("UPDATE $table SET loc_photo = '" . $id . "." . $ext . "' WHERE $table_id = $id");
                        $result .= LBL_PhotoUploaded . '<br />';
                        
                    }
                } else if (!is_numeric($_REQUEST['id'])) {
                    
                    $sql2 = $db->query("UPDATE $table SET loc_photo = 'default_photo.png' WHERE $table_id = $id");
                    $result .= LBL_DefaultPhotoUploaded . '<br />';
                    
                }
                
                if ($_REQUEST['id'] > 0) $label = LBL_Updated;
                else $label = LBL_Added;
                
                $msg = '<div class="alert alert-success alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . $label . '!</h4>' . LBL_Item . ' ' . $label . ' ' . LBL_Successfully . '<br />' . $result . '</div>';
    
                $_POST = [];
                
            } else {
                
                $msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . LBL_Error . '</h4>' . LBL_ErrorUpdateAdd . '</div>';
                
            }
            
        } catch (PDOException $e) {
            
            $msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . LBL_Error . '</h4>' . LBL_ErrorDB . ' ' . $e->getMessage() . '</div>';
            
        }
        
    }
    
    if ($id !== '') {
        
        $edit = true;
        $row = $db->query("SELECT * FROM $table WHERE $table_id = " . $id)->fetch();
    }

?>
<!-- Content Wrapper. Contains page content -->
<script type="text/javascript">

    var markersArray = [];

    function initialize() {


        var getLatLng = function (lat, lng) {
            return new google.maps.LatLng(lat, lng);
        };
        <?php if (isset($row) && $row['loc_lat'] && $row['loc_lng']) { ?>
        var mapOptions = {
            center: {lat: <?=$row['loc_lat']?>, lng: <?=$row['loc_lng']?>},
            zoom: 15
        };
        <?php } else { ?>
        var mapOptions = {
            center: {lat: 30.0162668, lng: 31.247869},
            zoom: 11
        };
        <?php } ?>
        var map = new google.maps.Map(document.getElementById('map-canvas'),
            mapOptions);

        // Create the search box and link it to the UI element.
        var input = document.getElementById('pac-input');
        var searchBox = new google.maps.places.SearchBox(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        // Bias the SearchBox results towards current map's viewport.
        map.addListener('bounds_changed', function () {
            searchBox.setBounds(map.getBounds());
        });

        searchBox.addListener('places_changed', function () {
            var places = searchBox.getPlaces();

            if (places.length == 0) {
                return;
            }

            // Clear out the old markers.
            markersArray.forEach(function (marker) {
                marker.setMap(null);
            });
            markersArray = [];

            // For each place, get the icon, name and location.
            var bounds = new google.maps.LatLngBounds();
            places.forEach(function (place) {
                if (!place.geometry) {
                    console.log("Returned place contains no geometry");
                    return;
                }
                /*
    var icon = {
      url: place.icon,
      size: new google.maps.Size(71, 71),
      origin: new google.maps.Point(0, 0),
      anchor: new google.maps.Point(17, 34),
      scaledSize: new google.maps.Size(25, 25)
    };

    // Create a marker for each place.
    markersArray.push(new google.maps.Marker({
      map: map,
      icon: icon,
      title: place.name,
      position: place.geometry.location
    }));
                */
                clearOverlays();
                var lat = place.geometry.location.lat(); // lat of clicked point
                var lng = place.geometry.location.lng(); // lng of clicked point

                $('#field_lat').val(lat);
                $('#field_lon').val(lng);

                var marker = new google.maps.Marker({
                    position: getLatLng(lat, lng),
                    map: map,
                    id: 'marker'
                });
                //map.setCenter(e.latLng);

                markersArray.push(marker);

                if (place.geometry.viewport) {
                    // Only geocodes have viewport.
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            });
            map.fitBounds(bounds);
        });
        
        
        
        
        <?php if (isset($row) && $row['loc_lat'] && $row['loc_lng']) { ?>
        var marker = new google.maps.Marker({
            position: getLatLng(<?=$row['loc_lat']?>, <?=$row['loc_lng']?>),
            map: map,
            id: 'marker'
        });
        markersArray.push(marker);
        <?php } ?>

        var addMarker = google.maps.event.addListener(map, 'click', function (e) {
            clearOverlays();

            var lat = e.latLng.lat(); // lat of clicked point
            var lng = e.latLng.lng(); // lng of clicked point

            $('#field_lat').val(e.latLng.lat());
            $('#field_lon').val(e.latLng.lng());

            var marker = new google.maps.Marker({
                position: getLatLng(lat, lng),
                map: map,
                id: 'marker'
            });
            //map.setCenter(e.latLng);

            markersArray.push(marker);

        });

    }

    function clearOverlays() {
        for (var i = 0; i < markersArray.length; i++) {
            markersArray[i].setMap(null);
        }
    }

    function getcurrentloc() {

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {

                var getLatLng = function (lat, lng) {
                    return new google.maps.LatLng(lat, lng);
                };

                var mapOptions = {
                    center: {lat: position.coords.latitude, lng: position.coords.longitude},
                    zoom: 17
                };

                var map = new google.maps.Map(document.getElementById('map-canvas'),
                    mapOptions);

                var addMarker = google.maps.event.addListener(map, 'click', function (e) {
                    clearOverlays();

                    var lat = e.latLng.lat(); // lat of clicked point
                    var lng = e.latLng.lng(); // lng of clicked point

                    $('#field_lat').val(e.latLng.lat());
                    $('#field_lon').val(e.latLng.lng());

                    var marker = new google.maps.Marker({
                        position: getLatLng(lat, lng),
                        map: map,
                        id: 'marker'
                    });
                    //map.setCenter(e.latLng);

                    markersArray.push(marker);

                });


                var pos = new google.maps.LatLng(position.coords.latitude,
                    position.coords.longitude);

                clearOverlays();


                $('#field_lat').val(position.coords.latitude);
                $('#field_lon').val(position.coords.longitude);

                var marker = new google.maps.Marker({
                    position: pos,
                    map: map,
                    id: 'marker'
                });

                map.setCenter(pos);

                markersArray.push(marker);

            }, function () {
                handleNoGeolocation(true);
            });
        } else {
            // Browser doesn't support Geolocation
            handleNoGeolocation(false);
        }


    }

    google.maps.event.addDomListener(window, 'load', initialize);
</script>
<script type="text/javascript"
        src="//maps.googleapis.com/maps/api/js?key=AIzaSyDFLHZP4EPU6YhCign3pe3RJ944VNZ08YA&libraries=places&callback=initialize"></script>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $title ?>
            <small><?= $edit ? LBL_Edit : LBL_New ?></small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                
                <?= $msg??'' ?>
                <!-- Input addon -->
                <div class="box box-info">

                    <div class="box-body">
                        <form role="form" method="post" enctype="multipart/form-data">
                            <input id="pac-input" class="controls" type="text" placeholder="Search Box">

                            <div id="map-canvas" style="height:300px; width:100%"></div>
                            <br/>

                            <div class="form-group">
                                <div class="controls">
                                    <button class="btn btn-primary"
                                            onclick="getcurrentloc(); return false;" type="button"><?= LBL_GetCurrentLocation ?></button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Geolocation ?></label>
                                <div class="controls">
                                    <input class="form-control" id="field_lat" name="field_lat" type="text"
                                           value="<?= $row['loc_lat'] ?? $_POST['field_lat']??'' ?>"
                                           placeholder="Latitude"/>
                                    <input class="form-control" id="field_lon" name="field_lon" type="text"
                                           value="<?= $row['loc_lng'] ?? $_POST['field_lon']??'' ?>"
                                           placeholder="Longitude"/>
                                </div>
                            </div>

                            <div class="row">

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleAr ?></label>
                                    <input type="text" class="form-control" name="loc_title_ar" required="required"
                                           value="<?= $_POST['loc_title_ar'] ?? $row['loc_title_ar']??'' ?>"/>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleEn ?></label>
                                    <input type="text" class="form-control" name="loc_title_en" required="required"
                                           value="<?php echo $_POST['loc_title_en'] ?? $row['loc_title_en']??'' ?>"/>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleUr ?></label>
                                    <input type="text" class="form-control" name="loc_title_ur" required="required"
                                           value="<?php echo $_POST['loc_title_ur'] ?? $row['loc_title_ur']??'' ?>"/>
                                </div>

                            </div>

                            <div class="form-group">
                                <label><?= LBL_Photo ?></label><br/>
                                <?php if (isset($row['loc_photo'])) echo '<img src="'.ASSETS_PATH.'media/ourlocations/' . $row['loc_photo'] . '" width="150"/>'; ?>
                                <input id="loc_uphoto" name="loc_uphoto" type="file" class="file">
                            </div>

                            <div class="form-group">
                                <label><?= LBL_ContactNumbers ?></label>
                                <input type="text" class="form-control" name="loc_phones"
                                       value="<?php echo $_POST['loc_phones'] ?? $row['loc_phones'] ?? '' ?>"/>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Order ?></label>
                                <input type="text" class="form-control" name="loc_order"
                                       value="<?php echo $_POST['loc_order'] ?? $row['loc_order'] ?? '' ?>"/>
                            </div>

                            <div class="form-group">
                                <label><input type="checkbox"
                                              name="loc_active" <?php if (!isset($row) || $row['loc_active'] == 1) echo 'checked="checked"'; ?> /> <?= LBL_Active ?>
                                </label>
                            </div>

                            <input type="submit" class="col-md-12 btn btn-success"
                                   value="<?php echo $edit ? LBL_Update : LBL_Add; ?>"/>
                            <input type="hidden" name="id" id="id" value="<?= $id ?>"/>
                            <input type="hidden" name="loc_photo" id="loc_photo" value="<?= $row['loc_photo']??'' ?>"/>
                            <input type="hidden" name="loc_dateadded" id="loc_dateadded"
                                   value="<?= $row['loc_dateadded']??'' ?>"/>
                        </form>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!--/.col (left) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->

</div>

<script>
    $('select').select2();
    $("#loc_uphoto").fileinput({
        showRemove: true,
        showUpload: false,
        showCancel: false,
        showPreview: true,
        minFileCount: 0,
        maxFileCount: 1,
        allowedFileTypes: ['image', 'pdf']
    });
</script>
