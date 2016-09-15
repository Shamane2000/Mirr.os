<?php
include('../config/glancrConfig.php');

$email = getConfigValue($email);
$firstName = getConfigValue($firstname);

setConfigValue('reset', "1");

// send mail
if(strlen($email) > 0 && strlen($firstName)) {
	$url = 'https://api.glancr.de/mail/';
	
	$ch = curl_init($url);
	
	$jsonData = array(
			'name' => $firstName,
			'email' => $email,
			'localip' => '192.168.1.1',
			'type' => 'reset'
	);
	
	$jsonDataEncoded = json_encode($jsonData);
	
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	
	$mailTries = 0;
	while(curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
		$result = curl_exec($ch);
		$mailTries++;
		usleep(300000);
		if($mailTries == 5) {
			error_log( "reset mail could not be sent " . print_r($jsonData) );
		} 
	}
}
$servername = "localhost";
$username = "root";
$password = "glancr";
$dbname = "glancr";

$conn = new mysqli($servername, $username, $password, $dbname);
$result = $conn->query('TRUNCATE TABLE configuration');

$datei = file($basedir .'/config/modules_enabled.default');
$fp = fopen($basedir .'/config/modules_enabled', 'w');
fwrite($fp, implode("", $datei));

// reinstall deleted default modules
foreach ($datei as $row) {
	$modules = explode("\t", $row);
	foreach($modules as $module) {
		if(is_dir($basedir .'/modules/' . $module) !== TRUE) {
			$version = file('http://localhost/server/modules/' . $module . '/version.txt');
			
			$url = 'http://localhost/config/installModule.php';
			$data = array('name' => $module, 'version' => $version);
			
			$options = array(
					'http' => array(
							'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
							'method'  => 'POST',
							'content' => http_build_query($data),
					),
			);
			$context  = stream_context_create($options);
			$installModule = file_get_contents($url, false, $context);
		}
	}
}

setConfigValue('ip', '');
setConfigValue('firstname', '');
setConfigValue('city', '');
setConfigValue('email', '');
setConfigValue('wlanInvisible', '0');
setConfigValue('wlanName', '');
setConfigValue('wlanPass', '');
setConfigValue('connectionType', 'wlan');
setConfigValue('owmApiKey', '');
setConfigValue('language', 'de_DE');
setConfigValue('reload', '0');
setConfigValue('emailNotSent', '0');
setConfigValue('timeformat', '24');

exec('sudo /home/pi/resetwlan.sh', $status);
?>
