<?php
    global $db, $session;
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        
        <?php
            $sqlclasses = $db->query("SELECT * FROM pils_classes ORDER BY field(pilc_id, 1, 3, 2)");
            
            // General Counts (MANAA)
            $availToOccu_suites_m = $db->query("SELECT SUM(hall_capacity) FROM suites_halls WHERE hall_active = 1 AND hall_suite_id IN (SELECT suite_id FROM suites WHERE suite_active = 1 AND suite_gender = 'm')")->fetchColumn();
            $availToOccu_suites_f = $db->query("SELECT SUM(hall_capacity) FROM suites_halls WHERE hall_active = 1 AND hall_suite_id IN (SELECT suite_id FROM suites WHERE suite_active = 1 AND suite_gender = 'f')")->fetchColumn();
            $availToOccu_suites_total = $availToOccu_suites_m + $availToOccu_suites_f;
            
            $occu_suites_m = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE  suite_id > 0 AND suite_id IN (SELECT suite_id FROM suites WHERE suite_active = 1 AND suite_gender = 'm')")->fetchColumn();
            $occu_suites_f = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE  suite_id > 0 AND suite_id IN (SELECT suite_id FROM suites WHERE suite_active = 1 AND suite_gender = 'f')")->fetchColumn();
            $occu_suites_total = $occu_suites_m + $occu_suites_f;
            
            $remaining_suites_m = $availToOccu_suites_m - $occu_suites_m;
            $remaining_suites_f = $availToOccu_suites_f - $occu_suites_f;
            $remaining_suites_total = $remaining_suites_m + $remaining_suites_f;
            
            // General Counts (Arafa)
            $availToOccu_suites_m_arafa = $db->query("SELECT SUM(tent_capacity) FROM tents WHERE tent_active = 1 AND type = 2  AND tent_gender = 'm'")->fetchColumn();
            $availToOccu_suites_f_arafa = $db->query("SELECT SUM(tent_capacity) FROM tents WHERE tent_active = 1 AND type = 2  AND tent_gender = 'f'")->fetchColumn();
            $availToOccu_suites_total_arafa = $availToOccu_suites_m_arafa + $availToOccu_suites_f_arafa;
    
            $occu_suites_m_arafa = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE  halls_id > 0 AND halls_id IN (SELECT tent_id FROM tents WHERE tent_active = 1 AND type = 2  AND tent_gender = 'm')")->fetchColumn();
            $occu_suites_f_arafa = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE  halls_id > 0 AND halls_id IN (SELECT tent_id FROM tents WHERE tent_active = 1 AND type = 2   AND tent_gender = 'f')")->fetchColumn();
            $occu_suites_total_arafa = $occu_suites_m_arafa + $occu_suites_f_arafa;
    
            $remaining_suites_m_arafa = $availToOccu_suites_m_arafa - $occu_suites_m_arafa;
            $remaining_suites_f_arafa = $availToOccu_suites_f_arafa - $occu_suites_f_arafa;
            $remaining_suites_total_arafa = $remaining_suites_m_arafa + $remaining_suites_f_arafa;
            
            // other data
            
            $availToOccu_buildings_m = $db->query("SELECT SUM(room_capacity) FROM buildings_rooms WHERE room_active = 1 AND room_gender = 'm' AND room_floor_id IN (SELECT floor_id FROM buildings_floors WHERE floor_active = 1 AND floor_bld_id IN (SELECT bld_id FROM buildings WHERE bld_active = 1))")->fetchColumn();
            $availToOccu_buildings_f = $db->query("SELECT SUM(room_capacity) FROM buildings_rooms WHERE room_active = 1 AND room_gender = 'f' AND room_floor_id IN (SELECT floor_id FROM buildings_floors WHERE floor_active = 1 AND floor_bld_id IN (SELECT bld_id FROM buildings WHERE bld_active = 1))")->fetchColumn();
            $availToOccu_buildings_total = $availToOccu_buildings_m + $availToOccu_buildings_f;
            
            $occu_buildings_m = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE  bld_id > 0 AND room_id IN (SELECT room_id FROM buildings_rooms WHERE room_active = 1 AND room_gender = 'm')")->fetchColumn();
            $occu_buildings_f = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE   bld_id > 0 AND room_id IN (SELECT room_id FROM buildings_rooms WHERE room_active = 1 AND room_gender = 'f')")->fetchColumn();
            $occu_buildings_total = $occu_buildings_m + $occu_buildings_f;
            
            $remaining_buildings_m = $availToOccu_buildings_m - $occu_buildings_m;
            $remaining_buildings_f = $availToOccu_buildings_f - $occu_buildings_f;
            $remaining_buildings_total = $remaining_buildings_m + $remaining_buildings_f;
            
            
            $availToOccu_tents_m = $db->query("SELECT SUM(tent_capacity) FROM tents WHERE tent_active = 1 AND type = 1 AND  tent_gender = 'm'")->fetchColumn();
            $availToOccu_tents_f = $db->query("SELECT SUM(tent_capacity) FROM tents WHERE tent_active = 1 AND type = 1 AND tent_gender = 'f'")->fetchColumn();
            $availToOccu_tents_total = $availToOccu_tents_m + $availToOccu_tents_f;
            
            $occu_tents_m = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE  tent_id > 0 AND tent_id IN (SELECT tent_id FROM tents WHERE tent_active = 1 AND type = 1 AND tent_gender = 'm')")->fetchColumn();
            $occu_tents_f = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE  tent_id > 0 AND tent_id IN (SELECT tent_id FROM tents WHERE tent_active = 1 AND  type = 1 AND tent_gender = 'f')")->fetchColumn();
            $occu_tents_total = $occu_tents_m + $occu_tents_f;
    
            $occu_suites_m_arafa = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE  halls_id > 0 AND halls_id IN (SELECT tent_id FROM tents WHERE tent_active = 1 AND type = 2  AND tent_gender = 'm')")->fetchColumn();
            $occu_suites_f_arafa = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE  halls_id > 0 AND halls_id IN (SELECT tent_id FROM tents WHERE tent_active = 1 AND type = 2   AND tent_gender = 'f')")->fetchColumn();
            $occu_suites_total_arafa = $occu_suites_m_arafa + $occu_suites_f_arafa;
            
            $remaining_tents_m = $availToOccu_tents_m - $occu_tents_m;
            $remaining_tents_f = $availToOccu_tents_f - $occu_tents_f;
            $remaining_tents_total = $remaining_tents_m + $remaining_tents_f;
    
            $remaining_suites_m_arafa = $availToOccu_suites_m_arafa - $occu_suites_m_arafa;
            $remaining_suites_f_arafa = $availToOccu_suites_f_arafa - $occu_suites_f_arafa;
            $remaining_suites_total_arafa = $remaining_suites_m_arafa + $remaining_suites_f_arafa;
            
            while ($rowc = $sqlclasses->fetch(PDO::FETCH_ASSOC)) {
                // pilgrims
                $totalPilgrims_m = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_gender = 'm' AND pil_pilc_id = " . $rowc['pilc_id'])->fetchColumn();
                $totalPilgrims_f = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_gender = 'f' AND pil_pilc_id = " . $rowc['pilc_id'])->fetchColumn();
                $totalPilgrims_total = $totalPilgrims_m + $totalPilgrims_f;
    
                $totalPilgrims_m_arafa = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_gender = 'm' AND pil_pilc_id = " . $rowc['pilc_id'])->fetchColumn();
                $totalPilgrims_f_arafa = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_gender = 'f' AND pil_pilc_id = " . $rowc['pilc_id'])->fetchColumn();
                $totalPilgrims_total_arafa = $totalPilgrims_m_arafa + $totalPilgrims_f_arafa;
                
                // Suites
                $accomoPils_Suites_m = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_gender = 'm' AND pil_pilc_id = " . $rowc['pilc_id'] . " AND pil_code IN (SELECT pil_code FROM pils_accomo WHERE  suite_id > 0)")->fetchColumn();
                $accomoPils_Suites_f = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_gender = 'f' AND pil_pilc_id = " . $rowc['pilc_id'] . " AND pil_code IN (SELECT pil_code FROM pils_accomo WHERE  suite_id > 0)")->fetchColumn();
                $accomoPils_Suites_total = $accomoPils_Suites_m + $accomoPils_Suites_f;
                
                
                $noaccomoPils_Suites_m = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_gender = 'm' AND pil_pilc_id = " . $rowc['pilc_id'] . " AND pil_code NOT IN (SELECT pil_code FROM pils_accomo WHERE  suite_id > 0)")->fetchColumn();
                $noaccomoPils_Suites_f = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_gender = 'f' AND pil_pilc_id = " . $rowc['pilc_id'] . " AND pil_code NOT IN (SELECT pil_code FROM pils_accomo WHERE    suite_id > 0)")->fetchColumn();
                $noaccomoPils_Suites_total = $noaccomoPils_Suites_m + $noaccomoPils_Suites_f;
    
                $noaccomoPils_Suites_m_arafa = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_gender = 'm' AND pil_pilc_id = " . $rowc['pilc_id'] . " AND pil_code NOT IN (SELECT pil_code FROM pils_accomo WHERE  halls_id > 0)")->fetchColumn();
                $noaccomoPils_Suites_f_arafa = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_gender = 'f' AND pil_pilc_id = " . $rowc['pilc_id'] . " AND pil_code NOT IN (SELECT pil_code FROM pils_accomo WHERE    halls_id > 0)")->fetchColumn();
                $noaccomoPils_Suites_total_arafa = $noaccomoPils_Suites_m + $noaccomoPils_Suites_f;
                
                // Buildins
                $accomoPils_Buildings_m = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_gender = 'm' AND pil_pilc_id = " . $rowc['pilc_id'] . " AND pil_code IN (SELECT pil_code FROM pils_accomo WHERE  bld_id > 0)")->fetchColumn();
                $accomoPils_Buildings_f = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_gender = 'f' AND pil_pilc_id = " . $rowc['pilc_id'] . " AND pil_code IN (SELECT pil_code FROM pils_accomo WHERE  bld_id > 0)")->fetchColumn();
                $accomoPils_Buildings_total = $accomoPils_Buildings_m + $accomoPils_Buildings_f;
                
                $noaccomoPils_Buildings_m = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_gender = 'm' AND pil_pilc_id = " . $rowc['pilc_id'] . " AND pil_code NOT IN (SELECT pil_code FROM pils_accomo WHERE  bld_id > 0)")->fetchColumn();
                $noaccomoPils_Buildings_f = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_gender = 'f' AND pil_pilc_id = " . $rowc['pilc_id'] . " AND pil_code NOT IN (SELECT pil_code FROM pils_accomo WHERE  bld_id > 0)")->fetchColumn();
                $noaccomoPils_Buildings_total = $noaccomoPils_Buildings_m + $noaccomoPils_Buildings_f;
                
                // Tents
                $accomoPils_Tents_m = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_gender = 'm' AND pil_pilc_id = " . $rowc['pilc_id'] . " AND pil_code IN (SELECT pil_code FROM pils_accomo WHERE  tent_id > 0)")->fetchColumn();
                $accomoPils_Tents_f = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_gender = 'f' AND pil_pilc_id = " . $rowc['pilc_id'] . " AND pil_code IN (SELECT pil_code FROM pils_accomo WHERE  tent_id > 0)")->fetchColumn();
                $accomoPils_Tents_total = $accomoPils_Tents_m + $accomoPils_Tents_f;
                
                $noaccomoPils_Tents_m = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_gender = 'm' AND pil_pilc_id = " . $rowc['pilc_id'] . " AND pil_code NOT IN (SELECT pil_code FROM pils_accomo WHERE  tent_id > 0)")->fetchColumn();
                $noaccomoPils_Tents_f = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_gender = 'f' AND pil_pilc_id = " . $rowc['pilc_id'] . " AND pil_code NOT IN (SELECT pil_code FROM pils_accomo WHERE  tent_id > 0)")->fetchColumn();
                $noaccomoPils_Tents_total = $noaccomoPils_Tents_m + $noaccomoPils_Tents_f;
                
                ?>
                <div class="box">
                    <div class="box-body">
                        <h1><?= $rowc['pilc_title_' . $lang] ?></h1>
                        <div class="row">
                            <?php
                                if ($rowc['pilc_id'] == 1) {
                                    // Suites
                                    
                                    ?>
                                    <div class="col-sm-4">
                                        <b><?= HM_Suites . ' - ' . Manaa ?></b>
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th>
                                                    <?= LBL_Count ?>
                                                </th>
                                                <th>
                                                    <?= LBL_Males ?>
                                                </th>
                                                <th>
                                                    <?= LBL_Females ?>
                                                </th>
                                                <th>
                                                    <?= LBL_Totals ?>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td><?= LBL_AvailableForAccomo ?></td>
                                                <td><?= number_format($availToOccu_suites_m) ?></td>
                                                <td><?= number_format($availToOccu_suites_f) ?></td>
                                                <td><?= number_format($availToOccu_suites_total) ?></td>
                                            </tr>
                                            <tr>
                                                <td><?= LBL_TotalPilgrims ?></td>
                                                <td><?= number_format($totalPilgrims_m) ?></td>
                                                <td><?= number_format($totalPilgrims_f) ?></td>
                                                <td><?= number_format($totalPilgrims_total) ?></td>
                                            </tr>
                                            <tr>
                                                <td><?= LBL_AccomoPilgrims ?></td>
                                                <td><?= number_format($accomoPils_Suites_m) ?></td>
                                                <td><?= number_format($accomoPils_Suites_f) ?></td>
                                                <td><?= number_format($accomoPils_Suites_total) ?></td>
                                            </tr>
                                            <tr>
                                                <td><?= LBL_AvailableSeats ?></td>
                                                <td><?= number_format($remaining_suites_m) ?></td>
                                                <td><?= number_format($remaining_suites_f) ?></td>
                                                <td><?= number_format($remaining_suites_total) ?></td>
                                            </tr>
                                            <tr>
                                                <td><?= LBL_NotAccomoPilgrims ?></td>
                                                <td><?= number_format($noaccomoPils_Suites_m) ?></td>
                                                <td><?= number_format($noaccomoPils_Suites_f) ?></td>
                                                <td><?= number_format($noaccomoPils_Suites_total) ?></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-sm-4"></div>
                                    <div class="col-sm-4">
                                        <b><?= HM_Suites . ' - ' . Arafa ?></b>
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th>
                                                    <?= LBL_Count ?>
                                                </th>
                                                <th>
                                                    <?= LBL_Males ?>
                                                </th>
                                                <th>
                                                    <?= LBL_Females ?>
                                                </th>
                                                <th>
                                                    <?= LBL_Totals ?>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td><?= LBL_AvailableForAccomo ?></td>
                                                <td><?= number_format($availToOccu_suites_m_arafa) ?></td>
                                                <td><?= number_format($availToOccu_suites_f_arafa) ?></td>
                                                <td><?= number_format($availToOccu_suites_total_arafa) ?></td>
                                            </tr>
                                            <tr>
                                                <td><?= LBL_TotalPilgrims ?></td>
                                                <td><?= number_format($totalPilgrims_m_arafa) ?></td>
                                                <td><?= number_format($totalPilgrims_f_arafa) ?></td>
                                                <td><?= number_format($totalPilgrims_total_arafa) ?></td>
                                            </tr>
                                            <tr>
                                                <td><?= LBL_AccomoPilgrims ?></td>
                                                <td><?= number_format($occu_suites_m_arafa) ?></td>
                                                <td><?= number_format($occu_suites_f_arafa) ?></td>
                                                <td><?= number_format($occu_suites_total_arafa) ?></td>
                                            </tr>
                                            <tr>
                                                <td><?= LBL_AvailableSeats ?></td>
                                                <td><?= number_format($remaining_suites_m_arafa) ?></td>
                                                <td><?= number_format($remaining_suites_f_arafa) ?></td>
                                                <td><?= number_format($remaining_suites_total_arafa) ?></td>
                                            </tr>
                                            <tr>
                                                <td><?= LBL_NotAccomoPilgrims ?></td>
                                                <td><?= number_format($noaccomoPils_Suites_m_arafa) ?></td>
                                                <td><?= number_format($noaccomoPils_Suites_f_arafa) ?></td>
                                                <td><?= number_format($noaccomoPils_Suites_total_arafa) ?></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php
                                    
                                } elseif ($rowc['pilc_id'] == 2) {
                                    // Buildings
                                    ?>
                                    <div class="col-sm-4">
                                        <b><?= HM_Buildings . ' - ' . Manaa ?></b>
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th>
                                                    <?= LBL_Count ?>
                                                </th>
                                                <th>
                                                    <?= LBL_Males ?>
                                                </th>
                                                <th>
                                                    <?= LBL_Females ?>
                                                </th>
                                                <th>
                                                    <?= LBL_Totals ?>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <?= LBL_AvailableForAccomo ?>
                                                </td>
                                                <td><?= number_format($availToOccu_buildings_m) ?></td>
                                                <td><?= number_format($availToOccu_buildings_f) ?></td>
                                                <td><?= number_format($availToOccu_buildings_total) ?></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?= LBL_TotalPilgrims ?>
                                                </td>
                                                <td><?= number_format($totalPilgrims_m) ?></td>
                                                <td><?= number_format($totalPilgrims_f) ?></td>
                                                <td><?= number_format($totalPilgrims_total) ?></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?= LBL_AccomoPilgrims ?>
                                                </td>
                                                <td><?= number_format($accomoPils_Buildings_m) ?></td>
                                                <td><?= number_format($accomoPils_Buildings_f) ?></td>
                                                <td><?= number_format($accomoPils_Buildings_total) ?></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?= LBL_AvailableSeats ?>
                                                </td>
                                                <td><?= number_format($remaining_buildings_m) ?></td>
                                                <td><?= number_format($remaining_buildings_f) ?></td>
                                                <td><?= number_format($remaining_buildings_total) ?></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?= LBL_NotAccomoPilgrims ?>
                                                </td>
                                                <td><?= number_format($noaccomoPils_Buildings_m) ?></td>
                                                <td><?= number_format($noaccomoPils_Buildings_f) ?></td>
                                                <td><?= number_format($noaccomoPils_Buildings_total) ?></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-sm-4"></div>
                                    <div class="col-sm-4">
                                        <b><?= HM_Buildings . ' - ' . Arafa ?></b>
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th>
                                                    <?= LBL_Count ?>
                                                </th>
                                                <th>
                                                    <?= LBL_Males ?>
                                                </th>
                                                <th>
                                                    <?= LBL_Females ?>
                                                </th>
                                                <th>
                                                    <?= LBL_Totals ?>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <?= LBL_AvailableForAccomo ?>
                                                </td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?= LBL_TotalPilgrims ?>
                                                </td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?= LBL_AccomoPilgrims ?>
                                                </td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?= LBL_AvailableSeats ?>
                                                </td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?= LBL_NotAccomoPilgrims ?>
                                                </td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php
                                } elseif ($rowc['pilc_id'] == 3) {
                                    // Tents
                                    ?>
                                    <div class="col-sm-4">
                                        <b><?= HM_Tents . ' - ' . Manaa  ?></b>
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th>
                                                    <?= LBL_Count ?>
                                                </th>
                                                <th>
                                                    <?= LBL_Males ?>
                                                </th>
                                                <th>
                                                    <?= LBL_Females ?>
                                                </th>
                                                <th>
                                                    <?= LBL_Totals ?>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <?= LBL_AvailableForAccomo ?>
                                                </td>
                                                <td><?= number_format($availToOccu_tents_m) ?></td>
                                                <td><?= number_format($availToOccu_tents_f) ?></td>
                                                <td><?= number_format($availToOccu_tents_total) ?></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?= LBL_TotalPilgrims ?>
                                                </td>
                                                <td><?= number_format($totalPilgrims_m) ?></td>
                                                <td><?= number_format($totalPilgrims_f) ?></td>
                                                <td><?= number_format($totalPilgrims_total) ?></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?= LBL_AccomoPilgrims ?>
                                                </td>
                                                <td><?= number_format($accomoPils_Tents_m) ?></td>
                                                <td><?= number_format($accomoPils_Tents_f) ?></td>
                                                <td><?= number_format($accomoPils_Tents_total) ?></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?= LBL_AvailableSeats ?>
                                                </td>
                                                <td><?= number_format($remaining_tents_m) ?></td>
                                                <td><?= number_format($remaining_tents_f) ?></td>
                                                <td><?= number_format($remaining_tents_total) ?></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?= LBL_NotAccomoPilgrims ?>
                                                </td>
                                                <td><?= number_format($noaccomoPils_Tents_m) ?></td>
                                                <td><?= number_format($noaccomoPils_Tents_f) ?></td>
                                                <td><?= number_format($noaccomoPils_Tents_total) ?></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-sm-4"></div>
                                    <div class="col-sm-4">
                                        <b><?= HM_Tents . ' - ' . Arafa  ?></b>
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th>
                                                    <?= LBL_Count ?>
                                                </th>
                                                <th>
                                                    <?= LBL_Males ?>
                                                </th>
                                                <th>
                                                    <?= LBL_Females ?>
                                                </th>
                                                <th>
                                                    <?= LBL_Totals ?>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <?= LBL_AvailableForAccomo ?>
                                                </td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?= LBL_TotalPilgrims ?>
                                                </td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?= LBL_AccomoPilgrims ?>
                                                </td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?= LBL_AvailableSeats ?>
                                                </td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?= LBL_NotAccomoPilgrims ?>
                                                </td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php
                                }
                            ?>
                        </div>
                        <div class="clearfix" style="margin-top:40px;">
                            &nbsp;
                        </div>
                    </div>
                </div>
                <?php
            }
        ?>
    
    
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
                                <th>
                                    <?= Occu ?>
                                </th>
                                <th>
                                    <?= LBL_ٍRemaining ?>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            
                                // Suites
                                while ($row = $sql->fetch()) {
                                    $occu = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_bus_id = " . $row['bus_id'])->fetchColumn();
                                    $remaining = $row['bus_seats'] - $occu;
    
                                    ?>
                                    <tr>
                                        <td><?= $row['city_title_ar'] ?></td>
                                        <td><?= number_format($row['bus_seats']) ?></td>
                                        <td><?= $occu ?></td>
                                        <td><?= $remaining ?></td>
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
