<?php
include('glancrConfig.php');
$modules_enabled = file('/var/www/html/config/modules_enabled');
$modules_enabled[$_POST['row']] = implode("\t", $_POST['modules']) . "\n";
$fp = fopen('/var/www/html/config/modules_enabled', 'w');
fwrite($fp, implode("", $modules_enabled));

setConfigValue('reload', "1");
?>