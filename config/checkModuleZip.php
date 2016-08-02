<?php
if(!array_key_exists('moduleZip',$_FILES)) {
	exit('ERROR: no zip file given');	
}
if(substr($_FILES['moduleZip']['name'], -4) != '.zip') {
	exit(' ERROR: given file is not zip file');	
}

$za = new ZipArchive();

$za->open($_FILES['moduleZip']["tmp_name"]);

for ($i = 0; $i < $za->numFiles; $i++) {
	$fullFilePath = $za->statIndex($i)['name']; 
	$pathParts = explode('/', $fullFilePath);
	if($i == 0) {
		$moduleName = $pathParts[0];
	} else {
		if($moduleName != $pathParts[0]) {
			exit('ERROR: multiple base folders');
			
		}
	}
	if(array_key_exists(1, $pathParts)) {
		if($pathParts[1] == 'frontend') {
			if(array_key_exists(2, $pathParts)) {
				if($pathParts[2] == 'template.php') {
					$frontendTemplateExists = true;
				} else if($pathParts[2] == 'styles.css') {
					$frontendStyleExists = true;
				} else if($pathParts[2] == 'script.js') {
					$frontendScriptExists = true;
				}
			}
		} else if($pathParts[1] == 'backend') {
			if(array_key_exists(2, $pathParts)) {
				if($pathParts[2] == 'template.php') {
					$backendTemplateExists = true;
				} else if($pathParts[2] == 'styles.css') {
					$backendStyleExists = true;
				} else if($pathParts[2] == 'script.js') {
					$backendScriptExists = true;
				}
			}
		} else if($pathParts[1] == 'locales') {
		} else if($pathParts[1] == 'version.txt') {
			$fp = $za->getStream($fullFilePath);
			if(!$fp) exit("Fehler\n");

			while (!feof($fp)) {
				$versionFileValid = true;
				$version .= fread($fp, 2);
			}
			

		}
	}	
}

if($frontendTemplateExists && $frontendStyleExists && $frontendScriptExists && $backendTemplateExists && $backendStyleExists && $backendScriptExists && $versionFileValid) {
	echo $moduleName . ": " . $version;
} else {
	if(!$frontendTemplateExists) {
		$error[] = 'no frontend template in zip file';
	} 
	if(!$frontendScriptExists) {
		$error[] = 'no frontend script in zip file';
	} 
	if(!$frontendScriptExists) {
		$error[] = 'no frontend stylesheet in zip file';
	} 
	if(!$backendTemplateExists) {
		$error[] = 'no backend template in zip file';
	} 
	if(!$backendScriptExists) {
		$error[] = 'no backend script in zip file';
	} 
	if(!$backendScriptExists) {
		$error[] = 'no backend stylesheet in zip file';
	} 
	if(!$versionFileValid) {
		$error[] = 'version file not exists or empty';#
	}
	
	echo 'ERROR: ' . implode(', ', $error);
}
	
?>
