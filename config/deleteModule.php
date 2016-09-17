<?php
include('glancrConfig.php');

set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

function deleteDir($dirPath)  {
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

try {
    deleteDir($basedir .'/modules/' . $_POST['module']);
} catch (Exception $e) {
    http_response_code(500);
    echo $e->getMessage();
}


if($_POST['action'] == 'delete') {
	$modulesActive = file($basedir .'/config/modules_enabled');
	foreach ($modulesActive as $nr => $row) {
		$modulesActive[$nr] = str_replace($_POST['module'], '', $row);
	}
	$fp = fopen($basedir .'/config/modules_enabled', 'w');
	fwrite($fp, implode("", $modulesActive));
	
	setConfigValue('reload', '1');
}



?>
