<?php
$host = 'glancr.net';
$port = 80;
$waitTimeoutInSeconds = 1;
if($fp = fsockopen($host,$port,$errCode,$errStr,$waitTimeoutInSeconds)){
	echo 'work';
} else {
	echo 'It didnt work';
}
fclose($fp);
?>