<?php
    global $db, $session, $lang, $url;
    
    $title = HM_Halls;
    $table = 'suites_halls_stuff';
    $table_id = 'stuff_id';
    $newedit_page = CP_PATH . '/basic_info/edit/stuff';
    
    if (isset($_GET['hall_id']) && $_GET['hall_id'] > 0) $newedit_page .= '?hall_id=' . $_GET['hall_id'];
    
    if (isset($_GET['del']) && is_numeric($_GET['del'])) {
        
        $id = $_GET['del'];
        $sqldel1 = $db->query("DELETE FROM $table WHERE $table_id = $id");
        
    }
    
    if (isset($_POST['add_multi'])) {
        $number = (int) $_POST['stuff_number'];
        $type = $_POST['stuff_type'];
        $hall_id = $_GET['hall_id'];
        
        $count = $db->query("SELECT COUNT($table_id) FROM $table WHERE hall_id=$hall_id AND stuff_type='$type'")->fetchColumn();
        $c = 0;
        
        for ($i=1;$i <= $number;$i++) {
            $n = $count + $i;
            $db->query("INSERT INTO $table VALUES(NULL,$hall_id,'$hall_id-$n','$type',$n,1,now(),now())");
            $c++;
        }
        
        $msg = '<div class="alert alert-success alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button><h4><i class="icon fa fa-check"></i>' . LBL_Added . '!</h4>' . LBL_Item . ' ' . LBL_Added . ' ' . $c . ' ' . match ($type) {
                'bed' => LBL_Bed,
                'chair' => LBL_Chair1,
                'bench' => LBL_Chair2
            } .' ' . LBL_Successfully . '<br /></div>';
        unset($_POST);
    }

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $title ?>
            <a href="<?= $newedit_page ?>" class="btn btn-success pull-<?= DIR_AFTER ?>"><i
                    class="fa fa-star"></i> <?= BTN_AddNew ?></a>
            
            <?php
                if (isset($_GET['hall_id']) && $_GET['hall_id'] > 0) {
                    ?>
                    <a href="javascript:show_model();" style="margin-left: 10px"
                       class="btn btn-success pull-<?= DIR_AFTER ?>">
                        <i class="fa fa-star"></i> <?= BTN_AddNew_Multiple ?>
                    </a>
                    <?php
                }
            ?>
        </h1>
    </section>
    
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <?= $msg ?? '' ?>
                
                <div class="box">
                    <div class="box-body">
                        <table class="datatable-table4 table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th><?= LBL_Title ?></th>
                                <th><?= Type ?></th>
                                <th><?= HM_Hall ?></th>
                                <th><?= LBL_Status ?></th>
                                <th><?= LBL_Order ?></th>
                                <th><?= LBL_Actions ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                $sqlmore = '';
                                if (isset($_GET['hall_id']) && is_numeric($_GET['hall_id'])) $sqlmore = "WHERE h.hall_id = " . $_GET['hall_id'];
                                
                                $sql = $db->query("SELECT s.*, h.hall_title FROM $table s LEFT OUTER JOIN suites_halls h ON h.hall_id = s.hall_id $sqlmore ORDER BY s.stuff_order");
                                while ($row = $sql->fetch()) {
                                    ?>
                                    <tr>
                                        <td><?= $row['stuff_title'] ?></td>
                                        <td>
                                            <?= match ($row['stuff_type']) {
                                                'bed' => LBL_Bed,
                                                'chair' => LBL_Chair1,
                                                'bench' => LBL_Chair2
                                            } ?>
                                        </td>
                                        <td><?= $row['hall_title'] ?></td>
                                        <td>
                                            <span
                                                class="label label-<?= ($row['stuff_active'] == 1) ? 'success' : 'danger' ?>"><?= ($row['stuff_active'] == 1) ? LBL_Active : LBL_Inactive ?></span>
                                        </td>
                                        <td><?= $row['stuff_order'] ?></td>
                                        <td>
                                            
                                            <a href="<?= $newedit_page . (isset($_GET['hall_id']) ? '&' : '?') . 'id=' . $row[$table_id] ?>"
                                               class="label label-info">
                                                <i class="fa fa-edit"></i><?= LBL_Modify ?>
                                            </a>
                                            <a href="<?= $url . (isset($_GET['hall_id']) ? '?hall_id=' . $_GET['hall_id'] . '&' : '?') . 'del=' . $row[$table_id] ?>"
                                               class="label label-danger"
                                               onclick="return confirm('<?= LBL_DeleteConfirm ?>');">
                                                <i class="fa fa-trash"></i><?= LBL_Delete ?>
                                            </a>
                                        
                                        </td>
                                    </tr>
                                    <?php
                                }
                            ?>
                            
                            </tbody>
                        </table>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!--/.col (right) -->
        
        </div>   <!-- /.row -->
    </section><!-- /.content -->
    
    <section class="multi-add-model modal modal-backdrop flex-center" style="display: none;">
        <form class="modal-body flex-center" action="#" method="post" style="background: #fff; width: 60%; height: 60%">
            <a href="javascript:hide_model();"
               style="margin-left: 10px; position: absolute; top: 50px; right: 50px;font-size: 25px"
               class="btn text-danger">
                
                <i class="fa fa-close"></i>
            
            </a>
            
            <div class="row" style="width: 70%">
                <div class="form-group margin-bottom">
                    <label>العدد</label>
                    <input type="number" min="1" class="col-12 form-control" name="stuff_number" required/>
                </div>
                
                <div class="form-group margin-bottom">
                    <label>النوع</label>
                    <div class="col-12">
                        <select name="stuff_type" class="form-control select2" style="width: 100%">
                            <option value="bed"><?= LBL_Bed ?></option>
                            <option value="chair"><?= LBL_Chair1 ?></option>
                            <option value="bench"><?= LBL_Chair2 ?></option>
                        </select>
                    </div>
                </div>
                
                <input type="submit" class="col-md-12 btn btn-success" name="add_multi" value="ارسال"/>
            </div>
        </form>
    </section>
</div>

<script>
    function show_model() {
        $('.multi-add-model').show()
    }

    function hide_model() {
        $('.multi-add-model').hide()
    }
</script>
