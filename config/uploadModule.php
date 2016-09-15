<?php
include('glancrConfig.php');

if(!array_key_exists('moduleZip',$_FILES)) {
	exit('ERROR: no zip file given');	
}
if(substr($_FILES['moduleZip']['name'], -4) != '.zip') {
	exit(' ERROR: given file is not zip file');	
}

$za = new ZipArchive();

if($za->open($_FILES['moduleZip']["tmp_name"])) {
	$za->extractTo('/var/www/html/modules/');
	
	$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('/var/www/html/modules/' . explode('/', $za->statIndex(0)['name'])[0]));
	
	foreach($iterator as $item) {
		chmod($item, 0777);
	}

	$za->close();
	echo 'ok';
} else {
	echo 'Fehler';
	
}
setConfigValue('reload', '1');
?>