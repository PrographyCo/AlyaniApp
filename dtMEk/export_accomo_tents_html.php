<?php
    $lang = 'ar';
    
    require_once 'init.php';
    require_once 'config/db.php';
    require_once 'functions.php';
    require_once 'constants.php';
    
    global $css1, $css2, $css3, $css4;
    
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
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="css/skins/<?= $css4; ?>" rel="stylesheet" type="text/css"/>
    <link href="plugins/iCheck/flat/blue.css" rel="stylesheet" type="text/css"/>
    <link href="plugins/select2/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="css/<?= $css1; ?>" rel="stylesheet" type="text/css"/>
    <link href="plugins/iCheck/all.css" rel="stylesheet" type="text/css"/>
    <link href="plugins/datepicker/datepicker3.css" rel="stylesheet" type="text/css"/>
    <link href="plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css"/>
    <link href="plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css"/>
    <link href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="plugins/DataTables2/datatables.min.css"/>
    <link href="css/fileinput.min.css" media="all" rel="stylesheet" type="text/css"/>
    <link href="css/bootstrap-colorpicker.css" media="all" rel="stylesheet" type="text/css"/>
    <link href="css/bootstrap-datetimepicker.min.css" media="all" rel="stylesheet" type="text/css"/>
    <link href="css/font-awesome.css" rel="stylesheet" type="text/css"/>
    <?
        if ($css2) echo '<link href="css/' . $css2 . '" rel="stylesheet" type="text/css" />';
        if ($css3) echo '<link href="css/' . $css3 . '" rel="stylesheet" type="text/css" />';
    ?>
</head>
<body>


<div class="box" style="border:0; padding:0; margin:0; box-shadow:none">
    <div class="box-body">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th><?= LBL_Code; ?></th>
                <th><?= LBL_Name; ?></th>
                <th><?= LBL_NationalId; ?></th>
                <th><?= LBL_TentNumber; ?></th>
            </tr>
            </thead>
            <tbody>
            <?
                
                $array_of_tents = array();
                if (is_numeric($_GET['tent_id']) && $_GET['tent_id'] > 0) $sqlmore1 = " AND pa.tent_id = " . $_GET['tent_id'];
                
                $sql = $db->query("SELECT pa.*, p.pil_name, p.pil_nationalid, p.pil_reservation_number, t.tent_title
					FROM pils_accomo pa
					INNER JOIN pils p ON pa.pil_code = p.pil_code
					LEFT OUTER JOIN tents t ON pa.tent_id = t.tent_id
					WHERE pa.tent_id > 0 $sqlmore1 ORDER BY pa.tent_id");
                while ($row = $sql->fetch()) {
                    
                    if (sizeof($array_of_tents) == 0) {
                        
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
