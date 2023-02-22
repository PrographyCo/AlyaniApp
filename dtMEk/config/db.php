<?php
    
    $dbhost = Config::$dbhost; // Host Name
    $dbport = Config::$dbport; //Port
    
    $dbuser = Config::$dbuser; // MySQL Database Username
    $dbpass = Config::$dbpass; // MySQL Database Password
    $dbname = Config::$dbname; // Database Name
    
    $db = new PDO("mysql:dbname={$dbname};host={$dbhost};port={$dbport};charset=utf8", $dbuser, $dbpass);
    $db->exec("set names utf8");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
