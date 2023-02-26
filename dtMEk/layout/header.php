<?php

    ob_start();
    require_once 'config/init.php';
    require_once 'config/config.inc.php';
    require_once 'config/db.php';
    require_once 'config/functions.php';
    require_once 'config/constants.php';
    require_once 'config/msegat.php';
    include '_includes/XLSClasses/PHPExcel/IOFactory.php';
    include'_includes/phpqrcode/qrlib.php';
    
    global $lang, $css1, $css2, $css3, $css4, $session, $db, $lang;
    
    $curpage = '/' . basename(explode('?',$_SERVER['REQUEST_URI'])[0]);
    $curpagewithquery = $_SERVER['REQUEST_URI'];

?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= PROJ_TITLE ?> CP</title>
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
        if ($css2) echo '<link href="'.CP_PATH.'/assets/css/' . $css2 . '" rel="stylesheet" type="text/css" />';
        if ($css3) echo '<link href="'.CP_PATH.'/assets/css/' . $css3 . '" rel="stylesheet" type="text/css" />';
    ?>
</head>
<body class="skin-blue sidebar-mini">
<div class="wrapper">

    <header class="main-header">
        <!-- Logo -->
        <div class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><?= PROJ_TITLE[0] . PROJ_TITLE[1] . PROJ_TITLE[2] ?></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b><?= PROJ_TITLE ?></b></span>
        </div>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <!-- <img src="dist/img/user2-160x160.jpg" class="user-image" alt="User Image" /> -->
                            <span class="hidden-xs"><?= $session->userinfo['name'] ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-right">
                                    <a href="<?= CP_PATH ?>/main/index?logout=1" class="btn btn-default btn-flat">Sign out</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- Sidebar user panel -->
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu">
                <li class="header"><?= HM_MAINNAVIGATION ?></li>
    
                <?php
                    $perms = $db->query("SELECT permid FROM _users_perms WHERE user_id = " . $_SESSION['userinfo']['user_id'])->fetchAll(PDO::FETCH_COLUMN);
                
                ?>

                <li <?php if ($curpage === '/index') echo 'class="active"'; ?>>
                    <a href="<?= CP_PATH ?>/main/index">
                        <i class="fa fa-dashboard fa-fw"></i> <span><?= HM_Dashboard ?></span>
                    </a>
                </li>

                <li <?php if ($curpage === '/soothing_summary') echo 'class="active"'; ?>>
                    <a href="<?= CP_PATH ?>/main/soothing_summary">
                        <i class="fa fa-dashboard fa-fw"></i>
                        <span><?= HM_soothing_summary ?> ( <?= LBL_MENA ?> ) </span>
                    </a>
                </li>
                <li <?php if ($curpage === '/soothing_summary_arafa') echo 'class="active"'; ?>>
                    <a href="<?= CP_PATH ?>/main/soothing_summary_arafa">
                        <i class="fa fa-dashboard fa-fw"></i> <span><?= HM_soothing_summary ?> ( <?= arafa ?> )</span>
                    </a>
                </li>
                <li <?php if ($curpage === '/bus_accommodation_summary') echo 'class="active"'; ?>>
                    <a href="<?= CP_PATH ?>/main/bus_accommodation_summary">
                        <i class="fa fa-dashboard fa-fw"></i> <span><?= HM_bus_accommodation_summary ?></span>
                    </a>
                </li>

                
                <li class="header"><?= HM_BASICINFORMATION ?></li>
                
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(2, $perms, true))) { ?>
                    <li <?php if ($curpage === '/ourlocations') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/basic_info/ourlocations">
                            <i <?php if ($curpage === '/ourlocations') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_OurLocations ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(3, $perms, true))) { ?>
                    <li <?php if ($curpage === '/ourcompanies') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/basic_info/ourcompanies">
                            <i <?php if ($curpage === '/ourcompanies') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_OurCompanies ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(4, $perms, true))) { ?>

                    <li <?php if ($curpage === '/promos') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/basic_info/promos">
                            <i <?php if ($curpage === '/promos') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_Promos ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(5, $perms, true))) { ?>

                    <li <?php if ($curpage === '/cities') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/basic_info/cities">
                            <i <?php if ($curpage === '/cities') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_Cities ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(6, $perms, true))) { ?>
    
                    <?php
                    
                    $menu_pages = array('/suites', '/suite', '/halls', '/hall');
                    
                    ?>

                    <li class="treeview <?php if (in_array($curpage, $menu_pages, true)) echo 'active'; ?>">
                        <a href="#">
                            <i class="fa fa-fw fa-list"></i> <span><?= HM_Suites ?></span> <i
                                    class="fa fa-angle-<?= DIR_AFTER ?> pull-<?= DIR_AFTER ?>"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li <?php if ($curpage === '/suite') echo 'class="active"'; ?>><a
                                        href="<?= CP_PATH ?>/basic_info/edit/suite"><i <?php if ($curpage === '/suite') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_AddNew ?>
                                </a></li>
                            <li <?php if ($curpage === '/suites') echo 'class="active"'; ?>><a
                                        href="<?= CP_PATH ?>/basic_info/suites"><i <?php if ($curpage === '/suites') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_ListAll ?>
                                </a></li>
                            <li <?php if ($curpage === '/halls') echo 'class="active"'; ?>><a
                                        href="<?= CP_PATH ?>/basic_info/halls"><i <?php if ($curpage === '/halls') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_Halls ?>
                                </a></li>
                        </ul>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(7, $perms, true))) { ?>
    
                    <?php
                    
                    $menu_pages = array('/buildings', '/building', '/floors', '/floor', '/rooms', '/room');
                    
                    ?>

                    <li class="treeview <?php if (in_array($curpage, $menu_pages, true)) echo 'active'; ?>">
                        <a href="#">
                            <i class="fa fa-fw fa-list"></i>
                            <span><?= HM_Buildings ?> / <?= LBL_PremisesPlus ?></span> <i
                                    class="fa fa-angle-<?= DIR_AFTER ?> pull-<?= DIR_AFTER ?>"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li <?php if ($curpage === '/building') echo 'class="active"'; ?>><a
                                        href="<?= CP_PATH ?>/basic_info/edit/building"><i <?php if ($curpage === '/building') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_AddNew ?>
                                </a></li>
                            <li <?php if ($curpage === '/buildings') echo 'class="active"'; ?>><a
                                        href="<?= CP_PATH ?>/basic_info/buildings"><i <?php if ($curpage === '/buildings') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_ListAll ?>
                                </a></li>
                            <li <?php if ($curpage === '/floors') echo 'class="active"'; ?>><a
                                        href="<?= CP_PATH ?>/basic_info/floors"><i <?php if ($curpage === '/floors') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_Floors ?>
                                </a></li>
                            <li <?php if ($curpage === '/rooms') echo 'class="active"'; ?>><a
                                        href="<?= CP_PATH ?>/basic_info/rooms"><i <?php if ($curpage === '/rooms') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_Rooms ?>
                                </a></li>
                        </ul>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(8, $perms, true))) { ?>

                    <li <?php if ($curpage === '/tents') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/basic_info/tents?type=1">
                            <i <?php if ($curpage === '/tents') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i>
                            <span> <?= HM_Tents ?> ( <?= mozdalifa ?> )</span>
                        </a>
                    </li>
                <?php } ?>
                
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(8, $perms, true))) { ?>

                    <li <?php if ($curpage === '/tents') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/basic_info/tents?type=2">
                            <i <?php if ($curpage === '/tents') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_Halls ?> ( <?= arafa ?> )</span>
                        </a>
                    </li>
                <?php } ?>
                
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(9, $perms, true))) { ?>

                    <li <?php if ($curpage === '/buses') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/basic_info/buses">
                            <i <?php if ($curpage === '/buses') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_PilgrimsBuses ?></span>
                        </a>
                    </li>
                <?php } ?>
                
                <li class="header"><?= HM_STAFF ?></li>
                
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(10, $perms, true))) { ?>

                    <li <?php if ($curpagewithquery === '/staff/index?type=1') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/staff?type=1">
                            <i <?php if ($curpagewithquery === '/staff/index?type=1') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_Managers ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(11, $perms, true))) { ?>

                    <li <?php if ($curpagewithquery === '/staff/index?type=2') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/staff?type=2">
                            <i <?php if ($curpagewithquery === '/staff/index?type=2') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_Supervisors ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(12, $perms, true))) { ?>

                    <li <?php if ($curpagewithquery === '/staff/index?type=3') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/staff?type=3">
                            <i <?php if ($curpagewithquery === '/staff/index?type=3') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_Muftis ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(13, $perms, true))) { ?>

                    <li class="header"><?= HM_PILGRIMS ?></li>
    
                    <?php
                    
                    $menu_pages = array('/pilgrims/index', '/pilgrims/edit/pilgrim', '/pilgrims/classes', '/pilgrims/edit/class');
                    
                    ?>

                    <li class="treeview <?php if (in_array($url, $menu_pages, true)) echo 'active'; ?>">
                        <a href="#">
                            <i class="fa fa-fw fa-list"></i> <span><?= HM_Pilgrims ?></span> <i
                                    class="fa fa-angle-<?= DIR_AFTER ?> pull-<?= DIR_AFTER ?>"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li <?php if ($url === '/edit') echo 'class="active"'; ?>><a
                                        href="<?= CP_PATH ?>/pilgrims/edit/pilgrim"><i <?php if ($url === '/pilgrims/edit/pilgrim') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_AddNew ?>
                                </a></li>
                            <li <?php if ($url === '/pilgrims') echo 'class="active"'; ?>><a
                                        href="<?= CP_PATH ?>/pilgrims"><i <?php if ($url === '/pilgrims/index') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_ListAll ?>
                                </a></li>
                            <li <?php if ($url === '/export-pils-table.php') echo 'class="active"'; ?>><a
                                        href="<?= CP_PATH ?>/pilgrims/export"><i <?php if ($url === '/pilgrims/export') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= LBL_exportfilepils ?>
                                </a></li>

                            <li <?php if ($url === '/pil_classes.php') echo 'class="active"'; ?>><a
                                        href="<?= CP_PATH ?>/pilgrims/classes"><i <?php if ($url === '/pilgrims/classes') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_ManageClasses ?>
                                </a></li>
                        </ul>
                    </li>
                <?php } ?>
                <!-- <span class="label label-warning pull-right">27</span> -->
                <li class="header"><?= HM_ACCOMODATIONS ?></li>
                <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(32, $perms, true))) { ?>
                    <li <?php if ($curpage === '/auto_accomo.php') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/auto_accomo.php">
                            <i <?php if ($curpage === '/auto_accomo.php') echo 'class="fa fa-fw fa-star text-white"'; else echo 'class="fa fa-fw fa-star"'; ?>></i>
                            <span><?= HM_AutoAccomo ?> ( <?= LBL_MENA ?> ) </span>
                        </a>
                    </li>

                    <li <?php if ($curpage === '/auto_accomo_arafa.php') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/auto_accomo_arafa.php">
                            <i <?php if ($curpage === '/auto_accomo_arafa.php') echo 'class="fa fa-fw fa-star text-white"'; else echo 'class="fa fa-fw fa-star"'; ?>></i>
                            <span><?= HM_AutoAccomo ?> ( <?= arafa ?> ) </span>
                        </a>
                    </li>

                    <li <?php if ($curpage === '/auto_accomo_buses.php') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/auto_accomo_buses.php">
                            <i <?php if ($curpage === '/auto_accomo_buses.php') echo 'class="fa fa-fw fa-star text-white"'; else echo 'class="fa fa-fw fa-star"'; ?>></i>
                            <span><?= HM_AutoAccomoBuses ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php
                    
                    $menu_pages = array('/accomo_suites.php', '/accomo_buildings.php', '/accomo_tents.php', '/accomo_buses.php', '/bulkaccomosms.php');
                
                ?>

                <li class="treeview <?php if (in_array($curpage, $menu_pages, true)) echo 'active'; ?>">
                    <a href="#">
                        <i class="fa fa-fw fa-list"></i>
                        <span><?= HM_ViewAccomodations ?> ( <?= HM_export_files ?> ) </span> <i
                                class="fa fa-angle-<?= DIR_AFTER ?> pull-<?= DIR_AFTER ?>"></i>
                    </a>
                    <ul class="treeview-menu">
                        <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(14, $perms, true))) { ?>

                            <li <?php if ($curpage === '/accomo_suites.php') echo 'class="active"'; ?>><a
                                        href="<?= CP_PATH ?>/accomo_suites.php"><i <?php if ($curpage === '/accomo_suites.php') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_SuitesAccomodations ?>
                                </a></li>
                        
                        <?php } ?>
                        <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(15, $perms, true))) { ?>


                            <li <?php if ($curpage === '/accomo_buildings.php') echo 'class="active"'; ?>><a
                                        href="<?= CP_PATH ?>/accomo_buildings.php"><i <?php if ($curpage === '/accomo_buildings.php') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_BuildingsAccomodations ?>
                                </a></li>
                        
                        <?php } ?>
                        <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(16, $perms, true))) { ?>

                            <li <?php if ($curpage === '/accomo_tents.php') echo 'class="active"'; ?>><a
                                        href="<?= CP_PATH ?>/accomo_tents.php"><i <?php if ($curpage === '/accomo_tents.php') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_TentsAccomodations ?>
                                    ( <?= mozdalifa ?> ) </a></li>
                        
                        <?php } ?>
                        <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(16, $perms, true))) { ?>

                            <li <?php if ($curpage === '/accomo_tents_halls.php') echo 'class="active"'; ?>><a
                                        href="<?= CP_PATH ?>/accomo_tents_halls.php"><i <?php if ($curpage === '/accomo_tents_halls.php') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_HallsAccomodations ?>
                                </a></li>
                        
                        <?php } ?>
                        <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(17, $perms, true))) { ?>

                            <li <?php if ($curpage === '/accomo_buses.php') echo 'class="active"'; ?>><a
                                        href="<?= CP_PATH ?>/accomo_buses.php"><i <?php if ($curpage === '/accomo_buses.php') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_BusesAccomodations ?>
                                </a></li>
                        <?php } ?>
                        <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(37, $perms, true))) { ?>

                            <li <?php if ($curpage === '/bulkaccomosms.php') echo 'class="active"'; ?>><a
                                        href="<?= CP_PATH ?>/bulkaccomosms.php"><i <?php if ($curpage === '/bulkaccomosms.php') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_bulkaccomosms ?>
                                </a></li>
                        <?php } ?>
                        <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(38, $perms, true))) { ?>

                            <li <?php if ($curpage === '/bulkbussms.php') echo 'class="active"'; ?>><a
                                        href="<?= CP_PATH ?>/bulkbussms.php"><i <?php if ($curpage === '/bulkbussms.php') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_bulkbussms ?>
                                </a></li>
                        <?php } ?>
                    </ul>
                </li>

                <li class="header"><?= HM_PUSHNOTIFICATIONS ?></li>
                
                <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(18, $perms, true))) { ?>

                    <li <?php if ($curpage === '/push_new.php') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/push_new.php">
                            <i <?php if ($curpage === '/push_new.php') echo 'class="fa fa-fw fa-star text-white"'; else echo 'class="fa fa-fw fa-star"'; ?>></i>
                            <span><?= HM_SendNewPushNotification ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(19, $perms, true))) { ?>

                    <li <?php if ($curpage === '/push_list.php') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/push_list.php">
                            <i <?php if ($curpage === '/push_list.php') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>></i>
                            <span><?= HM_NotificationsHistory ?></span>
                        </a>
                    </li>
                <?php } ?>

                <li class="header"><?= HM_GENERALGUIDE ?></li>
                <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(31, $perms, true))) { ?>

                    <li <?php if ($curpage === '/general_guide.php') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/general_guide.php">
                            <i <?php if ($curpage === '/general_guide.php') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_GENERALGUIDE ?></span>
                        </a>
                    </li>
                <?php } ?>
                
                <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(31, $perms, true))) { ?>

                    <li <?php if ($curpage === '/general_guide_staff.php') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/general_guide_staff.php">
                            <i <?php if ($curpage === '/general_guide_staff.php') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i>
                            <span> <?= HM_GENERALGUIDESTAFF ?></span>
                        </a>
                    </li>
                <?php } ?>


                <li class="header"><?= HM_GENERAL ?></li>
                <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(20, $perms, true))) { ?>

                    <li <?php if ($curpage === '/news.php') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/news.php">
                            <i <?php if ($curpage === '/news.php') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_News ?></span>
                        </a>
                    </li>
                <?php } ?>
                
                <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(20, $perms, true))) { ?>

                    <li <?php if ($curpage === '/competitions.php') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/competitions.php">
                            <i <?php if ($curpage === '/competitions.php') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_competitions ?></span>
                        </a>
                    </li>
                <?php } ?>
                
                <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(20, $perms, true))) { ?>

                    <li <?php if ($curpage === '/competitions-result.php') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/competitions-result.php">
                            <i <?php if ($curpage === '/competitions-result.php') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i>
                            <span> <?= HM_competitions_results ?></span>
                        </a>
                    </li>
                <?php } ?>
                
                
                <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(20, $perms, true))) { ?>

                    <li <?php if ($curpage === '/new_questions.php') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/new_questions.php">
                            <i <?php if ($curpage === '/new_questions.php') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_question ?></span>
                        </a>
                    </li>
                <?php } ?>
                
                <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(20, $perms, true))) { ?>

                    <li <?php if ($curpage === '/answers.php') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/answers.php">
                            <i <?php if ($curpage === '/answers.php') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_answer ?></span>
                        </a>
                    </li>
                <?php } ?>
                
                
                
                <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(21, $perms, true))) { ?>

                    <li <?php if ($curpage === '/aboutus.php') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/aboutus.php">
                            <i <?php if ($curpage === '/aboutus.php') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_Aboutus ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(22, $perms, true))) { ?>

                    <li <?php if ($curpage === '/contactinfo.php') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/contactinfo.php">
                            <i <?php if ($curpage === '/contactinfo.php') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_ContactInfo ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(23, $perms, true))) { ?>

                    <li <?php if ($curpage === '/social.php') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/social.php">
                            <i <?php if ($curpage === '/social.php') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_Social ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(24, $perms, true))) { ?>

                    <li <?php if ($curpage === '/faq.php') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/faq.php">
                            <i <?php if ($curpage === '/faq.php') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_FAQ ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(25, $perms, true))) { ?>

                    <li <?php if ($curpage === '/haj_album.php') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/haj_album.php">
                            <i <?php if ($curpage === '/haj_album.php') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_HajAlbum ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(26, $perms, true))) { ?>

                    <li <?php if ($curpage === '/haj_guide_cats.php') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/haj_guide_cats.php">
                            <i <?php if ($curpage === '/haj_guide_cats.php') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_HajGuideCats ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(27, $perms, true))) { ?>

                    <li <?php if ($curpage === '/haj_guide_articles.php') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/haj_guide_articles.php">
                            <i <?php if ($curpage === '/haj_guide_articles.php') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i>
                            <span> <?= HM_HajGuideArticles ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(28, $perms, true))) { ?>

                    <li <?php if ($curpage === '/feedback.php') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/feedback.php">
                            <i <?php if ($curpage === '/feedback.php') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_Feedback ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php
                    
                    $menu_pages = array('/questions.php', '/qanswers.php');
                
                ?>

                <!-- <li class="treeview <?php if (in_array($curpage, $menu_pages, true)) echo 'active'; ?>">
              <a href="#">
                <i class="fa fa-fw fa-list"></i> <span><?= HM_RatingQuestions ?></span> <i class="fa fa-angle-<?= DIR_AFTER ?> pull-<?= DIR_AFTER ?>"></i>
              </a>
              <ul class="treeview-menu">
		            <li <?php if ($curpage === '/questions.php') echo 'class="active"'; ?>><a href="<?= CP_PATH ?>/questions.php"><i <?php if ($curpage === '/questions.php') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_ManageQuestions ?></a></li>
                <li <?php if ($curpage === '/qanswers.php') echo 'class="active"'; ?>><a href="<?= CP_PATH ?>/qanswers.php"><i <?php if ($curpage === '/qanswers.php') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_ViewAnswers ?></a></li>
              </ul>
            </li> -->


                <li class="header"><?= HM_SYSTEM ?></li>
                
                <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(29, $perms, true))) { ?>

                    <li <?php if ($curpage === '/settings.php') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/settings.php">
                            <i <?php if ($curpage === '/settings.php') echo 'class="fa fa-fw fa-cog text-white"'; else echo 'class="fa fa-fw fa-cog"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span><?= HM_Settings ?></span>
                        </a>
                    </li>
                <?php } ?>
                
                <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(33, $perms, true))) { ?>

                    <li <?php if ($curpage === '/employees.php') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/employees.php">
                            <i <?php if ($curpage === '/employees.php') echo 'class="fa fa-fw fa-cog text-white"'; else echo 'class="fa fa-fw fa-cog"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span><?= HM_Employees ?></span>
                        </a>
                    </li>
                <?php } ?>
                
                <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(35, $perms, true))) { ?>

                    <li <?php if ($curpage === '/update_pils_photos.php') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/update_pils_photos.php">
                            <i <?php if ($curpage === '/update_pils_photos.php') echo 'class="fa fa-fw fa-cog text-white"'; else echo 'class="fa fa-fw fa-cog"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i>
                            <span><?= HM_UpdatePilsPhotos ?></span>
                        </a>
                    </li>
                <?php } ?>
                
                <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(36, $perms, true))) { ?>

                    <li <?php if ($curpage === '/update_emps_photos.php') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/update_emps_photos.php">
                            <i <?php if ($curpage === '/update_emps_photos.php') echo 'class="fa fa-fw fa-cog text-white"'; else echo 'class="fa fa-fw fa-cog"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i>
                            <span><?= HM_UpdateEmpsPhotos ?></span>
                        </a>
                    </li>
                <?php } ?>
                
                <?php if ($_SESSION['userinfo']['userlevel'] == 9 || (in_array(34, $perms, true))) { ?>

                    <li <?php if ($curpage === '/designs_managers.php') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/designs_managers.php">
                            <i <?php if ($curpage === '/designs_managers.php') echo 'class="fa fa-fw fa-cog text-white"'; else echo 'class="fa fa-fw fa-cog"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i>
                            <span><?= HM_Employees_Designs ?></span>
                        </a>
                    </li>
                <?php } ?>


                <li>
    
    
                    <?php
                        if ($lang === 'en') {
                            echo '<a href="' . CP_PATH . '/?lang=ar">';
                            echo '<i class="fa fa-fw fa-language" style="padding-' . DIR_AFTER . ': 7px;"></i> <span>العربية</span>';
                            echo '</a>';
                        } else {
                            echo '<a href="' . CP_PATH . '/?lang=en">';
                            echo '<i class="fa fa-fw fa-language" style="padding-' . DIR_AFTER . ': 7px;"></i> <span>English</span>';
                            echo '</a>';
                        }
                    ?>


                </li>
                
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(30, $perms, true))) { ?>

                    <li <?php if ($curpage === '/users_all.php') echo 'class="active"'; ?>>
                        <a href="<?= CP_PATH ?>/users_all.php">
                            <i <?php if ($curpage === '/users_all.php') echo 'class="fa fa-fw fa-users text-white"'; else echo 'class="fa fa-fw fa-users"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span><?= HM_SystemUsers ?></span>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>
