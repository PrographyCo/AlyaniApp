<?php
    global $db, $session, $lang;
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <?php
            
            $sql = $db->query("SELECT b.*, c.city_title_ar FROM buses b
                                        LEFT OUTER JOIN cities c ON b.bus_city_id = c.city_id ORDER BY b.bus_order");
        ?>
        <div class="box">
            <div class="box-body">
                <h1><?= HM_bus_accommodation_summary ?></h1>
                <div class="row">
                    <div class="col-sm-4">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>
                                    <?= LBL_City ?>
                                </th>
                                <th>
                                    <?= LBL_BusSeats ?>
                                </th>

                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                
                                // Suites
                                while ($row = $sql->fetch()) {
                                    ?>
                                    <tr>
                                        <td><?= $row['city_title_ar'] ?></td>
                                        <td><?= number_format($row['bus_seats']) ?></td>
                                    </tr>
                                    <?php
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="clearfix" style="margin-top:40px;">
                </div>
            </div>
        </div>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
