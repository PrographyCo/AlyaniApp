<?php
    $url = explode('?',$_SERVER['REQUEST_URI'])[0];
    
    if ($url === '' || $url === '/')
        header('Location: /main/index?'.http_build_query($_GET));
    
    elseif (file_exists('parts/'.trim($url,'/').'.php')) {
        if (strpos($url,'post') > -1) {
    
            require_once 'config/config.inc.php';
            require_once 'config/db.php';
            require_once 'config/init.php';
            require_once 'config/functions.php';
            require 'config/msegat.php';
    
            include_once 'parts/' . trim($url, '/') . '.php';
            exit();
        } elseif (strpos($url,'export') > -1) {
            
            require_once 'config/config.inc.php';
            require 'config/db.php';
            include '_includes/XLSClasses/PHPExcel/IOFactory.php';
            include'_includes/phpqrcode/qrlib.php';
            require 'config/msegat.php';
            require_once 'config/init.php';
            
    
            include_once 'parts/' . trim($url, '/') . '.php';
            exit();
        }else {
            require_once 'layout/header.php';
    
            if (file_exists('install.php')) {
                header("Location: install.php");
                exit();
            }
            include_once 'parts/' . trim($url, '/') . '.php';
    
            include 'layout/footer.php';
            exit();
        }
    } elseif (file_exists('parts/'.trim($url,'/').'/index.php')) {
        header('Location: /' . trim($url, '/') . '/index?'.http_build_query($_GET));
        exit();
    }
    else http_response_code(404);
