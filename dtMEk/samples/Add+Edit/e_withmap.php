<?php include '../../layout/header.php';

$title = 'Sample';
$table = 'sample';
$table_id = 'sample_id';

if ($_POST) {

	try {
	$sql = $db->prepare("REPLACE INTO $table VALUES (
	:id,
	:title,
	:title2,
	:lat,
	:lon
	)");

	// check if new or update
	if (is_numeric($_GET['id'])) $id = $_GET['id'];
	else $id = '';

	$sql->bindValue("id", $id);
	$sql->bindValue("title", $_POST['title']);
	$sql->bindValue("title2", $_POST['title2']);
	$sql->bindValue("lat", $_POST['lat']);
	$sql->bindValue("lon", $_POST['lon']);

	if ($sql->execute()) {

		$del_id = $db->lastInsertId();

		if ($_GET['id'] > 0) $label = 'Updated';
		else $label = 'Added';

		$msg = '<div class="alert alert-success alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>'.$label.'!</h4>Item '.$label.' successfully<br />'.$result.'</div>';

		unset($_POST);

	} else {

		$msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>Error</h4>Error Updating / Inserting Record</div>';

	}

	} catch(PDOException $e) {

		$msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>Error</h4>Error Updating / Inserting Record, Database Error: '.$e->getMessage().'</div>';

	}

}

if (is_numeric($_GET['id'])) {

	$edit = true;
	$row = $db->query("SELECT * FROM $table WHERE $table_id = ".$_GET['id'])->fetch();

}




?>
<!-- Content Wrapper. Contains page content -->
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key=AIzaSyBGj9zNMQr5Tw8j0iw5liUGhEIrr8n46UQ"></script>
<script type="text/javascript">

var markersArray = [];
      function initialize() {


		  var getLatLng = function(lat, lng) {
				return new google.maps.LatLng(lat, lng);
			};
		<? if ($row['field_lat'] && $row['field_lon']) { ?>
		var mapOptions = {
          center: { lat: <?=$row['field_lat'];?>, lng: <?=$row['field_lon'];?>},
          zoom: 11
        };
		<? } else { ?>
        var mapOptions = {
          center: { lat: 30.0162668, lng: 31.247869},
          zoom: 11
        };
		<? } ?>
        var map = new google.maps.Map(document.getElementById('map-canvas'),
            mapOptions);

		<? if ($row['field_lat'] && $row['field_lon']) { ?>
		var marker = new google.maps.Marker({
				position: getLatLng(<?=$row['field_lat'];?>, <?=$row['field_lon'];?>),
				map: map,
				id: 'marker'
			});
		markersArray.push(marker);
		<? } ?>

		var addMarker = google.maps.event.addListener(map, 'click', function(e) {
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
		  for (var i = 0; i < markersArray.length; i++ ) {
		   markersArray[i].setMap(null);
		  }
		}

	function getcurrentloc() {

		if(navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {

			var getLatLng = function(lat, lng) {
				return new google.maps.LatLng(lat, lng);
			};

			var mapOptions = {
          	center: { lat: position.coords.latitude, lng: position.coords.longitude},
          	zoom: 17
        	};

        var map = new google.maps.Map(document.getElementById('map-canvas'),
            mapOptions);

		var addMarker = google.maps.event.addListener(map, 'click', function(e) {
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

		}, function() {
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
            <?=$title;?>
            <small><? echo $edit ? "Edit" : "New";?></small>
          </h1>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <!-- left column -->
            <div class="col-md-12">
				<? echo $msg; ?>

              <!-- Input addon -->
              <div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title"><?=$title;?> Information</h3>
                </div>

                <div class="box-body">
					<form role="form" method="post" enctype="multipart/form-data">

						<div id="map-canvas" style="height:300px; width:100%"></div>
						<br />

						<div class="form-group">
							<div class="controls">
								<button class="btn btn-primary" onclick="getcurrentloc(); return false;">Get Current Location</button>
							</div>
						</div>

						<div class="form-group">
							<label>Geolocation</label>
							<div class="controls">
								<input class="form-control" id="field_lat" name="field_lat" type="text" value="<? echo $row['field_lat'] ? $row['field_lat'] : $_POST['field_lat'];?>" placeholder="Latitude" />
								<input class="form-control" id="field_lon" name="field_lon" type="text" value="<? echo $row['field_lon'] ? $row['field_lon'] : $_POST['field_lon'];?>" placeholder="Longitude" />
							</div>
						</div>

						<div class="form-group">
							<label>Title</label>
							<input type="text" class="form-control" name="title" required="required" value="<? echo $_POST['title'] ? $_POST['title'] : $row['title']?>" />
						</div>

						<div class="form-group">
							<label>Title2</label>
							<input type="text" class="form-control" name="title2" required="required" value="<? echo $_POST['title2'] ? $_POST['title2'] : $row['title2']?>" />
						</div>

						<input type="submit" class="col-md-12 btn btn-success" value="<? echo $edit ? "Update" : "Add"; ?>" />
						<input type="hidden" name="id" id="id" value="<?=$_GET['id'];?>" />

					</form>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!--/.col (left) -->
          </div>   <!-- /.row -->
        </section><!-- /.content -->

      </div>

<?php include '../../layout/footer.php'; ?>
<script>
	$('select').select2();
</script>
