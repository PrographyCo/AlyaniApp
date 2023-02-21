<?php
    if (file_exists('install.php')) {
        
        header("Location: install.php");
        exit();
        
    }
    
    include 'header.php';
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">

        <!--<center>-->
        <!--	<img src="logo-card-header.png" style="width:50%; min-width:200px; padding-top:2%; margin-bottom:30px" />-->
        <!--</center>-->
        
        
        <?
            $sqlclasses = $db->query("SELECT * FROM pils_classes ORDER BY field(pilc_id, 1, 3, 2)");
            
            // General Counts
            $availToOccu_suites_m = $db->query("SELECT SUM(tent_capacity) FROM tents WHERE tent_active = 1 AND type = 2  AND tent_gender = 'm'")->fetchColumn();
            $availToOccu_suites_f = $db->query("SELECT SUM(tent_capacity) FROM tents WHERE tent_active = 1 AND type = 2  AND tent_gender = 'f'")->fetchColumn();
            $availToOccu_suites_total = $availToOccu_suites_m + $availToOccu_suites_f;
            
            $occu_suites_m = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE  halls_id > 0 AND halls_id IN (SELECT tent_id FROM tents WHERE tent_active = 1 AND type = 2  AND tent_gender = 'm')")->fetchColumn();
            $occu_suites_f = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE  halls_id > 0 AND halls_id IN (SELECT tent_id FROM tents WHERE tent_active = 1 AND type = 2   AND tent_gender = 'f')")->fetchColumn();
            $occu_suites_total = $occu_suites_m + $occu_suites_f;
            
            $remaining_suites_m = $availToOccu_suites_m - $occu_suites_m;
            $remaining_suites_f = $availToOccu_suites_f - $occu_suites_f;
            $remaining_suites_total = $remaining_suites_m + $remaining_suites_f;
            
            
            while ($rowc = $sqlclasses->fetch(PDO::FETCH_ASSOC)) {
                
                $totalPilgrims_m = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_gender = 'm' AND pil_pilc_id = " . $rowc['pilc_id'])->fetchColumn();
                $totalPilgrims_f = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_gender = 'f' AND pil_pilc_id = " . $rowc['pilc_id'])->fetchColumn();
                $totalPilgrims_total = $totalPilgrims_m + $totalPilgrims_f;
                
                $noaccomoPils_Suites_m = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_gender = 'm' AND pil_pilc_id = " . $rowc['pilc_id'] . " AND pil_code NOT IN (SELECT pil_code FROM pils_accomo WHERE  halls_id > 0)")->fetchColumn();
                $noaccomoPils_Suites_f = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_gender = 'f' AND pil_pilc_id = " . $rowc['pilc_id'] . " AND pil_code NOT IN (SELECT pil_code FROM pils_accomo WHERE    halls_id > 0)")->fetchColumn();
                $noaccomoPils_Suites_total = $noaccomoPils_Suites_m + $noaccomoPils_Suites_f;
                
                // Tents
                $accomoPils_Tents_m = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_gender = 'm' AND pil_pilc_id = " . $rowc['pilc_id'] . " AND pil_code IN (SELECT pil_code FROM pils_accomo WHERE  halls_id > 0)")->fetchColumn();
                $accomoPils_Tents_f = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_gender = 'f' AND pil_pilc_id = " . $rowc['pilc_id'] . " AND pil_code IN (SELECT pil_code FROM pils_accomo WHERE  halls_id > 0)")->fetchColumn();
                $accomoPils_Tents_total = $accomoPils_Tents_m + $accomoPils_Tents_f;
                
                $noaccomoPils_Tents_m = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_gender = 'm' AND pil_pilc_id = " . $rowc['pilc_id'] . " AND pil_code NOT IN (SELECT pil_code FROM pils_accomo WHERE  halls_id > 0)")->fetchColumn();
                $noaccomoPils_Tents_f = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_gender = 'f' AND pil_pilc_id = " . $rowc['pilc_id'] . " AND pil_code NOT IN (SELECT pil_code FROM pils_accomo WHERE  halls_id > 0)")->fetchColumn();
                $noaccomoPils_Tents_total = $noaccomoPils_Tents_m + $noaccomoPils_Tents_f;
                
                echo '<div class="box">
										<div class="box-body">';
                echo '<h1>' . $rowc['pilc_title_' . $lang] . '</h1>';
                
                echo '<div class="row">';
                
                if ($rowc['pilc_id'] == 1) {
                    // Suites
                    echo '<div class="col-sm-4">
										<b>' . HM_Halls . '</b>
										<table class="table table-bordered table-striped">
										<thead>
										<tr>
										<th>
										' . LBL_Count . '
										</th>
										<th>
										' . LBL_Males . '
										</th>
										<th>
										' . LBL_Females . '
										</th>
										<th>
										' . LBL_Totals . '
										</th>
										</tr>
										</thead>
										<tbody>
										<tr>
										<td>
										' . LBL_AvailableForAccomo . '
										</td>
										<td>' . number_format($availToOccu_suites_m) . '</td>
										<td>' . number_format($availToOccu_suites_f) . '</td>
										<td>' . number_format($availToOccu_suites_total) . '</td>
										</tr>
										<tr>
										<td>
										' . LBL_TotalPilgrims . '
										</td>
										<td>' . number_format($totalPilgrims_m) . '</td>
										<td>' . number_format($totalPilgrims_f) . '</td>
										<td>' . number_format($totalPilgrims_total) . '</td>
										</tr>
										<tr>
										<td>
										' . LBL_AccomoPilgrims . '
										</td>
										<td>' . number_format($occu_suites_m) . '</td>
										<td>' . number_format($occu_suites_f) . '</td>
										<td>' . number_format($occu_suites_total) . '</td>
										</tr>
										<tr>
										<td>
										' . LBL_AvailableSeats . '
										</td>
										<td>' . number_format($remaining_suites_m) . '</td>
										<td>' . number_format($remaining_suites_f) . '</td>
										<td>' . number_format($remaining_suites_total) . '</td>
										</tr>
										<tr>
										<td>
										' . LBL_NotAccomoPilgrims . '
										</td>
										<td>' . number_format($noaccomoPils_Suites_m) . '</td>
										<td>' . number_format($noaccomoPils_Suites_f) . '</td>
										<td>' . number_format($noaccomoPils_Suites_total) . '</td>
										</tr>
										</tbody>
										</table>
										</div>';
                }
                echo '</div>';
                
                echo '<div class="clearfix" style="margin-top:40px;">
									&nbsp;
									</div>';
                
                echo '</div>
								</div>';
            }
        ?>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<?php include 'footer.php'; ?>
