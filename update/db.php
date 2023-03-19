<?php

    include_once '../dtMEk/config/constants.php';
    include_once '../dtMEk/config/config.inc.php';
    include_once '../dtMEk/config/db.php';
    
    global $db;
    
    $db->query('ALTER TABLE feedback DROP status');
    $db->query('ALTER TABLE feedback ADD is_read tinyint(1) DEFAULT 0 AFTER feedb_message');
    $db->query('ALTER TABLE feedback ADD replied tinyint(1) DEFAULT 0 AFTER is_read');
    $db->query('ALTER TABLE feedback ADD reply_subject text AFTER replied');
    $db->query('ALTER TABLE feedback ADD reply_body text AFTER reply_subject');

    $db->query('ALTER TABLE pils_accomo ADD type enum("pil","emp") DEFAULT "pil" AFTER pil_code');
