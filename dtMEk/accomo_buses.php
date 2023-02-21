<? include 'header.php';

$title = HM_BusesAccomodations;

if ($_GET['remove'] == 1) {

  if (is_numeric($_GET['bus_id']) && $_GET['bus_id'] > 0) $sqlmore1 = " AND pil_bus_id = ".$_GET['bus_id'];
  if (is_numeric($_GET['city_id']) && $_GET['city_id'] > 0) $sqlmore2 = " AND pil_city_id = ".$_GET['city_id'];

  $sqlupdate = $db->query("UPDATE pils SET pil_bus_id = 0 WHERE 1 $sqlmore1 $sqlmore2");

}


?>
<!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            <?=$title;?>
             <!--<img src="images/logo.png" style="width:10%"/>-->
            <button  class="btn btn-success pull-<?=DIR_AFTER;?>" onclick="pdfbuses()" style="margin-<?=DIR_AFTER;?>: 10px" target="_blank"><i class="fa fa-file-pdf-o"></i> <?=BTN_ExportToPDF;?></button>
            <a href="?remove=1&bus_id=<?=$_GET['bus_id'];?>&city_id=<?=$_GET['city_id'];?>" onclick="return confirm('<?=LBL_RemoveConfirm;?>');" class="btn btn-danger pull-<?=DIR_AFTER;?>" style="margin-<?=DIR_AFTER;?>: 10px"><i class="fa fa-trash"></i> <?=BTN_REMOVEACCOMO2;?></a>

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
												<label><?=LBL_City;?></label>
												<select name="city_id" id="city_id" class="form-control select2" onchange="grabCityBuses(this.value); return false;">
													<option value="0">
														<?=HM_ListAll;?>
													</option>
													<?
														$sqlcities = $db->query("SELECT * FROM cities WHERE city_active = 1 ORDER BY city_title_$lang");
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


											<div class="form-group col-sm-6 busesarea">
												<label><?=LBL_BusNumber;?></label>
												<select name="bus_id" id="bus_id" class="form-control select2">
													<option value="0">
														<?=LBL_Choose;?>
													</option>
													<?
                            if ($_GET['city_id'] > 0) $sqlmore = " AND bus_city_id = ".$_GET['city_id'];
														$sqlbuses = $db->query("SELECT * FROM buses WHERE bus_active = 1 $sqlmore ORDER BY bus_title");
														while ($rowb = $sqlbuses->fetch(PDO::FETCH_ASSOC)){
															echo '<option value="'.$rowb['bus_id'].'" ';
															if ($_GET['bus_id'] == $rowb['bus_id']) echo 'selected="selected"';
															echo '>
															'.$rowb['bus_title'].'
															</option>';
														}
													?>

												</select>
											</div>
										</div>

										<input type="submit" class="btn btn-primary col-xs-12" value="<?=LBL_SearchFilter;?>" />

									</form>
								</div>
							</div>
							<div class="box html-content">
							    <div class="container-fluid"> 
                                           <div>
                                             <img src="images/logo.png" style="width:10%"/>
                                          </div>
							      <?php if($_GET['city_id']){
							        
                                        $stmt=$db->prepare("SELECT * FROM cities WHERE city_id = ?");
                                        $stmt->execute(array($_GET['city_id']));
                                        $city=$stmt->fetch();
                                        
                                        $stmt=$db->prepare("SELECT * FROM buses WHERE bus_id = ?");
                                        $stmt->execute(array($_GET['bus_id']));
                                        $bus=$stmt->fetch();
                                        
                                        $stmt=$db->prepare("SELECT * FROM staff WHERE staff_id = ?");
                                        $stmt->execute(array($bus['bus_staff_id']));
                                        $staff=$stmt->fetch();
                                        ?>
                                      
                                    <div style="padding: 26px;font-size: 18px;">
                                            <p><?php echo $bus['bus_title'] ." - ".$bus['bus_order'];?></p>
                                            <p><?php echo HM_staff_name.': '.$staff['staff_name'] ." - ".HM_staff_phone.': '.$staff['staff_phones'];?></p>
                                            <p><?php echo $city['city_title_'.$_COOKIE['lang']];?></p>
                                    </div>
                                      </div>
							  
                <div class="box-body">
                  <table class=" table table-bordered table-striped" id="myTable2">
                    <thead>
                      <tr>
                                <th><?=LBL_Code;?></th>
                                <th><?=LBL_Name;?></th>
                                <th><?=LBL_NationalId;?></th>
                                <th><?=LBL_PhoneNumber;?></th>
                      </tr>
                    </thead>
                    <tbody>
	                    <?

												if (is_numeric($_GET['bus_id']) && $_GET['bus_id'] > 0) $sqlmore1 = " AND p.pil_bus_id = ".$_GET['bus_id'];
                        if (is_numeric($_GET['city_id']) && $_GET['city_id'] > 0) $sqlmore2 = " AND p.pil_city_id = ".$_GET['city_id'];

		                    $sql = $db->query("SELECT p.pil_code, p.pil_name, p.pil_nationalid, p.pil_reservation_number , p.pil_phone, b.bus_title
												FROM pils p
												LEFT OUTER JOIN buses b ON p.pil_bus_id = b.bus_id
												WHERE p.pil_bus_id > 0 $sqlmore1 $sqlmore2");
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
			                    echo '</td>';
													echo '<td>';
													echo $row['pil_phone'];
			                    echo '</td>';
											
		                      echo '</tr>';

		                    }
		                    ?>

                    </tbody>
                  </table>
                </div><!-- /.box-body -->
                <?php  }else{ ?>
							 
				<div class="box-body">
                  <table class=" table table-bordered table-striped" id="myTable2">
                    <thead>
                      <tr>
                                <th><?=LBL_Code;?></th>
                                <th><?=LBL_Name;?></th>
                                <th><?=LBL_NationalId;?></th>
                                <th><?=LBL_PhoneNumber;?></th>
                                  <th><?=HM_staff_name;?></th>
                                 <th><?=HM_staff_phone;?></th>
                      </tr>
                    </thead>
                    <tbody>
	                    <?

												if (is_numeric($_GET['bus_id']) && $_GET['bus_id'] > 0) $sqlmore1 = " AND p.pil_bus_id = ".$_GET['bus_id'];
                        if (is_numeric($_GET['city_id']) && $_GET['city_id'] > 0) $sqlmore2 = " AND p.pil_city_id = ".$_GET['city_id'];

		                    $sql = $db->query("SELECT p.pil_code, p.pil_name, p.pil_nationalid, p.pil_reservation_number , p.pil_phone, b.bus_title , b.bus_staff_id
												FROM pils p
												LEFT OUTER JOIN buses b ON p.pil_bus_id = b.bus_id
												WHERE p.pil_bus_id > 0 $sqlmore1 $sqlmore2");
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
			                    echo '</td>';
													echo '<td>';
													echo $row['pil_phone'];
			                    echo '</td>';
			                     echo '<td>';
                													$staff = $db->query("SELECT staff_name , staff_phones FROM staff WHERE staff_id = ".$row['bus_staff_id'])->fetch();
                													echo $staff['staff_name'];
                			     echo '</td>';
                			     echo '<td>';
													echo $staff['staff_phones'];
			                    echo '</td>';
											
		                      echo '</tr>';

		                    }
		                    ?>

                    </tbody>
                  </table>
                </div><!-- /.box-body -->
                
							    
				<?php }?>
              </div><!-- /.box -->
            </div><!--/.col (right) -->

          </div>   <!-- /.row -->
        </section><!-- /.content -->

      </div>

<? include 'footer.php'; ?>

   <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.print.min.js"></script>

 
    
<script>
  function grabCityBuses(city_id) {

    $('.busesarea').html('<?=LBL_Loading;?>');

    var data = {
     city_id
     };

     $.post('post/accomo_buses_cities_selected2.php', data, function(response){

       $('.busesarea').html(response);
       $('select').select2();

     });

  }
</script>

