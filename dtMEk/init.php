<?php
    
    if ($_GET['lang'] === 'en') {
        
        setcookie('lang', 'en');
        $_COOKIE['lang'] = 'en';
        $lang = 'en';
        
    } elseif ($_GET['lang'] === 'ar') {
        
        setcookie('lang', 'ar');
        $_COOKIE['lang'] = 'ar';
        $lang = 'ar';
        
    }
    
    if (!isset($_COOKIE['lang'])) {
        
        setcookie('lang', 'ar');
        $_COOKIE['lang'] = 'ar';
        $lang = 'ar';
        
    } elseif ($_COOKIE['lang'] == 'en' || $_COOKIE['lang'] == 'ar') $lang = $_COOKIE['lang'];
    else $lang = 'ar';
    
    if ($lang == 'ar') {
        
        include 'lang/ar.php';
        $css1 = 'AdminLTE_ar.css?v=1.0';
        $css2 = 'bootstrap-rtl.min.css';
        $css3 = 'rtl.css?v=1.0';
        $css4 = '_all-skins_ar.css';
        $js_file = 'app_ar.min.js';
        
    } else {
        
        include 'lang/en.php';
        $css1 = 'AdminLTE.css';
        $css2 = '';
        $css3 = '';
        $css4 = '_all-skins.css';
        $js_file = 'app.js';
        
    }

?>
