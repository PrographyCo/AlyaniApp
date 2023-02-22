<?php
    $lang = 'ar';
    if (isset($_GET['lang']) && in_array($lang=strtolower($_GET['lang']),['en','ar'])){
        setcookie('lang', $lang);
        $_COOKIE['lang'] = $lang;
    }
    
    if (!isset($_COOKIE['lang'])) {
        
        setcookie('lang', $lang);
        $_COOKIE['lang'] = $lang;
        
    } elseif (in_array($_COOKIE['lang'],['en','ar'])) $lang = $_COOKIE['lang'];
    
    
    include 'lang/'.strtolower($lang).'.php';
    $css1 = ($lang==='ar')?'AdminLTE_ar.css?v=1.0':'AdminLTE.css';
    $css2 = ($lang==='ar')?'bootstrap-rtl.min.css':'';
    $css3 = ($lang==='ar')?'rtl.css?v=1.0':'';
    $css4 = ($lang==='ar')?'_all-skins_ar.css':'_all-skins.css';
    $js_file = ($lang==='ar')?'app_ar.min.js':'app.js';
