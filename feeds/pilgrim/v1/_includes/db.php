<?php
    $dbhost = "localhost"; // Host Name
    $dbport = "3306"; //Port
    
    $dbuser = "root"; // iqbay487_db3 // MySQL Database Username
    $dbpass = ""; //;QQiJrEjHDpz // MySQL Database Password
    $dbname = "iqbay487_db3"; // Database Name


//	$dbuser = "alyaniap_user"; // MySQL Database Username
    //	$dbpass = "u1tY~c1.G65A"; // MySQL Database Password
    //	$dbname = "alyaniap_db"; // Database Name
    $db = new PDO("mysql:dbname={$dbname};host={$dbhost};port={$dbport};charset=utf8", $dbuser, $dbpass);
    $db->exec("set names utf8");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
