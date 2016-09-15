<?php
include('glancrConfig.php');

function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
        exit("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}

deleteDir('/var/www/html/modules/' . $_POST['module']);

if($_POST['action'] == 'delete') {
	$modulesActive = file('/var/www/html/config/modules_enabled');
	foreach ($modulesActive as $nr => $row) {
		$modulesActive[$nr] = str_replace($_POST['module'], '', $row);
	}
	$fp = fopen('/var/www/html/config/modules_enabled', 'w');
	fwrite($fp, implode("", $modulesActive));
	
	setConfigValue('reload', '1');
}



?>
