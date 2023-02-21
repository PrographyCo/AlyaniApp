<? include 'header.php';

$title = HM_Room;
$table = 'buildings_rooms';
$table_id = 'room_id';

if ($_POST) {

	// check if new or update
	if (is_numeric($_GET['id'])) $id = $_GET['id'];
	else $id = '';

	$chk = $db->prepare("SELECT $table_id FROM $table WHERE room_title = :title AND room_bld_id = :room_bld_id AND $table_id != '$id' LIMIT 1");
	$chk->bindValue("title", $_POST['room_title']);
	$chk->bindValue("room_bld_id", $_POST['room_bld_id']);
	$chk->execute();

	if ($chk->rowCount() > 0) {

		$msg = '<div class="alert alert-danger alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>'.LBL_Error.'</h4>'.LBL_Title.' '.LBL_AlreadyExists.'</div>';

	} else {

		try {

			$sql = $db->prepare("REPLACE INTO $table VALUES (
			:id,
			:room_bld_id,
			:room_floor_id,
			:room_title,
			:room_gender,
			:room_capacity,
			:room_order,
			:room_active,
			:room_dateadded,
			:room_lastupdated
			)");

			$sql->bindValue("id", $id);
			$sql->bindValue("room_bld_id", $_POST['room_bld_id']);
			$sql->bindValue("room_floor_id", $_POST['room_floor_id']);
			$sql->bindValue("room_title", $_POST['room_title']);
			$sql->bindValue("room_gender", $_POST['room_gender']);
			$sql->bindValue("room_capacity", $_POST['room_capacity']);
			$sql->bindValue("room_order", $_POST['room_order']);
			$sql->bindValue("room_active", ($_POST['room_active'] ? 1 : 0));
			$sql->bindValue("room_dateadded", ($_POST['room_dateadded'] ?: time()));
			$sql->bindValue("room_lastupdated", time());

			if ($sql->execute()) {

				$id = $db->lastInsertId();

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
											<label><?=HM_Building;?></label>
											<select name="room_bld_id" class="form-control select2" onchange="bldchoosen(this.value);">
												<option value=""><?=LBL_Choose;?></option>
												<?
												$sqlb = $db->query("SELECT bld_id, bld_title FROM buildings WHERE bld_active = 1 ORDER BY bld_order");
												while ($rowb = $sqlb->fetch(PDO::FETCH_ASSOC)) {

													echo '<option value="'.$rowb['bld_id'].'" ';
													if ($row['room_bld_id'] == $rowb['bld_id']) echo 'selected="selected"';
													echo '>'.$rowb['bld_title'].'</option>';

												}
												?>
											</select>
										</div>

										<div class="form-group">
											<label><?=HM_Floor;?></label>
											<span id="floorsarea">
											<? if ($row) { ?>

											<select name="room_floor_id" class="form-control select2">
												<option value=""><?=LBL_Choose;?></option>
												<?
												$sqlf = $db->query("SELECT floor_id, floor_title FROM buildings_floors WHERE floor_active = 1 AND floor_bld_id = ".$row['room_bld_id']." ORDER BY floor_order");
												while ($rowf = $sqlf->fetch(PDO::FETCH_ASSOC)) {

													echo '<option value="'.$rowf['floor_id'].'" ';
													if ($row['room_floor_id'] == $rowf['floor_id']) echo 'selected="selected"';
													echo '>'.$rowf['floor_title'].'</option>';

												}
												?>
											</select>

										<? } else {

											echo '<br />'.LBL_ChooseBuildingFirst;

										}?>
										</span>
										</div>


										<div class="form-group">
											<label><?=LBL_RoomNumber;?></label>
											<input type="text" class="form-control" name="room_title" required="required" value="<? echo $_POST['room_title'] ?: $row['room_title']?>" />
										</div>

										<div class="form-group">
											<label><?=LBL_Gender;?></label>
											<select name="room_gender" class="form-control select2">
												<option value="m" <? if ($row['room_gender'] == 'm') echo 'selected="selected"';?>><?=LBL_Male;?></option>
												<option value="f" <? if ($row['room_gender'] == 'f') echo 'selected="selected"';?>><?=LBL_Female;?></option>
											</select>
										</div>

										<div class="form-group">
											<label><?=LBL_Capacity;?></label>
											<input type="number" class="form-control" name="room_capacity" value="<? echo $_POST['room_capacity'] ?: $row['room_capacity']?>" />
										</div>

										<div class="form-group">
											<label><?=LBL_Order;?></label>
											<input type="text" class="form-control" name="room_order" value="<? echo $_POST['room_order'] ?: $row['room_order']?>" />
										</div>

										<div class="form-group">
											<label><input type="checkbox" name="room_active" <? if (!$row || $row['room_active'] == 1) echo 'checked="checked"';?> /> <?=LBL_Active;?></label>
										</div>

										<input type="submit" class="col-md-12 btn btn-success" value="<? echo $edit ? LBL_Update : LBL_Add; ?>" />
										<input type="hidden" name="id" id="id" value="<?=$_GET['id'];?>" />
										<input type="hidden" name="room_dateadded" id="room_dateadded" value="<?=$row['room_dateadded'];?>" />
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

	function bldchoosen(bld_id) {

		if (bld_id > 0) {

			$('#floorsarea').html('<br /><?=LBL_Loading;?>');

			var data = {
			 bld_id:bld_id
			 };

			 $.post('post/grabfloors.php', data, function(response){

				 $('#floorsarea').html(response);
				 $('select').select2();
			 });

		} else {

			$('#floorsarea').html('<br /><?=LBL_ChooseBuildingFirst;?>');

		}

	}
</script>
