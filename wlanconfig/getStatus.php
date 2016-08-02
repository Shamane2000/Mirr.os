<?php
include('../config/glancrConfig.php');

$connectionType = getConfigValue('connectionType');

exec('ip -f inet -o addr show ' . $connectionType . '0|cut -d\  -f 7 | cut -d/ -f 1', $ip);

if(is_array($ip) && array_key_exists(0, $ip)) {
	if($ip[0] == '') {
		echo '0';
	} else if($ip[0] != '192.168.8.1') {
		$savedIp = getConfigValue('ip');
		if($ip[0] != $savedIp) {	
			if($savedIp != '') {
				if(ipChangedEMail($ip[0])) {
					setConfigValue('ip', $ip[0]);
				}
			}
		}
		echo '1';
	} else {
		echo '0';
	}
} else {
	echo '0';
}

function ipChangedEMail($newIp) {
	$email = getConfigValue('email');
	$firstName = getConfigValue('firstname');
	
	if($email == "") {
		return true;
	}
	
	// send mail
	$url = 'https://api.glancr.de/mail/';
	
	$ch = curl_init($url);
	
	$jsonData = array(
			'name' => $firstName,
			'email' => $email,
			'localip' => $newIp,
			'type' => 'change'
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
			setConfigValue('connectionType', 'wlan');
			setConfigValue('emailNotSent', '1');
			exec('sudo /home/pi/resetwlan.sh', $status);
			return false;
		} 
	}
	return true;
	
}
?>
