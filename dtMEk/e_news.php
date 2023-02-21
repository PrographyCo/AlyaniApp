<? include 'header.php';

$title = HM_News;
$table = 'news';
$table_id = 'news_id';

if ($_POST) {

	try {
	$sql = $db->prepare("REPLACE INTO $table VALUES (
	:id,
	:news_body_ar,
	:news_body_en,
	:news_body_ur,
	:news_photo,
	:news_order,
	:news_active,
	:news_dateadded,
	:news_lastupdated
	)");

	// check if new or update
	if (is_numeric($_GET['id'])) $id = $_GET['id'];
	else $id = '';

	$sql->bindValue("id", $id);
	$sql->bindValue("news_body_ar", $_POST['news_body_ar']);
	$sql->bindValue("news_body_en", $_POST['news_body_en']);
	$sql->bindValue("news_body_ur", $_POST['news_body_ur']);
	$sql->bindValue("news_photo", $_POST['news_photo']);
	$sql->bindValue("news_order", $_POST['news_order']);
	$sql->bindValue("news_active", ($_POST['news_active'] ? 1 : 0));
	$sql->bindValue("news_dateadded", ($_POST['news_dateadded'] ?: time()));
	$sql->bindValue("news_lastupdated", time());

	if ($sql->execute()) {

		$id = $db->lastInsertId();

		if ($_FILES['news_uphoto']['tmp_name']) {
			$ext = strtolower(pathinfo($_FILES['news_uphoto']['name'], PATHINFO_EXTENSION));
			$newname = GUID();
			if (copy($_FILES['news_uphoto']['tmp_name'], 'media/news/'.$newname.'.'.$ext)) {

				$sql2 = $db->query("UPDATE $table SET news_photo = '".$newname.".".$ext."' WHERE $table_id = $id");
				$result .= LBL_PhotoUploaded.'<br />';

			}
		} else {
			if (!is_numeric($_GET['id'])) {

				$sql2 = $db->query("UPDATE $table SET news_photo = 'default_photo.png' WHERE $table_id = $id");
				$result .= LBL_DefaultPhotoUploaded.'<br />';

			}
		}

		if ($_GET['id'] > 0) $label = LBL_Updated;
		else $label = LBL_Added;

		$msg = '<div class="alert alert-success alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>'.$label.'!</h4>'.LBL_Item.' '.$label.' '.LBL_Successfully.'<br />'.$result.'</div>';

		unset($_POST);

	} else {

		$msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>'.LBL_Error.'</h4>'.LBL_ErrorUpdateAdd.'</div>';

	}

	} catch(PDOException $e) {

		$msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>'.LBL_Error.'</h4>'.LBL_ErrorDB.' '.$e->getMessage().'</div>';

	}

}

if (is_numeric($_GET['id'])) {

	$edit = true;
	$row = $db->query("SELECT * FROM $table WHERE $table_id = ".$_GET['id'])->fetch();

}

?>
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            <?=$title;?>
            <small><? echo $edit ? LBL_Edit : LBL_New;?></small>
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

                <div class="box-body">
									<form role="form" method="post" enctype="multipart/form-data">

										<div class="row">

											<div class="form-group col-sm-4">
												<label><?=LBL_DescrAr;?></label>
												<textarea name="news_body_ar" class="form-control"><? echo $_POST['news_body_ar'] ?: $row['news_body_ar']?></textarea>
											</div>

											<div class="form-group col-sm-4">
												<label><?=LBL_DescrEn;?></label>
												<textarea name="news_body_en" class="form-control"><? echo $_POST['news_body_en'] ?: $row['news_body_en']?></textarea>
											</div>

											<div class="form-group col-sm-4">
												<label><?=LBL_DescrUr;?></label>
												<textarea name="news_body_ur" class="form-control"><? echo $_POST['news_body_ur'] ?: $row['news_body_ur']?></textarea>
											</div>

										</div>

										<div class="form-group">
											<label><?=LBL_Photo;?></label><br />
											<? if ($row['news_photo']) echo '<img src="media/news/'.$row['news_photo'].'" width="150"/>'; ?>
											<input id="news_uphoto" name="news_uphoto" type="file" class="file">
										</div>

										<div class="form-group">
											<label><?=LBL_Order;?></label>
											<input type="text" class="form-control" name="news_order" value="<? echo $_POST['news_order'] ?: $row['news_order']?>" />
										</div>

										<div class="form-group">
											<label><input type="checkbox" name="news_active" <? if (!$row || $row['news_active'] == 1) echo 'checked="checked"';?> /> <?=LBL_Active;?></label>
										</div>

										<input type="submit" class="col-md-12 btn btn-success" value="<? echo $edit ? LBL_Update : LBL_Add; ?>" />
										<input type="hidden" name="id" id="id" value="<?=$_GET['id'];?>" />
										<input type="hidden" name="news_photo" id="news_photo" value="<?=$row['news_photo'];?>" />
										<input type="hidden" name="news_dateadded" id="news_dateadded" value="<?=$row['news_dateadded'];?>" />
									</form>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!--/.col (left) -->
          </div>   <!-- /.row -->
        </section><!-- /.content -->

      </div>

<? include 'footer.php'; ?>
<script>
	$('select').select2();
	$("#news_uphoto").fileinput({
		showRemove: true,
		showUpload: false,
		showCancel: false,
		showPreview: true,
		minFileCount: 0,
		maxFileCount: 1,
		allowedFileTypes: ['image', 'pdf']
	});
</script>
