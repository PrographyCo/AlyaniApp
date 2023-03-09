<?php

    ob_start();
    require_once 'config/init.php';
    require_once 'config/config.inc.php';
    require_once 'config/db.php';
    require_once 'config/functions.php';
    require_once 'config/pushfunctions.php';
    require_once 'config/constants.php';
    require_once 'config/msegat.php';
    include_once '_includes/XLSClasses/PHPExcel/IOFactory.php';
    include_once '_includes/phpqrcode/qrlib.php';
    
    global $lang, $css1, $css2, $css3, $css4, $session, $db, $lang, $url;
    
    $curpage = '/' . explode('?',trim(str_replace(CP_PATH,'',$_SERVER['REQUEST_URI']),' /'))[0];
    $curpagewithquery = '/' . trim(str_replace(CP_PATH,'',$_SERVER['REQUEST_URI']),' /');

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
    <aside class="main-sidebar" style="height: 100vh;overflow:hidden;overflow-y: scroll">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar" style="height: 100vh;">
            <!-- Sidebar user panel -->
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu">
                <li class="header"><?= HM_MAINNAVIGATION ?></li>
    
                <?php
                    $perms = $db->query("SELECT permid FROM _users_perms WHERE user_id = " . $_SESSION['userinfo']['user_id'])->fetchAll(PDO::FETCH_COLUMN);
                
                ?>

                <li <?php if ($curpage === '/main/index') echo 'class="active" id="active"'; ?>>
                    <a href="<?= CP_PATH ?>/main/index">
                        <i class="fa fa-dashboard fa-fw"></i> <span><?= HM_Dashboard ?></span>
                    </a>
                </li>

                <li <?php if ($curpage === '/main/soothing_summary') echo 'class="active" id="active"'; ?>>
                    <a href="<?= CP_PATH ?>/main/soothing_summary">
                        <i class="fa fa-dashboard fa-fw"></i>
                        <span><?= HM_soothing_summary ?> ( <?= LBL_MENA ?> ) </span>
                    </a>
                </li>
                <li <?php if ($curpage === '/main/soothing_summary_arafa') echo 'class="active" id="active"'; ?>>
                    <a href="<?= CP_PATH ?>/main/soothing_summary_arafa">
                        <i class="fa fa-dashboard fa-fw"></i> <span><?= HM_soothing_summary ?> ( <?= arafa ?> )</span>
                    </a>
                </li>
                <li <?php if ($curpage === '/main/bus_accommodation_summary') echo 'class="active" id="active"'; ?>>
                    <a href="<?= CP_PATH ?>/main/bus_accommodation_summary">
                        <i class="fa fa-dashboard fa-fw"></i> <span><?= HM_bus_accommodation_summary ?></span>
                    </a>
                </li>

                
                <li class="header"><?= HM_BASICINFORMATION ?></li>
                
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(2, $perms, true))) { ?>
                    <li <?php if ($curpage === '/basic_info/ourlocations') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/basic_info/ourlocations">
                            <i <?php if ($curpage === '/basic_info/ourlocations') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_OurLocations ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(3, $perms, true))) { ?>
                    <li <?php if ($curpage === '/basic_info/ourcompanies') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/basic_info/ourcompanies">
                            <i <?php if ($curpage === '/basic_info/ourcompanies') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_OurCompanies ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(4, $perms, true))) { ?>

                    <li <?php if ($curpage === '/basic_info/promos') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/basic_info/promos">
                            <i <?php if ($curpage === '/basic_info/promos') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_Promos ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(5, $perms, true))) { ?>

                    <li <?php if ($curpage === '/basic_info/cities') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/basic_info/cities">
                            <i <?php if ($curpage === '/basic_info/cities') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_Cities ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(6, $perms, true))) { ?>
    
                    <?php
                    
                    $menu_pages = array('/basic_info/suites', '/basic_info/suite', '/basic_info/halls', '/basic_info/hall');
                    
                    ?>

                    <li class="treeview <?php if (in_array($curpage, $menu_pages, true)) echo 'active'; ?>">
                        <a href="#">
                            <i class="fa fa-fw fa-list"></i> <span><?= HM_Suites ?></span> <i
                                    class="fa fa-angle-<?= DIR_AFTER ?> pull-<?= DIR_AFTER ?>"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li <?php if ($curpage === '/basic_info/suite') echo 'class="active" id="active"'; ?>><a
                                        href="<?= CP_PATH ?>/basic_info/edit/suite"><i <?php if ($curpage === '/basic_info/suite') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_AddNew ?>
                                </a></li>
                            <li <?php if ($curpage === '/basic_info/suites') echo 'class="active" id="active"'; ?>><a
                                        href="<?= CP_PATH ?>/basic_info/suites"><i <?php if ($curpage === '/basic_info/suites') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_ListAll ?>
                                </a></li>
                            <li <?php if ($curpage === '/basic_info/halls') echo 'class="active" id="active"'; ?>><a
                                        href="<?= CP_PATH ?>/basic_info/halls"><i <?php if ($curpage === '/basic_info/halls') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_Halls ?>
                                </a></li>
                        </ul>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(7, $perms, true))) { ?>
    
                    <?php
                    
                    $menu_pages = array('/basic_info/buildings', '/basic_info/building', '/basic_info/floors', '/basic_info/floor', '/basic_info/rooms', '/basic_info/room');
                    
                    ?>

                    <li class="treeview <?php if (in_array($curpage, $menu_pages, true)) echo 'active'; ?>">
                        <a href="#">
                            <i class="fa fa-fw fa-list"></i>
                            <span><?= HM_Buildings ?> / <?= LBL_PremisesPlus ?></span> <i
                                    class="fa fa-angle-<?= DIR_AFTER ?> pull-<?= DIR_AFTER ?>"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li <?php if ($curpage === '/basic_info/building') echo 'class="active" id="active"'; ?>><a
                                        href="<?= CP_PATH ?>/basic_info/edit/building"><i <?php if ($curpage === '/basic_info/building') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_AddNew ?>
                                </a></li>
                            <li <?php if ($curpage === '/basic_info/buildings') echo 'class="active" id="active"'; ?>><a
                                        href="<?= CP_PATH ?>/basic_info/buildings"><i <?php if ($curpage === '/basic_info/buildings') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_ListAll ?>
                                </a></li>
                            <li <?php if ($curpage === '/basic_info/floors') echo 'class="active" id="active"'; ?>><a
                                        href="<?= CP_PATH ?>/basic_info/floors"><i <?php if ($curpage === '/basic_info/floors') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_Floors ?>
                                </a></li>
                            <li <?php if ($curpage === '/basic_info/rooms') echo 'class="active" id="active"'; ?>><a
                                        href="<?= CP_PATH ?>/basic_info/rooms"><i <?php if ($curpage === '/basic_info/rooms') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_Rooms ?>
                                </a></li>
                        </ul>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(8, $perms, true))) { ?>

                    <li <?php if ($curpage === '/basic_info/tents') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/basic_info/tents?type=1">
                            <i <?php if ($curpage === '/basic_info/tents') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i>
                            <span> <?= HM_Tents ?> ( <?= mozdalifa ?> )</span>
                        </a>
                    </li>
                <?php } ?>
                
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(8, $perms, true))) { ?>

                    <li <?php if ($curpage === '/basic_info/tents') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/basic_info/tents?type=2">
                            <i <?php if ($curpage === '/basic_info/tents') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_Halls ?> ( <?= arafa ?> )</span>
                        </a>
                    </li>
                <?php } ?>
                
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(9, $perms, true))) { ?>

                    <li <?php if ($curpage === '/basic_info/buses') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/basic_info/buses">
                            <i <?php if ($curpage === '/basic_info/buses') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_PilgrimsBuses ?></span>
                        </a>
                    </li>
                <?php } ?>
                
                <li class="header"><?= HM_STAFF ?></li>
                
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(10, $perms, true))) { ?>
                    
                    <li <?php if ($curpagewithquery === '/staff/index?type=1') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/staff?type=1">
                            <i <?php if ($curpagewithquery === '/staff/index?type=1') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_Managers ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(11, $perms, true))) { ?>

                    <li <?php if ($curpagewithquery === '/staff/index?type=2') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/staff?type=2">
                            <i <?php if ($curpagewithquery === '/staff/index?type=2') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_Supervisors ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(12, $perms, true))) { ?>

                    <li <?php if ($curpagewithquery === '/staff/index?type=3') echo 'class="active" id="active"'; ?>>
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

                    <li class="treeview <?php if (in_array($curpage, $menu_pages, true)) echo 'active'; ?>">
                        <a href="#">
                            <i class="fa fa-fw fa-list"></i> <span><?= HM_Pilgrims ?></span> <i
                                    class="fa fa-angle-<?= DIR_AFTER ?> pull-<?= DIR_AFTER ?>"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li <?php if ($curpage === '/pilgrims/edit/pilgrim') echo 'class="active" id="active"'; ?>><a
                                        href="<?= CP_PATH ?>/pilgrims/edit/pilgrim"><i <?php if ($curpage === '/pilgrims/edit/pilgrim') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_AddNew ?>
                                </a></li>
                            <li <?php if ($curpage === '/pilgrims/index') echo 'class="active" id="active"'; ?>><a
                                        href="<?= CP_PATH ?>/pilgrims"><i <?php if ($url === '/pilgrims/index') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_ListAll ?>
                                </a></li>
                            <li <?php if ($curpage === '/pilgrims/actions/export') echo 'class="active" id="active"'; ?>><a
                                        href="<?= CP_PATH ?>/pilgrims/actions/export"><i <?php if ($curpage === '/pilgrims/actions/export') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= LBL_exportfilepils ?>
                                </a></li>

                            <li <?php if ($curpage === '/pilgrims/classes') echo 'class="active" id="active"'; ?>><a
                                        href="<?= CP_PATH ?>/pilgrims/classes"><i <?php if ($url === '/pilgrims/classes') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_ManageClasses ?>
                                </a></li>
                        </ul>
                    </li>
                <?php } ?>
                <!-- <span class="label label-warning pull-right">27</span> -->
                <li class="header"><?= HM_ACCOMODATIONS ?></li>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(32, $perms, true))) { ?>
                    <li <?php if ($curpage === '/accomo/auto_accomo') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/accomo/auto_accomo">
                            <i <?php if ($curpage === '/accomo/auto_accomo') echo 'class="fa fa-fw fa-star text-white"'; else echo 'class="fa fa-fw fa-star"'; ?>></i>
                            <span><?= HM_AutoAccomo ?> ( <?= LBL_MENA ?> ) </span>
                        </a>
                    </li>

                    <li <?php if ($curpage === '/accomo/auto_accomo_arafa') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/accomo/auto_accomo_arafa">
                            <i <?php if ($curpage === '/accomo/auto_accomo_arafa') echo 'class="fa fa-fw fa-star text-white"'; else echo 'class="fa fa-fw fa-star"'; ?>></i>
                            <span><?= HM_AutoAccomo ?> ( <?= arafa ?> ) </span>
                        </a>
                    </li>

                    <li <?php if ($curpage === '/accomo/auto_accomo_buses') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/accomo/auto_accomo_buses">
                            <i <?php if ($curpage === '/accomo/auto_accomo_buses') echo 'class="fa fa-fw fa-star text-white"'; else echo 'class="fa fa-fw fa-star"'; ?>></i>
                            <span><?= HM_AutoAccomoBuses ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php
                    
                    $menu_pages = array('/accomo/accomo_suites', '/accomo/accomo_buildings', '/accomo/accomo_tents', '/accomo/accomo_tents_halls', '/accomo/accomo_buses', '/accomo/bulkaccomosms', '/accomo/bulkbussms');
                
                ?>

                <li class="treeview <?php if (in_array($curpage, $menu_pages, true)) echo 'active'; ?>">
                    <a href="#">
                        <i class="fa fa-fw fa-list"></i>
                        <span><?= HM_ViewAccomodations ?> ( <?= HM_export_files ?> ) </span> <i
                                class="fa fa-angle-<?= DIR_AFTER ?> pull-<?= DIR_AFTER ?>"></i>
                    </a>
                    <ul class="treeview-menu">
                        <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(14, $perms, true))) { ?>

                            <li <?php if ($curpage === '/accomo/accomo_suites') echo 'class="active" id="active"'; ?>><a
                                        href="<?= CP_PATH ?>/accomo/accomo_suites"><i <?php if ($curpage === '/accomo/accomo_suites') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_SuitesAccomodations ?>
                                </a></li>
                        
                        <?php } ?>
                        <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(15, $perms, true))) { ?>


                            <li <?php if ($curpage === '/accomo/accomo_buildings') echo 'class="active" id="active"'; ?>><a
                                        href="<?= CP_PATH ?>/accomo/accomo_buildings"><i <?php if ($curpage === '/accomo/accomo_buildings') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_BuildingsAccomodations ?>
                                </a></li>
                        
                        <?php } ?>
                        <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(16, $perms, true))) { ?>

                            <li <?php if ($curpage === '/accomo/accomo_tents') echo 'class="active" id="active"'; ?>><a
                                        href="<?= CP_PATH ?>/accomo/accomo_tents"><i <?php if ($curpage === '/accomo/accomo_tents') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_TentsAccomodations ?>
                                    ( <?= mozdalifa ?> ) </a></li>
                        
                        <?php } ?>
                        <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(16, $perms, true))) { ?>

                            <li <?php if ($curpage === '/accomo/accomo_tents_halls') echo 'class="active" id="active"'; ?>><a
                                        href="<?= CP_PATH ?>/accomo/accomo_tents_halls"><i <?php if ($curpage === '/accomo/accomo_tents_halls') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_HallsAccomodations ?>
                                </a></li>
                        
                        <?php } ?>
                        <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(17, $perms, true))) { ?>

                            <li <?php if ($curpage === '/accomo/accomo_buses') echo 'class="active" id="active"'; ?>><a
                                        href="<?= CP_PATH ?>/accomo/accomo_buses"><i <?php if ($curpage === '/accomo/accomo_buses') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_BusesAccomodations ?>
                                </a></li>
                        <?php } ?>
                        <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(37, $perms, true))) { ?>

                            <li <?php if ($curpage === '/accomo/bulkaccomosms') echo 'class="active" id="active"'; ?>><a
                                        href="<?= CP_PATH ?>/accomo/bulkaccomosms"><i <?php if ($curpage === '/accomo/bulkaccomosms') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_bulkaccomosms ?>
                                </a></li>
                        <?php } ?>
                        <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(38, $perms, true))) { ?>

                            <li <?php if ($curpage === '/accomo/bulkbussms') echo 'class="active" id="active"'; ?>><a
                                        href="<?= CP_PATH ?>/accomo/bulkbussms"><i <?php if ($curpage === '/accomo/bulkbussms') echo 'class="fa fa-fw fa-circle text-white"'; else echo 'class="fa fa-fw fa-circle-o"'; ?>></i> <?= HM_bulkbussms ?>
                                </a></li>
                        <?php } ?>
                    </ul>
                </li>

                <li class="header"><?= HM_PUSHNOTIFICATIONS ?></li>
                
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(18, $perms, true))) { ?>

                    <li <?php if ($curpage === '/notifications/push_new') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/notifications/push_new">
                            <i <?php if ($curpage === '/notifications/push_new') echo 'class="fa fa-fw fa-star text-white"'; else echo 'class="fa fa-fw fa-star"'; ?>></i>
                            <span><?= HM_SendNewPushNotification ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(19, $perms, true))) { ?>

                    <li <?php if ($curpage === '/notifications/push_list') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/notifications/push_list">
                            <i <?php if ($curpage === '/notifications/push_list') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>></i>
                            <span><?= HM_NotificationsHistory ?></span>
                        </a>
                    </li>
                <?php } ?>

                <li class="header"><?= HM_GENERALGUIDE ?></li>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(31, $perms, true))) { ?>

                    <li <?php if ($curpage === '/guide/general_guide') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/guide/general_guide">
                            <i <?php if ($curpage === '/guide/general_guide') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_GENERALGUIDE ?></span>
                        </a>
                    </li>
                <?php } ?>
                
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(31, $perms, true))) { ?>

                    <li <?php if ($curpage === '/guide/general_guide_staff') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/guide/general_guide_staff">
                            <i <?php if ($curpage === '/guide/general_guide_staff') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i>
                            <span> <?= HM_GENERALGUIDESTAFF ?></span>
                        </a>
                    </li>
                <?php } ?>


                <li class="header"><?= HM_GENERAL ?></li>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(20, $perms, true))) { ?>

                    <li <?php if ($curpage === '/general/news') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/general/news">
                            <i <?php if ($curpage === '/general/news') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_News ?></span>
                        </a>
                    </li>
                <?php } ?>
                
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(20, $perms, true))) { ?>

                    <li <?php if ($curpage === '/general/competitions') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/general/competitions">
                            <i <?php if ($curpage === '/general/competitions') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_competitions ?></span>
                        </a>
                    </li>
                <?php } ?>
                
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(20, $perms, true))) { ?>

                    <li <?php if ($curpage === '/general/competitions-result') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/general/competitions-result">
                            <i <?php if ($curpage === '/general/competitions-result') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i>
                            <span> <?= HM_competitions_results ?></span>
                        </a>
                    </li>
                <?php } ?>
                
                
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(20, $perms, true))) { ?>

                    <li <?php if ($curpage === '/general/new_questions') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/general/new_questions">
                            <i <?php if ($curpage === '/general/new_questions') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_question ?></span>
                        </a>
                    </li>
                <?php } ?>
                
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(20, $perms, true))) { ?>

                    <li <?php if ($curpage === '/general/answers') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/general/answers">
                            <i <?php if ($curpage === '/general/answers') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_answer ?></span>
                        </a>
                    </li>
                <?php } ?>
                
                
                
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(21, $perms, true))) { ?>

                    <li <?php if ($curpage === '/general/aboutus') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/general/aboutus">
                            <i <?php if ($curpage === '/general/aboutus') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_Aboutus ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(22, $perms, true))) { ?>

                    <li <?php if ($curpage === '/general/contactinfo') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/general/contactinfo">
                            <i <?php if ($curpage === '/general/contactinfo') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_ContactInfo ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(23, $perms, true))) { ?>

                    <li <?php if ($curpage === '/general/social') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/general/social">
                            <i <?php if ($curpage === '/general/social') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_Social ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(24, $perms, true))) { ?>

                    <li <?php if ($curpage === '/general/faq') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/general/faq">
                            <i <?php if ($curpage === '/general/faq') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_FAQ ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(25, $perms, true))) { ?>

                    <li <?php if ($curpage === '/general/haj_album') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/general/haj_album">
                            <i <?php if ($curpage === '/general/haj_album') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_HajAlbum ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(26, $perms, true))) { ?>

                    <li <?php if ($curpage === '/general/haj_guide_cats') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/general/haj_guide_cats">
                            <i <?php if ($curpage === '/general/haj_guide_cats') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_HajGuideCats ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(27, $perms, true))) { ?>

                    <li <?php if ($curpage === '/general/haj_guide_articles') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/general/haj_guide_articles">
                            <i <?php if ($curpage === '/general/haj_guide_articles') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i>
                            <span> <?= HM_HajGuideArticles ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(28, $perms, true))) { ?>

                    <li <?php if ($curpage === '/general/feedback') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/general/feedback">
                            <i <?php if ($curpage === '/general/feedback') echo 'class="fa fa-fw fa-list text-white"'; else echo 'class="fa fa-fw fa-list"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span> <?= HM_Feedback ?></span>

                            <?php
                                $c = $db->query('SELECT * FROM feedback WHERE is_read=0')->rowCount();
                                if ($c>0) {
                            ?>
                            <span class="badge rounded-pill bg-danger" style="position: absolute;top: 0; background-color: red">
                                <?= $c ?>
                                <span class="sr-only">unread messages</span>
                            </span>
                            <?php } ?>
                        </a>
                    </li>
                <?php } ?>


                <li class="header"><?= HM_SYSTEM ?></li>
                
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(29, $perms, true))) { ?>

                    <li <?php if ($curpage === '/system/settings') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/system/settings">
                            <i <?php if ($curpage === '/system/settings') echo 'class="fa fa-fw fa-cog text-white"'; else echo 'class="fa fa-fw fa-cog"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span><?= HM_Settings ?></span>
                        </a>
                    </li>
                <?php } ?>
                
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(33, $perms, true))) { ?>

                    <li <?php if ($curpage === '/system/employees') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/system/employees">
                            <i <?php if ($curpage === '/system/employees') echo 'class="fa fa-fw fa-cog text-white"'; else echo 'class="fa fa-fw fa-cog"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span><?= HM_Employees ?></span>
                        </a>
                    </li>
                <?php } ?>
                
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(35, $perms, true))) { ?>

                    <li <?php if ($curpage === '/system/update_pils_photos') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/system/update_pils_photos">
                            <i <?php if ($curpage === '/system/update_pils_photos') echo 'class="fa fa-fw fa-cog text-white"'; else echo 'class="fa fa-fw fa-cog"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i>
                            <span><?= HM_UpdatePilsPhotos ?></span>
                        </a>
                    </li>
                <?php } ?>
                
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(36, $perms, true))) { ?>

                    <li <?php if ($curpage === '/system/update_emps_photos') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/system/update_emps_photos">
                            <i <?php if ($curpage === '/system/update_emps_photos') echo 'class="fa fa-fw fa-cog text-white"'; else echo 'class="fa fa-fw fa-cog"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i>
                            <span><?= HM_UpdateEmpsPhotos ?></span>
                        </a>
                    </li>
                <?php } ?>
                
                <?php if ($_SESSION['userinfo']['userlevel'] === 9 || (in_array(34, $perms, true))) { ?>

                    <li <?php if ($curpage === '/system/designs_managers') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/system/designs_managers">
                            <i <?php if ($curpage === '/system/designs_managers') echo 'class="fa fa-fw fa-cog text-white"'; else echo 'class="fa fa-fw fa-cog"'; ?>
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

                    <li <?php if ($curpage === '/system/users_all') echo 'class="active" id="active"'; ?>>
                        <a href="<?= CP_PATH ?>/system/users_all">
                            <i <?php if ($curpage === '/system/users_all') echo 'class="fa fa-fw fa-users text-white"'; else echo 'class="fa fa-fw fa-users"'; ?>
                                    style="padding-<?= DIR_AFTER ?>: 7px;"></i> <span><?= HM_SystemUsers ?></span>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>
