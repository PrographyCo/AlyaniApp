<?php

    include_once '../dtMEk/config/constants.php';
    include_once '../dtMEk/config/config.inc.php';
    include_once '../dtMEk/config/db.php';
    
    global $db;
    
//    $db->query('ALTER TABLE feedback DROP status');
//    $db->query('ALTER TABLE feedback ADD is_read tinyint(1) DEFAULT 0 AFTER feedb_message');
//    $db->query('ALTER TABLE feedback ADD replied tinyint(1) DEFAULT 0 AFTER is_read');
//    $db->query('ALTER TABLE feedback ADD reply_subject text AFTER replied');
//    $db->query('ALTER TABLE feedback ADD reply_body text AFTER reply_subject');
//
//    $db->query('ALTER TABLE pils_accomo ADD type enum("pil","emp") DEFAULT "pil" AFTER pil_code');
//
//    $db->query('CREATE TABLE suites_halls_stuff(
//        stuff_id INT AUTO_INCREMENT PRIMARY KEY,
//        hall_id INT NOT NULL REFERENCES suites_halls(hall_id),
//        stuff_title VARCHAR(20) NOT NULL,
//        stuff_type ENUM("bed","chair","bench") NOT NULL,
//        stuff_order INT NOT NULL DEFAULT 0,
//        stuff_active TINYINT(1) NOT NULL DEFAULT 0,
//        stuff_dateadded datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
//        stuff_lastupdated datetime ON UPDATE CURRENT_TIMESTAMP
//    )');

//    ALTER TABLE `pils_accomo` ADD `stuff_id` INT NOT NULL AFTER `hall_id`;
