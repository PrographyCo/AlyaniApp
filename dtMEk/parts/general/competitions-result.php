<?php
    global $db, $url, $lang, $session;
    
    $title = HM_competitions_results;
    $table = 'competition_result';
    $table_id = 'id';
    $sql = $db->query("SELECT * FROM $table ORDER BY result DESC");
    
    if (isset($_GET['filter'])) {
        $competition_id = $_GET['competition_id'];
        $sqlmore = '';
        
        if (isset($_GET['winners']) && $_GET['winners']==1) $sqlmore = ' AND result = total';
        
        
        if ($competition_id != 0) {
            $sql = $db->query("SELECT * FROM $table WHERE competition_id = $competition_id $sqlmore ORDER BY result DESC");
        } else {
            $sql = $db->query("SELECT * FROM $table ".str_replace(' AND ',' WHERE ', $sqlmore)." ORDER BY result DESC");
            
        }
        
        
    }

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $title ?> <small><?= (isset($_GET['winners']) && $_GET['winners'] == 1)?LBL_Winners:'' ?></small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <?= $msg??'' ?>

                <div class="box">

                    <form style="margin: 40px;">
                        <div class="row">
                            <div class="col-lg-2">
                                <label><?= HM_competitions ?> </label>
                                <select name="competition_id" class="form-control select2">
                                    <option value="0"><?= LBL_Choose ?></option>
                                    <option value="0"><?= HM_ListAll ?></option>
                                    <?php
                                        $competitions = $db->query("SELECT * FROM competitions");
                                        while ($rowcompetitions = $competitions->fetch(PDO::FETCH_ASSOC)) { ?>

                                            <option value="<?= $rowcompetitions['id'] ?>"><?= $rowcompetitions['name_' . $_COOKIE['lang']] ?></option>
                                        
                                        <?php }
                                    ?>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <label><?= LBL_Winners ?></label>
                                <select name="winners" class="form-control select2">
                                    <option value="0"><?= LBL_Choose ?></option>
                                    <option value="0"><?= HM_ListAll ?></option>
                                    <option value="1"><?= LBL_Winners ?></option>
                                </select>
                            </div>

                            <div class="col-lg-2" style="margin-top: 25px;">
                                <button class="btn btn-success" name="filter"><?= HM_show ?></button>
                            </div>
                        </div>
                    </form>

                    <div class="box-body">
                        <table class="datatable-table4 table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>
                                    <?= HM_competitions_name ?>
                                </th>
                                <th>
                                    <?= HM_competitions_user ?>
                                </th>
                                <th><?= LBL_total_degree ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                $i = 1;
                                while ($row = $sql->fetch()) {
                                    $competition_id = $row['competition_id'];
                                    $com = $db->query("SELECT * FROM competitions WHERE id = $competition_id ");
                                    $competition_name = $com->fetch();
                                    
                                    $user_id = $row['user_id'];
                                    $user = $db->query("SELECT * FROM pils WHERE pil_id = $user_id ");
                                    $user_name = $user->fetch();
                                    
                                    echo '<tr>';
                                    echo '<td>';
                                    echo $i;
                                    echo '</td>';
                                    echo '<td>';
                                    echo $competition_name['name_' . $_COOKIE['lang']]??'';
                                    echo '</td>';
                                    
                                    echo '<td>';
                                    echo $user_name['pil_name']??'';
                                    echo '</td>';
                                    echo '<td>';
                                    echo $row['result'] . ' / ' . $row['total'];
                                    echo '</td>';
                                    
                                    
                                    echo ' </tr>';
                                    
                                    $i++;
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
