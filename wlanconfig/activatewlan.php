<?php
include('../config/glancrConfig.php');

set_time_limit(0);

if(!array_key_exists('firstname', $_POST) || !array_key_exists('email', $_POST) || 
		!array_key_exists('city', $_POST)) {
			header("Location: index.php");
			exit;
}

setConfigValue('email', '');

$connectionType = getConfigValue('connectionType');

if($connectionType == 'wlan') {
	$ssid = getConfigValue('wlanName');
	$pass = getConfigValue('wlanPass');
	exec('sudo /home/pi/activatewlan.sh ' . escapeshellarg($ssid) . ' ' . escapeshellarg($pass), $status);
} else {
	$status[0] = 1;
}

if(!$status[0]) {
	echo " failed";
	sleep(5);
	header("Location: index.php");
	exit;
} else {
	$host = 'glancr.net';
	$port = 80;
	$waitTimeoutInSeconds = 1;
	$pingTries = 0;
	while(!$fp = fsockopen($host,$port,$errCode,$errStr,$waitTimeoutInSeconds)){
		usleep(500000);
		$pingTries++;
		if($pingTries == 10) {
			echo " failed";
			sleep(5);
			header("Location: index.php");
			exit;
		}
	}
	fclose($fp);
	
	exec('ip -f inet -o addr show ' . $connectionType . '0|cut -d\  -f 7 | cut -d/ -f 1', $ip);
	
	// send email
	$url = 'https://api.glancr.de/mail/';
	
	$ch = curl_init($url);
	
	$jsonData = array(
			'name' => $_POST['firstname'],
			'email' => $_POST['email'],
			'localip' => $ip[0],
			'type' => 'setup'
	);
	
	$jsonDataEncoded = json_encode($jsonData);
	
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	
	$result = curl_exec($ch);
	
	$mailTries = 0;
	while(curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
		$result = curl_exec($ch);
		$mailTries++;
		usleep(300000);
		if($mailTries == 7) {
			setConfigValue('connectionType', 'wlan');
			setConfigValue('emailNotSent', '1');
			
			exec('sudo /home/pi/resetwlan.sh', $status);
			header("Location: index.php");
			exit;
		} 
	}
	
	// save data
	setConfigValue('ip', $ip[0]);
	
	setConfigValue('firstname', $_POST['firstname']);
	
	setConfigValue('email', $_POST['email']);
	
	setConfigValue('city', $_POST['city']);
	
	setConfigValue('reload', "1");
}

?>
