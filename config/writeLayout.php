<?php
include('glancrConfig.php');
$modules_enabled = file(GLANCR_ROOT .'/config/modules_enabled');
$modules_enabled[$_POST['row']] = implode("\t", $_POST['modules']) . "\n";
$fp = fopen(GLANCR_ROOT .'/config/modules_enabled', 'w');
fwrite($fp, implode("", $modules_enabled));

setConfigValue('reload', "1");
?>