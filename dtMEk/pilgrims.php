<? include 'header.php';

$title = HM_PILGRIMS;
$table = 'pils';
$table_id = 'pil_id';
$newedit_page = 'e_pil.php';

	if (is_numeric($_GET['del'])) {

		$id = $_GET['del'];

		$pil_code = $db->query("SELECT pil_code FROM pils WHERE pil_id = $id")->fetchColumn();
		$photo = $db->query("SELECT pil_photo FROM $table WHERE $table_id = $id")->fetchColumn();
		$qr_photo = $db->query("SELECT pil_qrcode FROM $table WHERE $table_id = $id")->fetchColumn();

		if ($pil_code) $sqldel2 = $db->query("DELETE FROM pils_accomo WHERE pil_code = '$pil_code'");
		if ($photo && $photo != 'default_photo.png' && $photo != 'default_male.png' && $photo != 'default_female.png' && is_file('media/pils/'.$photo)) @unlink('media/pils/'.$photo);
		if ($qr_photo && is_file('media/pils_qrcodes/'.$qr_photo)) @unlink('media/pils_qrcodes/'.$qr_photo);

		$sqldel1 = $db->query("DELETE FROM $table WHERE $table_id = $id");

	}

	if ($_GET['deleteall'] == 1) {

		if (is_numeric($_GET['city_id']) && $_GET['city_id'] > 0) $sqlmore1 = " AND pil_city_id = ".$_GET['city_id'];
		if ($_GET['gender'] == 'm' || $_GET['gender'] == 'f') $sqlmore2 = " AND pil_gender = '".$_GET['gender']."'";
		if (is_numeric($_GET['pilc_id']) && $_GET['pilc_id'] > 0) $sqlmore3 = " AND pil_pilc_id = ".$_GET['pilc_id'];
		if ($_GET['code']) $sqlmore4 = " AND pil_code LIKE '%".$_GET['code']."%'";
		if ($_GET['resno']) $sqlmore5 = " AND pil_reservation_number LIKE '%".$_GET['resno']."%'";
		if (is_numeric($_GET['accomo']) && $_GET['accomo'] == 1) $sqlmore6 = " AND pil_code IN (SELECT pil_code FROM pils_accomo)";
		if (is_numeric($_GET['accomo']) && $_GET['accomo'] == 2) $sqlmore6 = " AND pil_code NOT IN (SELECT pil_code FROM pils_accomo)";


		$sqlallpils = $db->query("SELECT p.*, c.country_title_$lang, ci.city_title_$lang FROM $table p
		LEFT OUTER JOIN countries c ON p.pil_country_id = c.country_id
		LEFT OUTER JOIN cities ci ON p.pil_city_id = ci.city_id
		WHERE 1 $sqlmore1 $sqlmore2 $sqlmore3 $sqlmore4 $sqlmore5 $sqlmore6
		ORDER BY pil_name");

		while ($rowallpils = $sqlallpils->fetch(PDO::FETCH_ASSOC)) {

			$id = $rowallpils['pil_id'];
			
			$pil_code = $db->query("SELECT pil_code FROM pils WHERE pil_id = $id")->fetchColumn();
			$photo = $db->query("SELECT pil_photo FROM $table WHERE $table_id = $id")->fetchColumn();
			$qr_photo = $db->query("SELECT pil_qrcode FROM $table WHERE $table_id = $id")->fetchColumn();

			if ($pil_code) $sqldel2 = $db->query("DELETE FROM pils_accomo WHERE pil_code = '$pil_code'");
			if ($photo && $photo != 'default_photo.png' && $photo != 'default_male.png' && $photo != 'default_female.png' && is_file('media/pils/'.$photo)) @unlink('media/pils/'.$photo);
			if ($qr_photo && is_file('media/pils_qrcodes/'.$qr_photo)) @unlink('media/pils_qrcodes/'.$qr_photo);

			$sqldel1 = $db->query("DELETE FROM $table WHERE $table_id = $id");

		}

	}

?>
<!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            <?=$title;?>
            <a href="<?=$newedit_page;?>" class="btn btn-success pull-<?=DIR_AFTER;?>"><i class="fa fa-star"></i> <?=BTN_AddNew;?></a>
					<? /*
						<a href="export_pils_cards.php?city_id=<?=$_GET['city_id'];?>&gender=<?=$_GET['gender'];?>&pilc_id=<?=$_GET['pilc_id'];?>&code=<?=$_GET['code'];?>&resno=<?=$_GET['resno'];?>&accomo=<?=$_GET['accomo'];?>" class="btn btn-success pull-<?=DIR_AFTER;?>" style="margin-<?=DIR_AFTER;?>: 10px"><i class="fa fa-file-pdf-o"></i> <?=BTN_ExportPilsCards;?></a>
					*/ ?>
                     	<a href="pil_cards_designs/pils/generated/sticker.php??city_id=<?=$_GET['city_id'];?>&gender=<?=$_GET['gender'];?>&pilc_id=<?=$_GET['pilc_id'];?>&code=<?=$_GET['code'];?>&resno=<?=$_GET['resno'];?>&accomo=<?=$_GET['accomo'];?>" target="_blank" class="btn btn-success pull-<?=DIR_AFTER;?>" style="margin-<?=DIR_AFTER;?>: 10px"><i class="fa fa-file-pdf-o"></i> <?=LBL_sticker;?></a>
                     	<a href="pil_cards_designs/pils/generated/index.php??city_id=<?=$_GET['city_id'];?>&gender=<?=$_GET['gender'];?>&pilc_id=<?=$_GET['pilc_id'];?>&code=<?=$_GET['code'];?>&resno=<?=$_GET['resno'];?>&accomo=<?=$_GET['accomo'];?>" target="_blank" class="btn btn-success pull-<?=DIR_AFTER;?>" style="margin-<?=DIR_AFTER;?>: 10px"><i class="fa fa-file-pdf-o"></i> <?=BTN_ExportPilsCards;?></a>
                    
                    					
						<a href="export_pils.php?city_id=<?=$_GET['city_id'];?>&gender=<?=$_GET['gender'];?>&pilc_id=<?=$_GET['pilc_id'];?>&code=<?=$_GET['code'];?>&resno=<?=$_GET['resno'];?>&accomo=<?=$_GET['accomo'];?>" class="btn btn-success pull-<?=DIR_AFTER;?>" style="margin-<?=DIR_AFTER;?>: 10px"><i class="fa fa-file-excel-o"></i> <?=BTN_ExportToExcel;?></a>
						<a href="import_pils.php" class="btn btn-success pull-<?=DIR_AFTER;?>" style="margin-<?=DIR_AFTER;?>: 10px"><i class="fa fa-file-excel-o"></i> <?=BTN_ImportFromExcel;?></a>
						<a href="?deleteall=1&city_id=<?=$_GET['city_id'];?>&gender=<?=$_GET['gender'];?>&pilc_id=<?=$_GET['pilc_id'];?>&code=<?=$_GET['code'];?>&resno=<?=$_GET['resno'];?>&accomo=<?=$_GET['accomo'];?>" onclick="return confirm('<?=LBL_DeleteConfirm;?>');" class="btn btn-danger pull-<?=DIR_AFTER;?>" style="margin-<?=DIR_AFTER;?>: 10px"><i class="fa fa-trash"></i> <?=BTN_DELETEALLPILS;?></a>
          </h1>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <!-- left column -->
            <div class="col-md-12">
						<? echo $msg; ?>
							<div class="box">
								<div class="box-body">
									<form method="get">
										<div class="row">
											<div class="form-group col-sm-4">
												<label><?=LBL_City;?></label>
												<select name="city_id" id="city_id" class="form-control select2">
													<option value="0">
														<?=LBL_Choose;?>
													</option>
													<?
														$sqlcities = $db->query("SELECT * FROM cities WHERE city_active = 1 ORDER BY city_title_".$lang);
														while ($rowc = $sqlcities->fetch(PDO::FETCH_ASSOC)){
															echo '<option value="'.$rowc['city_id'].'" ';
															if ($_GET['city_id'] == $rowc['city_id']) echo 'selected="selected"';
															echo '>
															'.$rowc['city_title_'.$lang].'
															</option>';
														}
													?>

												</select>
											</div>
											<div class="form-group col-sm-4">
												<label><?=LBL_Gender;?></label>
												<select name="gender" id="gender" class="form-control select2">
													<option value="0">
														<?=LBL_Choose;?>
													</option>
													<option value="m" <? if ($_GET['gender'] == 'm') echo 'selected="selected"'; ?>>
														<?=LBL_Male;?>
													</option>
													<option value="f" <? if ($_GET['gender'] == 'f') echo 'selected="selected"'; ?>>
														<?=LBL_Female;?>
													</option>
												</select>
											</div>
											<div class="form-group col-sm-4">
												<label><?=LBL_Class;?></label>
												<select name="pilc_id" id="pilc_id" class="form-control select2">
													<option value="0">
														<?=LBL_Choose;?>
													</option>
													<?
														$sqlpilc = $db->query("SELECT * FROM pils_classes WHERE pilc_active = 1 ORDER BY pilc_title_".$lang);
														while ($rowpc = $sqlpilc->fetch(PDO::FETCH_ASSOC)){
															echo '<option value="'.$rowpc['pilc_id'].'" ';
															if ($_GET['pilc_id'] == $rowpc['pilc_id']) echo 'selected="selected"';
															echo '>
															'.$rowpc['pilc_title_'.$lang].'
															</option>';
														}
													?>

												</select>
											</div>
										</div>

										<div class="row">
											<div class="form-group col-sm-4">
												<label><?=LBL_Code;?></label>
												<input type="text" name="code" id="code" class="form-control" value="<?=$_GET['code'];?>" />
											</div>
											<div class="form-group col-sm-4">
												<label><?=LBL_ReservationNumber;?></label>
												<input type="text" name="resno" id="resno" class="form-control" value="<?=$_GET['resno'];?>" />
											</div>
											<div class="form-group col-sm-4">
												<label><?=LBL_Accomodation;?></label>
												<select name="accomo" id="accomo" class="form-control select2">
													<option value="0">
														<?=LBL_Choose;?>
													</option>
													<option value="1" <? if ($_GET['accomo'] == 1) echo 'selected="selected"'; ?>>
														<?=LBL_VALIDACCOM;?>
													</option>
													<option value="2" <? if ($_GET['accomo'] == 2) echo 'selected="selected"'; ?>>
														<?=LBL_NOACCOM;?>
													</option>
												</select>
											</div>

										</div>

										<input type="submit" class="btn btn-primary col-xs-12" value="<?=LBL_SearchFilter;?>" />

									</form>
								</div>
							</div>
							<div class="box">
                <div class="box-body">
									<?
									if (is_numeric($_GET['city_id']) && $_GET['city_id'] > 0) {
										echo '<a href="#" onclick="removeAccomoCity('.$_GET['city_id'].')" class="btn btn-danger">'.LBL_RemoveAccomoForCity.'</a> <a href="#" onclick="removeAccomoCityBus('.$_GET['city_id'].')" class="btn btn-danger">'.LBL_RemoveAccomoForCityBus.'</a> <span id="removecityaccomo_loading"></span>';
									}
									?>
                  <table class="datatable-table4 table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th><?=LBL_Name;?></th>
												<th><?=LBL_NationalId;?></th>
												<th><?=LBL_ReservationNumber;?></th>
                        <th><?=LBL_Code;?></th>
												<th><?=LBL_City;?></th>
												<th><?=LBL_Status;?></th>
												<th><?=LBL_Accomodation;?></th>
												<th><?=LBL_BusAccomodation;?></th>
												<th><?=LBL_Card;?></th>
                        <th><?=LBL_Actions;?></th>
                      </tr>
                    </thead>
                    <tbody>
	                    <?

												if (is_numeric($_GET['city_id']) && $_GET['city_id'] > 0) $sqlmore1 = " AND pil_city_id = ".$_GET['city_id'];
												if ($_GET['gender'] == 'm' || $_GET['gender'] == 'f') $sqlmore2 = " AND pil_gender = '".$_GET['gender']."'";
												if (is_numeric($_GET['pilc_id']) && $_GET['pilc_id'] > 0) $sqlmore3 = " AND pil_pilc_id = ".$_GET['pilc_id'];
												if ($_GET['code']) $sqlmore4 = " AND pil_code LIKE '%".$_GET['code']."%'";
												if ($_GET['resno']) $sqlmore5 = " AND pil_reservation_number LIKE '%".$_GET['resno']."%'";
												if (is_numeric($_GET['accomo']) && $_GET['accomo'] == 1) $sqlmore6 = " AND pil_code IN (SELECT pil_code FROM pils_accomo)";
												if (is_numeric($_GET['accomo']) && $_GET['accomo'] == 2) $sqlmore6 = " AND pil_code NOT IN (SELECT pil_code FROM pils_accomo)";

		                    $sql = $db->query("SELECT p.*, c.country_title_$lang, ci.city_title_$lang FROM $table p
													LEFT OUTER JOIN countries c ON p.pil_country_id = c.country_id
													LEFT OUTER JOIN cities ci ON p.pil_city_id = ci.city_id
													WHERE 1 $sqlmore1 $sqlmore2 $sqlmore3 $sqlmore4 $sqlmore5 $sqlmore6
													ORDER BY pil_name");
		                    while ($row = $sql->fetch()) {

			                    echo '<tr>';
			                    echo '<td>';
													echo $row['pil_name'];
			                    echo '</td>';
													echo '<td>';
													echo $row['pil_nationalid'];
			                    echo '</td>';
													echo '<td>';
													echo $row['pil_reservation_number'];
			                    echo '</td>';
													echo '<td>';
													echo $row['pil_code'];
			                    echo '</td>';
													echo '<td>';
													echo $row['city_title_'.$lang];
			                    echo '</td>';
			                    echo '<td>';
													if ($row['pil_active'] == 1) echo '<span class="label label-success">'.LBL_Active.'</span>';
													else echo '<span class="label label-danger">'.LBL_Inactive.'</span>';
			                    echo '</td>';
			                    echo '<td id="pilaccomo_'.$row['pil_id'].'">';
													$accomo = $db->query("SELECT pil_code FROM pils_accomo WHERE pil_code = '".$row['pil_code']."'")->fetchColumn();
													if ($accomo) echo '<span class="label label-success">'.LBL_VALIDACCOM.'</span> <a href="#" onclick="confirmRemoveAccomo('.$row['pil_id'].'); return false;" class="label label-default">'.LBL_RemoveAccomo.'</a>';
													else echo '<span class="label label-danger">'.LBL_NOACCOM.'</span>';
			                    echo '</td>';
													echo '<td>';
													if ($row['pil_bus_id'] > 0) echo '<span class="label label-success">'.LBL_VALIDACCOM.'</span>';
													else echo '<span class="label label-danger">'.LBL_NOACCOM.'</span>';
													echo '</td>';
													echo '<td>';
													echo '<a href="pil_cards_designs/pils/common/index.php?id='.$row[$table_id].'" target="_blank">'.LBL_Card.'</a>';
			                    echo '</td>';

	                        echo '<td>

	                        <a href="'.$newedit_page.'?id='.$row[$table_id].'" class="label label-info"><i class="fa fa-edit"></i> '.LBL_Modify.'</a>
													<a href="'.basename($_SERVER['PHP_SELF']).'?del='.$row[$table_id].'" class="label label-danger" onclick="return confirm(\''.LBL_DeleteConfirm.'\');"><i class="fa fa-trash"></i> '.LBL_Delete.'</a>

	                        </td>
		                      </tr>';

		                    }
		                    ?>

                    </tbody>
                  </table>
                  
                  
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!--/.col (right) -->

          </div>   <!-- /.row -->
        </section><!-- /.content -->

      </div>

<? include 'footer.php'; ?>
<script>
	function confirmRemoveAccomo(pil_id) {

		var confirmed = confirm('<?=LBL_RemoveAccomoPilgrimConfirm;?>');
		if (confirmed) {

			$('#pilaccomo_' + pil_id).text('<?=LBL_Loading;?>');

			var data = {
			 pil_id:pil_id
			 };

			 $.post('post/removePilAccomo.php', data, function(response){
			 	$('#pilaccomo_' + pil_id).html('<span class="label label-danger"><?=LBL_NOACCOM;?></span>');
			 });

		}

	}

	function removeAccomoCity(city_id) {

		var confirmed = confirm('<?=LBL_RemoveAccomoForCityConfirm;?>');
		if (confirmed) {

			$('#removecityaccomo_loading').html('<?=LBL_Loading;?>');
			var data = {
			 city_id
			 };

			 $.post('post/removeAccomoCity.php', data, function(response){
				 $('#removecityaccomo_loading').html(response);
				 window.location.reload();
			 });


		}

	}

	function removeAccomoCityBus(city_id) {

		var confirmed = confirm('<?=LBL_RemoveAccomoForCityConfirm;?>');
		if (confirmed) {

			$('#removecityaccomo_loading').html('<?=LBL_Loading;?>');
			var data = {
			 city_id
			 };

			 $.post('post/removeAccomoCityBus.php', data, function(response){
				 $('#removecityaccomo_loading').html(response);
				 window.location.reload();
			 });


		}

	}

</script>
