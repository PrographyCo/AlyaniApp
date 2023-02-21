<? include 'header.php';

$title = HM_ContactInfo;
$table = 'contactinfo';
$table_id = 'ci_id';

if ($_POST) {

	try {
	$sql = $db->prepare("REPLACE INTO $table VALUES (
	:id,
	:ci_value,
	:ci_type,
	:ci_order
	)");

	// check if new or update
	if (is_numeric($_GET['id'])) $id = $_GET['id'];
	else $id = '';

	$sql->bindValue("id", $id);
	$sql->bindValue("ci_value", $_POST['ci_value']);
	$sql->bindValue("ci_type", $_POST['ci_type']);
	$sql->bindValue("ci_order", $_POST['ci_order']);

	if ($sql->execute()) {

		$del_id = $db->lastInsertId();

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
<!-- Content Wrapper. Contains page content -->

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

										<div class="form-group">
											<label><?=LBL_Type;?></label>
											<select name="ci_type" id="ci_type" class="form-control select2">
												<option value="1" <? if ($row['ci_type'] == 1) echo 'selected="selected"';?>><?=LBL_Phone;?></option>
												<option value="2" <? if ($row['ci_type'] == 2) echo 'selected="selected"';?>><?=LBL_Email;?></option>
											</select>
										</div>

										<div class="form-group">
											<label><?=LBL_Name;?></label>
											<input type="text" class="form-control" name="ci_value" required="required" value="<? echo $_POST['ci_value'] ? $_POST['ci_value'] : $row['ci_value']?>" />
										</div>

										<div class="form-group">
											<label><?=LBL_Order;?></label>
											<input type="text" class="form-control" name="ci_order" value="<? echo $_POST['ci_order'] ? $_POST['ci_order'] : $row['ci_order']?>" />
										</div>

										<input type="submit" class="col-md-12 btn btn-success" value="<? echo $edit ? LBL_Update : LBL_Add; ?>" />
										<input type="hidden" name="id" id="id" value="<?=$_GET['id'];?>" />

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
</script>
