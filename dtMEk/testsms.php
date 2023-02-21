<?php

require 'msegat.php';

$sms = new Sms('alolayany', 'asdzxc987', 'alolayany');

$teles = array("966555005344");

$message = "محتوى عربي";

//$timeToSend = "2022-01-18 15:07:27";

$result = $sms->send($teles, $message, 0);

print_r($result);

echo 'done';
