<? include 'header.php';

$title = HM_BuildingsAccomodations;

if ($_GET['remove'] == 1) {

  if (is_numeric($_GET['building_id']) && $_GET['building_id'] > 0) $sqlmore1 = " AND bld_id = ".$_GET['building_id'];
  if (is_numeric($_GET['floor_id']) && $_GET['floor_id'] > 0) $sqlmore2 = " AND floor_id = ".$_GET['floor_id'];

  $sqlremove = $db->query("DELETE FROM pils_accomo WHERE pil_accomo_type = 1 AND bld_id > 0 $sqlmore1 $sqlmore2");
}


?>
<!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            <?=$title;?>
              <img src="images/logo.png" style="width:10%"/>
            <a href="export_accomo_buildings.php?building_id=<?=$_GET['building_id'];?>&floor_id=<?=$_GET['floor_id'];?>" class="btn btn-success pull-<?=DIR_AFTER;?>" style="margin-<?=DIR_AFTER;?>: 10px" target="_blank"><i class="fa fa-file-pdf-o"></i> <?=BTN_ExportToPDF;?></a>
            <a href="?remove=1&building_id=<?=$_GET['building_id'];?>&floor_id=<?=$_GET['floor_id'];?>" onclick="return confirm('<?=LBL_RemoveConfirm;?>');" class="btn btn-danger pull-<?=DIR_AFTER;?>" style="margin-<?=DIR_AFTER;?>: 10px"><i class="fa fa-trash"></i> <?=BTN_REMOVEACCOMO2;?></a>

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
											<div class="form-group col-sm-6">
												<label><?=LBL_BuildingNumber;?></label>
												<select name="building_id" id="building_id" class="form-control select2">
													<option value="0">
														<?=LBL_Choose;?>
													</option>
													<?
														$sqlblds = $db->query("SELECT * FROM buildings WHERE bld_active = 1 ORDER BY bld_title");
														while ($rowb = $sqlblds->fetch(PDO::FETCH_ASSOC)){
															echo '<option value="'.$rowb['bld_id'].'" ';
															if ($_GET['building_id'] == $rowb['bld_id']) echo 'selected="selected"';
															echo '>
															'.$rowb['bld_title'].'
															</option>';
														}
													?>

												</select>
											</div>
											<div class="form-group col-sm-6">
												<label><?=HM_Floor;?></label>
												<select name="floor_id" id="floor_id" class="form-control select2">
													<option value="0">
														<?=LBL_Choose;?>
													</option>
													<?
													if (is_numeric($_GET['building_id']) && $_GET['building_id'] > 0) {

														$sqlfloors = $db->query("SELECT * FROM buildings_floors WHERE floor_bld_id = ".$_GET['building_id']." AND floor_active = 1 ORDER BY floor_title");
														while ($rowf = $sqlfloors->fetch(PDO::FETCH_ASSOC)){
															echo '<option value="'.$rowf['hall_id'].'" ';
															if ($_GET['floor_id'] == $rowf['floor_id']) echo 'selected="selected"';
															echo '>
															'.$rowf['floor_title'].'
															</option>';
														}

													}
													?>

												</select>
											</div>
										</div>

										<input type="submit" class="btn btn-primary col-xs-12" value="<?=LBL_SearchFilter;?>" />

									</form>
								</div>
							</div>
							<div class="box">
							    
							    <?php if($_GET['building_id']){
							        
                                        $stmt=$db->prepare("SELECT * FROM buildings WHERE bld_id = ?");
                                        $stmt->execute(array($_GET['building_id']));
                                        $building=$stmt->fetch();
                                        
                                        $stmt=$db->prepare("SELECT * FROM buildings_floors WHERE floor_id = ?");
                                        $stmt->execute(array($_GET['floor_id']));
                                        $floor=$stmt->fetch();
                                      
                                        ?>
                                    <div style="padding: 26px;font-size: 18px;">
                                            <p><?php echo $building['bld_title'] ." - ".$floor['floor_title'];?></p>
                                          
                                    </div>
							  <?php  }
							    ?>
							    
                <div class="box-body">
                  <table class=" table table-bordered table-striped" id="myTable2">
                    <thead>
                      <tr>
												<th><?=LBL_Code;?></th>
                        <th><?=LBL_Name;?></th>
												<th><?=LBL_NationalId;?></th>
											
												<th><?=HM_Floor;?></th>
                        <th><?=LBL_RoomNumber;?></th>
                      </tr>
                    </thead>
                    <tbody>
	                    <?

												if (is_numeric($_GET['building_id']) && $_GET['building_id'] > 0) $sqlmore1 = " AND pa.bld_id = ".$_GET['building_id'];
												if (is_numeric($_GET['floor_id']) && $_GET['floor_id'] > 0) $sqlmore2 = " AND pa.floor_id = ".$_GET['floor_id'];

		                    $sql = $db->query("SELECT pa.*, p.pil_name, p.pil_nationalid, p.pil_reservation_number, b.bld_title, f.floor_title, r.room_title
												FROM pils_accomo pa
												INNER JOIN pils p ON pa.pil_code = p.pil_code
												LEFT OUTER JOIN buildings b ON pa.bld_id = b.bld_id
												LEFT OUTER JOIN buildings_floors f ON pa.floor_id = f.floor_id
                        LEFT OUTER JOIN buildings_rooms r ON pa.room_id = r.room_id
												WHERE pa.bld_id > 0 $sqlmore1 $sqlmore2");
		                    while ($row = $sql->fetch()) {

			                    echo '<tr>';
													echo '<td>';
													echo $row['pil_code'];
			                    echo '</td>';
			                    echo '<td>';
													echo $row['pil_name'];
			                    echo '</td>';
													echo '<td>';
													echo $row['pil_nationalid'];
			                   
													echo '<td>';
													echo $row['floor_title'];
			                    echo '</td>';
													echo '<td>';
													echo LBL_RoomNumber.': '.$row['room_title'];
			                    echo '</td>';
		                      echo '</tr>';

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
