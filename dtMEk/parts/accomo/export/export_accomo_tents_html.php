<?php
    $lang = 'ar';
    
    global $css1, $css2, $css3, $css4, $db, $lang, $url;
    
    $curpage = '/' . basename($_SERVER['REQUEST_URI']);
    $curpagewithquery = $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ِExported PDF File</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.4 -->
    <link href="<?= CP_PATH ?>/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/css/skins/<?= $css4 ?>" rel="stylesheet" type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/plugins/iCheck/flat/blue.css" rel="stylesheet" type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/plugins/select2/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/css/<?= $css1 ?>" rel="stylesheet" type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/plugins/iCheck/all.css" rel="stylesheet" type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/plugins/datepicker/datepicker3.css" rel="stylesheet" type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="<?= CP_PATH ?>/assets/plugins/DataTables2/datatables.min.css"/>
    <link href="<?= CP_PATH ?>/assets/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/css/bootstrap-colorpicker.css" media="all" rel="stylesheet" type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/css/bootstrap-datetimepicker.min.css" media="all" rel="stylesheet" type="text/css"/>
    <link href="<?= CP_PATH ?>/assets/css/font-awesome.css" rel="stylesheet" type="text/css"/>
    <?php
        if (isset($css2)) echo '<link href="'.CP_PATH.'/assets/css/' . $css2 . '" rel="stylesheet" type="text/css" />';
        if (isset($css3)) echo '<link href="'.CP_PATH.'/assets/css/' . $css3 . '" rel="stylesheet" type="text/css" />';
    ?>
</head>
<body>


<div class="box" style="border:0; padding:0; margin:0; box-shadow:none">
    <div class="box-body">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th><?= LBL_Code ?></th>
                <th><?= LBL_Name ?></th>
                <th><?= LBL_NationalId ?></th>
                <th><?= LBL_TentNumber ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
                $sqlmore1 = '';
                $array_of_tents = array();
                if (is_numeric($_GET['tent_id']) && $_GET['tent_id'] > 0) $sqlmore1 = " AND pa.tent_id = " . $_GET['tent_id'];
                
                $sql = $db->query("SELECT pa.*, p.pil_name, p.pil_nationalid, p.pil_reservation_number, t.tent_title
					FROM pils_accomo pa
					INNER JOIN pils p ON pa.pil_code = p.pil_code
					LEFT OUTER JOIN tents t ON pa.tent_id = t.tent_id
					WHERE pa.tent_id > 0 $sqlmore1 ORDER BY pa.tent_id");
                while ($row = $sql->fetch()) {
                    
                    if (count($array_of_tents) == 0) {
                        
                        $array_of_tents[] = $row['tent_title'];
                        
                    } else {
                        
                        if (!in_array($row['tent_title'], $array_of_tents)) {
                            
                            $array_of_tents[] = $row['tent_title'];
                            
                            echo '</tbody>
							</table>
						</div><!-- /.box-body -->
					</div>
								<p style="page-break-after: always;"></p>
											<p style="page-break-before: always;"></p>
											<div class="box">
												<div class="box-body">
													<table class="table table-bordered table-striped">
														<thead>
															<tr>
																<th>' . LBL_Code . '</th>
																<th>' . LBL_Name . '</th>
																<th>' . LBL_NationalId . '</th>
																<th>' . LBL_TentNumber . '</th>
															</tr>
														</thead>
														<tbody>
														';
                        
                        }
                        
                    }
                    
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
                    /*echo '<td>';
                    echo $row['pil_reservation_number'];
                    echo '</td>';*/
                    echo '<td>';
                    echo $row['tent_title'];
                    echo '</td>';
                    echo '</tr>';
                    
                    
                }
            ?>

            </tbody>
        </table>
    </div><!-- /.box-body -->
</div><!-- /.box -->
</body>
</html>
