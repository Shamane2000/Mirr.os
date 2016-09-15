<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Glancr</title>
    <script src="js/lib/jquery.min.js"></script>  
    <script src="js/lib/moment-with-locales.min.js"></script> 
    <link rel="stylesheet" href="css/frontend.css">
    
</head>
<body style="overflow: hidden;">
<?php 
include('../config/glancrConfig.php');

$basedir = substr(__DIR__, 0, strrpos(__DIR__, '/'));
$language = getConfigValue('language');

$langParts = explode('_', $language);
$languageShort = $langParts[0];
putenv("LANG=$language");
setlocale(LC_ALL, $language . '.utf8');

setGetTextDomain($basedir ."/locale");


$modules_content = scandir($basedir .'/modules');

foreach($modules_content as $file) {
	if(is_dir($basedir .'/modules/' . $file) && $file != '.' && $file != '..') {
		$modules_available[] = $file;
	}
}

$modules_enabled = file($basedir .'/config/modules_enabled');
foreach ($modules_enabled as $module_enabled) {
	$modules = explode("\t", $module_enabled);
	(sizeof($modules) > 1) ? $width = 'half' : $width = 'full';
	foreach($modules as $module) {
		if(trim($module) == '' || !in_array(trim($module), $modules_available)) {
			echo "<section class=\"module__" . $width. "width placeholdermodule\">\n";
		} else {
			setGetTextDomain("/var/www/html/modules/" . trim($module) . "/locale");
			echo "<section class=\"module__" . $width. "width " . trim($module) . "module\">\n";
			echo "<link rel=\"stylesheet\" href=\"../modules/" . trim($module) . "/frontend/styles.css\">\n";
			echo "<script>";
			include('../modules/' . trim($module) . '/frontend/script.js');
			echo "</script>";
			include('../modules/' . trim($module) . '/frontend/template.php');
		}
    	echo "</section>\n";			
	}
}
?>

<script>
moment.locale('<?php echo $languageShort;?>');	
setInterval(function() {
	var relaodFlagReceived = false;
	var netStatusReceived = false;
	$.ajax({
	  url: "../config/getReload.php"
	}).done(function( data ) {
		relaodFlagReceived = true;
		if(data == 1) {
			location.reload();
		}
	}).complete(function( status ) {
		if(!relaodFlagReceived) {
			location.reload();
		}
	});
	$.ajax({
		url: "../wlanconfig/getStatus.php",
		timeout: 10000
	}).done(function( status ) {
		netStatusStatusReceived = true;
		if(status != 1) {
			location.href = "../nonet.php";
		}
	}).complete(function( status ) {
		if(!netStatusReceived) {
		//	location.href = "../nonet.php";
		}
	});
}, 1000);

</script>
    
</body>
</html>