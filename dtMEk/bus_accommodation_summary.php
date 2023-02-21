<?php
    
    if (file_exists('install.php')) {
        
        header("Location: install.php");
        exit();
        
    }
    
    include 'header.php'; ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">

        <!--<center>-->
        <!--	<img src="logo-card-header.png" style="width:50%; min-width:200px; padding-top:2%; margin-bottom:30px" />-->
        <!--</center>-->
        
        
        <?
            
            $sql = $db->query("SELECT b.*, c.city_title_ar FROM buses b
                                        LEFT OUTER JOIN cities c ON b.bus_city_id = c.city_id ORDER BY b.bus_order");
            
            
            echo '<div class="box">
										<div class="box-body">';
            echo '<h1>' . HM_bus_accommodation_summary . '</h1>';
            echo '<div class="row">';
            
            // Suites
            echo '<div class="col-sm-4">
										<table class="table table-bordered table-striped">
										<thead>
										<tr>
										<th>
										' . LBL_City . '
										</th>
										<th>
										' . LBL_BusSeats . '
										</th>
									
										</tr>
										</thead>
										<tbody>';
            while ($row = $sql->fetch()) {
                echo '<tr>
									
										<td>' . $row['city_title_ar'] . '</td>
										<td>' . number_format($row['bus_seats']) . '</td>
										</tr>';
            
            }
            
            echo '</tbody>
										</table>
										</div>';
            
            echo '</div>';
            
            echo '<div class="clearfix" style="margin-top:40px;">
									&nbsp;
									</div>';
            
            echo '</div>
								</div>';
        
        ?>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<?php include 'footer.php'; ?>
