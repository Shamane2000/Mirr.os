<?php
include('glancrConfig.php');

$reload = getConfigValue('reload');
echo $reload;
if($reload) {
	setConfigValue('reload', '0');
}

?>