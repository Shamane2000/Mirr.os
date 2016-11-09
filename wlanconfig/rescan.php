<?php
exec("sudo /home/pi/rescan.sh", $wlans);
foreach($wlans as $wlan) {
	echo $wlan . ' ';
}
?>
