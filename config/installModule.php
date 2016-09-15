<?php
include('glancrConfig.php');

$orderZip = file('http://localhost/server/zipModule.php?module=' . $_POST['name']);

$file = 'http://localhost/server/moduleZips/' . $_POST['name'] . '-' . $_POST['version'] . '.zip';

$newFile = 'tmp_file.zip';

if (!copy($file, $newFile)) {
	echo "failed to copy $file...\n";
}

$zip = new ZipArchive();
if ($zip->open($newFile, ZIPARCHIVE::CREATE)) {
	$zip->extractTo($basedir .'/modules/');
	
	$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basedir .'/modules/' . $_POST['name']));
	
	foreach($iterator as $item) {
		chmod($item, 0777);
	}

	$zip->close();
	echo 'ok';
} else {
	echo 'Fehler';
	
}

unlink($newFile);
setConfigValue('reload', '1');
?>