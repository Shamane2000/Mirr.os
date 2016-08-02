<?php
$modules_content = scandir('versions');

foreach($modules_content as $file) {
	if($file != '.' && $file != '..') {
		$versionFile = file('versions/' . $file);
		$versions[$file] = $versionFile[0];
	}
}

echo json_encode($versions);
?>