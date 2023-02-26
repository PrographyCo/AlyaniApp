<?php
    global $db,$session,$lang;
    
    $edit = false;
    $title = HM_OurCompanies;
    $table = 'ourcompanies';
    $table_id = 'comp_id';
    
    if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) $id = $_REQUEST['id'];
    else $id = '';
    
    if ($_POST) {
        
        try {
            $sql = $db->prepare("REPLACE INTO $table VALUES (
                           :id,
                           :comp_title_ar,
                           :comp_title_en,
                           :comp_title_ur,
                           :comp_addr_ar,
                           :comp_addr_en,
                           :comp_addr_ur,
                           :comp_photo,
                           :comp_lat,
                           :comp_lng,
                           :comp_phone,
                           :comp_fb,
                           :comp_twitter,
                           :comp_order,
                           :comp_active,
                           :comp_dateadded,
                           :comp_lastupdated
                       )");
            
            // check if new or update
            
            $sql->bindValue("id", $id);
            $sql->bindValue("comp_title_ar", $_POST['comp_title_ar']);
            $sql->bindValue("comp_title_en", $_POST['comp_title_en']);
            $sql->bindValue("comp_title_ur", $_POST['comp_title_ur']);
            $sql->bindValue("comp_addr_ar", $_POST['comp_addr_ar']);
            $sql->bindValue("comp_addr_en", $_POST['comp_addr_en']);
            $sql->bindValue("comp_addr_ur", $_POST['comp_addr_ur']);
            $sql->bindValue("comp_photo", $_POST['comp_photo']);
            $sql->bindValue("comp_lat", $_POST['field_lat']);
            $sql->bindValue("comp_lng", $_POST['field_lon']);
            $sql->bindValue("comp_phone", $_POST['comp_phone']);
            $sql->bindValue("comp_fb", $_POST['comp_fb']);
            $sql->bindValue("comp_twitter", $_POST['comp_twitter']);
            $sql->bindValue("comp_order", $_POST['comp_order']);
            $sql->bindValue("comp_active", (isset($_POST['comp_active']) ? 1 : 0));
            $sql->bindValue("comp_dateadded", ($_POST['comp_dateadded'] ?? time()));
            $sql->bindValue("comp_lastupdated", time());
            
            if ($sql->execute()) {
                $result = '';
                $id = $db->lastInsertId();
                
                if ($_FILES['comp_uphoto']['tmp_name']) {
                    $ext = strtolower(pathinfo($_FILES['comp_uphoto']['name'], PATHINFO_EXTENSION));
                    if (copy($_FILES['comp_uphoto']['tmp_name'], 'assets/media/ourcompanies/' . $id . '.' . $ext)) {
                        
                        $sql2 = $db->query("UPDATE $table SET comp_photo = '" . $id . "." . $ext . "' WHERE $table_id = $id");
                        $result .= LBL_PhotoUploaded . '<br />';
                        
                    }
                } else {
                    if (!is_numeric($_REQUEST['id'])) {
                        
                        $sql2 = $db->query("UPDATE $table SET comp_photo = 'default_photo.png' WHERE $table_id = $id");
                        $result .= LBL_DefaultPhotoUploaded . '<br />';
                        
                    }
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
    
    if (is_numeric($id)) {
        
        $edit = true;
        $row = $db->query("SELECT * FROM $table WHERE $table_id = " .$id)->fetch();
        
    }

?>
<!-- Content Wrapper. Contains page content -->
<script type="text/javascript"
        src="//maps.googleapis.com/maps/api/js?key=AIzaSyDFLHZP4EPU6YhCign3pe3RJ944VNZ08YA&libraries=places&callback=initialize"></script>
<script type="text/javascript">

    var markersArray = [];

    function initialize() {


        var getLatLng = function (lat, lng) {
            return new google.maps.LatLng(lat, lng);
        };
        <?php if (isset($row) && $row['comp_lat'] && $row['comp_lng']) { ?>
        var mapOptions = {
            center: {lat: <?=$row['comp_lat']?>, lng: <?=$row['comp_lng']?>},
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
        
        
        
        
        <?php if (isset($row) && $row['comp_lat'] && $row['comp_lng']) { ?>
        var marker = new google.maps.Marker({
            position: getLatLng(<?=$row['comp_lat']?>, <?=$row['comp_lng']?>),
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
                                            onclick="getcurrentloc(); return false;"><?= LBL_GetCurrentLocation ?></button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label><?= LBL_Geolocation ?></label>
                                <div class="controls">
                                    <input class="form-control" id="field_lat" name="field_lat" type="text"
                                           value="<?= $row['comp_lat'] ?? $_POST['field_lat'] ?? '' ?>"
                                           placeholder="Latitude"/>
                                    <input class="form-control" id="field_lon" name="field_lon" type="text"
                                           value="<?= $row['comp_lng'] ?? $_POST['field_lon'] ?? '' ?>"
                                           placeholder="Longitude"/>
                                </div>
                            </div>

                            <div class="row">

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleAr ?></label>
                                    <input type="text" class="form-control" name="comp_title_ar" required="required"
                                           value="<?= $_POST['comp_title_ar'] ?? $row['comp_title_ar'] ?? '' ?>"/>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleEn ?></label>
                                    <input type="text" class="form-control" name="comp_title_en" required="required"
                                           value="<?= $_POST['comp_title_en'] ?? $row['comp_title_en'] ?? '' ?>"/>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_TitleUr ?></label>
                                    <input type="text" class="form-control" name="comp_title_ur" required="required"
                                           value="<?= $_POST['comp_title_ur'] ?? $row['comp_title_ur'] ?? '' ?>"/>
                                </div>

                            </div>

                            <div class="row">

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_AddrAr ?></label>
                                    <input type="text" class="form-control" name="comp_addr_ar" required="required"
                                           value="<?= $_POST['comp_addr_ar'] ?? $row['comp_addr_ar'] ?? '' ?>"/>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_AddrEn ?></label>
                                    <input type="text" class="form-control" name="comp_addr_en" required="required"
                                           value="<?= $_POST['comp_addr_en'] ?? $row['comp_addr_en'] ?? '' ?>"/>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label><?= LBL_AddrUr ?></label>
                                    <input type="text" class="form-control" name="comp_addr_ur" required="required"
                                           value="<?= $_POST['comp_addr_ur'] ?? $row['comp_addr_ur'] ?? '' ?>"/>
                                </div>

                            </div>

                            <div class="form-group">
                                <label><?= LBL_Photo ?></label><br/>
                                <?php if (isset($row['comp_photo']) && $row['comp_photo']) echo '<img src="'.ASSETS_PATH.'media/ourcompanies/' . $row['comp_photo'] . '" width="150"/>'; ?>
                                <input id="comp_uphoto" name="comp_uphoto" type="file" class="file">
                            </div>

                            <div class="form-group">
                                <label><?= LBL_PhoneNumber ?></label>
                                <input type="text" class="form-control" name="comp_phone"
                                       value="<?= $_POST['comp_phone'] ?? $row['comp_phone'] ?? '' ?>"/>
                            </div>

                            <div class="row">

                                <div class="form-group col-sm-6">
                                    <label><?= LBL_Facebook ?></label>
                                    <input type="text" class="form-control" name="comp_fb"
                                           value="<?= $_POST['comp_fb'] ?? $row['comp_fb'] ?? '' ?>"/>
                                </div>

                                <div class="form-group col-sm-6">
                                    <label><?= LBL_Twitter ?></label>
                                    <input type="text" class="form-control" name="comp_twitter"
                                           value="<?= $_POST['comp_twitter'] ?? $row['comp_twitter'] ?? '' ?>"/>
                                </div>

                            </div>

                            <div class="form-group">
                                <label><?= LBL_Order ?></label>
                                <input type="text" class="form-control" name="comp_order"
                                       value="<?= $_POST['comp_order'] ?? $row['comp_order'] ?? '' ?>"/>
                            </div>

                            <div class="form-group">
                                <label><input type="checkbox"
                                              name="comp_active" <?php if (!isset($row) || $row['comp_active'] === 1) echo 'checked="checked"'; ?> /> <?= LBL_Active ?>
                                </label>
                            </div>

                            <input type="submit" class="col-md-12 btn btn-success"
                                   value="<?= $edit ? LBL_Update : LBL_Add ?>"/>
                            <input type="hidden" name="id" id="id" value="<?= $id ?>"/>
                            <input type="hidden" name="comp_photo" id="comp_photo" value="<?= $row['comp_photo']??'' ?>"/>
                            <input type="hidden" name="comp_dateadded" id="comp_dateadded"
                                   value="<?= $row['comp_dateadded'] ?? '' ?>"/>
                        </form>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!--/.col (left) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->

</div>

<script>
    $('select').select2();
    $("#comp_uphoto").fileinput({
        showRemove: true,
        showUpload: false,
        showCancel: false,
        showPreview: true,
        minFileCount: 0,
        maxFileCount: 1,
        allowedFileTypes: ['image', 'pdf']
    });
</script>
