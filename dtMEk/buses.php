<? include 'header.php';

$title = HM_PilgrimsBuses;
$table = 'buses';
$table_id = 'bus_id';
$newedit_page = 'e_bus.php';

$sql = $db->query("SELECT b.*, c.city_title_ar FROM $table b
													LEFT OUTER JOIN cities c ON b.bus_city_id = c.city_id ORDER BY b.bus_order");
													
if(isset($_POST['filter'])){
    $soothing = $_POST['tent_soothing'];
    $bus_city_id = $_POST['bus_city_id'];
    $gender = $_POST['tent_gender'];
    $tent_type = $_POST['tent_type'];
     $arr=array();
    // if($tent_type != 0){
    //     array_push($arr,"tent_type = ". $tent_type);
    // }
    // if($gender != 'All'){
    //      array_push($arr,"tent_gender = '$gender' ");
    // }
    // if($bus_city_id != 0){
    //      array_push($arr,"c.city_id = '$bus_city_id' ");
    // }
    //  if(!empty($arr)){
    //              $option = implode(" AND ",$arr);
    //             //  echo $option;die;
    //                     $sql = $db->query("SELECT * FROM $table WHERE $option ORDER BY tent_order");

                                
                         
    //         }else{
    //                 $sql = $db->query("SELECT * FROM $table  ORDER BY tent_order");

    //         }
            
    // $sql = $db->query("SELECT * FROM $table WHERE tent_gender = '$gender' ORDER BY tent_order");

    if($bus_city_id != 0){
          $sql = $db->query("SELECT b.*, c.city_title_ar FROM $table b
													LEFT OUTER JOIN cities c ON b.bus_city_id = c.city_id WHERE c.city_id = $bus_city_id ORDER BY b.bus_order");  
    }else{
        $sql = $db->query("SELECT b.*, c.city_title_ar FROM $table b
													LEFT OUTER JOIN cities c ON b.bus_city_id = c.city_id ORDER BY b.bus_order");
    }

													
}

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
							    
							    	    <form method="post" style="margin: 40px;">
				            <div class="row">
				                   <div class="col-lg-2">
				                       	<label><?=LBL_City;?></label>
											<select name="bus_city_id" class="form-control select2">
												<option value="0"><?=LBL_Choose;?></option>
												<option value="0"><?=HM_ListAll;?></option>
												<?
													$sqlcities = $db->query("SELECT city_id, city_title_en, city_title_ar FROM cities ORDER BY city_title_".$lang);
													while ($rowcities = $sqlcities->fetch(PDO::FETCH_ASSOC)) {

														echo '<option value="'.$rowcities['city_id'].'" ';
														if ($row['bus_city_id'] == $rowcities['city_id'] || $_POST['bus_city_id'] == $rowcities['city_id']) echo 'selected="selected"';
														echo '>'.$rowcities['city_title_'.$lang].'</option>';

													}
												?>
											</select>
				                    </div>
				                    <div class="col-lg-2">
				                       <label for="exampleFormControlSelect2"><?=LBL_Type;?></label>
                                            <select name="tent_type" class="form-control select2">
                                                <option value="0" ><?=HM_ListAll;?></option>
												<option value="1" ><?=LBL_TentType1;?></option>
												<option value="2" ><?=LBL_TentType2;?></option>
											</select>
				                    </div>
				                     <div class="col-lg-2">
				                        <label for="exampleFormControlSelect2"><?=LBL_Gender;?></label>
                                            <select name="tent_gender" class="form-control select2">
                                                <option value="All" ><?=HM_ListAll;?></option>
												<option value="m"><?=LBL_Male;?></option>
												<option value="f"><?=LBL_Female;?></option>
											</select>
				                    </div>
				                     <div class="col-lg-2">
				                         <label for="form-control"><?=HM_soothing;?></label>
                                            <select name="tent_soothing" class="form-control select2">
                                                <option value="0" ><?=HM_ListAll;?></option>
												<option value="1"><?=HM_soothing_true;?></option>
												<option value="2"><?=HM_soothing_false;?></option>
											</select>
				                    </div>
				                     <div class="col-lg-2" style="margin-top: 25px;">
                                           <button class="btn btn-success" name="filter"><?=HM_show;?></button>
				                    </div>
				            </div>
				    </form>
				    
                <div class="box-body">
                  <table class="datatable-table4 table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th><?=LBL_BusNumber;?></th>
						<th><?=LBL_City;?></th>
						<th><?=LBL_BusSeats;?></th>
						<th><?=LBL_ٍRemaining;?></th>
						<th><?=HM_staff_name;?></th>
						<th><?=LBL_Status;?></th>
                        <th><?=LBL_Order;?></th>
                        <th><?=LBL_Actions;?></th>
                      </tr>
                    </thead>
                    <tbody>
	                    <?
		                    
		                    while ($row = $sql->fetch()) {
		                            if(isset($_POST['filter'])){
                                            $occu = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_bus_id = ".$row['bus_id'])->fetchColumn();
                                            $remaining = $row['bus_seats'] - $occu;
                                            //  echo $soothing."<br>";
                                            // echo $occu."<br>";
                                            // echo $row['tent_capacity'];die;
                                            if($soothing == 1 && $row['bus_seats'] == $occu){
                                                     echo '<tr>';
                			                    echo '<td>';
                													echo $row['bus_title'];
                			                    echo '</td>';
                													echo '<td>';
                													echo $row['city_title_ar'];
                			                    echo '</td>';
                													echo '<td>';
                													echo number_format($row['bus_seats']);
                			                    echo '</td>';
                			                    echo '<td>';
                													$occu = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_bus_id = ".$row['bus_id'])->fetchColumn();
                													$remaining = $row['bus_seats'] - $occu;
                													echo number_format($remaining);
                			                    echo '</td>';
                			                      echo '<td>';
                													$staff = $db->query("SELECT staff_name FROM staff WHERE staff_id = ".$row['bus_staff_id'])->fetchColumn();
                													echo $staff;
                			                    echo '</td>';
                
                													echo '<td>';
                													if ($row['bus_active'] == 1) echo '<span class="label label-success">'.LBL_Active.'</span>';
                													else echo '<span class="label label-danger">'.LBL_Inactive.'</span>';
                													echo '</td>';
                
                													echo '<td>';
                													echo $row['bus_order'];
                			                    echo '</td>';
                
                	                        echo '<td>
                
                	                        <a href="'.$newedit_page.'?id='.$row[$table_id].'" class="label label-info"><i class="fa fa-edit"></i> '.LBL_Modify.'</a>
                													<a href="'.basename($_SERVER['PHP_SELF']).'?del='.$row[$table_id].'" class="label label-danger" onclick="return confirm(\''.LBL_DeleteConfirm.'\');"><i class="fa fa-trash"></i> '.LBL_Delete.'</a>
                
                	                        </td>
                		                      </tr>';
                		                      
                                            }elseif($soothing == 2 && $row['bus_seats'] != $occu){
                                                     echo '<tr>';
                			                    echo '<td>';
                													echo $row['bus_title'];
                			                    echo '</td>';
                													echo '<td>';
                													echo $row['city_title_ar'];
                			                    echo '</td>';
                													echo '<td>';
                													echo number_format($row['bus_seats']);
                			                    echo '</td>';
                			                    echo '<td>';
                													$occu = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_bus_id = ".$row['bus_id'])->fetchColumn();
                													$remaining = $row['bus_seats'] - $occu;
                													echo number_format($remaining);
                			                    echo '</td>';
                			                       echo '<td>';
                													$staff = $db->query("SELECT staff_name FROM staff WHERE staff_id = ".$row['bus_staff_id'])->fetchColumn();
                													echo $staff;
                			                    echo '</td>';
                
                													echo '<td>';
                													if ($row['bus_active'] == 1) echo '<span class="label label-success">'.LBL_Active.'</span>';
                													else echo '<span class="label label-danger">'.LBL_Inactive.'</span>';
                													echo '</td>';
                
                													echo '<td>';
                													echo $row['bus_order'];
                			                    echo '</td>';
                
                	                        echo '<td>
                
                	                        <a href="'.$newedit_page.'?id='.$row[$table_id].'" class="label label-info"><i class="fa fa-edit"></i> '.LBL_Modify.'</a>
                													<a href="'.basename($_SERVER['PHP_SELF']).'?del='.$row[$table_id].'" class="label label-danger" onclick="return confirm(\''.LBL_DeleteConfirm.'\');"><i class="fa fa-trash"></i> '.LBL_Delete.'</a>
                
                	                        </td>
                		                      </tr>';
                		                      
                                            }elseif($soothing == 0){
                                                
                                                     echo '<tr>';
                			                    echo '<td>';
                													echo $row['bus_title'];
                			                    echo '</td>';
                													echo '<td>';
                													echo $row['city_title_ar'];
                			                    echo '</td>';
                													echo '<td>';
                													echo number_format($row['bus_seats']);
                			                    echo '</td>';
                			                    echo '<td>';
                													$occu = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_bus_id = ".$row['bus_id'])->fetchColumn();
                													$remaining = $row['bus_seats'] - $occu;
                													echo number_format($remaining);
                			                    echo '</td>';
                                                echo '<td>';
                													$staff = $db->query("SELECT staff_name FROM staff WHERE staff_id = ".$row['bus_staff_id'])->fetchColumn();
                													echo $staff;
                			                    echo '</td>';
                													echo '<td>';
                													if ($row['bus_active'] == 1) echo '<span class="label label-success">'.LBL_Active.'</span>';
                													else echo '<span class="label label-danger">'.LBL_Inactive.'</span>';
                													echo '</td>';
                
                													echo '<td>';
                													echo $row['bus_order'];
                			                    echo '</td>';
                
                	                        echo '<td>
                
                	                        <a href="'.$newedit_page.'?id='.$row[$table_id].'" class="label label-info"><i class="fa fa-edit"></i> '.LBL_Modify.'</a>
                													<a href="'.basename($_SERVER['PHP_SELF']).'?del='.$row[$table_id].'" class="label label-danger" onclick="return confirm(\''.LBL_DeleteConfirm.'\');"><i class="fa fa-trash"></i> '.LBL_Delete.'</a>
                
                	                        </td>
                		                      </tr>';
                		                      
                                            }
                                            
                                                
		                      
		                            }else{
		                                         echo '<tr>';
			                    echo '<td>';
													echo $row['bus_title'];
			                    echo '</td>';
													echo '<td>';
													echo $row['city_title_ar'];
			                    echo '</td>';
													echo '<td>';
													echo number_format($row['bus_seats']);
			                    echo '</td>';
			                    echo '<td>';
													$occu = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_bus_id = ".$row['bus_id'])->fetchColumn();
													$remaining = $row['bus_seats'] - $occu;
													echo number_format($remaining);
			                    echo '</td>';
                                                    echo '<td>';
                                                    		$staff = $db->query("SELECT staff_name FROM staff WHERE staff_id = ".$row['bus_staff_id'])->fetchColumn();
                                                    		echo $staff;
                                                    echo '</td>';

													echo '<td>';
													if ($row['bus_active'] == 1) echo '<span class="label label-success">'.LBL_Active.'</span>';
													else echo '<span class="label label-danger">'.LBL_Inactive.'</span>';
													echo '</td>';

													echo '<td>';
													echo $row['bus_order'];
			                    echo '</td>';

	                        echo '<td>

	                        <a href="'.$newedit_page.'?id='.$row[$table_id].'" class="label label-info"><i class="fa fa-edit"></i> '.LBL_Modify.'</a>
													<a href="'.basename($_SERVER['PHP_SELF']).'?del='.$row[$table_id].'" class="label label-danger" onclick="return confirm(\''.LBL_DeleteConfirm.'\');"><i class="fa fa-trash"></i> '.LBL_Delete.'</a>

	                        </td>
		                      </tr>';
		                            }

			           

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
