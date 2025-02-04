<?php
    require './config/constants.php';
    
    $url = explode('?',str_replace(CP_PATH,'',$_SERVER['REQUEST_URI']))[0];
    $root_absolute_path = str_replace(trim(CP_PATH,'/'),'',__DIR__);
    
    $includes = [
        'post' => [
            'config/config.inc.php',
            'config/db.php',
            'config/init.php',
            'config/functions.php',
            'config/msegat.php'
        ],
        'export' => [
            'config/config.inc.php',
            'config/db.php',
            '_includes/XLSClasses/PHPExcel/IOFactory.php',
            '_includes/phpqrcode/qrlib.php',
            'config/msegat.php',
            'config/init.php'
        ],
        'cards' => [
            'config/init.php',
            'config/config.inc.php',
            'config/db.php',
        ],
        'login' => [
            'config/config.inc.php',
            'config/db.php',
        ]
    ];
    
    if ($url === '' || $url === '/')
        header('Location: '.CP_PATH.'/main/index?'.http_build_query($_GET));
    
    elseif (file_exists('parts/'.trim($url,'/').'.php')) {
        
        foreach ($includes as $include => $files) {
            if (strpos($url,$include) > -1) {
                foreach ($files as $file)
                    require_once $file;
        
                include_once 'parts/' . trim($url, '/') . '.php';
                exit();
            }
        }
    
        require_once 'layout/header.php';
    
        if (file_exists('install.php')) {
            header("Location: install.php");
            exit();
        }
        include_once 'parts/' . trim($url, '/') . '.php';
    
        include 'layout/footer.php';
        exit();
        
    } elseif (file_exists('parts/'.trim($url,'/').'/index.php')) {
        header('Location: '.CP_PATH.'/' . trim($url, '/') . '/index?'.http_build_query($_GET));
        exit();
    }
    else http_response_code(404);
