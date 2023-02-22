<?php include '../../layout/header.php';

$title = 'Sample';
$table = 'sample';
$table_id = 'sample_id';

if ($_POST) {

	try {
	$sql = $db->prepare("REPLACE INTO $table VALUES (
	:id,
	:title,
	:title2
	)");

	// check if new or update
	if (is_numeric($_GET['id'])) $id = $_GET['id'];
	else $id = '';

	$sql->bindValue("id", $id);
	$sql->bindValue("title", $_POST['title']);
	$sql->bindValue("title2", $_POST['title2']);

	if ($sql->execute()) {

		$id = $db->lastInsertId();

		if ($_FILES['d_file']['tmp_name']) {

			$ext = strtolower(pathinfo($_FILES['d_file']['name'], PATHINFO_EXTENSION));
			if (copy($_FILES['d_file']['tmp_name'], 'media/designs/'.$id.'.'.$ext)) {

				$sql2 = $db->query("UPDATE table SET photo = '".$id.".".$ext."' WHERE id = $id");
				$result .= 'Photo Uploaded Successfully<br />';

			}


		} else {

			if (!is_numeric($_GET['id'])) {

				$sql2 = $db->query("UPDATE table SET photo = 'default_photo.png' WHERE id = $id");
				$result .= 'Default Photo Applied Successfully<br />';

			}


		}

		// Handle Gallery
		if (!file_exists('media/gallery/'.$p_id)) {
			mkdir('media/gallery/'.$p_id, 0777, true);
		}
		// Handle Images
		if ($_FILES['gallery']) {

		    $uploaddir = 'media/gallery/'.$p_id;
			$cnt = 1;
			$len = count($_FILES['gallery']['name']);

			for($i = 0; $i < $len; $i++) {

				$ext = strtolower(pathinfo($_FILES['gallery']['name'][$i], PATHINFO_EXTENSION));
				$uploadfile = $uploaddir . '/File_'.$cnt.'_'.time().'.'.$ext;

				if (move_uploaded_file($_FILES['gallery']['tmp_name'][$i], $uploadfile)) {
					$cnt++;
				}

			}

		}


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

						<div class="form-group">
							<label>Title</label>
							<input type="text" class="form-control" name="title" required="required" value="<? echo $_POST['title'] ? $_POST['title'] : $row['title']?>" />
						</div>

						<div class="form-group">
							<label>Title2</label>
							<input type="text" class="form-control" name="title2" required="required" value="<? echo $_POST['title2'] ? $_POST['title2'] : $row['title2']?>" />
						</div>

						<div class="form-group">
							<label>Image</label><br />
							<? if ($row['d_file']) echo '<img src="media/designs/'.$row['d_file'].'" width="150"/>'; ?>
							<input id="d_file" name="d_file" type="file" class="file">
						</div>

						<div class="form-group">
	                  	<label>Gallery</label><br />


	                  	<?
		                  	if ($row['p_id']) {
				 				$dir = 'media/gallery/'.$row['p_id'];
					 			if (is_dir($dir)) {
								    if ($dh = opendir($dir)) {
										$imgcnt = 1;
								        while (($file = readdir($dh)) !== false) {

									        if ($file != '.' && $file != '..') {


										        echo '<div id="item_'.$row['p_id'].'_'.$imgcnt.'" class="col-md-3" style="border:1px solid #333; margin:10px;">
										        <a href="#" onclick="removepic('.$row['p_id'].', \''.$file.'\', '.$imgcnt.'); return false;" style="display:block; float:right; position:absolute; top:-10px; right:5px; width:26px; height:26px; border:1px solid red; border-radius: 13px; background:red; z-index:2"><img src="images/remove.png" style="width:100%; position: relative; display:block" /></a>


										        <img class="fancybox" src="'.$dir.'/'.$file.'" style="height:120px" /></div>';
												$imgcnt++;
											}
								        }

								        closedir($dh);
								    }
								}
							}

				 			?>

				 			<div class="clearfix" style="margin-top:20px"></div>
				 			<input id="gallery[]" name="gallery[]" type="file" class="file" multiple="multiple">
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
	$("#d_file").fileinput({
		showRemove: true,
		showUpload: false,
		showCancel: false,
		showPreview: true,
		minFileCount: 0,
		maxFileCount: 1,
		allowedFileTypes: ['image', 'pdf']
	});

	function removepic(p_id, filetitle, cnt) {

		var del = confirm('Are you sure you want to remove this image from this project?');
		if (del == true) {

			$('#item_' + p_id + '_' + cnt).css("opacity", 0.5);

			$.post( "post/removepic.php",{ p_id: p_id, filetitle: filetitle })
			.done(function( data ){

					$('#item_' + p_id + '_' + cnt).fadeOut(300, function() { $(this).remove(); });

				});

		}

	}

</script>
