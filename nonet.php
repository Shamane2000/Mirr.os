<?php 
include('config/glancrConfig.php');

$language = getConfigValue('language');

putenv("LANG=$language");
setlocale(LC_ALL, $language . '.utf8');

setGetTextDomain('config', GLANCR_ROOT ."/locale");
?>
<!DOCTYPE html>
<html>
<head>
	<title>Glancr Start</title>
	<link rel="stylesheet" type="text/css" href="config/css/main.css">
<style>
body {
	background-color: black;
	color: white;
/*	font-family: 'Alegreya Sans';*/
	overflow: hidden;
} 
section {
    max-width: 810px;
    margin: 144px auto 0 auto;
    padding: 0 30px;
}
h2 {
	font-size: 50px;
}
p {
	font-size: 30px;
}
ol {
	font-size: 23px;
}
</style>
 
</head>
 
<body>
<section>
<p>
<?php
$connectionType = getConfigValue('connectionType');

exec('ip -f inet -o addr show ' . $connectionType . '0|cut -d\  -f 7 | cut -d/ -f 1', $ip);

$noIp = false;

if(is_array($ip)) {
	if(array_key_exists(0, $ip)) {
		if($ip[0] == '') {
			$noIp = true;
		}
	} else {
		$noIp = true;
	}
} else {
	$noIp = true;
}

if($noIp) {
	echo '<h2>';
	if(time() % 4 == 0)
		echo _('connecting');
	else if(time() % 4 == 1)
		echo _('connecting') . '.';
	else if(time() % 4 == 2)
		echo _('connecting') . '..';
	else
		echo _('connecting') . '...';
	
	if(isset($_GET['tryTime'])) {
		$time = $_GET['tryTime'];
	} else {
		$time = time();
	}
	echo '</h2>';
	$sleep = 1;
	$url = 'nonet.php?tryTime=' . $time;
} else if($ip[0] != '192.168.8.1') {
	echo '<h2>' . _('connected') . '!</h2>';
	$sleep = 2;
	$url = 'glancr/index.php';
} else {
	if(isset($_GET['tryTime'])) {
		$reset = getConfigValue('reset');
		$emailNotSent = getConfigValue('emailNotSent');
		if($reset) {
			
			echo '<h2>' . _('factory settings') . '!</h2><p>' . _('factory setting text') . '</p>';
			
			setConfigValue('reset', '0');				
		} else if($emailNotSent) {
			
			echo '<h2>' . _('email not sent') . '!</h2><p>' . _('email not sent text') . '</p>';
			
			setConfigValue('emailNotSent', '0');
				
		} else {
			echo '<h2>' . _('failed') . '!</h2><p>' . _('wlan failed text') . '</p>';
		}
		$sleep = 10;
		$url = 'nonet.php';
	} else {
		echo '<h2>Herzlichen Glückwunsch! Wir starten jetzt die Einrichtung</h2>';
		echo '<p>Info: Für die Einrichtung von mirr.OS brauchst du keine App. Du steuerst alles über einen Browser</p>';
		echo '<p><ol><li>mirr.OS erzeugt ein eigenes WLAN. Es heisst „GlancrAP“ (kein Passwort). Verbinde jetzt dein Smartphone oder Notebook damit.</li>';
		echo '<li>Wenn du dich verbunden hast, gib in die Adressleiste eines Browsers (z.B. Firefox, Chrome oder Safari) http://glancr.conf ein. (http:// am Anfang ist wichtig)</li>';
		echo '<li>Der Assistent leitet dich durch das Setup.</li></ol></p>';
		echo '<hr>';
		echo '<h2>Congratulations! We will now proceed the setup</h2>';
		echo '<p>Info: You don’t need an App for the mirr.OS-Setup. You can controll everything with a browser</p>';
		echo '<p><ol><li>mirr.OS generates an own WiFi-Network, which states „GlancrAP“ (no password). Connect your smartphone or notebook with it.</li>';
		echo '<li>If you are connected, please type in the URL-Bar of your browser (i.e. Firefox, Chrome or Safari) http://glancr.conf (http:// at the beginning is important)</li>';
		echo '<li>The assistant will pass you through the setup.</li></ol></p>';
		$sleep = 1;
		$url = 'nonet.php';
	}		
}

?>

</section>
<script>
setTimeout(function(){location.href= '<?php echo $url; ?>'}, <?php echo $sleep * 1000; ?>);
</script>

</body>
</html>
