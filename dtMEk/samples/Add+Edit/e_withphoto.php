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

				$sql2 = $db->query("UPDATE $table SET photo = '".$id.".".$ext."' WHERE $table_id = $id");
				$result .= 'Photo Uploaded Successfully<br />';

			}


		} else {

			if (!is_numeric($_GET['id'])) {

				$sql2 = $db->query("UPDATE $table SET photo = 'default_photo.png' WHERE $table_id = $id");
				$result .= 'Default Photo Applied Successfully<br />';

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

						<input type="submit" class="col-md-12 btn btn-success" value="<? echo $edit ? "Update" : "Add"; ?>" />
						<input type="hidden" name="id" id="id" value="<?=$_GET['id'];?>" />

					</form>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!--/.col (left) -->
          </div>   <!-- /.row -->
        </section><!-- /.content -->

      </div>

<?php include '../../layoutfooter.php'; ?>
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
</script>
