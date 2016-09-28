<?php
include('glancrConfig.php');

$orderZip = file_get_contents(getApiBaseUrl() . '/update/zipModule.php?module=' . $_POST['name']);
$file = getApiBaseUrl() . '/update/moduleZips/' . $_POST['name'] . '-' . $_POST['version'] . '.zip';
$tmpFile = GLANCR_ROOT . '/tmp/tmp_file.zip';

if (!copy($file, $tmpFile)) {
    http_response_code(500);
    exit("failed to create $tmpFile from $file...\n");
}

$zip = new ZipArchive();
if ($zip->open($tmpFile, ZIPARCHIVE::CREATE)) {
	$zip->extractTo(GLANCR_ROOT .'/modules/');

    //@TODO This can be removed; PHP correctly sets 0755 for directories and 0644 for files
//	$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basedir .'/modules/' . $_FILES['moduleZip']['name']));
//
//	foreach($iterator as $item) {
//		chmod($item, 0777);
//	}

	$zip->close();
	http_response_code(200);
} else {
    http_response_code(500);
}

unlink($tmpFile);
setConfigValue('reload', '1');
