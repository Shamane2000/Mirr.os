<?php

$basedir = substr(__DIR__, 0, strrpos(__DIR__, '/'));

//$apibaseurl = 'http://api.glancr.dev:8888';
$apibaseurl = 'https://api.glancr.de';

function getConfigValue($key) {
	$servername = "localhost";
	$username = "glancr";
	$password = "glancr";
	$dbname = "glancr";
	
	$conn = new mysqli($servername, $username, $password, $dbname);
	$result = $conn->query('SELECT `value` FROM configuration WHERE `key`="' . $key . '"');
	
	if ($result->num_rows > 0) {
		if($row = $result->fetch_assoc()) {
			return $row['value'];
		} else {
			return 'GLANCR_DEFAULT';
		}
	} else {
		return 'GLANCR_DEFAULT';
	}
}

function setConfigValue($key, $value) {
	$servername = "localhost";
	$username = "glancr";
	$password = "glancr";
	$dbname = "glancr";
	
	$conn = new mysqli($servername, $username, $password, $dbname);
	
	$conn->query("INSERT INTO configuration (`key`, `value`) VALUES ('" . $key . "', '" . $value . "') ON DUPLICATE KEY UPDATE value = '" . $value . "'");
}

function setGetTextDomain($directory) {
	bindtextdomain('config', $directory);
	textdomain('config');
	bind_textdomain_codeset('config', 'UTF-8');
}

?>