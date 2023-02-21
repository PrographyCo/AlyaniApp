<? include 'header.php';

$title = HM_Buildings;
$table = 'buildings';
$table_id = 'bld_id';
$newedit_page = 'e_bld.php';

	if (is_numeric($_GET['del'])) {

		$id = $_GET['del'];
		$sqldel1 = $db->query("DELETE FROM $table WHERE $table_id = $id");

	}

?>
<!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            <?=$title;?>
            <a href="<?=$newedit_page;?>" class="btn btn-success pull-<?=DIR_AFTER;?>"><i class="fa fa-star"></i> <?=BTN_AddNew;?></a>
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
                  <table class="datatable-table4 table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th><?=LBL_BuildingNumber;?></th>
												<th><?=LBL_Type;?></th>
												<th><?=HM_Floors;?></th>
												<th><?=HM_Rooms;?></th>
												<th><?=LBL_TotalCapacity;?></th>
												<th><?=LBL_ٍRemaining;?></th>
												<th><?=LBL_Status;?></th>
                        <th><?=LBL_Order;?></th>
                        <th><?=LBL_Actions;?></th>
                      </tr>
                    </thead>
                    <tbody>
	                    <?
		                    $sql = $db->query("SELECT * FROM $table ORDER BY bld_order");
		                    while ($row = $sql->fetch()) {

			                    echo '<tr>';
			                    echo '<td>';
													echo $row['bld_title'];
			                    echo '</td>';

													echo '<td>';
													if ($row['bld_type'] == 1) echo HM_Building;
													elseif ($row['bld_type'] == 2) echo LBL_Premises;
			                    echo '</td>';

													echo '<td>';
													$countfloors = $db->query("SELECT COUNT(floor_id) FROM buildings_floors WHERE floor_bld_id = ".$row['bld_id'])->fetchColumn();
													echo '<a href="floors.php?bld_id='.$row['bld_id'].'">'.$countfloors.' '.HM_Floors.'</a>';
			                    echo '</td>';

													echo '<td>';
													$countrooms = $db->query("SELECT COUNT(room_id) FROM buildings_rooms WHERE room_bld_id = ".$row['bld_id'])->fetchColumn();
													echo '<a href="rooms.php?bld_id='.$row['bld_id'].'">'.$countrooms.' '.HM_Rooms.'</a>';
			                    echo '</td>';

													echo '<td>';
													$totalcapacity = $db->query("SELECT SUM(room_capacity) FROM buildings_rooms WHERE room_bld_id = ".$row['bld_id'])->fetchColumn();
													echo number_format($totalcapacity);
													echo '</td>';

													echo '<td>';
													$occu = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE bld_id = ".$row['bld_id'])->fetchColumn();
													$remaining = $totalcapacity - $occu;
													echo number_format($remaining);
													echo '</td>';


			                    echo '<td>';
													if ($row['bld_active'] == 1) echo '<span class="label label-success">'.LBL_Active.'</span>';
													else echo '<span class="label label-danger">'.LBL_Inactive.'</span>';
			                    echo '</td>';
			                    echo '<td>';
													echo $row['bld_order'];
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
