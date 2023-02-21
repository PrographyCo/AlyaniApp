<?php
    require '../db.php';

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $sqltrunc = $db->query("TRUNCATE TABLE _ip2country");
    if (($handle = fopen("IPs.CSV", 'rb')) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

            //echo "ipfrom: " . $data[0] . " - ipto: " . $data[1] . " - CO: " . $data['2'] . " - Country: " . $data['3'] . "<br />";


            $sql = $db->prepare("INSERT IGNORE INTO _ip2country VALUES (
            :ipfrom,
            :ipto,
            :countrycode,
            :country
            )");
            $sql->bindValue("ipfrom", $data[0]);
            $sql->bindValue("ipto", $data[1]);
            $sql->bindValue("countrycode", $data[2]);
            $sql->bindValue("country", $data[3]);
            $sql->execute();
        }
        fclose($handle);
    }

    echo 'Done';
