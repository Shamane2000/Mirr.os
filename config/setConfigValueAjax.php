<?php
if(!array_key_exists('key', $_POST) || !array_key_exists('value', $_POST)) {
	header("Location: /404/index.php");
	exit;
}

include('glancrConfig.php');

setConfigValue($_POST['key'], $_POST['value']);
setConfigValue('reload', '1');

exit;
?>
